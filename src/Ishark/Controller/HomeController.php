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
     * @throws \Exception
     */
    public function imageAction(Request $request, $image)
    {
        // remove ext
        $info = pathinfo($image);
        if ($info['extension'] == null) {
            throw new \Exception('File not found!', 404);
        }
        $md5 = ConvertService::unpackmd5($info['filename']);
        $path = $this->getApp()->getRootPath() . '/images/' . substr($md5, 0, 2) . '/' . $md5 . '.' . $info['extension'];

        if (!file_exists($path)) {
            throw new \Exception('File not found!', 404);
        }

        $mtime = @filemtime($path);
        $gmt_mtime = gmdate('D, d M Y H:i:s', $mtime) . ' GMT';
        $etag = sprintf('%08x-%08x', crc32($path), $mtime);

        header('ETag: "' . $etag . '"');
        header('Last-Modified: ' . $gmt_mtime);
        header('Cache-Control: private');

        if (isset($_SERVER['HTTP_IF_NONE_MATCH']) && !empty($_SERVER['HTTP_IF_NONE_MATCH'])) {
            $tmp = explode(';', $_SERVER['HTTP_IF_NONE_MATCH']); // IE fix!
            if (!empty($tmp[0]) && strtotime($tmp[0]) == strtotime($gmt_mtime)) {
                header('HTTP/1.1 304 Not Modified');
                exit;
            }
        }

        if (isset($_SERVER['HTTP_IF_NONE_MATCH'])) {
            if (str_replace(array('\"', '"'), '', $_SERVER['HTTP_IF_NONE_MATCH']) == $etag) {
                header('HTTP/1.1 304 Not Modified');
                exit;
            }
        }
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