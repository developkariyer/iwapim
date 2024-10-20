<?php

namespace App\Connector\Marketplace;

use Pimcore\Model\DataObject\VariantProduct;
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
    
    protected function getFromShopifyApi($method, $parameter, $query = [], $key = null)
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
        $this->listings = json_decode(Utility::getCustomCache('LISTINGS.json', PIMCORE_PROJECT_ROOT. "/tmp/marketplaces/".urlencode($this->marketplace->getKey())), true);
        if (!(empty($this->listings) || $forceDownload)) {
            echo "Using cached listings\n";
            return;
        }
        $this->listings = $this->getFromShopifyApi('GET', 'products.json', ['limit' => 50], 'products');
        if (empty($this->listings)) {
            echo "Failed to download listings\n";
            return;
        }
        Utility::setCustomCache('LISTINGS.json', PIMCORE_PROJECT_ROOT. "/tmp/marketplaces/".urlencode($this->marketplace->getKey()), json_encode($this->listings));
    }

    public function downloadInventory()
    {

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

}
