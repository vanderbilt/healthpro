<?php
require_once __DIR__ . '/../vendor/autoload.php';

use Pmi\Controller;
use Pmi\Application\HpoApplication;

$app = new HpoApplication();
$app['templatesDirectory'] = realpath(__DIR__ . '/../views');
$app['errorTemplate'] = 'error.html.twig';
$app['sessionTimeout'] = 7 * 60;
$app['sessionWarning'] = 2 * 60;
if (true) { // for now, use twig memcache everywhere
    $app['sessionHandler'] = 'datastore';
    $app['twigCacheHandler'] = 'memcache';
} else {
    $app['sessionHandler'] = 'datastore';
    $app['cacheDirectory'] = realpath(__DIR__ . '/../cache');
    $app['twigCacheHandler'] = 'file';
}

$app
    ->setup()
    ->mount('/', new Controller\DefaultController())
    ->mount('/', new Controller\OrderController())
    ->mount('/', new Controller\EvaluationController())
    ->mount('/_dev', new Controller\DevController())
    ->mount('/dashboard', new Controller\DashboardController())
    ->run()
;
