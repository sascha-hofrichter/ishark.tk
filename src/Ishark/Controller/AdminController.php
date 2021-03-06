<?php

namespace Ishark\Controller;

use Ishark\Services\ConvertService;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class AdminController extends BaseController
{
    /**
     * @param Request $request
     * @return Response
     */
    public function indexAction(Request $request)
    {
        $config = $this->getApp()->getConfig();

        list($_SERVER['PHP_AUTH_USER'], $_SERVER['PHP_AUTH_PW']) = explode(':', base64_decode(substr($_SERVER['HTTP_AUTHORIZATION'], 6)));

        if (!isset($_SERVER['PHP_AUTH_USER']) || !($_SERVER['PHP_AUTH_USER'] == $config['admin_u'] && $_SERVER['PHP_AUTH_PW'] == $config['admin_pw'])) {
            header('WWW-Authenticate: Basic realm="My Realm"');
            header('HTTP/1.0 401 Unauthorized');
            echo 'go away - bitch!';
            exit;
        }

        $path = $this->getApp()->getRootPath() . '/images';
        $filsize = 0;
        $timeMapping = array();
        $imageList = array();
        foreach (scandir($path) as $subDir) if ($subDir != '.' && $subDir != '..' && $subDir != '.gitkeep') {
            foreach (scandir($path . '/' . $subDir) as $file) if ($file != '.' && $file != '..') {
                $filePath = $path . '/' . $subDir . '/' . $file;
                $time = filemtime($filePath) * 100000;
                if (!array_key_exists($time, $timeMapping)) {
                    $timeMapping[$time] = 0;
                }
                $time += $timeMapping[$time]++;

                $pathInfo = pathinfo($file);
                $filsize += filesize($filePath);
                $imageList[$time] = ConvertService::packmd5($pathInfo['filename']) . '.' . $pathInfo['extension'];
            }
        }

        krsort($imageList);

        $filsize = $filsize / 1024 / 1024 / 1024;

        return Response::create($this->app->getTemplate()->render('admin::index',
                array(
                    'images' => $imageList,
                    'domain' => $this->getApp()->getDomain(),
                    'filesize' => round($filsize, 2)
                )
            )
        );
    }

} 