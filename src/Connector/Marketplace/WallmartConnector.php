<?php

namespace App\Connector\Marketplace;

use Pimcore\Model\DataObject\VariantProduct;
use App\Utils\Utility;
use Symfony\Component\HttpClient\HttpClient;

class WallmartConnector extends MarketplaceConnectorAbstract
{
    private static $apiUrl = [
        'loginTokenUrl' => "https://api-gateway.walmart.com/v3/token"
    ];
    public static $marketplaceType = 'Wallmart';

    function generateCorrelationId() 
    {
        $randomHex = bin2hex(random_bytes(4));
        return substr($randomHex, 0, 4) . '-' . substr($randomHex, 4, 4);
    }

    public function prepareToken()
    {
        $correlationId = $this->generateCorrelationId();
        echo "Generated Correlation ID: " . $correlationId . "\n";
        $response = $this->httpClient->request('POST', static::$apiUrl['loginTokenUrl'], [
            'headers' => [
                'Authorization' => 'Basic ' . base64_encode("{$this->marketplace->getWallmartClientId()}:{$this->marketplace->getWallmartSecretKey()}"),
                'WM_QOS.CORRELATION_ID' => $this->generateCorrelationId(),
                'Accept' => 'application/json'
            ],
            'body' => http_build_query([
                'grant_type' => 'client_credentials'
            ])
        ]);
        print_r($response->getContent());
    }

    public function download($forceDownload = false)
    {
        $this->prepareToken();
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