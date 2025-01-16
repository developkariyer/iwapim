<?php

namespace App\Controller;

use Doctrine\DBAL\Exception;
use Pimcore\Controller\FrontendController;
use Pimcore\Db;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;

class GoogleSheetsController extends FrontendController
{
    static string $iwaskuSql = "SELECT DISTINCT iwasku FROM iwa_amazon_daily_sales_summary ORDER BY iwasku";
    static string $asin2iwaskuSql = "SELECT DISTINCT regkey AS asin, regvalue AS iwasku FROM iwa_registry WHERE regtype='asin-to-iwasku' ORDER BY asin";
    static string $asinPreSql = "SELECT 
    iwasku,
    MAX(CASE WHEN sales_channel = 'Amazon.com' THEN asin END) AS us_asin,
    MAX(CASE WHEN sales_channel = 'Amazon.eu' THEN asin END) AS eu_asin,
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

}