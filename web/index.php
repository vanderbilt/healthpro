<?php
require_once __DIR__ . '/../vendor/autoload.php';

use Pmi\Controller;
use Pmi\Application\HpoApplication;

$app = new HpoApplication();
$app['templatesDirectory'] = realpath(__DIR__ . '/../views');
$app['errorTemplate'] = 'error.html.twig';
if ($app->isProd() || $app->isDev()) { // for now, use twig memcache in prod
    $app['memcacheSession'] = true;
    $app['twigCacheHandler'] = 'memcache';
} else {
    $app['memcacheSession'] = true;
    $app['cacheDirectory'] = realpath(__DIR__ . '/../cache');
    $app['twigCacheHandler'] = 'file';
}

$app
    ->setup()
    ->mount('/', new Controller\DefaultController())
    ->mount('/_dev', new Controller\DevController())
    ->mount('/dashboard', new Controller\DashboardController())
    ->run()
;
