<?php

namespace App\Connector\Marketplace;

use Doctrine\DBAL\Exception;
use Pimcore\Model\DataObject\VariantProduct;
use App\Utils\Utility;
use Symfony\Component\HttpClient\HttpClient;
use Symfony\Component\HttpClient\ScopingHttpClient;
use Symfony\Contracts\HttpClient\Exception\ClientExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\DecodingExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\RedirectionExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\ServerExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;

class WallmartConnector extends MarketplaceConnectorAbstract
{
    private static $apiUrl = [
        'loginTokenUrl' => "https://api-gateway.walmart.com/v3/token",
        'offers' => 'https://marketplace.walmartapis.com/v3/items',
        'item' => 'https://marketplace.walmartapis.com/v3/items/',
        'associations' => 'https://marketplace.walmartapis.com/v3/items/associations',
        'orders' => 'https://marketplace.walmartapis.com/v3/orders',
        'inventory' => 'https://marketplace.walmartapis.com/v3/inventory',
        'price' => 'https://marketplace.walmartapis.com/v3/price'
    ];
    public static string $marketplaceType = 'Wallmart';

    public static $expires_in;
    public static $correlationId;

    function generateCorrelationId() 
    {
        $randomHex = bin2hex(random_bytes(4));
        return substr($randomHex, 0, 4) . '-' . substr($randomHex, 4, 4);
    }

    public function prepareToken()
    {
        static::$correlationId = $this->generateCorrelationId();
        try {
            $response = $this->httpClient->request('POST', static::$apiUrl['loginTokenUrl'], [
                'headers' => [
                    'Accept' => 'application/json',
                    'Authorization' => 'Basic ' . base64_encode("{$this->marketplace->getWallmartClientId()}:{$this->marketplace->getWallmartSecretKey()}"),
                    'WM_QOS.CORRELATION_ID' => static::$correlationId,
                    'Content-Type' => 'application/x-www-form-urlencoded',
                    'WM_SVC.NAME' => 'Walmart Marketplace'
                ],
                'body' => http_build_query([
                    'grant_type' => 'client_credentials'
                ])
            ]);
            if ($response->getStatusCode() !== 200) {
                throw new \Exception('Failed to get token: ' . $response->getContent(false));
            }
            $data = $response->toArray();
            static::$expires_in = time() + $data['expires_in'];
            $this->marketplace->setWallmartAccessToken($data['access_token']);
            $this->marketplace->save();
        } catch (\Exception $e) {
            echo 'Error: ' . $e->getMessage();
        }
    }

    /**
     * @throws TransportExceptionInterface|ServerExceptionInterface|RedirectionExceptionInterface|DecodingExceptionInterface|ClientExceptionInterface
     */
    public function download($forceDownload = false): void
    {
       if (!isset(static::$expires_in) || time() >= static::$expires_in) {
            $this->prepareToken();
       }
       echo "Token is valid. Proceeding with download...\n";
        if (!$forceDownload && $this->getListingsFromCache()) {
            echo "Using cached listings\n";
            return;
        }
        $offset = 0;
        $limit = 20;
        $this->listings = [];
        do {
            $response = $this->httpClient->request('GET',  static::$apiUrl['offers'], [
                'headers' => [
                    'WM_SEC.ACCESS_TOKEN' => $this->marketplace->getWallmartAccessToken(),
                    'WM_QOS.CORRELATION_ID' => static::$correlationId,
                    'WM_SVC.NAME' => 'Walmart Marketplace',
                    'Accept' => 'application/json'
                ],
                'query' => [
                    'limit' => $limit,
                    'offset' => $offset
                ]
            ]);
            $statusCode = $response->getStatusCode();
            if ($statusCode !== 200) {
                echo "Error: $statusCode\n";
                break;
            }
            $data = $response->toArray();
            $products = $data['ItemResponse'];
            $totalItems = $data['totalItems'];
            $this->listings = array_merge($this->listings, $products);
            echo "Offset: " . $offset . " " . count($this->listings) . " ";
            $offset += $limit;
            echo ".";
            sleep(1);  
            echo "Total Items: " . $totalItems . "\n";
            echo "Count: " . count($this->listings) . "\n";
        } while (count($this->listings) < $totalItems);
        if (empty($this->listings)) {
            echo "Failed to download listings\n";
            return;
        }
        $this->putListingsToCache();
    }

    public function getAnItem($sku): void
    {
        $response = $this->httpClient->request('GET', static::$apiUrl['item'] . $sku);
        $statusCode = $response->getStatusCode();
        if ($statusCode !== 200) {
            echo "Error: $statusCode\n";
            return;
        }
        $data = $response->toArray();
        print_r($data);
    }

    protected function getAttributes($listing)
    {
        $attributeString = "";
        if (!empty($listing['variantGroupInfo']['groupingAttributes'])) {
            foreach ($listing['variantGroupInfo']['groupingAttributes'] as $attribute) {
                $attributeString .= $attribute['value'] . '-';
            }
        }
        return rtrim($attributeString, '-');
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
            echo "($index/$total) Processing Listing {$listing['sku']}:{$listing['productName']} ...";
            $parent = Utility::checkSetPath($marketplaceFolder);
            if (!empty($listing['variantGroupId'])) {
                $parent = Utility::checkSetPath(
                    Utility::sanitizeVariable($listing['variantGroupId']),
                    $parent
                );
            }
            VariantProduct::addUpdateVariant(
                variant: [
                    'imageUrl' => Utility::getCachedImage($listing['image_url']) ?? '',
                    'urlLink' => $this->getUrlLink("https://www.walmart.com/ip/" . str_replace(' ', '-', $listing['productName']) . "/" . $listing['wpid']) ?? '',
                    'salePrice' => $listing['price']['amount'] ?? 0,
                    'saleCurrency' => 'USD',
                    'title' => $listing['productName'] ?? '',
                    'attributes' => $this->getAttributes($listing) ?? '',
                    'uniqueMarketplaceId' => $listing['wpid'] ?? '',
                    'apiResponseJson' => json_encode($listing, JSON_PRETTY_PRINT),
                    'published' => $listing['publishedStatus'] === 'PUBLISHED' ? true : false,
                    'sku' => $listing['sku'] ?? '',
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

    public function downloadOrders()
    {
        if (!isset(static::$expires_in) || time() >= static::$expires_in) {
            $this->prepareToken();
        }
        $db = \Pimcore\Db::get();
        $now = time();
        $now = strtotime(date('Y-m-d 00:00:00', $now));
        $lastUpdatedAt = $db->fetchOne(" 
            SELECT COALESCE(DATE_FORMAT(FROM_UNIXTIME(MAX(JSON_UNQUOTE(JSON_EXTRACT(json, '$.orderLines.orderLine[0].statusDate')) / 1000)), '%Y-%m-%d'),DATE_FORMAT(DATE_SUB(NOW(), INTERVAL 180 DAY), '%Y-%m-%d')) AS lastUpdatedAt            
            FROM iwa_marketplace_orders
            WHERE marketplace_id = ?",
            [$this->marketplace->getId()]
        );
        echo "Last Updated At: $lastUpdatedAt\n";
        if ($lastUpdatedAt) {
            $lastUpdatedAtTimestamp = strtotime($lastUpdatedAt);
            $sixMonthsAgo = strtotime('-180 day', $now);
            $startDate = max($lastUpdatedAtTimestamp, $sixMonthsAgo);
        } else {
            $startDate = strtotime('-180 day');
        }
        echo "Start Date: " . date('Y-m-d', $startDate) . "\n";
        $endDate = min(strtotime('+2 weeks', $startDate), $now);
        $limit = 200;
        $offset = 0;
        echo  "Start Date: " . date('Y-m-d', $startDate) . " End Date: " . date('Y-m-d', $endDate) . "\n";
        echo "Downloading orders...\n";
        do {
            do {
                $response = $this->httpClient->request('GET',  static::$apiUrl['orders'], [
                    'headers' => [
                        'WM_SEC.ACCESS_TOKEN' => $this->marketplace->getWallmartAccessToken(),
                        'WM_QOS.CORRELATION_ID' => static::$correlationId,
                        'WM_SVC.NAME' => 'Walmart Marketplace',
                        'Accept' => 'application/json'
                    ],
                    'query' => [
                        'limit' => $limit,
                        'offset' => $offset,
                        'createdStartDate' =>date('Y-m-d', $startDate),
                        'createdEndDate' => date('Y-m-d', $endDate),
                        'productInfo' => 'true'
                    ]
                ]);
                $statusCode = $response->getStatusCode();
                if ($statusCode !== 200) {
                    echo "Error: $statusCode\n";
                    return;
                }
                try {
                    $data = $response->toArray();
                    $orders = $data['list']['elements']['order'];
                    $db->beginTransaction();
                    foreach ($orders as $order) {
                        $db->executeStatement(
                            "INSERT INTO iwa_marketplace_orders (marketplace_id, order_id, json) VALUES (?, ?, ?) ON DUPLICATE KEY UPDATE json = VALUES(json)",
                            [
                                $this->marketplace->getId(),
                                $order['purchaseOrderId'],
                                json_encode($order)
                            ]
                        );
                    }
                    $db->commit();
                } catch (\Exception $e) {
                    $db->rollBack();
                    echo "Error: " . $e->getMessage() . "\n";
                }
                $offset += $limit;
                $total = $data['list']['meta']['totalCount'];
                echo  "Start Date: " . date('Y-m-d', $startDate) . " End Date: " . date('Y-m-d', $endDate) . "\n";
                echo "Offset: " . $offset . " " . count($orders) . " ";
                echo "Total: " . $total . "\n";
                echo ".";
            } while($total == $limit);
            $offset = 0;
            $startDate = $endDate;
            $endDate = min(strtotime('+2 weeks', $startDate), $now);
            if ($startDate >= $now) {
                break;
            }
        } while($startDate < strtotime('now'));
        echo "Orders downloaded\n";
    }
    
    public function downloadInventory()
    {

    }

    /**
     * @throws RedirectionExceptionInterface|DecodingExceptionInterface|ClientExceptionInterface|TransportExceptionInterface|ServerExceptionInterface|Exception
     */
    public function setInventory(VariantProduct $listing, int $targetValue, $sku = null, $country = null): void
    {
        if ($targetValue < 0) {
            echo "Error: Quantity cannot be less than 0\n";
            return;
        }
        $sku = json_decode($listing->jsonRead('apiResponseJson'), true)['sku'];
        if ($sku === null) {
            echo "Error: Barcode is missing\n";
            return;
        }
        $request = [
            'query' => [
                'sku' => $sku
            ],
            'json' => [
                'sku' => $sku,
                'quantity' => [
                    'unit' => 'EACH',
                    'amount' => $targetValue
                ]
            ]
        ];
        $response = $this->httpClient->request('GET',  static::$apiUrl['inventory'], [
            'headers' => [
                'WM_SEC.ACCESS_TOKEN' => $this->marketplace->getWallmartAccessToken(),
                'WM_QOS.CORRELATION_ID' => static::$correlationId,
                'WM_SVC.NAME' => 'Walmart Marketplace',
                'Accept' => 'application/json'
            ],
            $request['query'],
            $request['json']
        ]);
        $statusCode = $response->getStatusCode();
        if ($statusCode !== 200) {
            echo "Error: $statusCode\n";
            return;
        }
        echo "Inventory set to $targetValue\n";
        $date = date('YmdHis');
        $data = $response->toArray();
        $filename = "SETINVENTORY_{$sku}_{$date}.json";
        $this->putToCache($filename, ['request' => $request, 'response' => $data]);
    }

    /**
     * @throws RedirectionExceptionInterface|DecodingExceptionInterface|ClientExceptionInterface|TransportExceptionInterface|ServerExceptionInterface|Exception
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
        $sku = json_decode($listing->jsonRead('apiResponseJson'), true)['sku'];
        if ($sku === null) {
            echo "Error: Barcode is missing\n";
            return;
        }
        $json = [
            'sku' => $sku,
            'pricing' => [
                'currentPriceType' => 'BASE',
                'currentPrice' => [
                    'currency' => $listing->getSaleCurrency(),
                    'amount' => (float) $finalPrice
                ]
            ]
        ];
        $response = $this->httpClient->request('GET',  static::$apiUrl['price'], [
            'headers' => [
                'WM_SEC.ACCESS_TOKEN' => $this->marketplace->getWallmartAccessToken(),
                'WM_QOS.CORRELATION_ID' => static::$correlationId,
                'WM_SVC.NAME' => 'Walmart Marketplace',
                'Accept' => 'application/json'
            ],
            'json' => $json,
        ]);
        $statusCode = $response->getStatusCode();
        if ($statusCode !== 200) {
            echo "Error: $statusCode\n";
            return;
        }
        echo "Price set to $finalPrice\n";
        $date = date('YmdHis');
        $data = $response->toArray();
        $filename = "SETPRICE_{$sku}_{$date}.json";
        $this->putToCache($filename, ['request' => $json, 'response' => $data]);
    }
   
}