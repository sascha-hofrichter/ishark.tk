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

} 