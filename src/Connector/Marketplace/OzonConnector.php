<?php

namespace App\Connector\Marketplace;

use Pimcore\Model\DataObject\VariantProduct;
use App\Utils\Utility;

class OzonConnector extends MarketplaceConnectorAbstract
{
    public static $marketplaceType = 'Ozon';

    public function getApiResponse($url, $method = 'GET', $query = [])
    {
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
            throw new \Exception("Error: $statusCode\n");
        }
        return $response->toArray();
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
        $this->listings = [];
        $limit = 1000;
        $lastId = null;
        do {
            $response = $this->getApiResponse(
                "https://api-seller.ozon.ru/v2/product/list", 
                'POST',
                $lastId ? ['last_id' => $lastId] : ['limit' => $limit]
            );
            $this->listings = array_merge($this->listings, $response['result']['items']);
            $lastId = $response['result']['last_id'];
        } while ($lastId !== null);
        foreach ($this->listings as &$product) {
            $detail = $this->getApiResponse(
                "https://api-seller.ozon.ru/v2/product/info",
                'POST',
                [
                    'product_id' => $product['product_id'],
                    'offer_id' => $product['offer_id']
                ]
            );
            $product['detail'] = $detail;
        }
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
