<?php

namespace Ishark\Controller;

use Ishark\Services\ContentTypeService;
use Ishark\Services\ConvertService;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class HomeController extends BaseController
{
    public function indexAction()
    {
        $template = $this->app->getTemplate();
        return Response::create($template->render('home::index'));
    }

    public function imageAction(Request $request, $image)
    {
        // remove ext
        $info = pathinfo($image);
        $md5 = ConvertService::unpackmd5($info['filename']);
        $path = $this->getApp()->getWebPath() . '/images/' . $md5 . '.' . $info['extension'];

        return Response::create(file_get_contents($path), 200, array('Content-Type' => ContentTypeService::toType($info['extension'])));
    }
} 