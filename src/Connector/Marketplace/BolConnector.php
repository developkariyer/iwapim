<?php

namespace App\Connector\Marketplace;

use Symfony\Component\HttpClient\HttpClient;
use Symfony\Component\HttpClient\ScopingHttpClient;
use Pimcore\Model\DataObject\Marketplace;
use Pimcore\Model\DataObject\VariantProduct;
use App\Utils\Utility;
use App\Connector\Marketplace\MarketplaceConnectorAbstract;

class BolConnector extends MarketplaceConnectorAbstract
{
    private static $apiUrl = [
        'loginTokenUrl' => "https://login.bol.com/token?grant_type=client_credentials",
        'offerExportUrl' => "/retailer/offers/export/",
        'processStatusUrl' => "/shared/process-status/",
        'productsUrl' => "/retailer/products/",
        'catalogProductsUrl' => "/retailer/content/catalog-products/",
        'commissionUrl' => "/retailer/commission/",
        'orders' => '/retailer/orders/'
    ];
    public static $marketplaceType = 'Bol.com';

    protected function prepareToken()
    {
        if (!Utility::checkJwtTokenValidity($this->marketplace->getBolJwtToken())) {
            $response = $this->httpClient->request('POST', static::$apiUrl['loginTokenUrl'], [
                'headers' => [
                    'Authorization' => 'Basic ' . base64_encode("{$this->marketplace->getBolClientId()}:{$this->marketplace->getBolSecret()}"),
                    'Accept' => 'application/json'
                ]
            ]);
            if ($response->getStatusCode() !== 200) {
                throw new \Exception('Failed to get JWT token from Bol.com');
            }
            $decodedResponse = json_decode($response->getContent(), true);
            $this->marketplace->setBolJwtToken($decodedResponse['access_token']);
            $this->marketplace->save();
        } 
        $this->httpClient = ScopingHttpClient::forBaseUri($this->httpClient, 'https://api.bol.com/', [
            'headers' => [
                'Authorization' => 'Bearer ' . $this->marketplace->getBolJwtToken(),
                'Accept' => 'application/vnd.retailer.v10+json',
                'Content-Type' => 'application/vnd.retailer.v10+json'
            ],
        ]);
    }

    protected function requestOfferReport()
    {
        $this->prepareToken();
        $response = $this->httpClient->request('POST', static::$apiUrl['offerExportUrl'], ['json' => ['format' => 'CSV']]);
        if ($response->getStatusCode() !== 202) {
            throw new \Exception('Failed to get offer report from Bol.com');
        }
        $decodedResponse = json_decode($response->getContent(), true);
        if ($decodedResponse['status'] !== 'SUCCESS' && $decodedResponse['status'] !== 'PENDING') {
            throw new \Exception('Failed to get offer report from Bol.com');
        }
        return $decodedResponse;
    }

    protected function reportStatus($decodedResponse)
    {
        $this->prepareToken();
        $status = $decodedResponse['status'] === 'SUCCESS';
        $statusUrl = $decodedResponse['links'][0]['href'] ?? static::$apiUrl['processStatusUrl'] . ($decodedResponse['processStatusId'] ?? '');

        while (!$status) {
            echo "  Waiting for report...\n";
            sleep(5);
            $response = $this->httpClient->request('GET', $statusUrl);
            if ($response->getStatusCode() !== 200) {
                throw new \Exception('Failed to get offer report from Bol.com');
            }
            $decodedResponse = json_decode($response->getContent(), true);
            switch ($decodedResponse['status'] ?? '') {
                case 'SUCCESS':
                    $status = true;
                    break;
                case 'PENDING':
                    $status = false;
                    break;
                default: throw new \Exception('Failed to get offer report from Bol.com: '. $response->getContent());
            }
        }
        if (empty($decodedResponse['entityId'])) {
            throw new \Exception('Failed to get offer report from Bol.com.');
        }
        return $decodedResponse['entityId'] ?? [];
    }

    protected function downloadOfferReport($forceDownload = false)
    {
        $this->prepareToken();
        $report = Utility::getCustomCache('OFFERS_EXPORT_REPORT.csv', PIMCORE_PROJECT_ROOT. "/tmp/marketplaces/{$this->marketplace->getKey()}");
        if ($report === false || $forceDownload) {
            echo "Requesting offer report from Bol.com\n";
            $entityId = $this->reportStatus($this->requestOfferReport());
            $response = $this->httpClient->request('GET', static::$apiUrl['offerExportUrl'] . $entityId, ['headers' => ['Accept' => 'application/vnd.retailer.v10+csv']]);
            if ($response->getStatusCode() !== 200) {
                throw new \Exception('Failed to get offer report from Bol.com:'.$response->getContent());
            }
            $report = $response->getContent();
            Utility::setCustomCache('OFFERS_EXPORT_REPORT.csv', PIMCORE_PROJECT_ROOT. "/tmp/marketplaces/{$this->marketplace->getKey()}", $report);
        } else {
            echo "Using cached report\n";
        }
        return $report;
    }

    protected function downloadExtra($apiEndPoint, $type, $parameter, $query = [])
    {
        $this->prepareToken();
        $response = $this->httpClient->request($type, $apiEndPoint . $parameter, ['query' => $query]);
        if ($response->getStatusCode() !== 200) {
            echo "Failed to {$type} {$apiEndPoint}{$parameter}: {$response->getContent()}\n";
            return null;
        }
        echo "{$apiEndPoint}{$parameter} ";
        usleep(200000);
        return json_decode($response->getContent(), true);
    }

    protected function getListings($report)
    {
        $rows = array_map('str_getcsv', explode("\n", trim($report)));
        $headers = array_shift($rows);
        $this->listings = [];
        $totalCount = count($rows);
        $index = 0;
        foreach ($rows as $row) {
            $index++;
            if (count($row) === count($headers)) {
                $rowData = array_combine($headers, $row);
                $ean = $rowData['ean'];
                echo "($index/$totalCount) Downloading $ean ";
                $this->listings[$ean] = $rowData;
                $this->listings[$ean]['catalog'] = $this->downloadExtra(static::$apiUrl['catalogProductsUrl'], 'GET', $ean);
                $this->listings[$ean]['assets'] = $this->downloadExtra(static::$apiUrl['productsUrl'], 'GET', "$ean/assets", ['usage' => 'IMAGE']);
                $this->listings[$ean]['placement'] = $this->downloadExtra(static::$apiUrl['productsUrl'], 'GET', "$ean/placement");
                $this->listings[$ean]['commission'] = $this->downloadExtra(static::$apiUrl['commissionUrl'], 'GET', $ean, ['condition' => 'NEW', 'unit-price' => $rowData['bundlePricesPrice']]);
                $this->listings[$ean]['product-ids'] = $this->downloadExtra(static::$apiUrl['productsUrl'], 'GET', "$ean/product-ids");
                Utility::setCustomCache("EAN_{$ean}.json", PIMCORE_PROJECT_ROOT. "/tmp/marketplaces/{$this->marketplace->getKey()}", json_encode($this->listings[$ean]));
                echo "OK\n";
            }
        }
    }

    protected function getAttribute($listing, $id)
    {
        $retval = '';
        if (!is_array($id)) {
            $id = [$id];
        }
        if (!empty($listing['catalog']['attributes']) && is_array($listing['catalog']['attributes'])) {
            foreach ($listing['catalog']['attributes'] as $attribute) {
                if (in_array($attribute['id'], $id, true)) {
                    $retval .= " " . ($attribute['values'][0]['value'] ?? '');
                    $key = array_search($attribute['id'], $id);
                    if ($key !== false) {
                        unset($id[$key]);
                    }
                    if (empty($id)) {
                        return trim($retval);
                    }
                }
            }
        }
        return trim($retval);
    }

    protected function getFolder($listing)
    {
        $folder = Utility::checkSetPath(
            Utility::sanitizeVariable($this->marketplace->getKey(), 190),
            Utility::checkSetPath('Pazaryerleri')
        );
        if (!empty($listing['placement']['categories'][0]['categoryName'])) {
            $folder = Utility::checkSetPath(
                Utility::sanitizeVariable($listing['placement']['categories'][0]['categoryName'], 190),
                $folder
            );
            $subcategory = $listing['placement']['categories'][0]['subCategories'][0] ?? [];
            while (!empty($subcategory)) {
                if (!empty($subcategory['name'])) {
                    $folder = Utility::checkSetPath(
                        Utility::sanitizeVariable($subcategory['name'], 190),
                        $folder
                    );
                }
                $subcategory = $subcategory['subCategories'][0] ?? [];
            }
        }
        $family = $this->getAttribute($listing, ['Family Name']);
        if (!empty($family)) {
            $folder = Utility::checkSetPath(
                Utility::sanitizeVariable($family, 190),
                $folder
            );
        }
        return $folder;
    }

    public function download($forceDownload = false)
    {
        $this->listings = json_decode(Utility::getCustomCache('BOL_LISTINGS.json', PIMCORE_PROJECT_ROOT. "/tmp/marketplaces/".urlencode($this->marketplace->getKey())), true);
        if (!(empty($this->listings) || $forceDownload)) {
            echo "Using cached listings\n";
            return;
        }
        $this->getListings(
            $this->downloadOfferReport($forceDownload)
        );
        Utility::setCustomCache('BOL_LISTINGS.json', PIMCORE_PROJECT_ROOT. "/tmp/marketplaces/{$this->marketplace->getKey()}", json_encode($this->listings));
    }

    public function import($updateFlag, $importFlag)
    {
        if (empty($this->listings)) {
            echo "Nothing to import\n";
        }
        $total = count($this->listings);
        $index = 0;
        foreach($this->listings as $listing) {
            $index++;
            echo "($index/$total) Importing ".($listing['product-ids']['bolProductId'] ?? '')." ...";
            VariantProduct::addUpdateVariant(
                variant: [
                    'imageUrl' => '',
                    'urlLink' => $this->getUrlLink($listing['placement']['url'] ?? ''),
                    'salePrice' => $listing['bundlePricesPrice'] ?? '0.00',
                    'saleCurrency' => 'EUR',
                    'title' => $this->getAttribute($listing, ['Title']),
                    'attributes' => $this->getAttribute($listing, ['Dropdown Size HxWxL', 'Colour']),
                    'quantity' => $listing['correctedStock'] ?? 0,
                    'uniqueMarketplaceId' => $listing['product-ids']['bolProductId'] ?? '',
                    'apiResponseJson' => json_encode($listing),
                    'published' => $listing['catalog']['published'] ?? false,
                ],
                importFlag: $importFlag,
                updateFlag: $updateFlag,
                marketplace: $this->marketplace,
                parent: $this->getFolder($listing),
            );
            echo "OK\n";
        }
    }

    public function downloadOrders()
    {
        $this->prepareToken();
        $page = 1;
        $db = \Pimcore\Db::get();
        $threeMonthsAgoTimestamp = strtotime('-2 months');
        $threeMonthsAgoTimestamp = strtotime('+2 weeks');
        $threeMonthsAgo = date('Y-m-d', $threeMonthsAgoTimestamp);
        do {
            $params = ['status' => 'ALL', 'page' => $page, 'fulfilment-method' => 'ALL','latest-change-date'=>$threeMonthsAgo];
            $response = $this->httpClient->request("GET", static::$apiUrl['orders'], ['query' => $params]);
            if ($response->getStatusCode() !== 200) {
                echo "Failed to download orders: " . $response->getContent() . "\n";
                return;
            }
            try {
                $data = $response->toArray();
                $orders = $data['orders'] ?? [];
                $db->beginTransaction();
                foreach ($orders as $order) {
                    $db->executeStatement(
                        "INSERT INTO iwa_bolcom_orders (marketplace_id, order_id, json) VALUES (?, ?, ?) ON DUPLICATE KEY UPDATE json = VALUES(json)",
                        [
                            $this->marketplace->getId(),
                            $order['orderId'],
                            json_encode($order)
                        ]
                    );
                    echo "Inserting order: " . $order['orderId'] . "\n";
                }
                $db->commit();
            } catch (\Exception $e) {
                $db->rollBack();
                echo "Error: " . $e->getMessage() . "\n";
            }
            $page++;
            usleep(200000);
        } while(count($orders) == 50);





        // $lastUpdatedAt = $db->fetchOne(
        //     "SELECT COALESCE(DATE_FORMAT(MAX(json_extract(json, '$.orderPlacedDateTime')), '%Y-%m-%d'), DATE_FORMAT(DATE_SUB(CURDATE(), INTERVAL 3 MONTH), '%Y-%m-%d')) FROM iwa_marketplace_orders WHERE marketplace_id = ?",
        //     [$this->marketplace->getId()]
        // );
        // $page = 1;

        // do {
        //     $params = ['status' => 'ALL', 'page' => $page, 'fulfilment-method' => 'ALL'];
        //     $data = $this->downloadExtra(static::$apiUrl['orders'], 'GET', '',$params);
        //     $orders = $data['orders'];
        //     try {
        //         $db->beginTransaction();
        //         foreach ($orders as $order) {
        //             $db->executeStatement(
        //                 "INSERT INTO iwa_bolcom_orders (marketplace_id, order_id, json) VALUES (?, ?, ?) ON DUPLICATE KEY UPDATE json = VALUES(json)",
        //                 [
        //                     $this->marketplace->getId(),
        //                     $order['orderId'],
        //                     json_encode($order)
        //                 ]
        //             );
        //         }
        //         $db->commit();
        //     } catch (\Exception $e) {
        //         $db->rollBack();
        //         echo "Error: " . $e->getMessage() . "\n";
        //     }
        //     $page++;
        //     sleep(2);
        // } while(count($orders) == 50);
    }

    public function downloadInventory()
    {
    }

}