<?php

namespace Ishark\Controller\Api;

use Ishark\Controller\BaseController;
use Ishark\Services\UploadService;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class ImageController extends BaseController
{

    public function testAction(Request $request)
    {
        return Response::create('test');
    }

    public function uploadAction(Request $request)
    {
        $content = $request->getContent();
        $contentType = trim($request->getContentType());

        if (strlen($contentType) === 0) {
            $contentType = trim($request->server->get('HTTP_CONTENT_TYPE'));
        }
        if (strlen($contentType) === 0) {
            $contentType = trim($request->request->get('content-type'));
        }

        /** @var UploadService $uploadService */
        $uploadService = $this->getApp()['service.upload'];
        $uploadService->checkContentType($contentType);
        $uploadService->saveFile($content, $contentType);

        return Response::create();
    }

} 