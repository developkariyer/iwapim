<?php

namespace App\Connector\Marketplace;

use Pimcore\Model\DataObject\VariantProduct;
use App\Utils\Utility;
use Symfony\Component\HttpClient\HttpClient;

class TakealotConnector extends MarketplaceConnectorAbstract
{
    private static $apiUrl = [
        'offers' => "https://seller-api.takealot.com/v2/offers/",
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
                $statusCode = $response->getStatusCode();
                if ($statusCode !== 200) {
                    echo "Error: $statusCode\n";
                    break;
                }
                $data = $response->toArray();
                $products = $data['offers'];
                $this->listings = array_merge($this->listings, $products);
                echo "Page: " . $page . " ";
                $page++;
                echo ".";
                sleep(1);  
            } while ($data['total_results'] === $size);
            file_put_contents($filename, json_encode($this->listings));
        }
        return count($this->listings);
    }

    public function createUrlLink()
    {
        foreach ($this->listings as $listing) {
            $url = $listing['offer_url'];
            $title = $listing['title'];
            $titleParts = explode('-', $title);
            foreach ($titleParts as $key => $part) {
                $titleParts[$key] = trim($part);
            }
            if (isset($titleParts[1])) {
                $colour_variant = $titleParts[1];
            }
            if (isset($titleParts[2])) {
                $size = $titleParts[2];
            }

            $newUrl = $url . "?";
            if ($colour_variant !== "") {
                $colour_variant = trim($colour_variant);
                $newUrl .= "colour_variant=".$colour_variant;
            }
            if ($size !== "" and $colour_variant !== "") {
                $size = trim($size);
                $size = str_replace(' ', '+', $size);
                $newUrl .= "&size=".$size;
            }
            if ($size !== "" and $colour_variant === "") {
                $size = trim($size);
                $size = str_replace(' ', '+', $size);
                $newUrl .= "size=".$size;
            }
            echo $title."\n";
            echo $url."\n";
            echo $newUrl."\n";
        
            /*$colour_variant="";
            $size  ="";

            $newUrl = $url . "?colour_variant=".$colour_variant."&size=".$size;
            echo $url."\n";
            echo $newUrl."\n";*/
        
        }


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
        $this->createUrlLink();        
    }



}