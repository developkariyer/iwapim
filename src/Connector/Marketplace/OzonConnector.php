<?php

namespace App\Connector\Marketplace;

use Pimcore\Model\DataObject\VariantProduct;
use App\Utils\Utility;

class OzonConnector extends MarketplaceConnectorAbstract
{
    public static $marketplaceType = 'Ozon';

    public function getApiResponse($method = 'GET', $url, $query = [], $data = [])
    {
        $options = [
            'headers' => [
                'Client-Id' => $this->marketplace->getOzonClientId(),
                'Api-Key' => $this->marketplace->getOzonApiKey(),
                'Content-Type' => 'application/json'
            ],
        ];
        if (!empty($query)) {
            $options['query'] = $query;
        }
        if (!empty($data)) {
            $options['json'] = $data;
        }
        try {
            $response = $this->httpClient->request($method, $url, $options);
            $statusCode = $response->getStatusCode();
            if ($statusCode !== 200) {
                echo "Error: ".json_encode($response->toArray())."\n";
            }
            return $response->toArray();
        } catch (\Symfony\Contracts\HttpClient\Exception\ClientExceptionInterface $e) {
            $response = $e->getResponse();
            $content = $response->getContent(false) ?? null;
            echo "Error response from API: $content\n";
            return json_decode($content, true) ?: [];
        } catch (\Exception $e) {
            echo "Unexpected error: " . $e->getMessage() . "\n" . $e->getCode() . "\n";
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
                'POST',
                "https://api-seller.ozon.ru/v2/product/list", 
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
            print_r($product);
            $detail = $this->getApiResponse(
                'POST',
                "https://api-seller.ozon.ru/v2/product/info",
                [],
                [
                    'product_id' => $product['product_id'],
                    'offer_id' => $product['offer_id'],
                    'sku' => $product['sku'] ?? 0,
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

    public function getCategoryTree()
    {
        // $sc = new \App\Connector\Marketplace\OzonConnector(\Pimcore\Model\DataObject\Marketplace::getById(268776)); $sc->getCategoryTree();
        $response = $this->getApiResponse('GET', "https://api-seller.ozon.ru/v2/category/tree", [], ['language' => 'TR']);
        Utility::setCustomCache('CATEGORY_TREE.json', PIMCORE_PROJECT_ROOT. "/tmp/marketplaces/".urlencode($this->marketplace->getKey()), json_encode($response['result'], JSON_PRETTY_PRINT));
        return $response['result'];
    }

}
