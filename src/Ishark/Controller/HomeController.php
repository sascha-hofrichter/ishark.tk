<?php

namespace Ishark\Controller;

use Ishark\Services\ContentTypeService;
use Ishark\Services\ConvertService;
use Ishark\Services\UploadService;
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
        $template = $this->app->getTemplate();
        return Response::create($template->render('home::index'));
    }

    /**
     * @param Request $request
     * @param $image
     * @return Response
     */
    public function imageAction(Request $request, $image)
    {
        // remove ext
        $info = pathinfo($image);
        $md5 = ConvertService::unpackmd5($info['filename']);
        $path = $this->getApp()->getRootPath() . '/images/' . substr($md5, 0, 2) . '/' . $md5 . '.' . $info['extension'];

        return Response::create(file_get_contents($path), 200, array('Content-Type' => ContentTypeService::toType($info['extension'])));
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

        /** @var UploadService $uploadService */
        $app = $this->getApp();
        $uploadService = $app['service.upload'];
        $tmpPah = $uploadService->fromFile($img);
        $filename = $uploadService->saveFile($tmpPah);

        $template = $this->app->getTemplate();
        return Response::create($template->render('home::upload', array('file' => $filename, 'domain' => $this->getApp()->getDomain())));
    }
} 