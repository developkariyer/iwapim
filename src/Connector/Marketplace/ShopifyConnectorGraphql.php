<?php

namespace App\Connector\Marketplace;

use Doctrine\DBAL\Exception;
use Pimcore\Db;
use Pimcore\Model\DataObject\Data\ExternalImage;
use Pimcore\Model\DataObject\VariantProduct;
use Pimcore\Model\DataObject\Marketplace;
use App\Utils\Utility;
use Pimcore\Model\Element\DuplicateFullPathException;
use Symfony\Contracts\HttpClient\Exception\ClientExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\RedirectionExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\ServerExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;

class ShopifyConnectorGraphql extends MarketplaceConnectorAbstract
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
                $response = $this->httpClient->request($method, $this->apiUrl . '/graphql.json', $headersToApi);
                $newData = json_decode($response->getContent(), true);
                if ($response->getStatusCode() === 200) {
                    break;
                }
                if ($response->getStatusCode() === 429) {
                    $this->processRateLimit($newData['extensions']);
                    continue;
                }
                echo "Failed to $method $this->apiUrl/graphql.json: {$response->getContent()} \n";
                break;
            }
            $itemsCount = count($newData['data'][$key]['nodes'] ?? []);
            $totalCount += $itemsCount;
            echo "Count: $totalCount\n";
            // Nested Pagination
            if ($key) {
                $newData['data'][$key]['nodes'] = $this->processShopifyDataByKey($key, $newData['data'][$key]['nodes'] ?? []);
            }
            $currentPageData = $key ? ($newData['data'][$key]['nodes'] ?? []) : $newData;
            $allData = array_merge($allData, $currentPageData);
            $pageInfo = $newData['data'][$key]['pageInfo'] ?? null;
            $cursor = $pageInfo['endCursor'] ?? null;
            $hasNextPage = $pageInfo['hasNextPage'] ?? false;
        } while ($hasNextPage);
        return $allData;
    }

    public function processRateLimit($extensions): void
    {
        $actualQueryCost = $extensions['cost']['actualQueryCost'];
        $currentlyAvailable = $extensions['cost']['throttleStatus']['currentlyAvailable'];
        $restoreRate = $extensions['cost']['throttleStatus']['restoreRate'];
        if ($actualQueryCost > $currentlyAvailable) {
            $requiredTimeToWait = ceil(($actualQueryCost - $currentlyAvailable) / $restoreRate);
            sleep($requiredTimeToWait);
        }
    }

    /**
     * @throws TransportExceptionInterface
     * @throws ServerExceptionInterface
     * @throws RedirectionExceptionInterface
     * @throws ClientExceptionInterface
     */
    protected function processShopifyDataByKey(string $key, array $nodes): array
    {
        foreach ($nodes as &$node) {
            match ($key) {
                'products' => $this->processProduct($node),
                'orders' => $this->processOrder($node),
                default => null,
            };
        }
        return $nodes;
    }

    /**
     * @throws TransportExceptionInterface
     * @throws ServerExceptionInterface
     * @throws RedirectionExceptionInterface
     * @throws ClientExceptionInterface
     */
    protected function processProduct(array &$product): void
    {
        $productId = $product['id'];
        $variants = $this->graphqlNestedPaginateDownload('ownerId', $productId, 'downloadVariant.graphql', 'product', 'variants', 50);
        if (!empty($variants)) {
            $product['variants']['nodes'] = array_merge($product['variants']['nodes'] ?? [], $variants);
        }
        $medias = $this->graphqlNestedPaginateDownload('ownerId', $productId, 'downloadMedia.graphql', 'product', 'media', 50);
        if (!empty($medias)) {
            $product['media']['nodes'] = array_merge($product['media']['nodes'] ?? [], $medias);
        }
    }

    /**
     * @throws TransportExceptionInterface
     * @throws ServerExceptionInterface
     * @throws RedirectionExceptionInterface
     * @throws ClientExceptionInterface
     */
    protected function processOrder(array &$order): void
    {
        $orderId = $order['id'];
        $lineItems = $this->graphqlNestedPaginateDownload('id', $orderId, 'downloadOrdersLineItems.graphql', 'order', 'lineItems', 50);
        if (!empty($lineItems)) {
            $order['lineItems']['nodes'] = array_merge($order['lineItems']['nodes'] ?? [], $lineItems);
        }
    }

    /**
     * @throws RedirectionExceptionInterface
     * @throws ClientExceptionInterface
     * @throws TransportExceptionInterface
     * @throws ServerExceptionInterface
     */
    public function graphqlNestedPaginateDownload($idKey, $id, $queryFile, $fieldKey, $nodeKey, $numItems = 50): array
    {
        $query = [
            'query' => file_get_contents($this->graphqlUrl . $queryFile),
            'variables' => [
                $idKey => $id,
                'cursor' => null,
                'numItems' => $numItems
            ]
        ];
        $headersToApi = [
            'headers' => [
                'X-Shopify-Access-Token' => $this->marketplace->getAccessToken(),
                'Content-Type' => 'application/json'
            ]
        ];
        $collectedItems = [];
        $cursor = null;
        $totalNestedItems = 0;
        do {
            $query['variables']['cursor'] = $cursor;
            while (true) {
                $response = $this->httpClient->request("POST", $this->apiUrl . '/graphql.json', [
                    'json' => $query,
                    'headers' => $headersToApi['headers']
                ]);
                $newData = json_decode($response->getContent(), true);
                if ($response->getStatusCode() === 200) {
                    break;
                }
                if ($response->getStatusCode() === 429) {
                    $this->processRateLimit($newData['extensions']);
                    continue;
                }
                echo "Failed to $query $this->apiUrl/graphql.json: {$response->getContent()} \n";
                break;
            }
            $data = json_decode($response->getContent(), true);
            $items = $data['data'][$fieldKey][$nodeKey]['nodes'] ?? [];
            $collectedItems = array_merge($collectedItems, $items);
            $pageInfo = $data['data'][$fieldKey][$nodeKey]['pageInfo'];
            $cursor = $pageInfo['endCursor'] ?? null;
            $hasNextPage = $pageInfo['hasNextPage'] ?? null;
            $totalNestedItems += count($items);
            print_r("Total $fieldKey/$nodeKey Count: $totalNestedItems\n");
        } while ($hasNextPage);
        return $collectedItems;
    }

    /**
     * @throws TransportExceptionInterface
     * @throws ServerExceptionInterface
     * @throws RedirectionExceptionInterface
     * @throws ClientExceptionInterface
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
        $result = Utility::fetchFromSqlFile(parent::SQL_PATH . 'Shopify/select_last_updated_at.sql', [
            'marketplace_id' => $this->marketplace->getId()
        ]);
        $lastUpdatedAt = $result[0]['lastUpdatedAt'];
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
        foreach ($orders as $order) {
            Utility::executeSqlFile(parent::SQL_PATH . 'insert_marketplace_orders.sql', [
                'marketplace_id' => $this->marketplace->getId(),
                'order_id' => $order['id'],
                'json' => json_encode($order)
            ]);
        }
    }

    /**
     * @throws TransportExceptionInterface
     * @throws ServerExceptionInterface
     * @throws RedirectionExceptionInterface
     * @throws ClientExceptionInterface
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
        $variantId = json_decode($listing->jsonRead('apiResponseJson'), true)['id'];
        if (empty($variantId)) {
            echo "Failed to get variant id for {$listing->getKey()}\n";
            return;
        }
        if (empty($targetPrice)) {
            echo "Price is empty for {$listing->getKey()}\n";
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
        $variants = [];
        $query = [
            'query' => file_get_contents($this->graphqlUrl . 'setPrice.graphql'),
            'variables' => [
                'productId' => $variantId,
                'variants' => $variants
            ]

        ];
        $response = $this->getFromShopifyApiGraphql('POST', $query, 'productVariantsBulkUpdate');
        echo "Price set\n";
        $filename = "SETPRICE_{$variantId}.json";
        $this->putToCache($filename, ['request'=>$query, 'response'=>$response]);
    }

    /**
     * @throws DuplicateFullPathException
     */
    public function import($updateFlag, $importFlag): void
    {
        $this->listings = [];
        $this->listings = $this->getFromCache("LISTINGS.json");

        $marketplaceFolder = Utility::checkSetPath(
            Utility::sanitizeVariable('Test10' . $this->marketplace->getKey(), 190),
            Utility::checkSetPath('Pazaryerleri')
        );
        $total = count($this->listings);
        $index = 0;
        foreach ($this->listings as $mainListing) {
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
            if (isset($parentResponseJson['variants']['nodes'])) {
                unset($parentResponseJson['variants']['nodes']);
            }
            foreach ($mainListing['variants']['nodes'] as $listing) {
                echo $this->marketplace->getMarketplaceUrl().'products/'.($mainListing['handle'] ?? '').'/?variant='.($listing['id'] ?? '');
                 try{
                    VariantProduct::addUpdateVariant(
                        variant: [
                            'imageUrl' => $this->getImage($listing, $mainListing) ?? '',
                            'urlLink' => $this->getUrlLink($this->marketplace->getMarketplaceUrl().'products/'.($mainListing['handle'] ?? '').'/?variant='.($listing['id'] ?? '')),
                            'salePrice' =>   $listing['price'] ?? '',
                            'saleCurrency' =>   $this->marketplace->getCurrency(),
                            'attributes' =>   $listing['title'] ?? '',
                            'title' =>  ($mainListing['title'] ?? '').($listing['title'] ?? ''),
                            'quantity' => $listing['inventory_quantity'] ?? 0,
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

    protected function getImage($listing, $mainListing): ?ExternalImage
    {
        $lastImage = null;
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

}
