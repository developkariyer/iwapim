<?php

namespace App\Connector\Marketplace;

use Pimcore\Model\DataObject\VariantProduct;
use App\Utils\Utility;
use Symfony\Component\HttpClient\HttpClient;

class CiceksepetiConnector extends MarketplaceConnectorAbstract
{
    private static $apiUrl = [
        'offers' => "https://apis.ciceksepeti.com/api/v1/Products"
    ];
    
    public static $marketplaceType = 'Ciceksepeti';
    
    public function download($forceDownload = false)
    {
        $this->listings = json_decode(Utility::getCustomCache('LISTINGS.json', PIMCORE_PROJECT_ROOT. "/tmp/marketplaces/".urlencode($this->marketplace->getKey())), true);
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
        Utility::setCustomCache('LISTINGS.json', PIMCORE_PROJECT_ROOT. "/tmp/marketplaces/".urlencode($this->marketplace->getKey()), json_encode($this->listings));
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
                    'startDate' => '2024-11-10T03:52:09.390Z',
                    'endDate' => '2024-11-20T03:52:09.390Z',
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

}