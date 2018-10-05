<?php
namespace Pmi\Controller;

use Pmi\Evaluation\Evaluation;
use Silex\Application;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Security\Csrf\CsrfToken;
use Pmi\Order\Order;

class ReviewController extends AbstractController
{
    protected static $name = 'review';

    protected static $routes = [
        ['today', '/'],
        ['orders', '/orders'],
        ['measurements', '/measurements'],
        ['participantNameLookup', '/participant/lookup'],
        ['measurementsRecentModify', '/measurements/recent/modify'],
        ['ordersRecentModify', '/orders/recent/modify']
    ];
    protected static $orderStatus = [
        'created_ts' => 'Created',
        'collected_ts' => 'Collected',
        'processed_ts' => 'Processed',
        'finalized_ts' => 'Finalized'
    ];
    protected static $measurementsStatus = [
        'created_ts' => 'Created',
        'finalized_ts' => 'Finalized'
    ];

    protected function getTodayRows($db, $today, $site)
    {
        $ordersQuery = 'SELECT o.participant_id, \'order\' as type, o.id, null as parent_id, o.order_id, o.rdr_id, o.created_ts, o.collected_ts, o.processed_ts, o.finalized_ts, o.finalized_samples, ' .
            'greatest(coalesce(o.created_ts, 0), coalesce(o.collected_ts, 0), coalesce(o.processed_ts, 0), coalesce(o.finalized_ts, 0), coalesce(oh.created_ts, 0)) AS latest_ts, ' .
            'oh.type as h_type ' .
            'FROM orders o ' .
            'LEFT JOIN orders_history oh ' .
            'ON o.history_id = oh.id WHERE ' .
            '(o.created_ts >= :today OR o.collected_ts >= :today OR o.processed_ts >= :today OR o.finalized_ts >= :today OR oh.created_ts >= :today) ' .
            'AND (o.site = :site OR o.collected_site = :site OR o.processed_site = :site OR o.finalized_site = :site) ';
        $measurementsQuery = 'SELECT e.participant_id, \'measurement\' as type, e.id, e.parent_id, null, e.rdr_id, e.created_ts, null, null, e.finalized_ts, null, ' .
            'greatest(coalesce(e.created_ts, 0), coalesce(e.finalized_ts, 0), coalesce(eh.created_ts, 0)) as latest_ts, ' .
            'eh.type as h_type ' .
            'FROM evaluations e ' .
            'LEFT JOIN evaluations_history eh ' .
            'ON e.history_id = eh.id WHERE ' .
            'e.id NOT IN (SELECT parent_id FROM evaluations WHERE parent_id IS NOT NULL) ' .
            'AND (e.created_ts >= :today OR e.finalized_ts >= :today OR eh.created_ts >= :today) ' .
            'AND (e.site = :site OR e.finalized_site = :site)';
        $query = "($ordersQuery) UNION ($measurementsQuery) ORDER BY latest_ts DESC";

        return $db->fetchAll($query, [
            'today' => $today,
            'site' => $site
        ]);
    }

    protected function getTodayParticipants($db, $today, $site)
    {
        $participants = [];
        $emptyParticipant = [
            'order' => null,
            'orderCount' => 0,
            'orderStatus' => '',
            'finalizedSamples' => null,
            'physicalMeasurement' => null,
            'physicalMeasurementCount' => 0,
            'physicalMeasurementStatus' => ''
        ];
        foreach ($this->getTodayRows($db, $today, $site) as $row) {
            $participantId = $row['participant_id'];
            if (!array_key_exists($participantId, $participants)) {
                $participants[$participantId] = $emptyParticipant;
            }
            switch ($row['type']) {
                case 'order':
                    if (is_null($participants[$participantId]['order'])) {
                        $participants[$participantId]['order'] = $row;
                        $participants[$participantId]['orderCount'] = 1;
                        // Get order status
                        foreach (self::$orderStatus as $field => $status) {
                            if ($row[$field]) {
                                $participants[$participantId]['orderStatus'] = $this->getOrderStatus($row, $status);
                            }
                        }
                        // Get number of finalized samples
                        if ($row['finalized_samples'] && ($samples = json_decode($row['finalized_samples'])) && is_array($samples)) {
                            $participants[$participantId]['finalizedSamples'] = count($samples);
                        }
                    } else {
                        $participants[$participantId]['orderCount']++;
                    }
                    break;
                case 'measurement':
                    if (is_null($participants[$participantId]['physicalMeasurement'])) {
                        $participants[$participantId]['physicalMeasurement'] = $row;
                        $participants[$participantId]['physicalMeasurementCount'] = 1;
                        // Get physical measurements status
                        foreach (self::$measurementsStatus as $field => $status) {
                            if ($row[$field]) {
                                $participants[$participantId]['physicalMeasurementStatus'] = $this->getEvaluationStatus($row, $status);
                            }
                        }
                    } else {
                        $participants[$participantId]['physicalMeasurementCount']++;
                    }
                    break;
            }
        }

        return $participants;
    }

    public function getOrderStatus($row, $status)
    {
        $order = new Order;
        $type = $row['h_type'];
        if ($type === $order::ORDER_CANCEL) {
            $status = 'Cancelled';
        } elseif ($type === $order::ORDER_UNLOCK) {
            $status = 'Unlocked';
        } elseif ($type === $order::ORDER_EDIT) {
            $status = 'Edited & Finalized';
        } elseif (!empty($row['finalized_ts']) && empty($row['rdr_id'])) {
            $status = 'Processed';
        }
        return $status;
    }

    public function getEvaluationStatus($row, $status)
    {
        $evaluation = new Evaluation();
        if ($row['h_type'] === $evaluation::EVALUATION_CANCEL) {
            $status = 'Cancelled';
        } elseif (!empty($row['parent_id']) && empty($row['rdr_id'])){
            $status = 'Unlocked';
        } elseif (!empty($row['parent_id']) && !empty($row['rdr_id'])) {
            $status = 'Edited & Finalized';
        } elseif (!empty($row['finalized_ts']) && empty($row['rdr_id'])) {
            $status = 'Created';
        }
        return $status;
    }

    public function todayAction(Application $app, Request $request)
    {
        $site = $app->getSiteId();
        if (!$site) {
            $app->addFlashError('You must select a valid site');
            return $app->redirectToRoute('home');
        }

        // Get beginning of today (at midnight) in user's timezone
        $startString = 'today';
        // Allow overriding start time to test in non-prod environments
        if (!$app->isProd() && intval($request->query->get('days')) > 0) {
            $startString = '-' . intval($request->query->get('days')) . ' days';
        }
        $startTime = new \DateTime($startString, new \DateTimeZone($app->getUserTimezone()));
        // Get MySQL date/time string in UTC
        $startTime->setTimezone(new \DateTimezone('UTC'));
        $today = $startTime->format('Y-m-d H:i:s');

        $participants = $this->getTodayParticipants($app['db'], $today, $site);
        
        // Preload first 5 names
        $count = 0;
        foreach (array_keys($participants) as $id) {
            $participants[$id]['participant'] = $app['pmi.drc.participants']->getById($id);
            if (++$count >= 5) {
                break;
            }
        }

        return $app['twig']->render('review/today.html.twig', [
            'participants' => $participants
        ]);
    }

    public function participantNameLookupAction(Application $app, Request $request)
    {
        if (!$app['csrf.token_manager']->isTokenValid(new CsrfToken('review', $request->get('csrf_token')))) {
            return new JsonResponse(['error' => 'Invalid request'], 403);
        }

        $id = trim($request->query->get('id'));
        if (!$id) {
            return new JsonResponse(null);
        }

        $participant = $app['pmi.drc.participants']->getById($id);
        if (!$participant) {
            return new JsonResponse(null);
        }

        return new JsonResponse([
            'id' => $id,
            'firstName' => $participant->firstName,
            'lastName' => $participant->lastName
        ]);
    }

    public function ordersAction(Application $app)
    {
        $site = $app->getSiteId();
        if (!$site) {
            $app->addFlashError('You must select a valid site');
            return $app->redirectToRoute('home');
        }
        $order = new Order($app);
        $unlockedOrders = $order->getSiteUnlockedOrders();
        $unfinalizedOrders = $order->getSiteUnfinalizedOrders();
        $orders = array_merge($unlockedOrders, $unfinalizedOrders);
        return $app['twig']->render('review/orders.html.twig', [
            'orders' => $orders
        ]);
    }

    public function measurementsAction(Application $app)
    {
        $site = $app->getSiteId();
        if (!$site) {
            $app->addFlashError('You must select a valid site');
            return $app->redirectToRoute('home');
        }
        $evaluation = new Evaluation($app);
        $measurements = $evaluation->getSiteUnfinalizedEvaluations();

        return $app['twig']->render('review/measurements.html.twig', [
            'measurements' => $measurements
        ]);
    }

    public function measurementsRecentModifyAction(Application $app)
    {
        $site = $app->getSiteId();
        if (!$site) {
            $app->addFlashError('You must select a valid site');
            return $app->redirectToRoute('home');
        }
        $evaluation = new Evaluation($app);
        $recentModifyMeasurements = $evaluation->getSiteRecentModifiedEvaluations();
        return $app['twig']->render('review/measurements-recent-modify.html.twig', [
            'measurements' => $recentModifyMeasurements
        ]);
    }

    public function ordersRecentModifyAction(Application $app)
    {
        $site = $app->getSiteId();
        if (!$site) {
            $app->addFlashError('You must select a valid site');
            return $app->redirectToRoute('home');
        }
        $order = new Order($app);
        $recentModifyOrders = $order->getSiteRecentModifiedOrders();
        return $app['twig']->render('review/orders-recent-modify.html.twig', [
            'orders' => $recentModifyOrders
        ]);
    }
}
