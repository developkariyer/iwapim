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
        $filename = 'tmp/' . urlencode($this->marketplace->getKey()) . '.json';
        if (!$forceDownload && file_exists($filename) && filemtime($filename) > time() - 86400) {
            $this->listings = json_decode(file_get_contents($filename), true);
            echo "Using cached data ";
        } else {
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
            file_put_contents($filename, json_encode($this->listings));
        }
        return count($this->listings);
    }

    public function downloadInventory()
    {
    }

    public function downloadOrders()
    {
        $db = \Pimcore\Db::get();
        $apiUrl = "https://api.trendyol.com/sapigw/suppliers/{$this->marketplace->getTrendyolSellerId()}/orders";
        $page = 0;
        $size = 200;
        $now = strtotime('now');
        /*$lastUpdatedAt = $db->fetchOne(
            "SELECT MAX(created_at)
            FROM iwa_marketplace_orders 
            WHERE marketplace_id = ?",
            [$this->marketplace->getId()]
        );*/
        $lastUpdatedAt = $db->fetchOne(
            "SELECT COALESCE(DATE_FORMAT(FROM_UNIXTIME(MAX(json_extract(json, '$.lastModifiedDate'))), '%Y-%m-%d'), DATE_FORMAT(DATE_SUB(NOW(), INTERVAL 3 MONTH), '%Y-%m-%d')) 
             FROM iwa_marketplace_orders 
             WHERE marketplace_id = ?",
            [$this->marketplace->getId()]
        );
        if ($lastUpdatedAt) {
            $lastUpdatedAtTimestamp = strtotime($lastUpdatedAt);
            $threeMonthsAgo = strtotime('-3 months', $now);
            $startDate = max($lastUpdatedAtTimestamp, $threeMonthsAgo); 
        } else {
            $startDate = strtotime('-3 months');
        }
        $endDate = min(strtotime('+2 weeks', $startDate), $now);
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
                        'endDate' => $endDate * 1000
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
                $total = $data['totalElements'];
                echo "Page $page for date range Size $total " . date('Y-m-d', $startDate) . " - " . date('Y-m-d', $endDate) . "\n";
                sleep(1);

            } while ($page <= $data['totalPages']);

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
}