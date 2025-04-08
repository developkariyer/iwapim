<?php

namespace App\Connector\Marketplace;

use Doctrine\DBAL\Exception;
use Pimcore\Model\DataObject\Product;
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

    private string $sellerId;

    public function __construct($marketplace)
    {
        parent::__construct($marketplace);
        $this->sellerId = $this->marketplace->getTrendyolSellerId();
       /* $this->httpClient = ScopingHttpClient::forBaseUri($this->httpClient, "https://apigw.trendyol.com/integration/", [
            'headers' => [
                'Authorization' => 'Basic ' . $this->marketplace->getTrendyolToken(),
            ]
        ]);
        static::$apiUrl = [
            'offers' => 'product/sellers/' . $sellerId . '/products?approved=true',
            'orders' => 'order/sellers/' . $sellerId . '/orders',
            'inventory_price' => 'inventory/sellers/' . $sellerId . '/products/price-and-inventory',
            'batch_requests' => 'product/sellers/' . $sellerId . '/products/batch-requests/',
            'returns' => 'order/sellers/' . $sellerId . '/claims',
        ];


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
        ];*/
    }

    /**
     * @throws RedirectionExceptionInterface
     * @throws ClientExceptionInterface
     * @throws TransportExceptionInterface
     * @throws ServerExceptionInterface
     */
    public function getFromTrendyolApi($method, $parameter, $query = [], $key = null, $body = null): array
    {
        $data = [];
        $url = "https://apigw.trendyol.com/integration/" . $parameter;
        $headersToApi = [
            'query' => $query,
            'headers' => [
                'Authorization' => 'Basic ' . $this->marketplace->getTrendyolToken(),
            ],
            'json' => $body
        ];
        $page = 0;
        do {
            $response = $this->httpClient->request($method, $url, $headersToApi);
            if ($response->getStatusCode() !== 200) {
                echo 'Error: ' . $response->getStatusCode() . ' ' . $response->getContent();
            }
            sleep(1);
            $newData = json_decode($response->getContent(), true);
            $data = array_merge($data, $key ? ($newData[$key] ?? []) : $newData);
            $page++;
            if (isset($headersToApi['query']['page'])) {
                $headersToApi['query']['page'] = $page;
            }
            echo ".";
        } while($page <= $newData['totalPages']);
        return $data;
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
        $this->listings = $this->getFromTrendyolApi('GET', "product/sellers/" . $this->sellerId . "/products?approved=true", ['page' => 0], 'content', null);
        if (empty($this->listings)) {
            echo "Failed to download listings\n";
            return;
        }
        $this->putListingsToCache();
    }

    public function downloadInventory(): void
    {
        $this->downloadReturns();
    }

    /**
     * @throws TransportExceptionInterface
     * @throws RandomException
     * @throws ServerExceptionInterface
     * @throws RedirectionExceptionInterface
     * @throws DecodingExceptionInterface
     * @throws ClientExceptionInterface
     */
    public function downloadReturns(): void
    {
        $allReturns  =  $this->getFromTrendyolApi('GET',  "order/sellers/" . $this->sellerId . "/claims", ['page' => 0], 'content', null);
        foreach ($allReturns as &$return) {
            foreach ($return['items'] as &$item) {
                $barcode = $item['orderLine']['barcode'];
                echo "Barcode: " . $barcode . "\n";
                $sql = "
                        SELECT object_id
                        FROM iwa_json_store
                        WHERE field_name = 'apiResponseJson'  AND JSON_UNQUOTE(JSON_EXTRACT(json_data, :jsonPath)) = :uniqueId LIMIT 1;";
                $jsonPath = '$.barcode';
                $result = Utility::fetchFromSql($sql, ['jsonPath' => $jsonPath, 'uniqueId' => $barcode]);
                $objectId = $result[0]['object_id'] ?? null;
                if (!$objectId) {
                    continue;
                }
                $variantObject = VariantProduct::getById($objectId);
                $mainProductObjectArray = $variantObject->getMainProduct();
                $mainProductObject = reset($mainProductObjectArray);
                if ($mainProductObject instanceof Product) {
                    $iwasku =  $mainProductObject->getInheritedField('Iwasku');
                    $path = $mainProductObject->getFullPath();
                    $parts = explode('/', trim($path, '/'));
                    $variantName = array_pop($parts);
                    $parentName = array_pop($parts);
                    $productIdentifier = $mainProductObject->getInheritedField('ProductIdentifier');
                    $productType = strtok($productIdentifier,'-');
                    $item['orderLine']['iwasku'] = $iwasku;
                    $item['orderLine']['variant_name'] = $variantName;
                    $item['orderLine']['parent_name'] = $parentName;
                    $item['orderLine']['parent_identifier'] = $productIdentifier;
                    $item['orderLine']['product_type'] = $productType;
                    echo "Iwasku: " . $iwasku . "\n";
                }
            }
        }
        foreach ($allReturns as $return) {
            $sqlInsertMarketplaceReturn = "
                            INSERT INTO iwa_marketplace_returns (marketplace_id, return_id, json) 
                            VALUES (:marketplace_id, :return_id, :json) ON DUPLICATE KEY UPDATE json = VALUES(json)";
            Utility::executeSql($sqlInsertMarketplaceReturn, [
                'marketplace_id' => $this->marketplace->getId(),
                'return_id' => $return['id'],
                'json' => json_encode($return)
            ]);
            echo "Inserting order: " . $return['id'] . "\n";
        }
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
        $allOrders = [];
        do {
            echo "Date Range: " . date('Y-m-d', $startDate) . " - " . date('Y-m-d', $endDate) . "\n";
            echo "-----------------------------\n";
            $query = [
                'page' => 0,
                'size' => 200,
                'startDate' => $startDate * 1000,
                'endDate' => $endDate *1000
            ];
            $orders = $this->getFromTrendyolApi('GET', "order/sellers/" . $this->sellerId . "/orders" , $query, 'content');
            $allOrders = array_merge($allOrders, $orders);
            $startDate = $endDate;
            $endDate = min(strtotime('+2 weeks', $startDate), $now);
            if ($startDate >= $now) {
                break;
            }
        } while ($startDate < strtotime('now'));

        foreach ($allOrders as $order) {
            $sqlInsertMarketplaceOrder = "
                            INSERT INTO iwa_marketplace_orders (marketplace_id, order_id, json) 
                            VALUES (:marketplace_id, :order_id, :json) ON DUPLICATE KEY UPDATE json = VALUES(json)";
            Utility::executeSql($sqlInsertMarketplaceOrder, [
                'marketplace_id' => $this->marketplace->getId(),
                'order_id' => $order['orderNumber'],
                'json' => json_encode($order)
            ]);

        }
        echo "Orders downloaded\n";

        /*do {
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
        } while ($startDate < strtotime('now'));*/
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
                    'saleCurrency' => $this->marketplace->getCurrency(),
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
        $response = $this->getFromTrendyolApi('POST', "inventory/sellers/" . $this->sellerId . "/products/price-and-inventory", [], null, $request);
        $combinedData = [
            'inventory' => $response,
            'batchRequestResult' => $this->getBatchRequestResult($response['batchRequestId']),
        ];
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
        $response = $this->getFromTrendyolApi('POST', "inventory/sellers/" . $this->sellerId . "/products/price-and-inventory", [], null, $request);
        $combinedData = [
            'price' => $response,
            'batchRequestResult' => $this->getBatchRequestResult($response['batchRequestId']),
        ];
        $filename = "SETPRICE_{$barcode}.json";
        $this->putToCache($filename, ['request' => $request, 'response' => $combinedData]);
    }

    /**
     * @throws TransportExceptionInterface
     * @throws ServerExceptionInterface
     * @throws RedirectionExceptionInterface
     * @throws ClientExceptionInterface
     */
    public function getBatchRequestResult($batchRequestId): array
    {
        return $this->getFromTrendyolApi('GET', "product/sellers/" . $this->sellerId . "/products/batch-requests/" . $batchRequestId);
    }

}