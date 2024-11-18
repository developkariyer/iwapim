<?php

namespace App\Connector\Marketplace;

use Pimcore\Model\DataObject\VariantProduct;
use App\Utils\Utility;
use Symfony\Component\HttpClient\HttpClient;
use Symfony\Component\HttpClient\ScopingHttpClient;

class EbayConnector extends MarketplaceConnectorAbstract
{
    private static $apiUrl = [
        'loginTokenUrl' => "https://auth.ebay.com/oauth2/authorize"
    ];
    private static $scopeList = [
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
        'https://api.ebay.com/oauth/api_scope/sell.stores.readonly'
    ];
    
    public static $marketplaceType = 'Ebay';

    protected function getConsentRequest()
    {
        $response = $this->httpClient->request('GET', static::$apiUrl['loginTokenUrl'], [
            'headers' => [
                'client_id' => $this->marketplace->getEbayClientId(),
                'redirect_uri' => $this->marketplace->getRedirectUri(),
                'response_type' => 'code',
                'scope' => "https://api.ebay.com/oauth/api_scope https://api.ebay.com/oauth/api_scope/sell.marketing.readonly https://api.ebay.com/oauth/api_scope/sell.marketing https://api.ebay.com/oauth/api_scope/sell.inventory.readonly https://api.ebay.com/oauth/api_scope/sell.inventory https://api.ebay.com/oauth/api_scope/sell.account.readonly https://api.ebay.com/oauth/api_scope/sell.account https://api.ebay.com/oauth/api_scope/sell.fulfillment.readonly https://api.ebay.com/oauth/api_scope/sell.fulfillment https://api.ebay.com/oauth/api_scope/sell.analytics.readonly https://api.ebay.com/oauth/api_scope/sell.finances https://api.ebay.com/oauth/api_scope/sell.payment.dispute https://api.ebay.com/oauth/api_scope/commerce.identity.readonly https://api.ebay.com/oauth/api_scope/sell.reputation https://api.ebay.com/oauth/api_scope/sell.reputation.readonly https://api.ebay.com/oauth/api_scope/commerce.notification.subscription https://api.ebay.com/oauth/api_scope/commerce.notification.subscription.readonly https://api.ebay.com/oauth/api_scope/sell.stores https://api.ebay.com/oauth/api_scope/sell.stores.readonly"
            ]
        ]);
        if ($response->getStatusCode() !== 200) {
            throw new \Exception('Failed Ebay login');
        }
        print_r($response->getContent());
    }
    
    
    public function download($forceDownload = false)
    {
     //   $this->prepareToken();
        $this->getConsentRequest();
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