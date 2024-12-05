<?php

namespace App\Connector\Marketplace;

use Pimcore\Model\DataObject\VariantProduct;
use App\Utils\Utility;

class OzonConnector extends MarketplaceConnectorAbstract
{
    public static $marketplaceType = 'Ozon';

    /**
        GET / HTTP/1.1
        Host: api-seller.ozon.ru
        Client-Id: <Client-Id>
        Api-Key: <Api-Key>
        Content-Type: application/json
     */
    public function download($forceDownload = false)
    {
        /*$this->listings = json_decode(Utility::getCustomCache('LISTINGS.json', PIMCORE_PROJECT_ROOT. "/tmp/marketplaces/".urlencode($this->marketplace->getKey())), true);
        if (!(empty($this->listings) || $forceDownload)) {
            echo "Using cached listings\n";
            return;
        }*/
        $this->listings = [];
        $limit = 1000;
        $response = $this->httpClient->request('POST', "https://api-seller.ozon.ru/v2/product/list", [
            'headers' => [
                'Client-Id' => $this->marketplace->getOzonClientId(),
                'Api-Key' => $this->marketplace->getOzonApiKey(),
                'Content-Type' => 'application/json'
            ],
            'query' => [
                'limit' => $limit
            ]
        ]);
        $statusCode = $response->getStatusCode();
        if ($statusCode !== 200) {
            echo "Error: $statusCode\n";
        }
        $data = $response->toArray();
        $products = $data['result']['items'];
        print_r($response);
        /*foreach ($products as &$product) {
            $detail = $this->httpClient->request('POST', "https://api-seller.ozon.ru/v2/product/info", [
                'headers' => [
                    'Client-Id' => $this->marketplace->getOzonClientId(),
                    'Api-Key' => $this->marketplace->getOzonApiKey(),
                    'Content-Type' => 'application/json'
                ],
                'json' => [
                    'product_id' => $product['product_id'],
                    'offer_id' => $product['offer_id']
                ]
            ]);
            $detailStatusCode = $detail->getStatusCode();
            if ($detailStatusCode !== 200) {
                echo "Error: $detailStatusCode\n";
                continue;
            }
            $detailData = $detail->toArray();
            $product['detail'] = $detailData;
        }
        $this->listings = array_merge($this->listings, $products);
        print_r($this->listings);
        Utility::setCustomCache('LISTINGS.json', PIMCORE_PROJECT_ROOT. "/tmp/marketplaces/".urlencode($this->marketplace->getKey()), json_encode($this->listings));*/
    }

    public function downloadInventory()
    {
        
    }

    public function downloadOrders()
    {

    }

    public function import($updateFlag, $importFlag)
    {

    }

}
