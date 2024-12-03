<?php

namespace App\Connector\Marketplace;

use Pimcore\Model\DataObject\VariantProduct;
use App\Utils\Utility;
use Symfony\Component\HttpClient\HttpClient;

class WayfairConnector extends MarketplaceConnectorAbstract
{
    private static $apiUrl = [
        'oauth' => 'https://sso.auth.wayfair.com/oauth/token'
    ];
    public static $marketplaceType = 'Wayfair';
    public static $expires_in;

    public function prepareToken()
    {
        try {
            $response = $this->httpClient->request('POST', static::$apiUrl['oauth'],[
                'headers' => [
                    'content-type' => 'application/json'
                ],
                'json' => [
                    'grant_type' => 'client_credentials',
                    'client_id' => $this->marketplace->getWayfairClientId(),
                    'client_secret' => $this->marketplace->getWayfairSecretKey(),
                    'audience' => 'https://sandbox.api.wayfair.com/'
                ]
            ]);
            if ($response->getStatusCode() !== 200) {
                throw new \Exception('Failed to get token: ' . $response->getContent(false));
            }
            $data = $response->toArray();
            static::$expires_in = time() + $data['expires_in'];
            $this->marketplace->setWayfairAccessToken($data['access_token']);
            $this->marketplace->save();
        } catch(\Exception $e) {
            echo 'Error: ' . $e->getMessage();
        }
    }

    public function download($forceDownload = false)
    {
        if (!isset(static::$expires_in) || time() >= static::$expires_in) {
            $this->prepareToken();
        }
        echo "Token is valid. Proceeding with download...\n";
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