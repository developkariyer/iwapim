<?php

namespace App\Controller;

use Doctrine\DBAL\Exception;
use Pimcore\Controller\FrontendController;
use Pimcore\Db;
use Pimcore\Model\DataObject\Marketplace;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ShopifyController extends FrontendController
{
    /**
     * @Route("/marketplace/shopify/{marketplaceId}", name="shopify_marketplace")
     * @throws Exception
     */
    public function shopifyAction(Request $request, $marketplaceId): JsonResponse
    {
        $marketplace = Marketplace::getById($marketplaceId);
        if (!$marketplace) {
            return new JsonResponse(['error' => 'Marketplace not found'], 404);
        }

        $db = Db::get();
        $variantProductIds = $db->fetchFirstColumn('SELECT src_id FROM object_relations_varyantproduct WHERE src_id = ?', [$marketplaceId]);
        if (empty($variantProductIds)) {
            return new JsonResponse(['error' => 'No variant products found'], 404);
        }

        return new JsonResponse($variantProductIds, 200);

    }

}