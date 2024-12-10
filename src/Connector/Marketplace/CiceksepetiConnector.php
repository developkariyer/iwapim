<?php

namespace App\Connector\Marketplace;

use App\Model\DataObject\VariantProduct;
use App\Utils\Utility;
use Symfony\Component\HttpClient\HttpClient;

class CiceksepetiConnector extends MarketplaceConnectorAbstract
{
    private static $apiUrl = [
        'offers' => "https://apis.ciceksepeti.com/api/v1/Products",
        'updateInventoryPrice' => "https://apis.ciceksepeti.com/api/v1/Products/price-and-stock",
        'batchStatus' => "https://apis.ciceksepeti.com/api/v1/Products/batch-status/"
    ];
    
    public static $marketplaceType = 'Ciceksepeti';
    
    public function download($forceDownload = false)
    {
        $variant = VariantProduct::getById(266097);
        //$this->setInventory($variant, 299);
        //$this->setPrice($variant, 1179);
        $this->getBatchRequestResult("e68b2802-14fb-43a0-bd9a-8e91015aa675");
        /*$this->listings = json_decode(Utility::getCustomCache('LISTINGS.json', PIMCORE_PROJECT_ROOT. "/tmp/marketplaces/".urlencode($this->marketplace->getKey())), true);
        if (!(empty($this->listings) || $forceDownload)) {
            echo "Using cached listings\n";
            return;
        }
        $page = 1;
        $size = 60;
        $this->listings = [];
        do {
            $response = $this->httpClient->request('GET', static::$apiUrl['offers'], [
                'headers' => [
                    'x-api-key' => $this->marketplace->getCiceksepetiApiKey()
                ],
                'query' => [
                    'Page' => $page,
                    'PageSize' => $size
                ]
            ]);
            $statusCode = $response->getStatusCode();
            if ($statusCode !== 200) {
                echo "Error: $statusCode\n";
                break;
            }
            $data = $response->toArray();
            $products = $data['products'];
            $this->listings = array_merge($this->listings, $products);
            $totalItems = $data['totalCount'];
            echo "Page: " . $page . " ";
            echo "Count: " . count($this->listings) . " / Total Count: " . $totalItems . "\n";
            $page++;
            sleep(5);
        } while (count($this->listings) < $totalItems);
        if (empty($this->listings)) {
            echo "Failed to download listings\n";
            return;
        }
        Utility::setCustomCache('LISTINGS.json', PIMCORE_PROJECT_ROOT. "/tmp/marketplaces/".urlencode($this->marketplace->getKey()), json_encode($this->listings));*/
    }

    private function getAttributes($listing)
    {
        $color = '';
        $size = '';
        if (!empty($listing['attributes'])) {
            foreach ($listing['attributes'] as $attribute) {
                if ($attribute['parentName'] === 'Renk' && $attribute['type'] === 'Variant Özelliği') {
                    $color = $attribute['name'];
                }
                if ($attribute['parentName'] === 'Ebat' || $attribute['parentName'] === 'Uzunluk' ) {
                    $size = $attribute['name'];
                }
            }
        }
        return trim($color . '-' . $size, '-');
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
            echo "($index/$total) Processing Listing {$listing['barcode']}:{$listing['productName']} ...";
            $parent = Utility::checkSetPath($marketplaceFolder);
            if ($listing['mainProductCode']) {
                $parent = Utility::checkSetPath(
                    Utility::sanitizeVariable($listing['mainProductCode']),
                    $parent
                );
            }
            VariantProduct::addUpdateVariant(
                variant: [
                    'imageUrl' => Utility::getCachedImage($listing['images'][0]) ?? '',
                    'urlLink' =>  $this->getUrlLink($listing['link']) ?? '',
                    'salePrice' => $listing['listPrice'] ?? 0,
                    'saleCurrency' => 'TL',
                    'title' => $listing['productName'] ?? '',
                    'attributes' => $listing['variantName']  ?? '',
                    'uniqueMarketplaceId' =>  $listing['productCode'] ?? '',
                    'apiResponseJson' => json_encode($listing, JSON_PRETTY_PRINT),
                    'published' => $listing['productStatusType'] === 'YAYINDA' ? 1 : 0,
                    'sku' => $listing['stockCode'] ?? '',
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
        $page = 0;
        $pageSize = 100;
        //do {
            $response = $this->httpClient->request('POST', 'https://apis.ciceksepeti.com/api/v1/Order/GetOrders', [
                'headers' => [
                    'x-api-key' => $this->marketplace->getCiceksepetiApiKey()
                ],
                'json' => [
                    'startDate' => '2024-10-03',
                    'endDate' => '2024-10-15',
                    'page' => $page,
                    'pageSize' => $pageSize
                ]
            ]);
            $statusCode = $response->getStatusCode();
            print_r($response->getContent());
            /*if ($statusCode !== 200) {
                echo "Error: $statusCode\n";
                return;
            }
            $data = $response->toArray();
            $count = $data['orderListCount'];
            $page++;*/
        //}while($count === $pageSize);
        

    }
    
    public function downloadInventory()
    {

    }

    public function setInventory(VariantProduct $listing, int $targetValue, $sku = null, $country = null, $locationId = null)
    {
        if (!$listing instanceof VariantProduct) {
            echo "Listing is not a VariantProduct\n";
            return;
        }
        $stockCode = json_decode($listing->jsonRead('apiResponseJson'), true)['stockCode'];
        if (empty($stockCode)) {
            echo "Failed to get inventory item id for {$listing->getKey()}\n";
            return;
        }
        $response = $this->httpClient->request('PUT', static::$apiUrl['updateInventoryPrice'], [
            'headers' => [
                'x-api-key' => $this->marketplace->getCiceksepetiApiKey(),
                'Content-Type' => 'application/json'
            ],
            'body' => json_encode([
                'items' => [
                    [
                        'stockCode' => $stockCode,
                        'StockQuantity' => $targetValue, 
                    ]
                ]
            ])
        ]);
        print_r($response->getContent());
        $statusCode = $response->getStatusCode();
        if ($statusCode !== 200) {
            echo "Error: $statusCode\n";
            return;
        }
        $data = $response->toArray();
        $combinedData = [
            'inventory' => $data,
            'batchRequestResult' => $this->getBatchRequestResult($data['batchId'])
        ];
        echo "Inventory set\n";
        $date = date('Y-m-d-H-i-s');
        $filename = "{$stockCode}-$date.json";  
        Utility::setCustomCache($filename, PIMCORE_PROJECT_ROOT. "/tmp/marketplaces/".urlencode($this->marketplace->getKey()) . '/SetInventory', json_encode($response));
    }

    public function setPrice(VariantProduct $listing,string $targetPrice, $targetCurrency = null, $sku = null, $country = null)
    {
        if (!$listing instanceof VariantProduct) {
            echo "Listing is not a VariantProduct\n";
            return;
        }
        if ($targetPrice === null) {
            echo "Error: Price cannot be null\n";
            return;
        }
        if ($targetCurrency === null) {
            $targetCurrency = $listing->getSaleCurrency();
        }
        $finalPrice = $this->convertCurrency($targetPrice, $targetCurrency, $listing->getSaleCurrency());
        if ($finalPrice === null) {
            echo "Error: Currency conversion failed\n";
            return;
        }
        $stockCode = json_decode($listing->jsonRead('apiResponseJson'), true)['stockCode'];
        if (empty($stockCode)) {
            echo "Failed to get inventory item id for {$listing->getKey()}\n";
            return;
        }
        $response = $this->httpClient->request('PUT', static::$apiUrl['updateInventoryPrice'], [
            'headers' => [
                'x-api-key' => $this->marketplace->getCiceksepetiApiKey(),
                'Content-Type' => 'application/json'
            ],
            'body' => json_encode([
                'items' => [
                    [
                        'stockCode' => $stockCode,
                        'salesPrice' => (float)$finalPrice, 
                    ]
                ]
            ])
        ]);
        $statusCode = $response->getStatusCode();
        if ($statusCode !== 200) {
            echo "Error: $statusCode\n";
            return;
        }
        $data = $response->toArray();
        $combinedData = [
            'price' => $data,
            'batchRequestResult' => $this->getBatchRequestResult($data['batchId'])
        ];
        echo "Price set\n";
        $date = date('Y-m-d-H-i-s');
        $filename = "{$stockCode}-$date.json";  
        Utility::setCustomCache($filename, PIMCORE_PROJECT_ROOT. "/tmp/marketplaces/".urlencode($this->marketplace->getKey()) . '/SetPrice', json_encode($response));
    } 

    /*public function updateProduct(VariantProduct $listing, string $sku)
    {
        if (!$listing instanceof VariantProduct) {
            echo "Listing is not a VariantProduct\n";
            return;
        }
        if ($sku === null) {
            echo "Error: SKU cannot be null\n";
            return;
        }
        $productName = json_decode($listing->jsonRead('apiResponseJson'), true)['productName'];
        if (empty($productName) || $productName === null) {
            echo "Failed to get product name for {$listing->getKey()}\n";
            return;
        }
        $mainProductCode = json_decode($listing->jsonRead('apiResponseJson'), true)['mainProductCode'];
        if (empty($mainProductCode) || $mainProductCode === null) {
            echo "Failed to get main product code for {$listing->getKey()}\n";
            return;
        }
        $stockCode = json_decode($listing->jsonRead('apiResponseJson'), true)['stockCode'];
        if (empty($stockCode) || $stockCode === null) {
            echo "Failed to get stock code for {$listing->getKey()}\n";
            return;
        }
        $isActive = json_decode($listing->jsonRead('apiResponseJson'), true)['productStatusType'] === 'YAYINDA' ? 1 : 0;
        $description = json_decode($listing->jsonRead('apiResponseJson'), true)['description'];
        if (empty($description) || $description === null) {
            echo "Failed to get description for {$listing->getKey()}\n";
            return;
        }
        $images = json_decode($listing->jsonRead('apiResponseJson'), true)['images'];
        if (empty($images) || $images === null) {
            echo "Failed to get images for {$listing->getKey()}\n";
            return;
        }
        $deliveryType = json_decode($listing->jsonRead('apiResponseJson'), true)['deliveryType'];
        if (empty($deliveryType) || $deliveryType === null) {
            echo "Failed to get delivery type for {$listing->getKey()}\n";
            return;
        }
        $deliveryMessageType = json_decode($listing->jsonRead('apiResponseJson'), true)['deliveryMessageType'];
        if (empty($deliveryMessageType) || $deliveryMessageType === null) {
            echo "Failed to get delivery message type for {$listing->getKey()}\n";
            return;
        }
        $attributes = json_decode($listing->jsonRead('apiResponseJson'), true)['attributes'];
        $response = $this->httpClient->request('PUT', static::$apiUrl['offers'], [
            'headers' => [
                'x-api-key' => $this->marketplace->getCiceksepetiApiKey()
            ],
            'json' => [
                'productName' => $productName,
                'mainProductCode' => $mainProductCode,
                'stockCode' => $stockCode,
                'isActive' => $isActive,
                'description' => $description,
                'images' => $images,
                'deliveryType' => $deliveryType,
                'deliveryMessageType' => $deliveryMessageType,
                'attributes' => $attributes,
            ]
        ]);
        $statusCode = $response->getStatusCode();
        if ($statusCode !== 200) {
            echo "Error: $statusCode\n";
            return;
        }
        $data = $response->toArray();
        $combinedData = [
            'price' => $data,
            'batchRequestResult' => $this->getBatchRequestResult($data['batchId'])
        ];
        echo "Product Update \n";
        $date = date('Y-m-d-H-i-s');
        $filename = "{$stockCode}-$date.json";  
        Utility::setCustomCache($filename, PIMCORE_PROJECT_ROOT. "/tmp/marketplaces/".urlencode($this->marketplace->getKey()) . '/UpdateSku', json_encode($response));
    }*/

    public function getBatchRequestResult($batchId)
    {
        $url = static::$apiUrl['batchStatus'] . $batchId;
        $response = $this->httpClient->request('GET', $url, [
            'headers' => [
                'x-api-key' => $this->marketplace->getCiceksepetiApiKey()
            ]
        ]);
        $statusCode = $response->getStatusCode();
        if ($statusCode !== 200) {
            echo "Error: $statusCode\n";
            return;
        }
        print_r($response->getContent());
        $data = $response->toArray();
        return $data;
    }

}