<?php

namespace App\Connector\Marketplace;

use Pimcore\Model\DataObject\Data\Link;
use Pimcore\Model\DataObject\VariantProduct;
use Symfony\Component\HttpClient\HttpClient;

use App\Utils\Utility;

class TrendyolConnector extends MarketplaceConnectorAbstract
{
    public static $marketplaceType = 'Trendyol';

    public function download($forceDownload = false)
    {
        $this->listings = json_decode(Utility::getCustomCache('LISTINGS.json', PIMCORE_PROJECT_ROOT. "/tmp/marketplaces/".urlencode($this->marketplace->getKey())), true);
        
        if (!(empty($this->listings) || $forceDownload)) {
            echo "Using cached listings\n";
            return;
        }
        $apiUrl = "https://api.trendyol.com/sapigw/suppliers/{$this->marketplace->getTrendyolSellerId()}/products?approved=true";
        $page = 0;
        $this->listings = [];
        do {
            $response = $this->httpClient->request('GET', $apiUrl, [
                'headers' => [
                    'Authorization' => 'Basic ' . $this->marketplace->getTrendyolToken(),
                ],
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
        Utility::setCustomCache('LISTINGS.json', PIMCORE_PROJECT_ROOT. "/tmp/marketplaces/".urlencode($this->marketplace->getKey()), json_encode($this->listings));
    }

    public function downloadInventory()
    {
    }

    public function downloadOrders()
    {
        $db = \Pimcore\Db::get();
        $apiUrl = "https://api.trendyol.com/sapigw/suppliers/{$this->marketplace->getTrendyolSellerId()}/orders";
        $now = time();
        $now = strtotime(date('Y-m-d 00:00:00', $now)); 
        $lastUpdatedAt = $db->fetchOne(
            "SELECT COALESCE(DATE_FORMAT(FROM_UNIXTIME(MAX(json_extract(json, '$.lastModifiedDate') / 1000)), '%Y-%m-%d'),DATE_FORMAT(DATE_SUB(NOW(), INTERVAL 3 MONTH), '%Y-%m-%d')) AS lastUpdatedAt
            FROM iwa_marketplace_orders
            WHERE marketplace_id = ?",
            [$this->marketplace->getId()]
        );
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
                $response = $this->httpClient->request('GET', $apiUrl, [
                    'headers' => [
                        'Authorization' => 'Basic ' . $this->marketplace->getTrendyolToken(),
                    ],
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
                    $db->beginTransaction();
                    foreach ($orders as $order) {
                        $db->executeStatement(
                            "INSERT INTO iwa_marketplace_orders (marketplace_id, order_id, json) VALUES (?, ?, ?) ON DUPLICATE KEY UPDATE json = VALUES(json)",
                            [
                                $this->marketplace->getId(),
                                $order['orderNumber'],
                                json_encode($order)
                            ]
                        );
                    }
                    $db->commit();
                } catch (\Exception $e) {
                    $db->rollBack();
                    echo "Error: " . $e->getMessage() . "\n";
                }
                $page++;
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
                sleep(0.06);
            } while ($page < $data['totalPages']);
            $startDate = $endDate;
            $endDate = min(strtotime('+2 weeks', $startDate), $now);
            if ($startDate >= $now) {
                break;
            }
        } while ($startDate < strtotime('now'));
    }

    private function getAttributes($listing) {
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

    private function getPublished($listing)
    {
        if (!isset($listing['archived'])) {
            return false;
        }
        return (bool) !$listing['archived'];
    }

    public function import($updateFlag, $importFlag)
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

    public function setInventory(VariantProduct $listing, int $targetValue, $sku = null, $country = null) // 15 dakika boyunca aynı isteği tekrarlı olarak atamazsınız!
    {
        if ($targetValue > 20000) {
            echo "Error: Quantity cannot be more than 20000\n";
            return;
        }
        if ($targetValue < 0) {
            echo "Error: Quantity cannot be less than 0\n";
            return;
        }
        if (!$listing instanceof VariantProduct) {
            echo "Listing is not a VariantProduct\n";
            return;
        }
        $apiUrl = "https://api.trendyol.com/sapigw/suppliers/{$this->marketplace->getTrendyolSellerId()}/products/price-and-inventory";
        $barcode = json_decode($listing->jsonRead('apiResponseJson'), true)['barcode'];
        if ($barcode === null) {
            echo "Error: Barcode is missing\n";
            return;
        }
        $response = $this->httpClient->request('POST', $apiUrl, [
            'headers' => [
                'Authorization' => 'Basic ' . $this->marketplace->getTrendyolToken()
            ],
            'json' => [
                'items' => [
                    [
                        'barcode' => $barcode,
                        'quantity' => $targetValue
                    ]
                ]
            ]
        ]);
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
        $date = date('Y-m-d H:i:s');
        $combinedJson = json_encode($combinedData);
        $filename = "{$barcode}-$date.json";
        Utility::setCustomCache($filename, PIMCORE_PROJECT_ROOT . "/tmp/marketplaces/" . urlencode($this->marketplace->getKey()) . '/SetInventory', $combinedJson);
    }

    public function setPrice(VariantProduct $listing,string $targetPrice, $targetCurrency = null, $sku = null, $country = null)
    {
        if (!$listing instanceof VariantProduct) {
            echo "Listing is not a VariantProduct\n";
            return;
        }
        if ($targetPrice === null) {
            echo "Error: Price cannot be null\n";
            return;
        }
        if ($targetCurrency === null) {
            $targetCurrency = $listing->getSaleCurrency();
        }
        $finalPrice = $this->convertCurrency($targetPrice, $targetCurrency, $listing->getSaleCurrency());
        if ($finalPrice === null) {
            echo "Error: Currency conversion failed\n";
            return;
        }
        $apiUrl = "https://api.trendyol.com/sapigw/suppliers/{$this->marketplace->getTrendyolSellerId()}/products/price-and-inventory";
        $barcode = json_decode($listing->jsonRead('apiResponseJson'), true)['barcode'];
        if ($barcode === null) {
            echo "Error: Barcode is missing\n";
            return;
        }
        $response = $this->httpClient->request('POST', $apiUrl, [
            'headers' => [
                'Authorization' => 'Basic ' . $this->marketplace->getTrendyolToken()
            ],
            'json' => [
                'items' => [
                    [
                        'barcode' => $barcode,
                        'salePrice' => $finalPrice
                    ]
                ]
            ]
        ]);
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
        $date = date('Y-m-d H:i:s');
        $combinedJson = json_encode($combinedData);
        $filename = "{$barcode}-$date.json";
        Utility::setCustomCache($filename, PIMCORE_PROJECT_ROOT . "/tmp/marketplaces/" . urlencode($this->marketplace->getKey()) . '/SetPrice', $combinedJson);
    }

    public function getBatchRequestResult($batchRequestId)
    {
        $apiUrl = "https://api.trendyol.com/sapigw/suppliers/{$this->marketplace->getTrendyolSellerId()}/products/batch-requests/{$batchRequestId}";
        $response = $this->httpClient->request('GET', $apiUrl, [
            'headers' => [
                'Authorization' => 'Bearer ' . $this->marketplace->getTrendyolToken()
            ]
        ]);
        $statusCode = $response->getStatusCode();
        if ($statusCode !== 200) {
            echo "Error: $statusCode\n";
            return;
        }
        return $response->toArray();
    }

}