<?php

namespace App\Connector\Marketplace;

use Pimcore\Model\DataObject\VariantProduct;
use App\Utils\Utility;
use Symfony\Component\HttpClient\HttpClient;

class TakealotConnector extends MarketplaceConnectorAbstract
{
    private static $apiUrl = [
        'offers' => "https://seller-api.takealot.com/api/v2/offers/",
    ];
    
    public static $marketplaceType = 'Takealot';
    
    public function download($forceDownload = false)
    {
        $filename = 'tmp/' . urlencode($this->marketplace->getKey()) . '.json';
        if (!$forceDownload && file_exists($filename) && filemtime($filename) > time() - 86400) {
            $this->listings = json_decode(file_get_contents($filename), true);
            echo "Using cached data ";
        } else {
            $page = 1;
            $size = 100;
            $this->listings = [];
            do {
                $response = $this->httpClient->request('GET', static::$apiUrl['offers'], [
                    'headers' => [
                        'Authorization' =>' Key ' . $this->marketplace->getTakealotKey()
                    ],
                    'query' => [
                        'page_number' => $page,
                        'page_size' => $size
                    ]
                ]);
                print_r($response->getContent());
                $statusCode = $response->getStatusCode();
                if ($statusCode !== 200) {
                    echo "Error: $statusCode\n";
                    break;
                }
                $data = $response->toArray();
                break; 
                $products = $data['content'];
                $this->listings = array_merge($this->listings, $products);
                $page++;
                echo ".";
                sleep(1);  
            } while ($page['page_size'] <= $size);
            //file_put_contents($filename, json_encode($this->listings));
        }
        return count($this->listings);
    }

    public function downloadInventory()
    {

    }

    public function downloadOrders()
    {
    }
    
    protected function getImage($listing, $mainListing) 
    {
        
    }

    public function import($updateFlag, $importFlag)
    {
        
    }



}