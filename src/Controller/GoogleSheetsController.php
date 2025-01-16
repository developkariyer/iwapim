<?php

namespace App\Controller;

use Doctrine\DBAL\Exception;
use Pimcore\Controller\FrontendController;
use Pimcore\Db;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class GoogleSheetsController extends FrontendController
{
    static string $iwaskuSql = "SELECT DISTINCT iwasku FROM iwa_amazon_daily_sales_summary ORDER BY iwasku";
    static string $asin2iwaskuSql = "SELECT DISTINCT regkey, regvalue FROM iwa_registry WHERE regtype='asin-to-iwasku' ORDER BY asin";

    /**
     * @Route("/sheets/main", name="sheets")
     * @returns JsonResponse
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
     * @returns JsonResponse
     * @throws Exception
     */
    public function asin2iwaskuAction(): JsonResponse
    {
        $db = Db::get();
        $iwaskuList = $db->fetchAllAssociative(self::$asin2iwaskuSql);
        return $this->json($iwaskuList);
    }


}