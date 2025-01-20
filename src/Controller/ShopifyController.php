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
        $variantProductIds = $db->fetchAllAssociative("SELECT 
    op.iwasku,
    op.oo_id,
    op.productCategory,
    op.key
FROM 
    object_relations_varyantproduct orvp
JOIN 
    object_relations_product orp
    ON orvp.src_id = orp.dest_id
JOIN 
    object_product op
    ON orp.src_id = op.oo_id
WHERE 
    orvp.fieldname = 'marketplace'
    AND orp.fieldname = 'listingItems'
    AND orvp.dest_id = ?; -- Replace '?' with the external input
", [$marketplaceId]);
        if (empty($variantProductIds)) {
            return new JsonResponse(['error' => 'No variant products found'], 404);
        }

        return new JsonResponse($variantProductIds, 200);

    }

}