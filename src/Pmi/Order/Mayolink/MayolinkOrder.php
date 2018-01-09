<?php
namespace Pmi\Order\Mayolink;

use Silex\Application;
use Pmi\HttpClient;
use Pmi\Order\Order;

class MayolinkOrder
{
    protected $ordersEndpoint = 'http://test.orders.mayomedicallaboratories.com';
    protected $nameSpace = 'http://orders.mayomedicallaboratories.com';
    protected $labelPdf = 'orders/labels.xml';
    protected $createOrder = 'orders/create.xml';
    protected $userName;
    protected $password;
    protected $app;

    protected static $tests = [
        '1SS08' => [
            'temperature' => 'Refrigerated',
            'specimen' => 'Serum SST',
            'code' => '1SSTP',
            'prompt' => 'SST Centrifuge Type'
        ],
        '1PS08' => [
            'temperature' => 'Refrigerated',
            'specimen' => 'Plasma PST',
            'code' => '1PSTP',
            'prompt' => 'PST Centrifuge Type'
        ],
        '1HEP4' => [
            'temperature' => 'Refrigerated',
            'specimen' => 'WB Sodium Heparin'
        ],
        '1ED04' => [
            'temperature' => 'Refrigerated',
            'specimen' => 'Whole Blood EDTA'
        ],
        '1ED10' => [
            'temperature' => 'Refrigerated',
            'specimen' => 'Whole Blood EDTA'
        ],
        '2ED10' => [
            'temperature' => 'Refrigerated',
            'specimen' => 'Whole Blood EDTA'
        ],
        '1UR10' => [
            'temperature' => 'Refrigerated',
            'specimen' => 'Urine'
        ]
    ];
    protected static $salivaTests = [
        '1SAL' => [
            'temperature' => 'Refrigerated',
            'specimen' => 'Saliva'
        ]
    ];

    protected static $doublePrint = '1UR10';

    private $client;

    public function __construct(Application $app)
    {
        $this->app = $app;
        $this->client = new HttpClient(['cookies' => true]);
        if ($app->getConfig('ml_orders_endpoint')) {
            $this->ordersEndpoint = $app->getConfig('ml_orders_endpoint');
        }
        $this->userName = $app->getConfig('ml_username');
        $this->password = $app->getConfig('ml_password');
    }

    public function createOrder($options)
    {
        $samples = $this->getSamples('collected', $options);
        $parameters = ['mayoUrl' => $this->nameSpace, 'options' => $options, 'samples' => $samples];
        $xmlFile = "mayolink/order-create.xml.twig";
        $xml = $this->app['twig']->render($xmlFile, $parameters);
        try {
            $response = $this->client->request('POST', "{$this->ordersEndpoint}/{$this->createOrder}", [
                'auth' => [$this->userName, $this->password],
                'body' => $xml
            ]);            
        } catch (\Exception $e) {
            syslog(LOG_CRIT, $e->getMessage());
            return false;
        }
        if ($response->getStatusCode() !== 201) {
            return false;
        }
        $xmlResponse = $response->getBody();
        $xmlObj = simplexml_load_string($xmlResponse);
        $mayoId = $xmlObj->order->number;
        return $mayoId;
    }

    public function getLabelsPdf($options)
    {
        $samples = $this->getSamples('requested', $options);
        $parameters = ['mayoUrl' => $this->nameSpace, 'options' => $options, 'samples' => $samples, 'doublePrint' => self::$doublePrint];
        $xmlFile = "mayolink/order-labels.xml.twig";
        $xml = $this->app['twig']->render($xmlFile, $parameters);
        try {
            $response = $this->client->request('POST', "{$this->ordersEndpoint}/{$this->labelPdf}", [
                'auth' => [$this->userName, $this->password],
                'body' => $xml
            ]);            
        } catch (\Exception $e) {
            syslog(LOG_CRIT, $e->getMessage());
            return false;
        }
        if ($response->getStatusCode() !== 200) {
            return false;
        }
        $xmlResponse = $response->getBody();
        $xmlObj = simplexml_load_string($xmlResponse);
        $pdf = base64_decode($xmlObj->order->labels);
        return $pdf;
    }

    public function getRequisitionPdf($id)
    {
        try {
            $response = $this->client->request('GET', "{$this->ordersEndpoint}/orders/{$id}.xml", [
                'auth' => [$this->userName, $this->password]
            ]);            
        } catch (\Exception $e) {
            syslog(LOG_CRIT, $e->getMessage());
            return false;       
        }
        if ($response->getStatusCode() !== 200) {
            return false;
        }
        $xmlResponse = $response->getBody();
        $xmlObj = simplexml_load_string($xmlResponse);
        $pdf = base64_decode($xmlObj->order->requisition);
        return $pdf;
    }

    public function getSamples($type, $options)
    {
        if (isset($options['type']) && $options['type'] === 'saliva') {
            $tests = self::$salivaTests;
        } else {
            $tests = self::$tests;
        }
        $mayoSamples = [];
        if ($options["{$type}_samples"]) {
            $samples = json_decode($options["{$type}_samples"]);
            foreach ($samples as $key => $sample) {
                if (!empty($options['centrifugeType']) && in_array($sample, Order::$samplesRequiringCentrifugeType)) {
                    $mayoSamples[] = [
                        'code' => $sample,
                        'name' => $tests[$sample]['specimen'],
                        'questionCode' => $tests[$sample]['code'],
                        'questionPrompt' => $tests[$sample]['prompt'],
                        'questionAnswer' => Order::$centrifugeType[$options['centrifugeType']]
                    ];
                } else {
                    $mayoSamples[] = ['code' => $sample, 'name' => $tests[$sample]['specimen']];
                }               
            }
        } else {
            if ($type !== 'collected') {
                foreach ($tests as $key => $sample) {
                    $mayoSamples[] = ['code' => $key, 'name' => $sample['specimen']];
                }
            }
        }
        return $mayoSamples;
    }
}
