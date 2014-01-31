<?php

namespace Ishark\Controller;


use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class HomeController extends BaseController
{
    public function indexAction()
    {
        $template = $this->app->getTemplate();

        $template->name = 'Jonathan';


        return Response::create($template->render('home::index'));
    }

    public function imageAction(Request $request, $image)
    {
        return Response::create($image);
    }
} 