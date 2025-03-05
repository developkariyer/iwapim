<?php

namespace App\Connector\Marketplace;

use Doctrine\DBAL\Exception;
use Pimcore\Model\DataObject\VariantProduct;
use App\Utils\Utility;
use Pimcore\Model\Element\DuplicateFullPathException;
use Random\RandomException;
use Symfony\Component\HttpClient\HttpClient;
use Symfony\Component\HttpClient\ScopingHttpClient;
use Symfony\Contracts\HttpClient\Exception\ClientExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\DecodingExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\RedirectionExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\ServerExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;

class WallmartConnector extends MarketplaceConnectorAbstract
{
    public static string $marketplaceType = 'Wallmart';
    private static string $correlationId;

    /**
     * @throws RandomException
     */
    function generateCorrelationId(): string
    {
        $randomHex = bin2hex(random_bytes(4));
        return substr($randomHex, 0, 4) . '-' . substr($randomHex, 4, 4);
    }

    /**
     * @throws RedirectionExceptionInterface
     * @throws DecodingExceptionInterface
     * @throws ClientExceptionInterface
     * @throws TransportExceptionInterface
     * @throws ServerExceptionInterface
     * @throws RandomException
     */
    public function prepareToken(): void
    {
        static::$correlationId = $this->generateCorrelationId();
        $expiresAt = $this->marketplace->getWallmartExpiresIn();
        $currentTimestamp = time();
        if ($expiresAt && ($expiresAt - $currentTimestamp) > 60) {
            echo "Token is valid expires seconds " . $expiresAt - $currentTimestamp . "\n";
            return;
        }
        echo "Token is not valid creating token\n";
        try {
            $response = $this->httpClient->request('POST', "https://api-gateway.walmart.com/v3/token", [
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
            $expiresIn = $data['expires_in'];
            $expiresAt = $currentTimestamp + $expiresIn;
            $this->marketplace->setWallmartAccessToken($data['access_token']);
            $this->marketplace->setWallmartExpiresIn($expiresAt);
            $this->marketplace->save();
        } catch (\Exception $e) {
            echo 'Error: ' . $e->getMessage();
        }
    }

    /**
     * @throws RandomException
     */
    public function getFromWallmartApi($method, $parameter, $query = [], $key = null, $secondKey = null, $body = null, $paginationType = null)
    {
        $this->prepareToken();
        static::$correlationId = $this->generateCorrelationId();
        $data = [];
        $url = "https://marketplace.walmartapis.com/v3/" . $parameter;
        $headersToApi = [
            'query' => $query,
            'headers' => [
                'WM_SEC.ACCESS_TOKEN' => $this->marketplace->getWallmartAccessToken(),
                'WM_QOS.CORRELATION_ID' => static::$correlationId,
                'WM_SVC.NAME' => 'Walmart Marketplace',
                'Accept' => 'application/json'
            ],
            'json' => $body
        ];
        try {
            $nextCursor = null;
            $totalItems = 0;
            do {
                $response = $this->httpClient->request($method, $url, $headersToApi);
                if ($response->getStatusCode() !== 200) {
                    echo 'Error: ' . $response->getStatusCode() . ' ' . $response->getContent();
                }
                $newData = json_decode($response->getContent(), true);
                $data = array_merge(
                    $data,
                    $key
                        ? ($secondKey ? ($newData[$key][$secondKey] ?? []) : ($newData[$key] ?? []))
                        : $newData
                );
                if ($paginationType === 'offset') {
                    $totalItems = $newData['totalItems'] ?? 0;
                    $headersToApi['query']['offset'] += $headersToApi['query']['limit'];
                }
                else if ($paginationType === 'cursor') {
                    $nextCursor = $newData['meta']['nextCursor'] ?? null;
                    $totalData = $newData['meta']['totalCount'] ?? 0;
                    $headersToApi['query']['nextCursor'] = $nextCursor;
                }
                echo ".";
                usleep(720000);
            } while (($nextCursor !== null && count($data) < $totalData) || count($data) < $totalItems);

        } catch (\Exception $e) {
            echo 'Error: ' . $e->getMessage();
        }
        return $data;
    }

    /**
     * @throws RandomException
     */
    public function itemSearch($gtin): array
    {
        return $this->getFromWallmartApi('GET', 'items/walmart/search', ['gtin' => $gtin]);
    }

    public function itemInventory($sku): array
    {
        return $this->getFromWallmartApi('GET', '/inventory', ['sku' => $sku]);
    }

    /**
     * @throws TransportExceptionInterface|ServerExceptionInterface|RedirectionExceptionInterface|DecodingExceptionInterface|ClientExceptionInterface
     * @throws RandomException
     */
    public function download(bool $forceDownload = false): void
    {
        if (!$forceDownload && $this->getListingsFromCache()) {
            echo "Using cached listings\n";
            return;
        }
        $this->listings = $this->getFromWallmartApi('GET', 'items', ['limit' => 50, 'offset' => 0], 'ItemResponse',null, null,'offset');
        foreach ($this->listings as &$listing) {
            if (!empty($listing['gtin'])) {
                $listing['extra'] = $this->itemSearch($listing['gtin']);
            } else {
                $listing['extra'] = null;
            }
            if (!empty($listing['sku'])) {
                $listing['inventory'] = $this->itemInventory($listing['sku']);
            } else {
                $listing['inventory'] = null;
            }
        }
        unset($listing);
        if (empty($this->listings)) {
            echo "Failed to download listings\n";
            return;
        }
        $this->putListingsToCache();
    }

    public function getEan($gtin)
    {
        return ltrim($gtin, '0');
    }
    protected function getAttributes($listing): string
    {
        $attributeString = "";
        if (!empty($listing['variantGroupInfo']['groupingAttributes'])) {
            foreach ($listing['variantGroupInfo']['groupingAttributes'] as $attribute) {
                $attributeString .= $attribute['value'] . '-';
            }
        }
        return rtrim($attributeString, '-');
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
                    'imageUrl' => Utility::getCachedImage($listing['extra']['items'][0]['images'][0]['url'] ?? ''),
                    'urlLink' => $this->getUrlLink("https://www.walmart.com/ip/" . str_replace(' ', '-', $listing['productName']) . "/" . $listing['wpid']  ?? ''),
                    'salePrice' => $listing['price']['amount'] ?? 0,
                    'saleCurrency' => $listing['price']['currency'] ?? '',
                    'title' => $listing['productName'] ?? '',
                    'attributes' => $this->getAttributes($listing) ?? '',
                    'uniqueMarketplaceId' => $listing['wpid'] ?? '',
                    'apiResponseJson' => json_encode($listing, JSON_PRETTY_PRINT),
                    'published' => $listing['lifecycleStatus'] === 'ACTIVE' ? 1 : 0,
                    'quantity' => $listing['inventory']['quantity']['amount'] ?? 0,
                    'sku' => $listing['sku'] ?? '',
                    'ean' => ltrim($listing['gtin'], '0') ?? ''
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
     */
    public function downloadOrders(): void
    {
        $now = time();
        $now = strtotime(date('Y-m-d 00:00:00', $now));
        try {
            $sqlLastUpdatedAt = "
                SELECT COALESCE(DATE_FORMAT(FROM_UNIXTIME(MAX(JSON_UNQUOTE(JSON_EXTRACT(json, '$.orderLines.orderLine[0].statusDate')) / 1000)), '%Y-%m-%d'),DATE_FORMAT(DATE_SUB(NOW(), INTERVAL 180 DAY), '%Y-%m-%d')) AS lastUpdatedAt
                FROM iwa_marketplace_orders
                WHERE marketplace_id = :marketplace_id
                LIMIT 1";
            $result = Utility::fetchFromSql($sqlLastUpdatedAt, ['marketplace_id' => $this->marketplace->getId()]);
            $lastUpdatedAt = $result[0]['lastUpdatedAt'];
        } catch (\Exception $e) {
            echo "Error: " . $e->getMessage() . "\n";
            return;
        }
        echo "Last Updated At: $lastUpdatedAt\n";
        if ($lastUpdatedAt) {
            $lastUpdatedAtTimestamp = strtotime($lastUpdatedAt);
            $sixMonthsAgo = strtotime('-180 day', $now);
            $startDate = max($lastUpdatedAtTimestamp, $sixMonthsAgo);
        } else {
            $startDate = strtotime('-180 day');
        }
        $endDate = min(strtotime('+2 weeks', $startDate), $now);
        $allOrders = [];
        do {
            echo  "Start Date: " . date('Y-m-d', $startDate) . " End Date: " . date('Y-m-d', $endDate) . "\n";
            $query = [
                'limit' => 50,
                'nextCursor' => null,
                'createdStartDate' =>date('Y-m-d', $startDate),
                'createdEndDate' => date('Y-m-d', $endDate),
                'productInfo' => 'true'
            ];
            $orders = $this->getFromWallmartApi('GET', 'orders', $query, 'list', null, null,'cursor');
            $allOrders = array_merge($allOrders, $orders['elements']['order']);
            echo "Count: " . count($orders['elements']['order']) . "\n";
            echo "Total Count: " . count($allOrders) . "\n";
            $startDate = $endDate;
            $endDate = min(strtotime('+2 weeks', $startDate), $now);
            if ($startDate >= $now) {
                break;
            }
        } while($startDate < strtotime('now'));

        foreach ($allOrders as $order) {
            $sqlInsertMarketplaceOrder = "
                            INSERT INTO iwa_marketplace_orders (marketplace_id, order_id, json) 
                            VALUES (:marketplace_id, :order_id, :json) ON DUPLICATE KEY UPDATE json = VALUES(json)";
            Utility::executeSql($sqlInsertMarketplaceOrder, [
                'marketplace_id' => $this->marketplace->getId(),
                'order_id' => $order['purchaseOrderId'],
                'json' => json_encode($order)
            ]);
        }
        echo "Orders downloaded\n";
    }

    /**
     * @throws TransportExceptionInterface
     * @throws ServerExceptionInterface
     * @throws RedirectionExceptionInterface
     * @throws DecodingExceptionInterface
     * @throws ClientExceptionInterface
     */
    public function downloadInventory(): void
    {
        $this->downloadReturns();
        //$allInventories = $this->getFromWallmartApi('GET', 'inventories', ['limit' => 50, 'nextCursor' => null], 'elements', 'inventories',null, 'cursor');
        //$this->putToCache('INVENTORY.json', $allInventories);
    }

    /**
     * @throws RedirectionExceptionInterface
     * @throws DecodingExceptionInterface
     * @throws ClientExceptionInterface
     * @throws TransportExceptionInterface
     * @throws ServerExceptionInterface
     */
    public function downloadReturns(): void
    {
        $allReturns = $this->getFromWallmartApi('GET', 'returns', ['limit' => 5, 'nextCursor' => null], 'returnOrders', null,null, 'cursor');
        foreach ($allReturns as $return) {
            foreach ($return['returnOrderLines'] as $orderLine) {
                $sku = $orderLine['item']['sku'];
                echo "Sku: " . $sku . "\n";
                $sql = "
                        SELECT object_id
                        FROM iwa_json_store
                        WHERE field_name = 'apiResponseJson'  AND JSON_UNQUOTE(JSON_EXTRACT(json_data, :jsonPath)) = :uniqueId LIMIT 1;";
                $jsonPath = '$.' . '"sku"';
                $result = Utility::fetchFromSql($sql, ['jsonPath' => $jsonPath, 'uniqueId' => $sku]);
                $objectId = $result[0]['object_id'] ?? null;
                $variantObject = VariantProduct::getById($objectId);
                echo "Object ID: " . $variantObject->getTitle() . "\n";

            }
        }


        //$this->putToCache('RETURNS.json', $allReturns);
    }

    /**
     * @throws RedirectionExceptionInterface|DecodingExceptionInterface|ClientExceptionInterface|TransportExceptionInterface|ServerExceptionInterface|Exception|RandomException
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
        $json = [
            'sku' => $sku,
            'quantity' => [
                'unit' => 'EACH',
                'amount' => $targetValue
            ]
        ];
        $response = $this->getFromWallmartApi('PUT', 'inventory', ['sku'=> $sku], null, null, $json, null);
        echo "Inventory set to $targetValue\n";
        $date = date('YmdHis');
        $filename = "SETINVENTORY_{$sku}_{$date}.json";
        $this->putToCache($filename, ['request' => $json, 'response' => $response]);
    }

    /**
     * @throws RedirectionExceptionInterface|DecodingExceptionInterface|ClientExceptionInterface|TransportExceptionInterface|ServerExceptionInterface|Exception|RandomException
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
        $response = $this->getFromWallmartApi('PUT', 'price', null, null, null, $json, null);
        $statusCode = $response->getStatusCode();
        if ($statusCode !== 200) {
            echo "Error: $statusCode\n";
            return;
        }
        echo "Price set to $finalPrice\n";
        $date = date('YmdHis');
        $filename = "SETPRICE_{$sku}_{$date}.json";
        $this->putToCache($filename, ['request' => $json, 'response' => $response]);
    }
   
}