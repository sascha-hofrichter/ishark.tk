<?php

namespace Ishark\Controller;

use Ishark\Services\ConvertService;
use Symfony\Component\HttpFoundation\Response;

class AdminController extends BaseController
{
    /**
     * @return Response
     */
    public function indexAction()
    {
        $path = $this->getApp()->getRootPath() . '/images';

        $timeMapping = array();
        $imageList = array();
        foreach (scandir($path) as $subDir) if ($subDir != '.' && $subDir != '..' && $subDir != '.gitkeep') {
            foreach (scandir($path . '/' . $subDir) as $file) if ($file != '.' && $file != '..') {
                $filePath = $path . '/' . $subDir . '/' . $file;
                $time = filemtime($filePath) * 100000;
                $time += $timeMapping[$time]++;

                $pathInfo = pathinfo($file);

                $imageList[$time] = ConvertService::packmd5($pathInfo['basename']) . '.' . $pathInfo['extension'];
            }
        }

        $template = $this->app->getTemplate();
        return Response::create($template->render('admin::index', array('images' => $imageList, 'domain' => $this->getApp()->getDomain())));
    }

} 