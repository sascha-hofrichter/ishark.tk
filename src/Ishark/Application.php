<?php

namespace Ishark;

use Ishark\Controller\AdminController;
use Ishark\Controller\Api\ImageController as ApiImageController;
use Ishark\Controller\HomeController;
use Ishark\Controller\ImageController;
use Ishark\Services\ImageService;
use Ishark\Services\SecurityService;
use Ishark\Services\UploadService;
use League\Plates\Engine;
use League\Plates\Template;
use Monolog\Handler\StreamHandler;
use Monolog\Logger;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Yaml\Yaml;

/**
 * Class Application
 * @package Ishark
 * @method ImageService getImageService()
 * @method Logger getLoggerUploads()
 * @method array getConfig()
 * @method Template getTemplate()
 * @method Request getRequest()
 * @method UploadService getUploadService()
 * @method SecurityService getSecurityService()
 * @method ApiImageController getApiImageController()
 * @method HomeController getHomeController()
 * @method AdminController getAdminController()
 * @method ImageController getImageController()
 */
class Application extends \Silex\Application
{
    public function __construct(array $values = array())
    {
        parent::__construct($values);

        $app = $this;

        // Controller
        $this['apiImageController'] = $this->share(function () use ($app) {
            return new ApiImageController($app);
        });
        $this['homeController'] = $this->share(function () use ($app) {
            return new HomeController($app);
        });
        $this['adminController'] = $this->share(function () use ($app) {
            return new AdminController($app);
        });
        $this['imageController'] = $this->share(function () use ($app) {
            return new ImageController($app);
        });

        // Service
        $this['uploadService'] = $this->share(function () use ($app) {
            return new UploadService($app);
        });
        $this['imageService'] = $this->share(function () use ($app) {
            return new ImageService($app);
        });
        $this['securityService'] = $this->share(function () use ($app) {
            return new SecurityService($app);
        });


        // Template
        $this['template'] = $this->share(function () use ($app) {
            // Create new Plates engine
            $viewsPath = __DIR__ . '/Resources/views';
            $engine = new Engine($viewsPath);
            $engine->addFolder('home', $viewsPath . '/home');
            $engine->addFolder('admin', $viewsPath . '/admin');

            // Create a new template
            $template = new Template($engine);
            return $template;
        });

        // Config
        $this['config'] = $this->share(function () use ($app) {
            return Yaml::parse($app->getRootPath() . '/config.yml');
        });

        // logger
        $this['loggerUploads'] = $this->share(function () use ($app) {
            $log = new Logger('uploads');
            $log->pushHandler(new StreamHandler($app->getRootPath() . '/logs/uploads.log', Logger::INFO));
            return $log;
        });
    }

    /**
     * @return string
     */
    public function getWebPath()
    {
        return $this->getRootPath() . '/web';
    }

    /**
     * @return string
     */
    public function getRootPath()
    {
        return __DIR__ . '/../..';
    }

    /**
     * @return string
     */
    public function getDomain()
    {
        return $_SERVER['SERVER_NAME'];
    }

    /**
     * @param $name
     * @param $arguments
     *
     * @return mixed
     * @throws \Exception
     */
    function __call($name, $arguments)
    {
        $offset = lcfirst(substr($name, 3));
        if ($this->offsetExists($offset)) {
            return $this[$offset];
        }
        throw new \Exception('Call to undefined method.');
    }
} 