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
    private static $scopeList = [
        'https://api.ebay.com/oauth/api_scope',
        'https://api.ebay.com/oauth/api_scope/sell.marketing.readonly',
        'https://api.ebay.com/oauth/api_scope/sell.marketing',
        'https://api.ebay.com/oauth/api_scope/sell.inventory.readonly',
        'https://api.ebay.com/oauth/api_scope/sell.inventory',
        'https://api.ebay.com/oauth/api_scope/sell.account.readonly',
        'https://api.ebay.com/oauth/api_scope/sell.account',
        'https://api.ebay.com/oauth/api_scope/sell.fulfillment.readonly',
        'https://api.ebay.com/oauth/api_scope/sell.fulfillment',
        'https://api.ebay.com/oauth/api_scope/sell.analytics.readonly',
        'https://api.ebay.com/oauth/api_scope/sell.finances',
        'https://api.ebay.com/oauth/api_scope/sell.payment.dispute',
        'https://api.ebay.com/oauth/api_scope/commerce.identity.readonly',
        'https://api.ebay.com/oauth/api_scope/sell.reputation',
        'https://api.ebay.com/oauth/api_scope/sell.reputation.readonly',
        'https://api.ebay.com/oauth/api_scope/commerce.notification.subscription',
        'https://api.ebay.com/oauth/api_scope/commerce.notification.subscription.readonly',
        'https://api.ebay.com/oauth/api_scope/sell.stores',
        'https://api.ebay.com/oauth/api_scope/sell.stores.readonly',
        'https://api.ebay.com/oauth/api_scope'
    ];
    
    public static $marketplaceType = 'Ebay';

    protected function prepareToken()
    {
        if (!Utility::checkJwtTokenValidity($this->marketplace->getEbayAccessToken())) {
            $scopeString = urlencode(implode(' ', self::$scopeList));

            $response = $this->httpClient->request('POST', static::$apiUrl['loginTokenUrl'], [
                'headers' => [
                    'Authorization' => 'Basic ' . base64_encode("{$this->marketplace->getEbayClientId()}:{$this->marketplace->getEbayClientSecret()}"),
                    'Content-Type' => 'application/x-www-form-urlencoded',
                    'Accept' => 'application/json'
                ],
                'body' => http_build_query([
                    'grant_type' => 'client_credentials',
                    'scope' => $scopeString
                ])
            ]);
            print_r($response->getContent());
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