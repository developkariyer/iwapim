<?php

namespace App\Controller;

use App\Utils\Registry;
use App\Utils\Utility;
use Doctrine\DBAL\Exception;
use Pimcore\Controller\FrontendController;
use Pimcore\Db;
use Pimcore\Model\DataObject\Marketplace;
use Random\RandomException;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class GoogleSheetsController extends FrontendController
{
    static string $iwaskuSql = "SELECT DISTINCT iwasku FROM iwa_amazon_daily_sales_summary ORDER BY iwasku";
    static string $asin2iwaskuSql = "SELECT DISTINCT regkey AS asin, regvalue AS iwasku FROM iwa_registry WHERE regtype='asin-to-iwasku' ORDER BY asin";
    static string $ebayCarParts = "SELECT iap.brand_code, COALESCE(iap.title, '') as title, COALESCE(iap.min_price, 0) AS min_price, COALESCE(iap.max_price, 0) AS max_price, MIN(iai.price) AS input_min_price, MAX(iai.price) AS input_max_price, COALESCE(SUM(iai.stock), 0) AS total_stock FROM iwa_autoparts_parts iap LEFT JOIN iwa_autoparts_inventory iai ON iap.brand_code = iai.brand_code AND iai.price > 0 GROUP BY iap.brand_code, iap.title, iap.min_price, iap.max_price;";
    static string $asinPreSql =    "SELECT 
                                        iwasku,
                                        MAX(CASE WHEN sales_channel = 'Amazon.com' THEN asin END) AS us_asin,
                                        MAX(CASE WHEN sales_channel = 'Amazon.co.uk' THEN asin END) AS eu_asin,
                                        MAX(CASE WHEN sales_channel = 'Amazon.co.uk' THEN asin END) AS uk_asin,
                                        MAX(CASE WHEN sales_channel = 'Amazon.ca' THEN asin END) AS ca_asin,
                                        MAX(CASE WHEN sales_channel = 'Amazon.au' THEN asin END) AS au_asin,
                                        MAX(CASE WHEN sales_channel = 'Amazon.co.jp' THEN asin END) AS jp_asin
                                    FROM 
                                        iwa_amazon_daily_sales_summary
                                    GROUP BY 
                                        iwasku
                                    ORDER BY
                                        iwasku;";
    static string $salesStatsSql = "WITH date_ranges AS (
                                        SELECT 
                                            CURRENT_DATE AS today,
                                            DATE_SUB(CURRENT_DATE, INTERVAL 6 DAY) AS last7_start,
                                            DATE_SUB(CURRENT_DATE, INTERVAL 29 DAY) AS last30_start,
                                            DATE_SUB(CURRENT_DATE, INTERVAL 89 DAY) AS last90_start,
                                            DATE_SUB(CURRENT_DATE, INTERVAL 179 DAY) AS last180_start,
                                            DATE_SUB(CURRENT_DATE, INTERVAL 364 DAY) AS last365_start,
                                            DATE_SUB(CURRENT_DATE, INTERVAL 1 YEAR) AS preYear_today,
                                            DATE_SUB(DATE_SUB(CURRENT_DATE, INTERVAL 1 YEAR), INTERVAL 6 DAY) AS preYearLast7_start,
                                            DATE_SUB(DATE_SUB(CURRENT_DATE, INTERVAL 1 YEAR), INTERVAL 29 DAY) AS preYearLast30_start,
                                            DATE_SUB(DATE_SUB(CURRENT_DATE, INTERVAL 1 YEAR), INTERVAL 89 DAY) AS preYearLast90_start,
                                            DATE_SUB(DATE_SUB(CURRENT_DATE, INTERVAL 1 YEAR), INTERVAL 179 DAY) AS preYearLast180_start,
                                            DATE_SUB(DATE_SUB(CURRENT_DATE, INTERVAL 1 YEAR), INTERVAL 364 DAY) AS preYearLast365_start,
                                            DATE_ADD(DATE_SUB(CURRENT_DATE, INTERVAL 1 YEAR), INTERVAL 1 DAY) AS preYearNext7_start,
                                            DATE_ADD(DATE_SUB(CURRENT_DATE, INTERVAL 1 YEAR), INTERVAL 7 DAY) AS preYearNext7_end,
                                            DATE_ADD(DATE_SUB(CURRENT_DATE, INTERVAL 1 YEAR), INTERVAL 30 DAY) AS preYearNext30_end,
                                            DATE_ADD(DATE_SUB(CURRENT_DATE, INTERVAL 1 YEAR), INTERVAL 90 DAY) AS preYearNext90_end,
                                            DATE_ADD(DATE_SUB(CURRENT_DATE, INTERVAL 1 YEAR), INTERVAL 180 DAY) AS preYearNext180_end
                                    )
                                    SELECT 
                                        iwasku,
                                        asin,
                                        SUM(CASE WHEN sale_date BETWEEN last7_start AND today THEN total_quantity ELSE 0 END) AS last7,
                                        SUM(CASE WHEN sale_date BETWEEN last30_start AND today THEN total_quantity ELSE 0 END) AS last30,
                                        SUM(CASE WHEN sale_date BETWEEN last90_start AND today THEN total_quantity ELSE 0 END) AS last90,
                                        SUM(CASE WHEN sale_date BETWEEN last180_start AND today THEN total_quantity ELSE 0 END) AS last180,
                                        SUM(CASE WHEN sale_date BETWEEN last365_start AND today THEN total_quantity ELSE 0 END) AS last366,
                                        SUM(CASE WHEN sale_date BETWEEN preYearLast7_start AND preYear_today THEN total_quantity ELSE 0 END) AS preYearLast7,
                                        SUM(CASE WHEN sale_date BETWEEN preYearLast30_start AND preYear_today THEN total_quantity ELSE 0 END) AS preYearLast30,
                                        SUM(CASE WHEN sale_date BETWEEN preYearLast90_start AND preYear_today THEN total_quantity ELSE 0 END) AS preYearLast90,
                                        SUM(CASE WHEN sale_date BETWEEN preYearLast180_start AND preYear_today THEN total_quantity ELSE 0 END) AS preYearLast180,
                                        SUM(CASE WHEN sale_date BETWEEN preYearLast365_start AND preYear_today THEN total_quantity ELSE 0 END) AS preYearLast365,
                                        SUM(CASE WHEN sale_date BETWEEN preYearNext7_start AND preYearNext7_end THEN total_quantity ELSE 0 END) AS preYearNext7,
                                        SUM(CASE WHEN sale_date BETWEEN preYearNext7_start AND preYearNext30_end THEN total_quantity ELSE 0 END) AS preYearNext30,
                                        SUM(CASE WHEN sale_date BETWEEN preYearNext7_start AND preYearNext90_end THEN total_quantity ELSE 0 END) AS preYearNext90,
                                        SUM(CASE WHEN sale_date BETWEEN preYearNext7_start AND preYearNext180_end THEN total_quantity ELSE 0 END) AS preYearNext180
                                    FROM 
                                        iwa_amazon_daily_sales_summary
                                    CROSS JOIN 
                                        date_ranges
                                    WHERE 
                                        data_source = 1
                                        AND sales_channel = ?
                                    GROUP BY 
                                        iwasku, asin;";
    static string $fbaStatsSql =   "SELECT * FROM iwa_amazon_inventory_summary WHERE warehouse = ?";
    static string $whStatsSql =    "SELECT
                                        JSON_UNQUOTE(JSON_EXTRACT(json_data, '$.ASIN')) AS asin,
                                        JSON_UNQUOTE(JSON_EXTRACT(json_data, '$.Name')) AS name,
                                        JSON_UNQUOTE(JSON_EXTRACT(json_data, '$.FNSKU')) AS fnsku,
                                        JSON_UNQUOTE(JSON_EXTRACT(json_data, '$.IWASKU')) AS iwasku,
                                        JSON_UNQUOTE(JSON_EXTRACT(json_data, '$.Category')) AS category,
                                        JSON_UNQUOTE(JSON_EXTRACT(json_data, '$.\"Total Count\"')) AS total_count,
                                        JSON_UNQUOTE(JSON_EXTRACT(json_data, '$.\"Count in Raf\"')) AS count_in_raf,
                                        JSON_UNQUOTE(JSON_EXTRACT(json_data, '$.\"Count in Ship\"')) AS count_in_ship
                                    FROM iwa_inventory
                                    WHERE
                                        warehouse = 'NJ'
                                        AND iwasku NOT IN ('', 'NULL')";

    static string $amazonSkuSql =  "SELECT 
                                        CONCAT(ocv.id, '-', ocv.listingId) AS id,
                                        ocv.title,
                                        SUBSTRING_INDEX(
                                            SUBSTRING_INDEX(ocv.urlLink, '\"', 14), 
                                            '\"', -1 
                                        ) AS url,
                                        ocv.salePrice,
                                        ocv.saleCurrency,
                                        ocv.marketplaceId AS country,
                                        ocv.sku,
                                        ocv.listingId AS amazonId,
                                        ocv.fulfillmentChannel,
                                        ocv.status,
                                        ocv.quantity,
                                        ocv.countryOfOrigin,
                                        ocv.madeInTurkiye,
                                        ocv.brand,
                                        ocv.lastUpdate AS pimUpdate,
                                        ocv.ean AS amazonEan,
                                        oqv.oo_id AS listingId,
                                        oqv.uniqueMarketplaceId AS asin
                                    FROM object_collection_AmazonMarketplace_varyantproduct AS ocv
                                    JOIN object_query_varyantproduct AS oqv 
                                        ON ocv.id = oqv.oo_id
                                    JOIN objects AS obj ON obj.id = oqv.oo_id WHERE obj.published = 1
                                    ORDER BY id;"; //



    static string $marketplaceListingsSql ="SELECT
                                                osv.id,
                                                osv.imageUrl,
                                                osv.title,
                                                osv.uniqueMarketplaceId,
                                                osv.salePrice,
                                                osv.saleCurrency,
                                                osv.quantity,
                                                osv.lastUpdate,
                                                osv.`key`,
                                                orvp.dest_id AS marketplaceId,
                                                oqm.marketplaceType,
                                                oqm.marketplaceUrl
                                            FROM
                                                object_varyantproduct osv
                                            JOIN
                                                object_relations_varyantproduct orvp
                                                ON osv.oo_id = orvp.src_id
                                                AND orvp.fieldname = 'marketplace'
                                            LEFT JOIN
                                                object_query_marketplace oqm
                                                ON oqm.oo_id = orvp.dest_id
                                            WHERE
                                                osv.published = 1";

    /**
     * @Route("/sheets/main", name="sheets")
     * @throws Exception
     */
    public function sheetsAction(): JsonResponse
    {
        $db = Db::get();
        $iwaskuList = $db->fetchFirstColumn(self::$iwaskuSql);
        $response = [];
        foreach ($iwaskuList as $iwasku) {
            $response[$iwasku] = [];
        }
        return $this->json($response);
    }

    /**
     * @Route("/marketplace/product2eangtin", name="product2eangtin")
     * @throws Exception
     */
    public function product2eangtinAction(): JsonResponse
    {
        $db = Db::get();

        $productEanGtin = $db->fetchAllAssociative("SELECT oo_id AS id, iwasku, eanGtin FROM object_query_product WHERE eanGtin IS NOT NULL AND eanGtin != '';");

        if (empty($productEanGtin)) {
            return new JsonResponse(['error' => 'No product EAN/GTIN found'], 404);
        }

        return new JsonResponse($productEanGtin, 200);
    }

    /**
     * @Route("/marketplace/listing2ean", name="listing2ean")
     * @throws Exception
     */
    public function listing2eanAction(): JsonResponse
    {
        $db = Db::get();

        $listingEan = $db->fetchAllAssociative("SELECT regkey as id, regvalue as ean from iwa_registry WHERE regtype='listing-to-ean'");

        if (empty($listingEan)) {
            return new JsonResponse(['error' => 'No listing EAN found'], 404);
        }

        return new JsonResponse($listingEan, 200);
    }


    /**
     * @Route("/marketplace/product2listing2ean", name="product2listing2ean")
     * @throws Exception
     */
    public function product2listing2eanAction(): JsonResponse
    {
        $db = Db::get();

        $productListings = $db->fetchAllAssociative("SELECT src_id AS productId, dest_id AS listingId FROM object_relations_product WHERE fieldname = 'listingItems'");

        if (empty($productListings)) {
            return new JsonResponse(['error' => 'No product listing EAN found'], 404);
        }

        $result = [];
        foreach ($productListings as $productListing) {
            $productListingEan = $db->fetchOne("SELECT regvalue from iwa_registry WHERE regtype='listing-to-ean' AND regkey=?", [$productListing['listingId']]);
            if (empty($productListingEan)) {
                continue;
            }
            $productId = $productListing['productId'];
            if (isset($result[$productId])) {
                $result[$productId] .= ",$productListingEan";
            } else {
                $result[$productId] = "eans:$productListingEan";
            }
        }
        $response = [];
        foreach ($result as $key => $value) {
            $response[] = ['id' => $key, 'eans' => $value];
        }

        return new JsonResponse($response, 200);
    }

    /**
     * @Route("/marketplace/asin2eantesvik", name="asin2eantesvik")
     * @throws Exception
     */
    public function asin2eantesvikAction(): JsonResponse
    {
        $db = Db::get();

        $asinEanTesvik = $db->fetchAllAssociative("SELECT regkey as asin, regvalue as eantesvik from iwa_registry WHERE regtype='asin-to-ean-tesvik'");

        if (empty($asinEanTesvik)) {
            return new JsonResponse(['error' => 'No ASIN to EAN Tesvik found'], 404);
        }

        $result = [];
        foreach ($asinEanTesvik as $item) {
            $iwasku = Registry::getKey($item['asin'], 'asin-to-iwasku');
            if (empty($iwasku)) {
                continue;
            }
            $result[] = ['iwasku' => $iwasku, 'ean_tesvik' => $item['eantesvik']];
        }

        return new JsonResponse($result, 200);
    }

    /**
     * @Route("/marketplace/listing2product", name="listing2product")
     * @throws Exception
     */
    public function listing2productAction(): JsonResponse
    {
        $db = Db::get();

        $listingItems = $db->fetchAllAssociative("SELECT dest_id AS id, src_id AS productId FROM object_relations_product WHERE fieldname = 'listingItems'");

        if (empty($listingItems)) {
            return new JsonResponse(['error' => 'No listing items found'], 404);
        }

        return new JsonResponse($listingItems, 200);
    }



    /**
     * @Route("/sheets/asin2iwasku", name="sheets_asin2iwasku")
     * @throws Exception
     */
    public function asin2iwaskuAction(): JsonResponse
    {
        $db = Db::get();
        $iwaskuList = $db->fetchAllAssociative(self::$asin2iwaskuSql);
        return $this->json($iwaskuList);
    }

    /**
     * @Route("/sheets/amazonsku", name="sheets_amazonsku")
     * @throws Exception
     */
    public function amazonSkuAction(): JsonResponse
    {
        $db = Db::get();
        $skuData = $db->fetchAllAssociative(self::$amazonSkuSql);
        return $this->json($skuData);
    }

    /**
     * @Route("/sheets/amazonsales/pre", name="sheets_amazonsales_pre")
     * @throws Exception
     */
    public function amazonSalesPreAction(): JsonResponse
    {
        $db = Db::get();
        $saleData = $db->fetchAllAssociative(self::$asinPreSql);
        return $this->json($saleData);
    }

    /**
     * @Route("/sheets/amazonsales/{channel}", name="sheets_amazonsales")
     * @throws Exception
     * @throws RandomException
     */
    public function amazonSalesAction(Request $request): JsonResponse
    {
        $db = Db::get();
        $channel = $request->get('channel');
        $filename = 'channelStats_' . $channel;
        $cachePath = PIMCORE_PROJECT_ROOT . "/tmp";
        if (!in_array($channel, ['Amazon.com', 'Amazon.co.uk', 'Amazon.ca', 'Amazon.eu', 'Amazon.co.uk', 'Amazon.com.au', 'all'])) {
            return new JsonResponse(['error' => 'Invalid channel'], 400);
        }
        $saleData = json_decode(Utility::getCustomCache($filename, $cachePath, 3600, true), true);
        if (empty($saleData)) {
            $saleData = $db->fetchAllAssociative(self::$salesStatsSql, [$channel]);
            Utility::setCustomCache($filename, $cachePath, json_encode($saleData));
        }
        return $this->json($saleData);
    }

    /**
     * @Route("/sheets/amazonfba/{warehouse}", name="sheets_amazonfba")
     * @throws Exception
     * @throws RandomException
     */
    public function amazonFbaAction(Request $request): JsonResponse
    {
        $db = Db::get();
        $warehouse = $request->get('warehouse');
        $filename = 'channelFba_' . $warehouse;
        $cachePath = PIMCORE_PROJECT_ROOT . "/tmp";
        if (!in_array($warehouse, ['CA', 'EU', 'UK', 'US', 'AU', 'NJ'])) {
            return new JsonResponse(['error' => 'Invalid warehouse'], 400);
        }
        $fbaData = json_decode(Utility::getCustomCache($filename, $cachePath, 3600, true), true);
        if (empty($fbaData)) {
            if ($warehouse === 'NJ') {
                $fbaData = $db->fetchAllAssociative(self::$whStatsSql);
            } else {
                $fbaData = $db->fetchAllAssociative(self::$fbaStatsSql, [$warehouse]);
            }
            Utility::setCustomCache($filename, $cachePath, json_encode($fbaData));
        }
        return $this->json($fbaData);
    }

    /**
     * @Route("/sheets/catalog", name="sheets_catalog")
     * @throws Exception
     */
    public function catalogAction(): JsonResponse
    {
        $db = Db::get();
        $catalogData = $db->fetchAllAssociative("SELECT iwasku, id, `key`, variationSize, variationColor, productIdentifier, packageDimension1, packageDimension2, packageDimension3, packageWeight, productCategory, name, '1' AS asin FROM object_product WHERE iwasku IS NOT NULL AND iwasku != ''");
        return $this->json($catalogData);
    }

    /**
     * @Route("/sheets/eancatalog", name="sheets_eancatalog")
     * @throws Exception
     */
    public function eanCatalogAction(): JsonResponse
    {
        $db = Db::get();
        $eanData = $db->fetchAllAssociative("SELECT iwasku, eanGtin, requireEan, id, `key`, variationSize, variationColor, productIdentifier, productCategory, name FROM object_product WHERE iwasku IS NOT NULL AND iwasku != '' AND published = 1 ORDER BY id;");
        return $this->json($eanData);
    }

    /**
     * @Route("/sheets/eanlisting", name="sheets_eanlisting")
     * @throws Exception
     */
    public function eanListingAction(): JsonResponse
    {
        $db = Db::get();
        $eanData = $db->fetchAllAssociative("SELECT ovp.id, ovp.key, ovp.ean, ovp.uniqueMarketplaceId, ovp.salePrice, ovp.saleCurrency, ovp.lastUpdate, ovp.imageUrl, ovp.quantity, orp.src_id AS product_id FROM object_varyantproduct AS ovp LEFT JOIN object_relations_product AS orp ON orp.dest_id = ovp.id AND orp.fieldname = 'listingItems' WHERE ovp.published = 1 ORDER BY ovp.id;");
        return $this->json($eanData);
    }

    /**
     * @Route("/marketplace/shopify/{marketplaceId}", name="shopify_marketplace", defaults={"marketplaceId"=null})
     * @throws Exception
     */
    public function shopifyAction($marketplaceId): JsonResponse
    {
        $db = Db::get();

        if (is_null($marketplaceId)) {
            $variantProducts = $db->fetchAllAssociative(self::$marketplaceListingsSql);
        } else {
            $marketplace = Marketplace::getById($marketplaceId);
            if (!$marketplace) {
                return new JsonResponse(['error' => 'Marketplace not found'], 404);
            }
            $variantProducts = $db->fetchAllAssociative(self::$marketplaceListingsSql." AND orvp.dest_id=?", [$marketplaceId]);
        }

        if (empty($variantProducts)) {
            return new JsonResponse(['error' => 'No variant products found'], 404);
        }

        return new JsonResponse($variantProducts, 200);
    }

    /**
     * @Route("/sheets/ebaycarparts", name="sheets_ebaycarparts")
     * @throws Exception
     */
    public function ebayCarPartsAction(): JsonResponse
    {
        $db = Db::get();
        $carPartsData = $db->fetchAllAssociative(self::$ebayCarParts);
        return $this->json($carPartsData);
    }

}