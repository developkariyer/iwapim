<?php

namespace Blackbit\BlackbitIframePortletBundle\Controller;

use Pimcore\Controller\FrontendController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class DefaultController extends FrontendController
{
    /**
     * @Route("/blackbit_iframe_portlet")
     */
    public function indexAction(Request $request)
    {
        return new Response('Hello world from blackbit_iframe_portlet');
    }
}
