<?php

namespace Ishark\Controller\Api;

use Ishark\Controller\BaseController;
use Ishark\Services\UploadService;
use Symfony\Component\HttpFoundation\JsonResponse;
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

        /** @var UploadService $uploadService */
        $app = $this->getApp();
        $uploadService = $app['service.upload'];
        $tmpPah = $uploadService->fromRaw($content);
        $filename = $uploadService->saveFile($content, $tmpPah);

        return JsonResponse::create(['filename' => $filename, 'url' => 'http://' . $this->getApp()->getDomain() . '/' . $filename], 201);
    }

} 