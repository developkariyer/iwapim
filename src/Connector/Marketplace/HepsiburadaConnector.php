<?php

namespace App\Connector\Marketplace;

use Pimcore\Model\DataObject\VariantProduct;
use App\Utils\Utility;
use Symfony\Component\HttpClient\HttpClient;
use Pimcore\Model\DataObject\Data\Link;

class HepsiburadaConnector extends MarketplaceConnectorAbstract
{
    /*private static $apiUrl = [
        'offers' => "https://listing-external-sit.hepsiburada.com/listings/merchantid/{$this->marketplace->getHepsiburadaMerchantId()}"
    ];*/
    
    public static $marketplaceType = 'Hepsiburada';
    
    public function download($forceDownload = false)
    {
        $this->listings = json_decode(Utility::getCustomCache('LISTINGS.json', PIMCORE_PROJECT_ROOT. "/tmp/marketplaces/".urlencode($this->marketplace->getKey())), true);
        if (!(empty($this->listings) || $forceDownload)) {
            echo "Using cached listings\n";
            return;
        }
        $offset = 0;
        $limit = 10;
        $this->listings = [];
        do {
            $response = $this->httpClient->request('GET', "https://listing-external.hepsiburada.com/listings/merchantid/{$this->marketplace->getSellerId()}", [
                'headers' => [
                    'Authorization' => 'Basic ' . base64_encode($this->marketplace->getSellerId() . ':' . $this->marketplace->getServiceKey()),
                    "User-Agent" => "colorfullworlds_dev",
                    'Accept' => 'application/json',
                    'Content-Type' => 'application/json'
                ],
                'query' => [
                    'offset' => $offset,
                    'limit' => $limit
                ]
            ]);
            $statusCode = $response->getStatusCode();
            if ($statusCode !== 200) {
                echo "Error: $statusCode\n";
                break;
            }
            $data = $response->toArray();
            $products = $data['listings'];
            $this->listings = array_merge($this->listings, $products);
            $totalItems = $data['totalCount'];
            echo "Offset: " . $offset . " " . count($this->listings) . " ";
            echo "Total Items: " . $totalItems . "\n";
            echo "Count: " . count($this->listings) . "\n";
            $offset += $limit;
        } while (count($this->listings) < $totalItems);

        if (empty($this->listings)) {
            echo "Failed to download listings\n";
            return;
        }
        $this->downloadAttributes();
        Utility::setCustomCache('LISTINGS.json', PIMCORE_PROJECT_ROOT. "/tmp/marketplaces/".urlencode($this->marketplace->getKey()), json_encode($this->listings));
    }

    protected function getProduct($hbSku)
    {
        $page = 0;
        $size = 1;
        $response = $this->httpClient->request('GET', "https://mpop.hepsiburada.com/product/api/products/all-products-of-merchant/{$this->marketplace->getSellerId()}", [
            'headers' => [
                'Authorization' => 'Basic ' . base64_encode($this->marketplace->getSellerId() . ':' . $this->marketplace->getServiceKey()),
                "User-Agent" => "colorfullworlds_dev",
                'Accept' => 'application/json',
                'Content-Type' => 'application/json'
            ],
            'query' => [
                'page' => $page,
                'size' => $size,
                'hbSku' => $hbSku
            ]
        ]);
        $statusCode = $response->getStatusCode();
        if ($statusCode !== 200) {
            echo "Error: $statusCode\n";
            return;
        }
        $data = $response->toArray();
        return $data;
    }

    public function downloadAttributes()
    {
        echo "Downloading Attributes\n";
        foreach ($this->listings as &$listing) {
            $response = $this->getProduct($listing['hepsiburadaSku']);
            if (empty($response)) {
                echo "Failed to get product\n";
                continue;
            }
            if (isset($response['data'][0])) {
                $listing['attributes'] = $response['data'][0];
            } else {
                $listing['attributes'] = []; 
            }
        }
        echo "Attributes Downloaded\n";
    }

    protected function getAttributes($variantTypeAttributes)
    {
        $attributeString = "";
        foreach ($variantTypeAttributes as $attribute) {
            $attributeString .= $attribute['value'] . "-";
        }
        return rtrim($attributeString, "-");
    }

    public function import($updateFlag, $importFlag)
    {
       /*if (empty($this->listings)) {
            echo "Nothing to import\n";
        }
        $marketplaceFolder = Utility::checkSetPath(
            Utility::sanitizeVariable($this->marketplace->getKey(), 190),
            Utility::checkSetPath('Pazaryerleri')
        );
        $total = count($this->listings);
        $index = 0;*/
        //$first = false;
        foreach ($this->listings as $listing) {
            echo "Variant group id: " . $listing['attributes']['variantGroupId'] . "\n";
            echo "Image url: " . $listing['attributes']['images'][0] . "\n";
            echo "Url: " . $listing['attributes']['url'] . "\n";
            echo "Price: " . $listing['price'] . "\n";
            echo "Sale currency: TRY" . "\n";
            echo "Title: " . $listing['attributes']['productName'] . "\n";
            echo "Attributes: " . $this->getAttributes($listing['attributes']['variantTypeAttributes']) . "\n";
            echo "Unique marketplace id: " . $listing['hepsiburadaSku'] . "\n";
            echo "Api response json: " . json_encode($listing, JSON_PRETTY_PRINT) . "\n";
            echo "Published: " . $listing['isSalable'] . "\n";
            echo "Sku: " . $listing['merchantSku'] . "\n";
            echo "-----------------------------------\n";




            /* echo "($index/$total) Processing Listing {$listing['merchantSku']} ...";
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
                    'urlLink' => $this->getUrlLink("https://www.hepsiburada.com/-p-" . $listing['hepsiburadaSku']) ?? '',
                    'salePrice' => $listing['price'] ?? 0,
                    'saleCurrency' => 'TRY',
                    'title' => $listing['productName'] ?? '',
                    'attributes' =>$listing['productName'] ?? '',
                    'uniqueMarketplaceId' => $listing['hepsiburadaSku'] ?? '',
                    'apiResponseJson' => json_encode($listing, JSON_PRETTY_PRINT),
                    'published' => $listing['isSalable'],
                    'sku' => $listing['merchantSku'] ?? '',
                ],
                importFlag: $importFlag,
                updateFlag: $updateFlag,
                marketplace: $this->marketplace,
                parent: $parent
            );
            echo "OK\n";
            $index++;*/
        }  
        
    }

    public function downloadOrders()
    {

    }
    
    public function downloadInventory()
    {

    }

}