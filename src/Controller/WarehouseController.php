<?php

namespace App\Controller;

use App\Utils\Utility;
use DateTime;
use Doctrine\DBAL\Exception;
use Pimcore\Controller\FrontendController;
use Pimcore\Db;
use Symfony\Component\HttpFoundation\JsonResponse;
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
                'logged_in' => (bool)$request->cookies->get('id_token'),
            ]
        );
    }

    /**
     * @Route("/warehouse/chart", name="chart_demo")
     * @throws Exception
     */
    public function chartDemoAction(): Response
    {
        $db = Db::get();
        $sale_data = $db->fetchAllAssociative("SELECT iwasku, sales_channel, sum(total_quantity) AS total_sale, GROUP_CONCAT(DISTINCT asin) AS asins
                    FROM iwa_amazon_daily_sales_summary GROUP BY iwasku, sales_channel ORDER BY total_sale DESC");
        $salesChannels = [];
        $iwaskus = [];
        foreach ($sale_data as $data) {
            $salesChannel = strtoupper(str_replace('Amazon.', '', $data['sales_channel']));
            if (!isset($iwaskus[$data['iwasku']])) {
                $iwaskus[$data['iwasku']] = [];
            }
            $iwaskus[$data['iwasku']][$salesChannel] = ['total_sale' => $data['total_sale'], 'asins' => $data['asins']];
            $salesChannels[$salesChannel] = true;

        }

        return $this->render('warehouse/chart_demo.html.twig', [
            'salesChannels' => array_keys($salesChannels),
            'iwaskus' => $iwaskus,
        ]);
    }

    /**
     * @Route("/warehouse/json/{iwasku}/{sales_channel}", name="sales_data")
     * @throws Exception
     * @throws \DateMalformedStringException
     */
    public function salesDataAction(Request $request): JsonResponse
    {
        $iwasku = $request->get('iwasku');
        $salesChannel = $request->get('sales_channel');

        if (!$iwasku || !$salesChannel) {
            return new JsonResponse(['error' => 'Missing required parameters'], 400);
        }

        if ($salesChannel !== 'all') {
            $salesChannel = "Amazon." . strtolower($salesChannel);
        }

        $queryText = "iwasku = :iwasku";
        $queryData = ['iwasku' => $iwasku];
        $queryText .= " AND sales_channel = :sales_channel";
        $queryData['sales_channel'] = $salesChannel;

        $db = Db::get();
        $yesterdayQuery = "SELECT MAX(sale_date) AS latest_date
            FROM iwa_amazon_daily_sales_summary
            WHERE data_source = 1 AND $queryText";
        $yesterday = $db->fetchOne($yesterdayQuery, $queryData);

        if (!$yesterday) {
            return new JsonResponse(['error' => 'No valid data found for the given ASIN and sales channel'], 404);
        }

        $endCurrentData = new DateTime($yesterday);
        $startCurrentData = (clone $endCurrentData)->modify('-26 weeks');
        $startPreviousYearData = (clone $startCurrentData)->modify('-53 weeks');
        $queryData['start_previous_year'] = $startPreviousYearData->format('Y-m-d');

        $salesData = $db->fetchAllAssociative(
            "SELECT sale_date, sum(total_quantity) AS total_quantity
                FROM iwa_amazon_daily_sales_summary
                WHERE $queryText AND sale_date >= :start_previous_year
                GROUP BY sale_date
                ORDER BY sale_date",
            $queryData
        );

        $response = [
            'lastYearLast90' => 0,
            'lastYearLast30' => 0,
            'lastYearLast7' => 0,
            'lastYearNext7' => 0,
            'lastYearNext30' => 0,
            'lastYearNext90' => 0,
            'lastYearTotal' => 0,
            'last90' => 0,
            'last30' => 0,
            'last7' => 0,
            'next7' => 0,
            'next30' => 0,
            'next90' => 0,
            'nextTotal' => 0,
            'xAxisLabels' => [],
            'previousYearData' => array_fill(0, 53, null),
            'currentData' => array_fill(0, 53, null),
            'forecastedData' => array_fill(0, 53, null),
        ];

        for ($week = 0; $week <= 53; $week++) {
            $response['xAxisLabels'][] = "Week $week";
        }

        foreach ($salesData as $data) {
            $date = new DateTime($data['sale_date']);
            $days = (int) $endCurrentData->diff($date)->format('%r%a');
            $week = (int) $startPreviousYearData->diff($date)->format('%r%a') / 7;
            $quantity = (int) $data['total_quantity'];

            $response['lastYearLast90'] += ($days <= -365 && $days > -365-90) ? $quantity : 0;
            $response['lastYearLast30'] += ($days <= -365 && $days > -365-30) ? $quantity : 0;
            $response['lastYearLast7'] += ($days <= -365 && $days > -365-7) ? $quantity : 0;
            $response['lastYearNext7'] += ($days > -365 && $days <= -365+7) ? $quantity : 0;
            $response['lastYearNext30'] += ($days > -365 && $days <= -365+30) ? $quantity : 0;
            $response['lastYearNext90'] += ($days > -365 && $days <= -365+90) ? $quantity : 0;
            $response['lastYearTotal'] += ($days < -182) ? $quantity : 0;
            $response['last90'] += ($days > -90 && $days <= 0) ? $quantity : 0;
            $response['last30'] += ($days > -30 && $days <= 0) ? $quantity : 0;
            $response['last7'] += ($days > -7 && $days <= 0) ? $quantity : 0;
            $response['next7'] += ($days > 0 && $days <= 7) ? $quantity : 0;
            $response['next30'] += ($days > 0 && $days <= 30) ? $quantity : 0;
            $response['next90'] += ($days > 0 && $days <= 90) ? $quantity : 0;
            $response['nextTotal'] += ($days >= -182) ? $quantity : 0;

            if ($week < 53) {
                if (is_null($response['previousYearData'][$week])) {
                    $response['previousYearData'][$week] = 0;
                }
                $response['previousYearData'][$week] += $quantity;
                continue;
            }
            if ($week < 79) {
                if (is_null($response['currentData'][$week - 53])) {
                    $response['currentData'][$week - 53] = 0;
                }
                $response['currentData'][$week - 53] += $quantity;
                continue;
            }
            if (is_null($response['forecastedData'][$week - 53])) {
                $response['forecastedData'][$week - 53] = 0;
            }
            $response['forecastedData'][$week - 53] += $quantity;
        }

        if ($response['forecastedData'][26]>0) {
            $response['currentData'][26] = $response['forecastedData'][26];
        }

        return $this->json($response);
    }
}
