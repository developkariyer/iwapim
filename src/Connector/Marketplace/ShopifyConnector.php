<?php

namespace App\Connector\Marketplace;

use App\Utils\Utility;
use Doctrine\DBAL\Exception;
use Pimcore\Model\DataObject\Data\ExternalImage;
use Pimcore\Model\DataObject\Marketplace;
use Pimcore\Model\DataObject\VariantProduct;
use Pimcore\Model\Element\DuplicateFullPathException;
use Random\RandomException;
use Symfony\Contracts\HttpClient\Exception\ClientExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\RedirectionExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\ServerExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;

class ShopifyConnector  extends MarketplaceConnectorAbstract
{
    public static string $marketplaceType = 'Shopify';

    private string $graphqlUrl = PIMCORE_PROJECT_ROOT . '/src/GraphQL/Shopify/';

    private string $apiUrl;

    public function __construct($marketplace)
    {
        parent::__construct($marketplace);
        $this->apiUrl = trim($this->marketplace->getApiUrl(), characters: "/ \n\r\t");
        if (empty($this->apiUrl)) {
            throw new \Exception("API URL is not set for Shopify marketplace {$this->marketplace->getKey()}");
        }
        if (!str_contains($this->apiUrl, 'https://')) {
            $this->apiUrl = "https://{$this->apiUrl}/admin/api/2024-07";
        }
    }

    /**
     * @throws RedirectionExceptionInterface
     * @throws ClientExceptionInterface
     * @throws TransportExceptionInterface
     * @throws ServerExceptionInterface
     */
    public function getFromShopifyApiGraphql($method, $data, $key = null): ?array
    {
        echo "Getting from Shopify GraphQL\n";
        $allData = [];
        $cursor = null;
        $totalCount = 0;
        $allData[$key] = [];
        do {
            $data['variables']['cursor'] = $cursor;
            $headersToApi = [
                'json' => $data,
                'headers' => [
                    'X-Shopify-Access-Token' => $this->marketplace->getAccessToken(),
                    'Content-Type' => 'application/json'
                ]
            ];
            while (true) {
                try {
                    $response = $this->httpClient->request($method, $this->apiUrl . '/graphql.json', $headersToApi);
                    $newData = json_decode($response->getContent(), true);
                    echo "Cost Info: " . json_encode($newData['extensions']['cost']) . PHP_EOL;
                    if ($newData['extensions']['cost']['throttleStatus']['currentlyAvailable'] < $newData['extensions']['cost']['actualQueryCost'] ) {
                        $restoreRate =  $this->rateLimitCalculate($newData['extensions']) ?? 5;
                        echo "Rate limit exceeded, waiting for {$restoreRate} seconds..." . PHP_EOL;
                        sleep($restoreRate);
                        continue;
                    }
                    $allData[$key] = array_merge($allData[$key] ?? [], $newData['data'][$key]['nodes'] ?? []);
                    break;
                } catch (\Exception $e) {
                    echo "Request Error: " . $e->getMessage() . PHP_EOL;
                    break;
                }
            }
            $itemsCount = count($newData['data'][$key]['nodes'] ?? []);
            $totalCount += $itemsCount;
            echo "$key Count: $totalCount\n";
            $pageInfo = $newData['data'][$key]['pageInfo'] ?? null;
            $cursor = $pageInfo['endCursor'] ?? null;
            $hasNextPage = $pageInfo['hasNextPage'] ?? false;
        } while ($hasNextPage);
        return $allData;
    }

    public function rateLimitCalculate($extensions): Int
    {
        $actualQueryCost = $extensions['cost']['actualQueryCost'];
        $currentlyAvailable = $extensions['cost']['throttleStatus']['currentlyAvailable'];
        $restoreRate = $extensions['cost']['throttleStatus']['restoreRate'];
        $waitTime = ceil(($actualQueryCost - $currentlyAvailable) / $restoreRate) + 1;
        return max($waitTime, 10);
    }

    /**
     * @throws TransportExceptionInterface
     * @throws ServerExceptionInterface
     * @throws RedirectionExceptionInterface
     * @throws ClientExceptionInterface
     * @throws RandomException
     */
    public function download($forceDownload = false): void
    {
        echo "GraphQL download\n";
        if ($this->getListingsFromCache()) {
            echo "Using cached listings\n";
            return;
        }
        $query = [
            'query' => file_get_contents($this->graphqlUrl . 'downloadListing.graphql'),
            'variables' => [
                'numProducts' => 50,
                'cursor' => null
            ]
        ];
        $this->listings = $this->getFromShopifyApiGraphql('POST', $query, 'products');
        if (empty($this->listings)) {
            echo "Failed to download listings\n";
            return;
        }
        $this->putListingsToCache();
    }


    /**
     * @throws TransportExceptionInterface
     * @throws ServerExceptionInterface
     * @throws RedirectionExceptionInterface
     * @throws ClientExceptionInterface
     * @throws Exception
     */
    public function downloadOrders(): void
    {
        try {
            $sqlLastUpdatedAt = "
                SELECT COALESCE(MAX(json_extract(json, '$.updatedAt')), '2000-01-01T00:00:00Z') AS lastUpdatedAt
                FROM iwa_marketplace_orders
                WHERE marketplace_id = :marketplace_id;";
            $result = Utility::fetchFromSql($sqlLastUpdatedAt, [
                'marketplace_id' => $this->marketplace->getId()
            ]);
            $lastUpdatedAt = $result[0]['lastUpdatedAt'];
        } catch (\Exception $e) {
            echo "Error: " . $e->getMessage() . "\n";
        }
        echo  "Last updated at: $lastUpdatedAt\n";
        $filter = 'updated_at:>=' . (string) $lastUpdatedAt;
        $query = [
            'query' => file_get_contents($this->graphqlUrl . 'downloadOrders.graphql'),
            'variables' => [
                'numOrders' => 50,
                'cursor' => null,
                'filter' => $filter
            ]
        ];
        $orders = $this->getFromShopifyApiGraphql('POST', $query, 'orders');
        try {
            foreach ($orders['orders']  as $order) {
                $sqlInsertMarketplaceOrder = "
                    INSERT INTO iwa_marketplace_orders (marketplace_id, order_id, json) 
                    VALUES (:marketplace_id, :order_id, :json) ON DUPLICATE KEY UPDATE json = VALUES(json)";
                Utility::executeSql($sqlInsertMarketplaceOrder, [
                    'marketplace_id' => $this->marketplace->getId(),
                    'order_id' => basename($order['id']),
                    'json' => json_encode($order)
                ]);
            }
        } catch (\Exception $e) {
            echo "Error: " . $e->getMessage() . "\n";
        }
    }

    /**
     * @throws TransportExceptionInterface
     * @throws ServerExceptionInterface
     * @throws RedirectionExceptionInterface
     * @throws ClientExceptionInterface
     * @throws RandomException
     */
    public function downloadInventory(): void
    {
        $inventory = $this->getFromCache('INVENTORY.json');
        if (!empty($inventory)) {
            echo "Using cached inventory\n";
            return;
        }
        $query = [
            'query' => file_get_contents($this->graphqlUrl . 'downloadInventory.graphql'),
            'variables' => [
                'numItems' => 50,
                'cursor' => null
            ]
        ];
        $inventories = $this->getFromShopifyApiGraphql('POST', $query, 'inventoryItems');
        if (empty($inventories)) {
            echo "Failed to download inventory\n";
            return;
        }
        $this->putToCache('INVENTORY.json', $inventories);
    }

    /**
     * @throws TransportExceptionInterface
     * @throws RandomException
     * @throws ServerExceptionInterface
     * @throws RedirectionExceptionInterface
     * @throws ClientExceptionInterface
     */
    public function downloadReturns(): void
    {
        $sql = "SELECT * FROM `iwa_marketplace_orders_line_items` WHERE marketplace_type = 'Shopify' and is_canceled = 'cancelled'";
        $returnOrders = Utility::fetchFromSql($sql, []);
        foreach ($returnOrders as $return) {
            $sqlInsertMarketplaceReturn = "
                            INSERT INTO iwa_marketplace_returns (marketplace_id, return_id, json) 
                            VALUES (:marketplace_id, :return_id, :json) ON DUPLICATE KEY UPDATE json = VALUES(json)";
            Utility::executeSql($sqlInsertMarketplaceReturn, [
                'marketplace_id' => $this->marketplace->getId(),
                'return_id' => $return['order_id'],
                'json' => json_encode($return)
            ]);
            echo "Inserting order: " . $return['order_id'] . "\n";
        }

        /*$query = [
            'query' => file_get_contents($this->graphqlUrl . 'downloadReturn.graphql'),
            'variables' => [
                    'numOrders' => 50,
                    'cursor' => null,
                    'filter' => "return_status:return_requested OR return_status:in_progress OR return_status:inspection_complete OR return_status:returned OR return_status:return_failed"
            ]
        ];
        $returns = $this->getFromShopifyApiGraphql('POST', $query, 'orders');
        foreach ($returns['orders'] as &$return) {
            $orderId = basename($return['id']);
            $sql = "select * from iwa_marketplace_orders_line_items where order_id = :order_id";
            try {
                $order = Utility::fetchFromSql($sql, ['order_id' => $orderId]);
            } catch (\Exception $e) {
                echo "Error: " . $e->getMessage() . "\n";
            }
            $return['orderDetail'] = $order;
        }
        //$this->putToCache("Returns.json", $returns);
        foreach ($returns['orders'] as $return) {
            $sqlInsertMarketplaceReturn = "
                            INSERT INTO iwa_marketplace_returns (marketplace_id, return_id, json) 
                            VALUES (:marketplace_id, :return_id, :json) ON DUPLICATE KEY UPDATE json = VALUES(json)";
            Utility::executeSql($sqlInsertMarketplaceReturn, [
                'marketplace_id' => $this->marketplace->getId(),
                'return_id' => basename($return['id']),
                'json' => json_encode($return)
            ]);
            echo "Inserting order: " . $return['id'] . "\n";
        }*/
    }

    /**
     * @throws DuplicateFullPathException
     * @throws RandomException
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
        $total = count($this->listings['products']);
        $index = 0;
        foreach ($this->listings['products'] as $mainListing) {
            echo "($index/$total) Processing Listing {$mainListing['id']}:{$mainListing['title']} ...\n";
            $parent = Utility::checkSetPath(
                Utility::sanitizeVariable($mainListing['productType'] ?? 'Tasnif-Edilmemiş'),
                $marketplaceFolder
            );
            if (!empty($mainListing['title'])) {
                $parent = Utility::checkSetPath(
                    Utility::sanitizeVariable($mainListing['title']),
                    $parent
                );
            }
            if (($mainListing['status'] ?? 'ACTIVE') !== 'ACTIVE') {
                $parent = Utility::checkSetPath(
                    Utility::sanitizeVariable('_Pasif'),
                    $marketplaceFolder
                );
            }
            $parentResponseJson = $mainListing;
            /*if (isset($parentResponseJson['variants']['nodes'])) {
                unset($parentResponseJson['variants']['nodes']);
            }*/
            $parentResponseJson['descriptionHtmlSafe'] = preg_replace('/<a\s+[^>]*href=[\'"]([^\'"]*)[\'"][^>]*>(.*?)<\/a>/i', '$2', $parentResponseJson['descriptionHtml']);
            foreach ($mainListing['variants']['nodes'] as $listing) {
                try{
                    VariantProduct::addUpdateVariant(
                        variant: [
                            'imageUrl' => $this->getImage($listing, $mainListing) ?? '',
                            'urlLink' => $this->getUrlLink($this->marketplace->getMarketplaceUrl().'products/'.($mainListing['handle'] ?? '').'/?variant='.($listing['id'] ?? '')),
                            'salePrice' =>   $listing['price'] ?? '',
                            'saleCurrency' =>   $this->marketplace->getCurrency(),
                            'attributes' =>   $listing['title'] ?? '',
                            'title' =>  ($mainListing['title'] ?? '').($listing['title'] ?? ''),
                            'quantity' => $listing['inventoryQuantity'] ?? 0,
                            'uniqueMarketplaceId' =>  basename($listing['id'] ?? ''),
                            'apiResponseJson' =>  json_encode($listing),
                            'parentResponseJson' => json_encode($parentResponseJson),
                            'published' =>  ($mainListing['status'] ?? 'ACTIVE') === 'ACTIVE',
                            'sku' =>   $listing['sku'] ?? '',
                            'ean' =>   $listing['barcode'] ?? '',
                        ],
                        importFlag: $importFlag,
                        updateFlag: $updateFlag,
                        marketplace: $this->marketplace,
                        parent: $parent
                    );
                    echo "v";
                } catch (\Exception $e) {
                    echo "Error: " . $e->getMessage() . "\n";
                    echo "Sku: " . $listing['sku'] ?? '' . "\n";
                    echo "ERRROR VARIANT: \n";
                }
            }
            echo "OK\n";
            $index++;
        }
    }

    protected function getImage($listing, $mainListing)
    {
        $lastImage = '';
        $images = $mainListing['media']['nodes'] ?? [];
        foreach ($images as $img) {
            $imageId = $listing['image']['id'] ?? null;
            $imgId = $img['id'] ?? null;
            if ($imageId === null || $imgId === null) {
                continue;
            }
            if ( basename($imgId) === basename($imageId)) {
                return Utility::getCachedImage($img['preview']['image']['url'] ?? '');
            }

            if (empty($lastImage)) {
                $lastImage = Utility::getCachedImage($img['preview']['image']['url'] ?? '');
            }
        }
        return $lastImage;
    }

    /**
     * @throws TransportExceptionInterface
     * @throws RandomException
     * @throws ServerExceptionInterface
     * @throws RedirectionExceptionInterface
     * @throws ClientExceptionInterface
     * @throws Exception
     */
    public function setSku(VariantProduct $listing, string $sku): void // not tested
    {
        if (empty($sku)) {
            echo "SKU is empty for {$listing->getKey()}\n";
            return;
        }
        $apiResponse = json_decode($listing->jsonRead('apiResponseJson'), true);
        $jsonSku = $apiResponse['sku'] ?? null;
        $inventoryItemId = $apiResponse['inventory_item_id'] ?? null;
        if (!empty($jsonSku) && $jsonSku === $sku) {
            echo "SKU is already set for {$listing->getKey()}\n";
            return;
        }
        if (empty($inventoryItemId)) {
            echo "Failed to get inventory item id for {$listing->getKey()}\n";
            return;
        }
        $query = [
            'query' => file_get_contents($this->graphqlUrl . 'setSku.graphql'),
            'variables' => [
                'id' => $inventoryItemId,
                'sku' => $sku,
            ]
        ];
        $response = $this->getFromShopifyApiGraphql('POST', $query, 'inventoryItemUpdate');
        if (empty($response)) {
            echo "Failed to set SKU for {$listing->getKey()}\n";
            return;
        }
        echo "SKU set\n";
        $this->putToCache("SETSKU_{$listing->getUniqueMarketplaceId()}.json", ['request'=>$query, 'response'=>$response]);
    }

    /**
     * @throws TransportExceptionInterface
     * @throws RandomException
     * @throws ServerExceptionInterface
     * @throws RedirectionExceptionInterface
     * @throws ClientExceptionInterface
     * @throws Exception
     */
    public function setInventory(VariantProduct $listing, int $targetValue, $sku = null, $country = null, $locationId = null): void // not tested
    {
        $inventoryItemId = json_decode($listing->jsonRead('apiResponseJson'), true)['inventory_item_id'];
        if (empty($inventoryItemId)) {
            echo "Failed to get inventory item id for {$listing->getKey()}\n";
            return;
        }
        $query = [
            'query' => file_get_contents($this->graphqlUrl . 'setInventory.graphql'),
            'variables' => [
                'name' => 'available',
                'quantities' => [
                    'inventoryItemId' => $inventoryItemId,
                    'locationId' => $locationId,
                    'quantity' => $targetValue
                ],
                'reason' => 'restock'
            ]
        ];
        $response = $this->getFromShopifyApiGraphql('POST', $query, 'inventorySetQuantities');
        echo "Inventory set\n";
        $filename = "SETINVENTORY_{$inventoryItemId}.json";
        $this->putToCache($filename, ['request'=>$query, 'response'=>$response]);
    }

    /**
     * @throws TransportExceptionInterface
     * @throws ServerExceptionInterface
     * @throws RedirectionExceptionInterface
     * @throws ClientExceptionInterface
     * @throws Exception
     * @throws RandomException
     */
    public function setPrice(VariantProduct $listing, string $targetPrice, $targetCurrency = null, $sku = null, $country = null): void // not tested
    {
        $currencies = [
            'CANADIAN DOLLAR' => 'CAD',
            'TL' => 'TL',
            'EURO' => 'EUR',
            'US DOLLAR' => 'USD',
            'SWEDISH KRONA' => 'SEK',
            'POUND STERLING' => 'GBP'
        ];
        $variantId = basename(json_decode($listing->jsonRead('apiResponseJson'), true)['id']);
        $productId = basename(json_decode($listing->jsonRead('parentResponseJson'), true)['id']);
        if (empty($variantId) || empty($productId) || empty($targetPrice)) {
            echo "Failed to get variant id for {$listing->getKey()}\n";
            return;
        }
        if (empty($targetCurrency)) {
            $marketplace = $listing->getMarketplace();
            if ($marketplace instanceof Marketplace) {
                $marketplaceCurrency = $marketplace->getCurrency();
                if (empty($marketplaceCurrency)) {
                    if (isset($currencies[$marketplaceCurrency])) {
                        $marketplaceCurrency = $currencies[$marketplaceCurrency];
                    }
                }
            }
        }
        if (empty($marketplaceCurrency)) {
            echo "Marketplace currency could not be found for {$listing->getKey()}\n";
            return;
        }
        $finalPrice = $this->convertCurrency($targetPrice, $targetCurrency, $marketplaceCurrency);
        if (empty($finalPrice)) {
            echo "Failed to convert currency for {$listing->getKey()}\n";
            return;
        }
        $query = [
            'query' => file_get_contents($this->graphqlUrl . 'setPrice.graphql'),
            'variables' => [
                'productId' => $productId,
                'variants' => [
                    'id' => $variantId,
                    'price' => $finalPrice,
                ]
            ]

        ];
        $response = $this->getFromShopifyApiGraphql('POST', $query, 'productVariantsBulkUpdate');
        echo "Price set\n";
        $filename = "SETPRICE_{$variantId}.json";
        $this->putToCache($filename, ['request'=>$query, 'response'=>$response]);
    }

    /**
     * @throws Exception
     * @throws RandomException
     */
    public function setBarcode(VariantProduct $listing, string $barcode): void //not tested
    {
        if (empty($barcode)) {
            echo "Barcode is empty for {$listing->getKey()}";
            return;
        }
        $variantId = basename(json_decode($listing->jsonRead('apiResponseJson'), true)['id']);
        $productId = basename(json_decode($listing->jsonRead('parentResponseJson'), true)['id']);
        if (empty($variantId) || empty($productId) || empty($targetPrice)) {
            echo "Failed to get variant id for {$listing->getKey()}\n";
            return;
        }
        $query = [
            'query' => file_get_contents($this->graphqlUrl . 'setBarcode.graphql'),
            'variables' => [
                'productId' => $productId,
                'variants' => [
                    'id' => $variantId,
                    'barcode' => $barcode,
                ]
            ]

        ];
        $response = $this->getFromShopifyApiGraphql('POST', $query, 'productVariantsBulkUpdate');
        if (empty($response)) {
            echo "Failed to set barcode for {$listing->getKey()}";
            return;
        }
        echo "Barcode set to $barcode";
        $this->putToCache("SETBARCODE_{$listing->getUniqueMarketplaceId()}.json", ['request'=>$query, 'response'=>$response]);
    }

}