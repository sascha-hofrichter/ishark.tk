<?php

namespace Ishark\Controller\Api;

use Ishark\Controller\BaseController;
use Ishark\Services\UploadService;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class ImageController extends BaseController
{

    /**
     * @param Request $request
     * @return Response|static
     */
    public function uploadAction(Request $request)
    {
        $content = $request->getContent();

        /** @var UploadService $uploadService */
        $uploadService = $this->getApp()->getUploadService();
        $tmpPah = $uploadService->fromRaw($content);
        $filename = $uploadService->saveFile($tmpPah);

        $this->getApp()->getLoggerUploads()->addInfo(sprintf('File %s by %s with API', $filename, $_SERVER['REMOTE_ADDR']));

        return JsonResponse::create(array('filename' => $filename, 'url' => 'http://' . $this->getApp()->getDomain() . '/' . $filename), 201);
    }

    /**
     * Download the image (given by url) into a directory on the server. <br />
     * Returns the 'new' filename of the image and the full url to the image
     * on the iShark server.
     *
     * @param Request $request
     * @return Response|static
     * @author Sascha Hofrichter <hofrichter.sascha@gmail.com>
     */
    public function uploadUrlAction(Request $request)
    {
        $url = trim($request->get('url'));
        if (!$url) {
            // no url in request
            return JsonResponse::create(array('message' => 'URL missing'), 422);
        }
        $url = urldecode($url);

        /** @var UploadService $uploadService */
        $uploadService = $this->getApp()->getUploadService();
        try {
            // Download the image by given url
            $tmpPath = $uploadService->fromUrl($url);

            // save image into the 'image' directory
            $filename = $uploadService->saveFile($tmpPath);
            return JsonResponse::create(array('filename' => $filename, 'url' => 'http://' . $this->getApp()->getDomain() . '/ishark/web/' . $filename), 201);
        } catch (\Exception $ex) {
            // Can't download or save the image
            return JsonResponse::create(array('message' => $ex->getMessage()), 400);
        }
    }
}