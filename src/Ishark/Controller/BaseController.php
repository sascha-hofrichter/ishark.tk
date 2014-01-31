<?php

namespace Ishark\Controller;

use Ishark\Application;

abstract class BaseController
{
    /** @var \Ishark\Application */
    protected $app;

    function __construct(Application $app)
    {
        $this->app = $app;
    }

    public function getApp()
    {
        return $this->app;
    }
}