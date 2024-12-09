<?php

namespace App\Connector\Marketplace;

use Pimcore\Model\DataObject\VariantProduct;
use Pimcore\Model\DataObject\Marketplace;
use App\Utils\Utility;

class ShopifyConnector extends MarketplaceConnectorAbstract
{
    public static $marketplaceType = 'Shopify';

    private $apiUrl = null;

    public function __construct($marketplace)
    {
        parent::__construct($marketplace);
        $this->apiUrl = trim($this->marketplace->getApiUrl(), characters: "/ \n\r\t");
        if (empty($this->apiUrl)) {
            throw new \Exception("API URL is not set for Shopify marketplace {$this->marketplace->getKey()}");
        }
        if (strpos($this->apiUrl, 'https://') === false) {
            $this->apiUrl = "https://{$this->apiUrl}/admin/api/2024-07";
        }
    }
    
    public function getFromShopifyApi($method, $parameter, $query = [], $key = null)
    {
        $data = [];
        $nextLink = "{$this->apiUrl}/{$parameter}";
        $headersToApi = [
            'query' => $query,
            'headers' => [
                'X-Shopify-Access-Token' => $this->marketplace->getAccessToken(),
                'Content-Type' => 'application/json',
                'Accept' => 'application/json'
            ]
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

    public function download($forceDownload = false)
    {
        $listing = VariantProduct::getById(238746);
        $this->setPrice($listing);
        /*$this->listings = json_decode(Utility::getCustomCache('LISTINGS.json', PIMCORE_PROJECT_ROOT. "/tmp/marketplaces/".urlencode($this->marketplace->getKey())), true);
        if (!(empty($this->listings) || $forceDownload)) {
            echo "Using cached listings\n";
            return;
        }
        $this->listings = $this->getFromShopifyApi('GET', 'products.json', ['limit' => 50], 'products');
        if (empty($this->listings)) {
            echo "Failed to download listings\n";
            return;
        }
        Utility::setCustomCache('LISTINGS.json', PIMCORE_PROJECT_ROOT. "/tmp/marketplaces/".urlencode($this->marketplace->getKey()), json_encode($this->listings));*/
    }

    public function downloadInventory()
    {
        $inventory = json_decode(Utility::getCustomCache('INVENTORY.json', PIMCORE_PROJECT_ROOT. "/tmp/marketplaces/".urlencode($this->marketplace->getKey())), true);
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
        Utility::setCustomCache('INVENTORY.json', PIMCORE_PROJECT_ROOT. "/tmp/marketplaces/".urlencode($this->marketplace->getKey()), json_encode($inventory));
    }

    public function downloadOrders()
    {
        $db = \Pimcore\Db::get();
        $lastUpdatedAt = $db->fetchOne(
            "SELECT COALESCE(MAX(json_extract(json, '$.updated_at')), '2000-01-01T00:00:00Z') FROM iwa_marketplace_orders WHERE marketplace_id = ?",
            [$this->marketplace->getId()]
        );
        $orders = $this->getFromShopifyApi('GET', 'orders.json', ['status' => 'any', 'updated_at_min' => $lastUpdatedAt], 'orders');
        $db->beginTransaction();
        try {
            foreach ($orders as $order) {
                $db->executeStatement(
                    "INSERT INTO iwa_marketplace_orders (marketplace_id, order_id, json) VALUES (?, ?, ?) ON DUPLICATE KEY UPDATE json = VALUES(json)",
                    [
                        $this->marketplace->getId(),
                        $order['id'],
                        json_encode($order)
                    ]
                );
            }
            $db->commit();
        } catch (\Exception $e) {
            $db->rollBack();
            echo "Error: " . $e->getMessage() . "\n";
        }
    }

    protected function getImage($listing, $mainListing) {
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

    public function setInventory(VariantProduct $listing, int $targetValue, $sku = null, $country = null, $locationId = null)
    {
        $inventoryItemId = json_decode($listing->jsonRead('apiResponseJson'), true)['inventory_item_id'];
        $response = $this->getFromShopifyApi('POST', "inventory_levels/set.json", ['location_id' => $locationId, 'inventory_item_id' => $inventoryItemId, 'available' => $targetValue]);
        if (empty($response)) {
            echo "Failed to set inventory for {$listing->getKey()}\n";
            return;
        }
        print_r($response);
        Utility::setCustomCache($inventoryItemId . '_' . date('Y-m-d H:i:s') . '_SetInventory.json', PIMCORE_PROJECT_ROOT. "/tmp/marketplaces/".urlencode($this->marketplace->getKey()) . '/Inventory', json_encode($response));
    }

    public function setSku(VariantProduct $listing, string $sku)
    {
        $inventoryItemId = json_decode($listing->jsonRead('apiResponseJson'), true)['inventory_item_id'];
        $response = $this->getFromShopifyApi('PUT', "inventory_items/{$inventoryItemId}.json", ['inventory_item' => ['id' => $inventoryItemId, 'sku' => $sku]]);
        if (empty($response)) {
            echo "Failed to set SKU for {$listing->getKey()}\n";
            return;
        }
        print_r($response);
        Utility::setCustomCache($inventoryItemId . '_' . date('Y-m-d H:i:s') . '_SetSku.json', PIMCORE_PROJECT_ROOT. "/tmp/marketplaces/".urlencode($this->marketplace->getKey()) . '/Skus', json_encode($response));
    }

    public function setPrice(VariantProduct $listing)
    {
        $currencies = [
            'CANADIAN DOLLAR' => 'CAD',
            'TL' => 'TRY',
            'EURO' => 'EUR',
            'US DOLLAR' => 'USD',
            'SWEDISH KRONA' => 'SEK',
            'POUND STERLING' => 'GBP'
        ];
        $variantId = json_decode($listing->jsonRead('apiResponseJson'), true)['id']; 
        $marketplace = $listing->getMarketplace();
        $marketplaceCurrency = $marketplace->getCurrency();
        $marketplaceCurrency = $currencies[$marketplaceCurrency];
        echo $marketplaceCurrency;
        


    }

}
