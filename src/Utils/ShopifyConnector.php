<?php

namespace App\Utils;

use Pimcore\Model\DataObject\Data\Link;
use Pimcore\Model\DataObject\VariantProduct;

use App\Utils\Utility;

class ShopifyConnector extends MarketplaceConnectorAbstract
{
    protected static $marketplaceType = 'Shopify';

    public function download($forceDownload = false)
    {
        $filename = 'tmp/'.urlencode($this->marketplace->getKey()).'.json';
        if (!$forceDownload && file_exists($filename) && filemtime($filename) > time() - 86400) {
            $this->listings = json_decode(file_get_contents($filename), true);
            echo "Using cached data\n";
        } else {
            $accessToken = $this->marketplace->getAccessToken();
            $apiUrl = $this->marketplace->getApiUrl();
            $apiVersion = '2024-01';
            $limit = 50;
            $this->listings = [];
            $url = "https://{$apiUrl}/admin/api/{$apiVersion}/products.json?limit={$limit}";
            do {
                $ch = curl_init();
                curl_setopt($ch, CURLOPT_URL, $url);
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
                curl_setopt($ch, CURLOPT_HEADER, 1);
                curl_setopt($ch, CURLOPT_HTTPHEADER, [
                    "X-Shopify-Access-Token: $accessToken"
                ]);
                $response = curl_exec($ch);
                $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
                if ($httpCode !== 200) {
                    echo "Error: $httpCode\n";
                    curl_close($ch);
                    break;
                }
                $headerSize = curl_getinfo($ch, CURLINFO_HEADER_SIZE);
                $header = substr($response, 0, $headerSize);
                $body = substr($response, $headerSize);
                $data = json_decode($body, true);
                $products = $data['products'];
                $this->listings = array_merge($this->listings, $products);
                $url = null;
                if (preg_match('/<([^>]+)>;\s*rel="next"/', $header, $matches)) {
                    $url = $matches[1];
                }
                curl_close($ch);
                echo ".";
                sleep(1);
            } while ($url);
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
        // find biggest id for this shop
        $sql = "SELECT MAX(order_id) FROM iwa_shopify_orders WHERE shopify_id = ?";
        $maxId = $db->fetchOne($sql, [$this->marketplace->getId()]);
        if (!$maxId) {
            $maxId = 0;
        }
        $accessToken = $this->marketplace->getAccessToken();
        $apiUrl = $this->marketplace->getApiUrl();
        $apiVersion = '2024-07';
        $url = "https://{$apiUrl}/admin/api/{$apiVersion}/orders.json?status=any&since_id=".($maxId+1);
        $sql = "INSERT INTO iwa_shopify_orders (shopify_id, order_id, json) VALUES (?, ?, ?) ON DUPLICATE KEY UPDATE json = VALUES(json)";
        do {
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
            curl_setopt($ch, CURLOPT_HEADER, 1);
            curl_setopt($ch, CURLOPT_HTTPHEADER, [
                "X-Shopify-Access-Token: $accessToken"
            ]);
            $response = curl_exec($ch);
            $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            if ($httpCode !== 200) {
                echo "Error: $httpCode\n";
                curl_close($ch);
                break;
            }
            $headerSize = curl_getinfo($ch, CURLINFO_HEADER_SIZE);
            $header = substr($response, 0, $headerSize);
            $body = substr($response, $headerSize);
            $data = json_decode($body, true);
            $orders = $data['orders'];
            try {
                $db->beginTransaction();
                foreach ($orders as $order) {
                    $db->executeUpdate($sql, [$this->marketplace->getId(), $order['id'], json_encode($order)]);
                }
                $db->commit();
            } catch (\Exception $e) {
                $db->rollBack();
                echo "Error: " . $e->getMessage() . "\n";
            }
            $url = null;
            if (preg_match('/<([^>]+)>;\s*rel="next"/', $header, $matches)) {
                $url = $matches[1];
            }
            curl_close($ch);
            echo ".".count($orders);
            sleep(1);
        } while ($url);
    }

    protected function getImage($listing, $mainListing) {
        $lastImage = null;
        $images = $mainListing['images'] ?? [];
        foreach ($images as $img) {
            if (!is_numeric($listing['image_id']) || $img['id'] === $listing['image_id']) {
                return static::getCachedImage($img['src']);
            } 
            if (empty($lastImage)) {
                $lastImage = static::getCachedImage($img['src']);
            }
        }
        if (!empty($mainListing['image']['src'])) {
            return static::getCachedImage($mainListing['image']['src']);
        }
        return $lastImage;
    }

    protected function getUrlLink($listing, $mainListing)
    {
        if (!empty($mainListing['handle']) && !empty($listing['id'])) {
            $l = new Link();
            $l->setPath($this->marketplace->getMarketplaceUrl().'products/'.$mainListing['handle'].'/?variant='.$listing['id']);
            return $l;
        }
        return null;
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
                        'urlLink' => $this->getUrlLink($listing, $mainListing),
                        'salePrice' => $listing['price'] ?? '',
                        'saleCurrency' => $this->marketplace->getCurrency(),
                        'attributes' => $listing['title'] ?? '',
                        'title' => ($mainListing['title'] ?? '').($listing['title'] ?? ''),
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
