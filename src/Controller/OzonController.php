<?php

namespace App\Controller;

use App\Utils\Utility;
use Exception;
use Pimcore\Controller\FrontendController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Pimcore\Model\DataObject\Marketplace;

class OzonController extends FrontendController
{

    /**
     * @Route("/ozon", name="ozon_menu")
     * @param Request $request
     * @return Response
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
        $mrkListing = new Marketplace\Listing();
        $mrkListing->setCondition("marketplaceType = ?", ['Ozon']);
        $marketplaces = $mrkListing->load();
        $tasks = [
            ['id' => 1, 'title' => 'Task 1']
        ];

        return $this->render('ozon/ozon.html.twig', [
            'tasks' => $tasks,
            'marketplaces' => $marketplaces,
        ]);
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

        $categories = json_decode(Utility::getCustomCache('CATEGORY_TREE.json', PIMCORE_PROJECT_ROOT. "/tmp/marketplaces/Ozon"), true) ?? $categories;

        return new JsonResponse($categories);
    }

}
