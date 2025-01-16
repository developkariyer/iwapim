<?php

namespace App\Controller;

use App\Utils\Utility;
use DateMalformedStringException;
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
                    FROM iwa_amazon_daily_sales_summary  WHERE data_source = 1 AND sale_date > CURDATE() - INTERVAL 365 DAY GROUP BY iwasku, sales_channel ORDER BY total_sale DESC");
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
     * @throws DateMalformedStringException
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

        $queryText = "iwasku = :iwasku AND sales_channel = :sales_channel";
        $queryData = ['iwasku' => $iwasku, 'sales_channel' => $salesChannel];

        $db = Db::get();
        $yesterdayQuery = "SELECT DISTINCT sale_date 
            FROM iwa_amazon_daily_sales_summary
            WHERE data_source = 1 AND $queryText ORDER BY sale_date DESC LIMIT 1 OFFSET 1";
        $yesterday = $db->fetchOne($yesterdayQuery, $queryData);

        if (!$yesterday) {
            return new JsonResponse(['error' => 'No valid data found for the given ASIN and sales channel'], 404);
        }

        $twoYearsAgo = (new DateTime($yesterday))->modify('-640 days');
        $yesterdayDate = (new DateTime($yesterday));
        $queryData['start_previous_year'] = $twoYearsAgo->format('Y-m-d');

        $salesData = $db->fetchAllAssociative(
            "SELECT sale_date, data_source, sum(total_quantity) AS total_quantity
                FROM iwa_amazon_daily_sales_summary
                WHERE $queryText AND sale_date >= :start_previous_year
                GROUP BY sale_date, data_source
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
            'last90' => 0,
            'last30' => 0,
            'last7' => 0,
            'next7' => 0,
            'next30' => 0,
            'next90' => 0,
            'xAxisLabels' => [],
            'previousYearData' => array_fill(0, 365, null),
            'currentData' => array_fill(0, 365, null),
            'forecastedData' => array_fill(0, 365, null),
        ];

        for ($day = 1; $day <= 365; $day++) {
            $response['xAxisLabels'][] = "$day";
        }

        foreach ($salesData as $data) {
            $quantity = $data['total_quantity'];
            $date = new DateTime($data['sale_date']);
            $days = (int) $yesterdayDate->diff($date)->format('%r%a');
            $dataSource = (int) $data['data_source'];

            $response['lastYearLast90'] += ($days <= -365 && $days > -365-90) ? $quantity : 0;
            $response['lastYearLast30'] += ($days <= -365 && $days > -365-30) ? $quantity : 0;
            $response['lastYearLast7'] += ($days <= -365 && $days > -365-7) ? $quantity : 0;
            $response['lastYearNext7'] += ($days > -365 && $days <= -365+7) ? $quantity : 0;
            $response['lastYearNext30'] += ($days > -365 && $days <= -365+30) ? $quantity : 0;
            $response['lastYearNext90'] += ($days > -365 && $days <= -365+90) ? $quantity : 0;
            $response['last90'] += ($dataSource == 1 && $days > -90 && $days <= 0) ? $quantity : 0;
            $response['last30'] += ($dataSource == 1 && $days > -30 && $days <= 0) ? $quantity : 0;
            $response['last7'] += ($dataSource == 1 && $days > -7 && $days <= 0) ? $quantity : 0;
            $response['next7'] += ($dataSource == 0 && $days > 0 && $days <= 7) ? $quantity : 0;
            $response['next30'] += ($dataSource == 0 && $days > 0 && $days <= 30) ? $quantity : 0;
            $response['next90'] += ($dataSource == 0 && $days > 0 && $days <= 90) ? $quantity : 0;

            if ($days < -275) {
                $response['previousYearData'][640 + $days] = $quantity;
                continue;
            }
            if ($dataSource == 1 && $days <= 0) {
                $response['currentData'][275 + $days] = $quantity;
            }
            if ($dataSource == 0 && $days >= 0) {
                $response['forecastedData'][275 + $days] = $quantity;
            }
        }
        if (is_null($response['forecastedData'][275]) && is_null($response['currentData'][275])) {
            $response['forecastedData'][275] = $response['currentData'][275] = ($response['forecastedData'][276] ?? 0 + $response['currentData'][274] ?? 0) / 2;
        }
        if (is_null($response['forecastedData'][275])) {
            $response['forecastedData'][275] = $response['currentData'][275];
        }
        if (is_null($response['currentData'][275])) {
            $response['currentData'][275] = $response['forecastedData'][275];
        }

        return $this->json($response);
    }
}
