<?php

namespace Ishark\Services;

use Ishark\Application;

class BaseService
{

    /** @var \Ishark\Application */
    protected $app;

    function __construct(Application $app)
    {
        $this->app = $app;
    }

    /**
     * @return \Ishark\Application|Application
     */
    public function getApp()
    {
        return $this->app;
    }
} 