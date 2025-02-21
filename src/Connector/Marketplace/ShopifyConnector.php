<?php

namespace App\Connector\Marketplace;

use Doctrine\DBAL\Exception;
use Pimcore\Db;
use Pimcore\Model\DataObject\Data\ExternalImage;
use Pimcore\Model\DataObject\VariantProduct;
use Pimcore\Model\DataObject\Marketplace;
use App\Utils\Utility;
use Pimcore\Model\Element\DuplicateFullPathException;
use Random\RandomException;
use Symfony\Contracts\HttpClient\Exception\ClientExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\RedirectionExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\ServerExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;

class ShopifyConnector extends MarketplaceConnectorAbstract
{
    public static string $marketplaceType = 'Shopify';

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
    public function getFromShopifyApi($method, $parameter, $query = [], $key = null, $body = null): ?array
    {
        echo "getFromShopifyApi";
        $data = [];
        $nextLink = "{$this->apiUrl}/{$parameter}";
        $headersToApi = [
            'query' => $query,
            'headers' => [
                'X-Shopify-Access-Token' => $this->marketplace->getAccessToken(),
                'Content-Type' => 'application/json',
                'Accept' => 'application/json'
            ],
            'json' => $body
        ];
        while ($nextLink) {
            $response = $this->httpClient->request($method, $nextLink, $headersToApi);
            if ($response->getStatusCode() !== 200) {
                echo "Failed to $method $nextLink: {$response->getContent()}\n";
                return null;
            }
            usleep(200000);
            $newData = json_decode($response->getContent(), true);
            $data = array_merge($data, $key ? ($newData[$key] ?? []) : $newData);
            $headers = $response->getHeaders(false);
            $links = $headers['link'] ?? [];
            $nextLink = null;
            foreach ($links as $link) {
                if (preg_match('/<([^>]+)>;\s*rel="next"/', $link, $matches)) {
                    $nextLink = $matches[1];
                    break;
                }
            }
            $headersToApi = [
                'headers' => [
                    'X-Shopify-Access-Token' => $this->marketplace->getAccessToken(),
                    'Content-Type' => 'application/json',
                    'Accept' => 'application/json'
                ]
            ];
            echo ".";
        }
        return $data;
    }

    /**
     * @throws TransportExceptionInterface
     * @throws ServerExceptionInterface
     * @throws RedirectionExceptionInterface
     * @throws ClientExceptionInterface|RandomException
     */
    public function download($forceDownload = false): void
    {
       if (!$forceDownload && $this->getListingsFromCache()) {
            echo "Using cached listings\n";
            return;
       }
       $this->listings = $this->getFromShopifyApi('GET', 'products.json', ['limit' => 50], 'products');
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
     * @throws ClientExceptionInterface|RandomException
     */
    public function downloadInventory(): void
    {
        $this->downloadReturns();
        /*$inventory = $this->getFromCache('INVENTORY.json');
        if (!empty($inventory)) {
            echo "Using cached inventory\n";
            return;
        }
        $inventory = [];
        $locations = $this->getFromShopifyApi('GET', 'locations.json', [], 'locations');
        if (empty($locations)) {
            echo "Failed to get locations\n";
            return;
        }
        foreach ($locations as $location) {
            $inventoryLevels = $this->getFromShopifyApi('GET', "inventory_levels.json", ['limit' => 50, 'location_ids' => $location['id']], 'inventory_levels');
            if (empty($inventoryLevels)) {
                echo "Failed to get inventory levels for location {$location['id']}\n";
                continue;
            }
            $inventory[] = [
                'location' => $location,
                'inventory_levels' => $inventoryLevels
            ];
        }
        $this->putToCache('INVENTORY.json', $inventory);*/
    }

    /**
     * @throws TransportExceptionInterface
     * @throws ServerExceptionInterface
     * @throws RedirectionExceptionInterface
     * @throws ClientExceptionInterface
     */
    public function downloadOrders(): void
    {
        try {
            $sqlLastUpdatedAt = "
                SELECT COALESCE(MAX(json_extract(json, '$.updated_at')), '2000-01-01T00:00:00Z') AS lastUpdatedAt
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
        $orders = $this->getFromShopifyApi('GET', 'orders.json', ['status' => 'any', 'updated_at_min' => $lastUpdatedAt], 'orders');
        try {
            foreach ($orders as $order) {
                $sqlInsertMarketplaceOrder = "
                    INSERT INTO iwa_marketplace_orders (marketplace_id, order_id, json) 
                    VALUES (:marketplace_id, :order_id, :json) ON DUPLICATE KEY UPDATE json = VALUES(json)";
                Utility::executeSql($sqlInsertMarketplaceOrder, [
                    'marketplace_id' => $this->marketplace->getId(),
                    'order_id' => $order['id'],
                    'json' => json_encode($order)
                ]);
            }
        } catch (\Exception $e) {
            echo "Error: " . $e->getMessage() . "\n";
        }
    }

    public function downloadReturns()
    {
        $returns = $this->getFromShopifyApi('GET', 'refunds.json', [], 'refunds');
        $this->putToCache('RETURNS.json', $returns);
    }

    // Not Used
    private function downloadAbondonedCheckouts(): void
    {
        echo "Downloading abandoned checkouts\n";
        $db = Db::get();
        $lastUpdatedAt = $db->fetchOne(
            "SELECT COALESCE(MAX(json_extract(json, '$.updated_at')), '2000-01-01T00:00:00Z') FROM iwa_marketplace_abandoned_checkouts WHERE marketplace_id = ?",
            [$this->marketplace->getId()]
        );
        $checkouts = $this->getFromShopifyApi('GET', 'checkouts.json', ['status' => 'any', 'updated_at_min' => $lastUpdatedAt], 'checkouts');
        $db->beginTransaction();
        try {
            foreach ($checkouts as $checkout) {
                $db->executeStatement(
                    "INSERT INTO iwa_marketplace_abandoned_checkouts (marketplace_id, checkout_id, json) VALUES (?, ?, ?) ON DUPLICATE KEY UPDATE json = VALUES(json)",
                    [
                        $this->marketplace->getId(),
                        $checkout['id'],
                        json_encode($checkout)
                    ]
                );
            }
            $db->commit();
        } catch (\Exception $e) {
            $db->rollBack();
            echo "Error: " . $e->getMessage() . "\n";
        }
    }

    protected function getImage($listing, $mainListing): ?ExternalImage
    {
        $lastImage = null;
        $images = $mainListing['images'] ?? [];
        foreach ($images as $img) {
            if (!is_numeric($listing['image_id']) || $img['id'] === $listing['image_id']) {
                return Utility::getCachedImage($img['src']);
            } 
            if (empty($lastImage)) {
                $lastImage = Utility::getCachedImage($img['src']);
            }
        }
        if (!empty($mainListing['image']['src'])) {
            return Utility::getCachedImage($mainListing['image']['src']);
        }
        return $lastImage;
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
        foreach ($this->listings as $mainListing) {
            echo "($index/$total) Processing Listing {$mainListing['id']}:{$mainListing['title']} ...";
            $parent = Utility::checkSetPath(
                Utility::sanitizeVariable($mainListing['product_type'] ?? 'Tasnif-Edilmemiş'),
                $marketplaceFolder
            );
            if (!empty($mainListing['title'])) {
                $parent = Utility::checkSetPath(
                    Utility::sanitizeVariable($mainListing['title']),
                    $parent
                );    
            }
            if (($mainListing['status'] ?? 'active') !== 'active') {
                $parent = Utility::checkSetPath(
                    Utility::sanitizeVariable('_Pasif'),
                    $marketplaceFolder
                );
            }
            $parentResponseJson = $mainListing;
            if (isset($parentResponseJson['variants'])) {
                unset($parentResponseJson['variants']);
            }
            foreach ($mainListing['variants'] as $listing) {
                VariantProduct::addUpdateVariant(
                    variant: [
                        'imageUrl' => $this->getImage($listing, $mainListing),
                        'urlLink' => $this->getUrlLink($this->marketplace->getMarketplaceUrl().'products/'.($mainListing['handle'] ?? '').'/?variant='.($listing['id'] ?? '')),
                        'salePrice' => $listing['price'] ?? '',
                        'saleCurrency' => $this->marketplace->getCurrency(),
                        'attributes' => $listing['title'] ?? '',
                        'title' => ($mainListing['title'] ?? '').($listing['title'] ?? ''),
                        'quantity' => $listing['inventory_quantity'] ?? 0,
                        'uniqueMarketplaceId' => $listing['id'] ?? '',
                        'apiResponseJson' => json_encode($listing),
                        'parentResponseJson' => json_encode($parentResponseJson),
                        'published' => ($mainListing['status'] ?? 'active') === 'active',
                        'sku' => $listing['sku'] ?? '',
                        'ean' => $listing['barcode'] ?? '',
                    ],
                    importFlag: $importFlag,
                    updateFlag: $updateFlag,
                    marketplace: $this->marketplace,
                    parent: $parent
                );
                echo "v";
            }
            echo "OK\n";
            $index++;
        }
    }

    /**
     * @throws TransportExceptionInterface
     * @throws ServerExceptionInterface
     * @throws RedirectionExceptionInterface
     * @throws ClientExceptionInterface
     * @throws Exception
     */
    public function setInventory(VariantProduct $listing, int $targetValue, $sku = null, $country = null, $locationId = null): void
    {
        $inventoryItemId = json_decode($listing->jsonRead('apiResponseJson'), true)['inventory_item_id'];
        if (empty($inventoryItemId)) {
            echo "Failed to get inventory item id for {$listing->getKey()}\n";
            return;
        }
        $request = [
            'location_id' => $locationId,
            'inventory_item_id' => $inventoryItemId,
            'available' => $targetValue
        ];
        $response = $this->getFromShopifyApi('POST', "inventory_levels/set.json", [], null, $request);
        echo "Inventory set\n";
        $filename = "SETINVENTORY_{$inventoryItemId}.json";
        $this->putToCache($filename, ['request'=>$request, 'response'=>$response]);
    }

    /**
     * @throws TransportExceptionInterface
     * @throws ServerExceptionInterface
     * @throws RedirectionExceptionInterface
     * @throws ClientExceptionInterface
     * @throws Exception|RandomException
     */
    public function setSku(VariantProduct $listing, string $sku): void
    {
        if (empty($sku)) {
            echo "SKU is empty for {$listing->getKey()}\n";
            return;
        }
        $apiResponse = json_decode($listing->jsonRead('apiResponseJson'), true);
        $jsonSku = $apiResponse['sku'] ?? null;
        $inventoryItemId = $apiResponse['inventory_item_id'] ?? null;
        if (!empty($jsonSku) && $jsonSku === $sku) {
            //echo "SKU is already set for {$listing->getKey()}: $jsonSku\n";
            return;
        }
        if (empty($inventoryItemId)) {
            echo "Failed to get inventory item id for {$listing->getKey()}\n";
            return;
        }
        $request = [
            'inventory_item' => [
                'id' => $inventoryItemId,
                'sku' => $sku
            ]
        ];
        $response = $this->getFromShopifyApi('PUT', "inventory_items/{$inventoryItemId}.json", [], null, $request);
        if (empty($response)) {
            echo "Failed to set SKU for {$listing->getKey()}\n";
            return;
        }
        echo "$sku SKU set\n";
        $this->putToCache("SETSKU_{$listing->getUniqueMarketplaceId()}.json", ['request'=>$request, 'response'=>$response]);
    }

    /**
     * @throws TransportExceptionInterface
     * @throws RandomException
     * @throws ServerExceptionInterface
     * @throws RedirectionExceptionInterface
     * @throws ClientExceptionInterface
     * @throws Exception
     */
    public function setBarcode(VariantProduct $listing, string $barcode): void
    {
        if (empty($barcode)) {
            echo "Barcode is empty for {$listing->getKey()}";
            return;
        }
        $variantId = json_decode($listing->jsonRead('apiResponseJson'), true)['id'];
        if (empty($variantId)) {
            echo "Failed to get variant id for {$listing->getKey()}";
            return;
        }
        $request = [
            'variant' => [
                'id' => $variantId,
                'barcode' => $barcode
            ]
        ];
        $response = $this->getFromShopifyApi('PUT', "variants/{$variantId}.json", [], null, $request);
        if (empty($response)) {
            echo "Failed to set barcode for {$listing->getKey()}";
            return;
        }
        echo "Barcode set to $barcode";
        $this->putToCache("SETBARCODE_{$listing->getUniqueMarketplaceId()}.json", ['request'=>$request, 'response'=>$response]);
    }

    /**
     * @throws TransportExceptionInterface
     * @throws ServerExceptionInterface
     * @throws RedirectionExceptionInterface
     * @throws ClientExceptionInterface
     * @throws Exception|RandomException
     */
    public function setPrice(VariantProduct $listing, string $targetPrice, $targetCurrency = null, $sku = null, $country = null): void
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
        $request = [
            'variant' => [
                'id' => $variantId,
                'price' => $finalPrice
            ]
        ];
        $response = $this->getFromShopifyApi('PUT', "variants/{$variantId}.json", [], null, $request);
        echo "Price set\n";
        $filename = "SETPRICE_{$variantId}.json";
        $this->putToCache($filename, ['request'=>$request, 'response'=>$response]);
    }

}
