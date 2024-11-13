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
        $filename = 'tmp/' . urlencode($this->marketplace->getKey()) . '.json';
        if (!$forceDownload && file_exists($filename) && filemtime($filename) > time() - 86400) {
            $this->listings = json_decode(file_get_contents($filename), true);
            echo "Using cached data ";
        } else {
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
            file_put_contents($filename, json_encode($this->listings));
        }
        return count($this->listings);
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
            if (!empty($listing['productCode'])) {
                $parent = Utility::checkSetPath(
                    Utility::sanitizeVariable($listing['productCode']),
                    $parent
                );
            }
            echo "imageUrl: ". Utility::getCachedImage($listing['images'][0]);
            echo "urlLink: ". $this->getUrlLink($listing['link']);
            echo "salePrice: ". $listing['listPrice'];
            echo "saleCurrency: ". 'TL';
            echo "title: ". $listing['productName'];
            echo "attributes: ". $listing['attributes'];
            echo "uniqueMarketplaceId: ". $listing['barcode'];
            //echo "apiResponseJson: ". json_encode($listing, JSON_PRETTY_PRINT);
            echo "published: ". $listing['isActive'];
            echo "sku: ". $listing['barcode'];

            /*VariantProduct::addUpdateVariant(
                variant: [
                    'imageUrl' => Utility::getCachedImage($listing['image_url']),
                    'urlLink' => $this->getUrlLink($this->createUrlLink($listing['offer_url'], $listing['title'])),
                    'salePrice' => $listing['selling_price'] ?? 0,
                    'saleCurrency' => 'ZAR',
                    'title' => $listing['title'] ?? '',
                    'attributes' => $listing['title'] ?? '',
                    'uniqueMarketplaceId' => $listing['tsin_id'] ?? '',
                    'apiResponseJson' => json_encode($listing, JSON_PRETTY_PRINT),
                    'published' => $listing['status'] === 'Buyable' ? true : false,
                    'sku' => $listing['sku'] ?? '',
                ],
                importFlag: $importFlag,
                updateFlag: $updateFlag,
                marketplace: $this->marketplace,
                parent: $parent
            );*/
            echo "OK\n";
            $index++;
        }    
    }

    public function downloadOrders()
    {

    }
    
    public function downloadInventory()
    {

    }

}