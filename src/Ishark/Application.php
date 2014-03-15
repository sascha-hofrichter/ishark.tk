<?php

namespace Ishark;

use Ishark\Controller\HomeController;
use Ishark\Services\UploadService;
use Symfony\Component\HttpFoundation\Request;

class Application extends \Silex\Application
{
    public function __construct(array $values = array())
    {
        parent::__construct($values);

        $app = $this;

        // Controller
        $this['controller.api.image'] = $this->share(function () use ($app) {
            return new \Ishark\Controller\Api\ImageController($app);
        });
        $this['controller.home'] = $this->share(function () use ($app) {
            return new HomeController($app);
        });

        // Service
        $this['service.upload'] = $this->share(function () use ($app) {
            return new UploadService($app);
        });

        // Template
        $this['template'] = $this->share(function () use ($app) {
            // Create new Plates engine
            $viewsPath = __DIR__ . '/Resources/views';
            $engine = new \League\Plates\Engine($viewsPath);
            $engine->addFolder('home', $viewsPath . '/home');

            // Create a new template
            $template = new \League\Plates\Template($engine);
            return $template;
        });

    }

    /**
     * @return Request
     */
    public function getRequest()
    {
        return $this['request'];
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
     * @return \League\Plates\Template
     */
    public function getTemplate()
    {
        return $this['template'];
    }

    public function getDomain()
    {
        return $_SERVER['SERVER_NAME'];
    }
} 