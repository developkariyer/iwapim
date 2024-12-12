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
     */
    public function chartDemoAction(): Response
    {
        return $this->render('warehouse/chart_demo.html.twig');
    }

    /**
     * @Route("/warehouse/json/{asin}/{sales_channel}", name="sales_data")
     * @throws Exception
     * @throws \DateMalformedStringException
     */
    public function salesDataAction(Request $request): JsonResponse
    {
        $asin = $request->get('asin');
        $salesChannel = $request->get('sales_channel');

        if (!$asin || !$salesChannel) {
            return new JsonResponse(['error' => 'Missing required parameters'], 400);
        }

        $db = Db::get();
        $yesterdayQuery = "SELECT MAX(sale_date) AS latest_date
            FROM iwa_amazon_daily_sales_summary
            WHERE data_source = 1 AND asin = :asin AND sales_channel = :sales_channel";
        $yesterday = $db->fetchOne($yesterdayQuery, [
            'asin' => $asin,
            'sales_channel' => $salesChannel,
        ]);

        if (!$yesterday) {
            return new JsonResponse(['error' => 'No valid data found for the given ASIN and sales channel'], 404);
        }

        $endCurrentData = new DateTime($yesterday);
        $startCurrentData = (clone $endCurrentData)->modify('-26 weeks');
        $startPreviousYearData = (clone $startCurrentData)->modify('-53 weeks');

        $salesData = $db->fetchAllAssociative(
            "SELECT sale_date, total_quantity
                FROM iwa_amazon_daily_sales_summary
                WHERE asin = :asin AND sales_channel = :sales_channel
                    AND sale_date >= :start_previous_year 
                ORDER BY sale_date",
            [
                'asin' => $asin,
                'sales_channel' => $salesChannel,
                'start_previous_year' => $startPreviousYearData->format('Y-m-d'),
            ]
        );

        $response = [
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
            $week = (int) $startPreviousYearData->diff($date)->format('%r%a') / 7;
            if ($week < 53) {
                if (is_null($response['previousYearData'][$week])) {
                    $response['previousYearData'][$week] = 0;
                }
                $response['previousYearData'][$week] += (int) $data['total_quantity'];
                continue;
            }
            if ($week < 79) {
                if (is_null($response['currentData'][$week - 53])) {
                    $response['currentData'][$week - 53] = 0;
                }
                $response['currentData'][$week - 53] += (int) $data['total_quantity'];
                continue;
            }
            if (is_null($response['forecastedData'][$week - 53])) {
                $response['forecastedData'][$week - 53] = 0;
            }
            $response['forecastedData'][$week - 53] += (int) $data['total_quantity'];
        }

        $response['currentData'][26] = $response['forecastedData'][26];

        return $this->json($response);
    }

}
