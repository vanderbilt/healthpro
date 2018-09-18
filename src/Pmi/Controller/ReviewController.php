<?php
namespace Pmi\Controller;

use Silex\Application;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Security\Csrf\CsrfToken;

class ReviewController extends AbstractController
{
    protected static $name = 'review';

    protected static $routes = [
        ['today', '/'],
        ['orders', '/orders'],
        ['measurements', '/measurements'],
        ['participantNameLookup', '/participant/lookup'],
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
        $ordersQuery = 'SELECT participant_id, \'order\' as type, id, order_id, created_ts, collected_ts, processed_ts, finalized_ts, finalized_samples, ' .
            'greatest(coalesce(created_ts, 0), coalesce(collected_ts, 0), coalesce(processed_ts, 0), coalesce(finalized_ts, 0)) AS latest_ts ' .
            'FROM orders WHERE ' .
            '(created_ts >= :today OR collected_ts >= :today OR processed_ts >= :today OR finalized_ts >= :today) ' .
            'AND (site = :site OR collected_site = :site OR processed_site = :site OR finalized_site = :site) ';
        $measurementsQuery = 'SELECT participant_id, \'measurement\' as type, id, null, created_ts, null, null, finalized_ts, null, coalesce(finalized_ts, created_ts) as latest_ts ' .
            'FROM evaluations WHERE ' .
            '(created_ts >= :today OR finalized_ts >= :today) ' .
            'AND (site = :site OR finalized_site = :site)';
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
                                $participants[$participantId]['orderStatus'] = $status;
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
                                $participants[$participantId]['physicalMeasurementStatus'] = $status;
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
        $ordersQuery = "
            SELECT orders.*, orders_history_tmp.*
                FROM orders
                LEFT JOIN
                (SELECT oh1.order_id AS oh_order_id,
                    oh1.user_id AS oh_user_id,
                    oh1.site AS oh_site,
                    oh1.type AS oh_type,
                    oh1.created_ts AS oh_created_ts
                    FROM orders_history AS oh1
                    LEFT JOIN orders_history AS oh2 ON oh1.order_id = oh2.order_id
                    AND oh1.created_ts < oh2.created_ts
                    WHERE oh2.order_id IS NULL
                      AND oh1.type != ?
                ) AS orders_history_tmp ON (orders.id = orders_history_tmp.oh_order_id)
                WHERE orders.site = ?
                  AND finalized_ts IS NULL
                ORDER BY orders.created_ts DESC
            ";
        // TODO get cancel value from order class
        $orders = $app['db']->fetchAll($ordersQuery, ['cancel', $app->getSiteId()]);
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

        $measurements = $app['em']->getRepository('evaluations')->fetchBySql(
            'site = ? AND finalized_ts IS NULL',
            [$app->getSiteId()],
            ['created_ts' => 'DESC']
        );

        return $app['twig']->render('review/measurements.html.twig', [
            'measurements' => $measurements
        ]);
    }

    public function ordersRecentModifyAction(Application $app)
    {
        $site = $app->getSiteId();
        if (!$site) {
            $app->addFlashError('You must select a valid site');
            return $app->redirectToRoute('home');
        }
        $query = "
            SELECT oh_tmp.*, o.*
                FROM
                (SELECT oh1.order_id AS oh_order_id,
                    oh1.type AS oh_type,
                    oh1.created_ts AS oh_created_ts
                    FROM orders_history AS oh1
                    LEFT JOIN orders_history AS oh2 ON oh1.order_id = oh2.order_id
                    AND oh1.created_ts < oh2.created_ts
                    WHERE oh2.order_id IS NULL
                      AND oh1.type != ?
                      AND oh1.type != ? 
                      AND oh1.created_ts >= UTC_TIMESTAMP() - INTERVAL 7 DAY
                ) AS oh_tmp 
                INNER JOIN orders o
                ON (oh_tmp.oh_order_id = o.id)
                WHERE o.site = ?
                ORDER BY oh_tmp.oh_created_ts DESC
            ";
        // TODO get cancel and edit values from order class
        $recentModifyOrders = $app['db']->fetchAll($query, ['active', 'restore', $app->getSiteId()]);
        return $app['twig']->render('review/orders-recent-modify.html.twig', [
            'orders' => $recentModifyOrders
        ]);
    }
}
