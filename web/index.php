<?php

require_once __DIR__ . '/../vendor/autoload.php';

$filename = __DIR__ . preg_replace('#(\?.*)$#', '', $_SERVER['REQUEST_URI']);
if (php_sapi_name() === 'cli-server' && is_file($filename)) {
    return false;
}

$app = new Ishark\Application();

if ($app->getDomain() != 'ishark.tk') {
    $app['debug'] = true;
    error_reporting(E_ALL);
    ini_set('display_errors', 1);
}

$app->post('/api/image', function () use ($app) {
    return $app->getApiImageController()->uploadAction($app->getRequest());
});

$app->get('/admin', function () use ($app) {
    return $app->getAdminController()->indexAction($app->getRequest());
});

$app->get('/{image}', function ($image) use ($app) {
    return $app->getImageController()->imageAction($app->getRequest(), $image);
})
    ->assert('image', '[a-zA-Z-_0-9]+\.\w{3}');

$app->get('/{image}', function ($image) use ($app) {
    return $app->getImageController()->imageThumbAction($app->getRequest(), $image);
})
    ->assert('image', '[a-zA-Z-_0-9]+\.thumb\.\w{3}');

$app->post('/upload', function () use ($app) {
    return $app->getHomeController()->uploadAction($app->getRequest());
});

$app->post('/uploadURL', function () use ($app) {
    return $app->getHomeController()->uploadUrlAction($app->getRequest());
});

$app->get('/', function () use ($app) {
    return $app->getHomeController()->indexAction($app->getRequest());
});


$app->run();
