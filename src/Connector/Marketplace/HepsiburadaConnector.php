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
        $filename = 'tmp/' . urlencode($this->marketplace->getKey()) . '.json';
        if (!$forceDownload && file_exists($filename) && filemtime($filename) > time() - 86400) {
            $this->listings = json_decode(file_get_contents($filename), true);
            echo "Using cached data ";
        } else {
            $offset = 0;
            $limit = 20;
            $this->listings = [];
            //do {
                $response = $this->httpClient->request('GET', "https://listing-external.hepsiburada.com/listings/merchantid/{$this->marketplace->getHepsiburadaMerchantId()}", [
                    'headers' => [
                        'accept' => 'application/json'
                    ],
                    'query' => [
                        'offset' => $offset,
                        'limit' => $limit
                    ]
                ]);
                $statusCode = $response->getStatusCode();
                /*if ($statusCode !== 200) {
                    echo "Error: $statusCode\n";
                    break;
                }*/
                $data = $response->toArray();
                $products = $data['listings'];
                $this->listings = array_merge($this->listings, $products);
                echo "Page: " . $offset . " " . count($this->listings) . " ";
                $offset += $limit;
                print_r($listing);
            //} while (count($this->listings) < $totalItems);
            file_put_contents($filename, json_encode($this->listings));
        }
        return count($this->listings);
    }

    public function import($updateFlag, $importFlag)
    {
        
    }

    public function downloadOrders()
    {

    }
    
    public function downloadInventory()
    {

    }

}