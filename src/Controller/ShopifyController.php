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
    const string marketplaceListingsSql = "SELECT
    osv.id,
    osv.imageUrl,
    TRIM(BOTH '\"' FROM SUBSTRING(
        urlLink,
        LOCATE('https://', urlLink),
        LOCATE('\"', urlLink, LOCATE('https://', urlLink)) - LOCATE('https://', urlLink)
    )) AS extractedUrl,
    osv.title,
    osv.uniqueMarketplaceId,
    osv.salePrice,
    osv.saleCurrency,
    osv.quantity,
    osv.lastUpdate,
    osv.`key`
FROM
    object_varyantproduct osv
JOIN
    object_relations_varyantproduct orvp
    ON osv.oo_id = orvp.src_id
    AND orvp.fieldname = 'marketplace'
    AND orvp.dest_id = ?;";


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

        $variantProducts = $db->fetchAllAssociative(self::marketplaceListingsSql, [$marketplaceId]);

        if (empty($variantProducts)) {
            return new JsonResponse(['error' => 'No variant products found'], 404);
        }

        return new JsonResponse($variantProducts, 200);

    }

    /**
     * @Route("/marketplace/listing2product", name="listing2product")
     * @throws Exception
     */
    public function listing2productAction(Request $request): JsonResponse
    {
        $db = Db::get();

        $listingItems = $db->fetchAllAssociative("SELECT dest_id AS id, src_id AS productId FROM object_relations_product WHERE fieldname = 'listingItems'");

        if (empty($listingItems)) {
            return new JsonResponse(['error' => 'No listing items found'], 404);
        }

        return new JsonResponse($listingItems, 200);
    }

    /**
     * @Route("/marketplace/product2eangtin", name="product2eangtin")
     * @throws Exception
     */
    public function product2eangtinAction(): JsonResponse
    {
        $db = Db::get();

        $productEanGtin = $db->fetchAllAssociative("SELECT oo_id AS id, eanGtin FROM object_query_product WHERE eanGtin IS NOT NULL AND eanGtin != '';");

        if (empty($productEanGtin)) {
            return new JsonResponse(['error' => 'No product EAN/GTIN found'], 404);
        }

        return new JsonResponse($productEanGtin, 200);
    }
}

