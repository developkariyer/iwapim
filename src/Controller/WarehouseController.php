<?php

namespace App\Controller;

use App\Utils\Utility;
use Pimcore\Controller\FrontendController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;


class WarehouseController extends FrontendController
{

    /**
     * @Route("/c/{container}", name="container")
     * 
     * Warehouse related function. This function shows container name
     * built into scanned code. QR Code contains following link:
     * http://iwa.web.tr/c/{container_name_encoded}
     */
    public function containerAction(Request $request): Response
    {
        return $this->render(
            'iwapim/container.html.twig', 
            [
                'container_name' => Utility::decodeContainer($request->get('container')),
                'logged_in' => $request->cookies->get('id_token') ? true : false,
            ]
        );
    }


}
