<?php

namespace Ishark\Controller;

use Symfony\Component\HttpFoundation\FileBag;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class HomeController extends BaseController
{
    /**
     * @return Response
     */
    public function indexAction()
    {
        return Response::create($this->app->getTemplate()->render('home::index'));
    }

    /**
     * @param Request $request
     * @return Response
     * @throws \Exception
     */
    public function uploadAction(Request $request)
    {
        $files = $request->files;
        if (!($files instanceof FileBag)) {
            throw new \Exception('Files missing', 400);
        }
        /** @var \Symfony\Component\HttpFoundation\File\UploadedFile $img */
        $img = $files->get('img');

        $uploadService = $this->getApp()->getUploadService();
        $tmpPath = $uploadService->fromFile($img);
        $filename = $uploadService->saveFile($tmpPath);

        $thumbPath = 'http://' . $this->getApp()->getDomain() . '/' . str_replace('.', '.thumb.', $filename);
        $picPath = 'http://' . $this->getApp()->getDomain() . '/' . $filename;

        $this->getApp()->getLoggerUploads()->addInfo(sprintf('File %s by %s with Web', $filename, $_SERVER['REMOTE_ADDR']));

        return Response::create($this->app->getTemplate()->render('home::upload',
            array(
                'thumbPath' => $thumbPath,
                'picPath' => $picPath
            )
        ));
    }

    /**
     * @param Request $request
     * @return Response
     * @throws \Exception
     */
    public function uploadUrlAction(Request $request)
    {
        $url = trim($request->get('url'));
        if (!$url) {
            throw new \Exception('URL missing', 400);
        }

        $uploadService = $this->getApp()->getUploadService();
        $tmpPath = $uploadService->fromUrl($url);
        $filename = $uploadService->saveFile($tmpPath);

        $thumbPath = 'http://' . $this->getApp()->getDomain() . '/' . str_replace('.', '.thumb.', $filename);
        $picPath = 'http://' . $this->getApp()->getDomain() . '/' . $filename;

        $this->getApp()->getLoggerUploads()->addInfo(sprintf('File %s by %s with URL', $filename, $_SERVER['REMOTE_ADDR']));

        return Response::create($this->app->getTemplate()->render('home::upload',
            array(
                'thumbPath' => $thumbPath,
                'picPath' => $picPath
            )
        ));
    }


}