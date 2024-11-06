<?php

namespace App\Connector\Marketplace;

use Pimcore\Model\DataObject\VariantProduct;
use App\Utils\Utility;
use Symfony\Component\HttpClient\HttpClient;
use Symfony\Component\HttpClient\ScopingHttpClient;

class EbayConnector extends MarketplaceConnectorAbstract
{
    private static $apiUrl = [
        'loginTokenUrl' => "https://api.ebay.com/identity/v1/oauth2/token"
    ];
    public static $marketplaceType = 'Ebay';

    protected function prepareToken()
    {
        if (!Utility::checkJwtTokenValidity($this->marketplace->getEbayAccessToken())) {
            $response = $this->httpClient->request('POST', static::$apiUrl['loginTokenUrl'], [
                'headers' => [
                    'Authorization' => 'Basic ' . base64_encode("{$this->marketplace->getEbayClientId()}:{$this->marketplace->getEbayClientSecret()}"),
                    'Accept' => 'application/json'
                ]
            ]);
            print_r($response);
            if ($response->getStatusCode() !== 200) {
                throw new \Exception('Failed Ebay login');
            }
            $decodedResponse = json_decode($response->getContent(), true);
            $this->marketplace->setEbayAccessToken($decodedResponse['access_token']);
            $this->marketplace->save();
            echo $decodedResponse;
        } 
        $this->httpClient = ScopingHttpClient::forBaseUri($this->httpClient, 'https://api.ebay.com/', [
            'headers' => [
                'Authorization' => 'Bearer ' . $this->marketplace->getEbayAccessToken(),
                'Accept' => 'application/json',
                'Content-Type' => 'application/json'
            ],
        ]);
    }

    public function download($forceDownload = false)
    {
        $this->prepareToken();
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