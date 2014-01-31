<?php

require_once __DIR__ . '/../vendor/autoload.php';

$filename = __DIR__.preg_replace('#(\?.*)$#', '', $_SERVER['REQUEST_URI']);
if (php_sapi_name() === 'cli-server' && is_file($filename)) {
    return false;
}

$app = new Ishark\Application();
$app['debug'] = true;

$app->get('/api/test', function () use ($app) {
    return $app['controller.api.image']->testAction($app->getRequest());
});

$app->post('/api/image', function () use ($app) {
    return $app['controller.api.image']->uploadAction($app->getRequest());
});

$app->get('/{image}', function ($image) use ($app) {
    return $app['controller.home']->imageAction($app->getRequest(), $image);
});

$app->get('/', function () use ($app) {
    return $app['controller.home']->indexAction($app->getRequest());
});

$app->run();
