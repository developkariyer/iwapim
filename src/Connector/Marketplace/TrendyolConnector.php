<?php

namespace App\Connector\Marketplace;

use Doctrine\DBAL\Exception;
use Pimcore\Model\DataObject\VariantProduct;
use Pimcore\Model\Element\DuplicateFullPathException;
use Random\RandomException;

use App\Utils\Utility;
use Symfony\Component\HttpClient\ScopingHttpClient;
use Symfony\Contracts\HttpClient\Exception\ClientExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\DecodingExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\RedirectionExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\ServerExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;

class TrendyolConnector extends MarketplaceConnectorAbstract
{
    private static array $apiUrl = [];

    public static string $marketplaceType = 'Trendyol';

    public function __construct($marketplace)
    {
        parent::__construct($marketplace);
        $this->httpClient = ScopingHttpClient::forBaseUri($this->httpClient, "https://apigw.trendyol.com/integration/", [
            'headers' => [
                'Authorization' => 'Basic ' . $this->marketplace->getTrendyolToken(),
            ]
        ]);
        $sellerId = $this->marketplace->getTrendyolSellerId();
        static::$apiUrl = [
            'offers' => 'product/sellers/' . $sellerId . '/products?approved=true',
            'orders' => 'order/sellers/' . $sellerId . '/orders',
            'inventory_price' => 'inventory/sellers/' . $sellerId . '/products/price-and-inventory',
            'batch_requests' => 'product/sellers/' . $sellerId . '/products/batch-requests/',
            'returns' => 'order/sellers/' . $sellerId . '/claims',
        ];
    }

    /**
     * @throws TransportExceptionInterface
     * @throws ServerExceptionInterface
     * @throws RedirectionExceptionInterface
     * @throws DecodingExceptionInterface
     * @throws ClientExceptionInterface|RandomException
     */
    public function download(bool $forceDownload = false): void
    {
        if (!$forceDownload && $this->getListingsFromCache()) {
            echo "Using cached listings\n";
            return;
        }
        $page = 0;
        $this->listings = [];
        do {
            $response = $this->httpClient->request('GET', static::$apiUrl['offers'], [
                'query' => [
                    'page' => $page
                ]
            ]);
            $statusCode = $response->getStatusCode();
            if ($statusCode !== 200) {
                echo "Error: $statusCode\n";
                break;
            }
            $data = $response->toArray();
            $products = $data['content'];
            $this->listings = array_merge($this->listings, $products);
            $page++;
            echo ".";
            sleep(1);  
        } while ($page <= $data['totalPages']);
        $this->putListingsToCache();
    }

    public function downloadInventory(): void
    {
        $this->downloadReturns();
    }

    public function downloadReturns(): void
    {
        $response = $this->httpClient->request('GET', static::$apiUrl['returns']);
        print_r($response->getContent());



    }

    /**
     * @throws RedirectionExceptionInterface
     * @throws DecodingExceptionInterface
     * @throws ClientExceptionInterface
     * @throws TransportExceptionInterface
     * @throws ServerExceptionInterface
     */
    public function downloadOrders(): void
    {
        $now = time();
        $now = strtotime(date('Y-m-d 00:00:00', $now));
        try {
            $sqlLastUpdatedAt = "
                SELECT COALESCE(DATE_FORMAT(FROM_UNIXTIME(MAX(json_extract(json, '$.lastModifiedDate') / 1000)), '%Y-%m-%d'),DATE_FORMAT(DATE_SUB(NOW(), INTERVAL 3 MONTH), '%Y-%m-%d')) AS lastUpdatedAt
                FROM iwa_marketplace_orders
                WHERE marketplace_id = :marketplace_id
                LIMIT 1;
            ";
            $result = Utility::fetchFromSql($sqlLastUpdatedAt, [
                'marketplace_id' => $this->marketplace->getId()
            ]);
            $lastUpdatedAt = $result[0]['lastUpdatedAt'];
        } catch (\Exception $e) {
            echo "Error: " . $e->getMessage() . "\n";
        }
        echo "Last Updated At: $lastUpdatedAt\n";
        if ($lastUpdatedAt) {
            $lastUpdatedAtTimestamp = strtotime($lastUpdatedAt);
            $threeMonthsAgo = strtotime('-3 months', $now);
            $startDate = max($lastUpdatedAtTimestamp, $threeMonthsAgo); 
        } else {
            $startDate = strtotime('-3 months');
        }
        $endDate = min(strtotime('+2 weeks', $startDate), $now);
        $size = 200;
        do {
            $page = 0;
            do {
                $response = $this->httpClient->request('GET', static::$apiUrl['orders'], [
                    'query' => [
                        'page' => $page,
                        'size' => $size,
                        'startDate' => $startDate * 1000, 
                        'endDate' => $endDate *1000
                    ]
                ]);
                $statusCode = $response->getStatusCode();
                if ($statusCode !== 200) {
                    echo "Error: $statusCode\n";
                    break;
                }
                try {
                    $data = $response->toArray();
                    $orders = $data['content'];
                    foreach ($orders as $order) {
                        $sqlInsertMarketplaceOrder = "
                            INSERT INTO iwa_marketplace_orders (marketplace_id, order_id, json) 
                            VALUES (:marketplace_id, :order_id, :json) ON DUPLICATE KEY UPDATE json = VALUES(json)";
                        Utility::executeSql($sqlInsertMarketplaceOrder, [
                            'marketplace_id' => $this->marketplace->getId(),
                            'order_id' => $order['orderNumber'],
                            'json' => json_encode($order)
                        ]);
                    }
                    $totalElements = $data['totalElements'];
                    $totalPages = $data['totalPages'];
                    $count = count($orders);
                    echo "-----------------------------\n";
                    echo "Total Elements: $totalElements\n";
                    echo "Total Pages: $totalPages\n";
                    echo "Current Page: $page\n";
                    echo "Items on this page: $count\n";
                    echo "Date Range: " . date('Y-m-d', $startDate) . " - " . date('Y-m-d', $endDate) . "\n";
                    echo "-----------------------------\n";
                } catch (\Exception $e) {
                    echo "Error: " . $e->getMessage() . "\n";
                }
                $page++;
                sleep(0.06);
            } while ($page < $data['totalPages']);
            $startDate = $endDate;
            $endDate = min(strtotime('+2 weeks', $startDate), $now);
            if ($startDate >= $now) {
                break;
            }
        } while ($startDate < strtotime('now'));
    }

    private function getAttributes($listing): string
    {
        if (!empty($listing['attributes'])) {
            $values = array_filter(array_map(function($value) {
                return str_replace(' ', '', $value);
            }, array_column($listing['attributes'], 'attributeValue')));
            if (!empty($values)) {
                return implode('-', $values);
            }
        }
        return '';
    }

    private function getPublished($listing): bool
    {
        if (!isset($listing['archived'])) {
            return false;
        }
        return !$listing['archived'];
    }

    /**
     * @throws DuplicateFullPathException
     * @throws \Exception
     */
    public function import($updateFlag, $importFlag): void
    {
        if (empty($this->listings)) {
            echo "Nothing to import\n";
        }
        $marketplaceFolder = Utility::checkSetPath(
            Utility::sanitizeVariable($this->marketplace->getKey(), 190),
            Utility::checkSetPath('Pazaryerleri')
        );
        $total = count($this->listings);
        $index = 0;
        foreach ($this->listings as $listing) {
            echo "($index/$total) Processing Listing {$listing['barcode']}:{$listing['title']} ...";
            $path = Utility::sanitizeVariable($listing['categoryName'] ?? 'Tasnif-Edilmemiş');
            $parent = Utility::checkSetPath($path, $marketplaceFolder);
            if ($listing['productMainId']) {
                $parent = Utility::checkSetPath(Utility::sanitizeVariable($listing['productMainId']), $parent);
            }
            VariantProduct::addUpdateVariant(
                variant: [
                    'imageUrl' => Utility::getCachedImage($listing['images'][0]['url'] ?? ''),
                    'urlLink' => $this->getUrlLink($listing['productUrl'] ?? ''),
                    'salePrice' => $listing['salePrice'] ?? 0,
                    'saleCurrency' => 'TL',
                    'title' => $listing['title'] ?? '',
                    'attributes' => $this->getAttributes($listing),
                    'uniqueMarketplaceId' => $listing['id'] ?? '',
                    'apiResponseJson' => json_encode($listing, JSON_PRETTY_PRINT),
                    'published' => $this->getPublished($listing),
                    'sku' => $listing['barcode'] ?? '',
                ],
                importFlag: $importFlag,
                updateFlag: $updateFlag,
                marketplace: $this->marketplace,
                parent: $parent
            );
            echo "OK\n";
            $index++;
        }
    }

    /**
     * @throws RedirectionExceptionInterface
     * @throws DecodingExceptionInterface
     * @throws ClientExceptionInterface
     * @throws TransportExceptionInterface
     * @throws ServerExceptionInterface
     * @throws Exception
     * @throws RandomException
     */
    public function setInventory(VariantProduct $listing, int $targetValue, $sku = null, $country = null): void // 15 dakika boyunca aynı isteği tekrarlı olarak atamazsınız!
    {
        if ($targetValue > 20000) {
            echo "Error: Quantity cannot be more than 20000\n";
            return;
        }
        if ($targetValue < 0) {
            echo "Error: Quantity cannot be less than 0\n";
            return;
        }
        $barcode = json_decode($listing->jsonRead('apiResponseJson'), true)['barcode'];
        if ($barcode === null) {
            echo "Error: Barcode is missing\n";
            return;
        }
        $request = [
            'items' => [
                [
                    'barcode' => $barcode,
                    'quantity' => $targetValue
                ]
            ]
        ];
        $response = $this->httpClient->request('POST', static::$apiUrl['inventory_price'], ['json' => $request]);
        $statusCode = $response->getStatusCode();
        if ($statusCode !== 200) {
            echo "Error: $statusCode\n";
            return;
        }
        $data = $response->toArray();
        $combinedData = [
            'inventory' => $data,
            'batchRequestResult' => $this->getBatchRequestResult($data['batchRequestId']),
        ];
        $combinedJson = json_encode($combinedData);
        $filename = "SETINVENTORY_{$barcode}.json";
        $this->putToCache($filename, ['request' => $request, 'response' => $combinedData]);
    }

    /**
     * @throws RedirectionExceptionInterface
     * @throws DecodingExceptionInterface
     * @throws ClientExceptionInterface
     * @throws TransportExceptionInterface
     * @throws ServerExceptionInterface
     * @throws Exception|RandomException
     */
    public function setPrice(VariantProduct $listing, string $targetPrice, $targetCurrency = null, $sku = null, $country = null): void
    {
        if (empty($targetPrice)) {
            echo "Error: Price cannot be null\n";
            return;
        }
        if (empty($targetCurrency)) {
            $targetCurrency = $listing->getSaleCurrency();
        }
        $finalPrice = $this->convertCurrency($targetPrice, $targetCurrency, $listing->getSaleCurrency());
        if (empty($finalPrice)) {
            echo "Error: Currency conversion failed\n";
            return;
        }
        $barcode = json_decode($listing->jsonRead('apiResponseJson'), true)['barcode'];
        if ($barcode === null) {
            echo "Error: Barcode is missing\n";
            return;
        }
        $request = [
            'items' => [
                [
                    'barcode' => $barcode,
                    'salePrice' => $finalPrice
                ]
            ]
        ];
        $response = $this->httpClient->request('POST', static::$apiUrl['inventory_price'], ['json' => $request]);
        $statusCode = $response->getStatusCode();
        if ($statusCode !== 200) {
            echo "Error: $statusCode\n";
            return;
        }
        $data = $response->toArray();
        $combinedData = [
            'price' => $data,
            'batchRequestResult' => $this->getBatchRequestResult($data['batchRequestId']),
        ];
        $combinedJson = json_encode($combinedData);
        $filename = "SETPRICE_{$barcode}.json";
        $this->putToCache($filename, ['request' => $request, 'response' => $combinedData]);
    }

    /**
     * @throws TransportExceptionInterface
     * @throws ServerExceptionInterface
     * @throws RedirectionExceptionInterface
     * @throws DecodingExceptionInterface
     * @throws ClientExceptionInterface
     */
    public function getBatchRequestResult($batchRequestId): array
    {
        $response = $this->httpClient->request('GET', static::$apiUrl['batch_requests'] . $batchRequestId);
        $statusCode = $response->getStatusCode();
        if ($statusCode !== 200) {
            echo "Error: $statusCode\n";
            return [];
        }
        return $response->toArray();
    }

}