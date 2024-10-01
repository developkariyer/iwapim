<?php

namespace App\MarketplaceConnector;

use Pimcore\Model\DataObject\Data\Link;
use Pimcore\Model\DataObject\VariantProduct;

use App\Utils\Utility;

class TrendyolConnector extends MarketplaceConnectorAbstract
{
    public function download($forceDownload = false)
    {
        $filename = 'tmp/'.urlencode($this->marketplace->getKey()).'.json';
        if (!$forceDownload && file_exists($filename) && filemtime($filename) > time() - 86400) {
            $this->listings = json_decode(file_get_contents($filename), true);
            echo "Using cached data ";
        } else {
            $apiUrl = "https://api.trendyol.com/sapigw/suppliers/{$this->marketplace->getTrendyolSellerId()}/products?approved=true";
            $page = 0;
            $this->listings = [];
            do {
                $ch = curl_init();
                curl_setopt($ch, CURLOPT_URL, "$apiUrl&page=$page");
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
                curl_setopt($ch, CURLOPT_HEADER, 1);
                curl_setopt($ch, CURLOPT_HTTPHEADER, [
                    "Authorization: Basic {$this->marketplace->getTrendyolToken()}",
                ]);
                $response = curl_exec($ch);
                $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
                if ($httpCode !== 200) {
                    echo "Error: $httpCode\n";
                    curl_close($ch);
                    break;
                }
                $headerSize = curl_getinfo($ch, CURLINFO_HEADER_SIZE);
                $body = substr($response, $headerSize);
                $data = json_decode($body, true);
                $products = $data['content'];
                $this->listings = array_merge($this->listings, $products);
                $page++;
                curl_close($ch);
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

