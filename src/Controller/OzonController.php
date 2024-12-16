<?php

namespace App\Controller;

use Pimcore\Controller\FrontendController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class OzonController extends FrontendController
{

    /**
     * @Route("/ozon", name="ozon_menu")
     */
    public function ozonAction(Request $request): Response
    {
        /*
        // ozon marketplace id is 268776
        $ozonMarketplace = Marketplace::getByMarketplaceType('Ozon', ['limit' => 1]);
        if (!$ozonMarketplace) {
            return new Response('Ozon marketplace not found');
        }

        $ozonConnector = new OzonConnector($ozonMarketplace);
        $categories = $ozonConnector->getCategories();
        */

        return $this->render('ozon/ozon.html.twig');
    }

    /**
     * @Route("/ozon/category-tree", name="ozon_category_tree")
     */
    public function categoryTreeAction(Request $request): JsonResponse
    {
        $categories = [
            ['id' => 1, 'name' => 'Electronics', 'children' => [
                ['id' => 2, 'name' => 'Laptops'],
                ['id' => 3, 'name' => 'Smartphones'],
            ]],
            ['id' => 4, 'name' => 'Fashion', 'children' => [
                ['id' => 5, 'name' => 'Men'],
                ['id' => 6, 'name' => 'Women'],
            ]],
        ];

        return new JsonResponse($categories);
    }

}
