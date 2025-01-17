<?php

namespace App\Controller;

use App\Utils\Utility;
use Doctrine\DBAL\Exception;
use Pimcore\Controller\FrontendController;
use Pimcore\Db;
use Random\RandomException;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class GoogleSheetsController extends FrontendController
{
    static string $iwaskuSql = "SELECT DISTINCT iwasku FROM iwa_amazon_daily_sales_summary ORDER BY iwasku";
    static string $asin2iwaskuSql = "SELECT DISTINCT regkey AS asin, regvalue AS iwasku FROM iwa_registry WHERE regtype='asin-to-iwasku' ORDER BY asin";
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
    static string $fbaStatsSql = "SELECT * FROM iwa_amazon_inventory_summary WHERE warehouse = ?";


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
        if (!in_array($channel, ['Amazon.com', 'Amazon.co.uk', 'Amazon.ca', 'Amazon.eu', 'Amazon.au', 'Amazon.co.jp', 'Amazon.co.uk', 'Amazon.com.au', 'all'])) {
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
        if (!in_array($warehouse, ['CA', 'EU', 'UK', 'US', 'AU'])) {
            return new JsonResponse(['error' => 'Invalid warehouse'], 400);
        }
        $fbaData = json_decode(Utility::getCustomCache($filename, $cachePath, 3600, true), true);
        if (empty($fbaData)) {
            $fbaData = $db->fetchAllAssociative(self::$fbaStatsSql, [$warehouse]);
            Utility::setCustomCache($filename, $cachePath, json_encode($fbaData));
        }
        return $this->json($fbaData);
    }
}