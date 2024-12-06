<?php

namespace App\Connector\Marketplace;

use Pimcore\Model\DataObject\VariantProduct;
use App\Utils\Utility;

class OzonConnector extends MarketplaceConnectorAbstract
{
    public static $marketplaceType = 'Ozon';

    public function getApiResponse($url, $method = 'GET', $query = [])
    {
        try {
            $response = $this->httpClient->request($method, $url, [
                'headers' => [
                    'Client-Id' => $this->marketplace->getOzonClientId(),
                    'Api-Key' => $this->marketplace->getOzonApiKey(),
                    'Content-Type' => 'application/json'
                ],
                'query' => $query
            ]);
            $statusCode = $response->getStatusCode();
            if ($statusCode !== 200) {
                throw new \Exception("Error: ".json_encode($response->toArray()));
            }
            return $response->toArray();
        } catch (\Exception $e) {
            echo "Error: ".$e->getMessage()."\n".$e->getCode()."\n";
            return [];
        }
    }

    /**
        GET / HTTP/1.1
        Host: api-seller.ozon.ru
        Client-Id: <Client-Id>
        Api-Key: <Api-Key>
        Content-Type: application/json
     */
    public function download($forceDownload = false)
    {
        $this->listings = json_decode(Utility::getCustomCache('LISTINGS.json', PIMCORE_PROJECT_ROOT. "/tmp/marketplaces/".urlencode($this->marketplace->getKey())), true);
        if (!(empty($this->listings) || $forceDownload)) {
            echo "Using cached listings\n";
            return;
        }
        $items = [];
        $this->listings = [];
        $limit = 1000;
        $lastId = null;
        do {
            $response = $this->getApiResponse(
                "https://api-seller.ozon.ru/v2/product/list", 
                'POST',
                $lastId ? ['last_id' => $lastId] : ['limit' => $limit]
            );
            $items = array_merge($items, $response['result']['items']);
            $lastId = $response['result']['last_id'];
            $totalCount = $response['result']['total'];
            echo " $totalCount";
            if (count ($items) >= $totalCount) {
                break;
            }
        } while ($lastId !== null);
        foreach ($items as $product) {
            $detail = $this->getApiResponse(
                "https://api-seller.ozon.ru/v2/product/info",
                'POST',
                [
                    'product_id' => $product['product_id'],
                    //'offer_id' => $product['offer_id']
                ]
            );
            $product['detail'] = $detail;
            $this->listings[] = $product;
            echo ".";
        }
        echo "\n";
        Utility::setCustomCache('LISTINGS.json', PIMCORE_PROJECT_ROOT. "/tmp/marketplaces/".urlencode($this->marketplace->getKey()), json_encode($this->listings));
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
