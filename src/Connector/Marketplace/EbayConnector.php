<?php

namespace App\Connector\Marketplace;

use Exception;
use Pimcore\Model\DataObject\VariantProduct;
use Symfony\Contracts\HttpClient\Exception\ClientExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\RedirectionExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\ServerExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;

class EbayConnector extends MarketplaceConnectorAbstract
{
    private static array $apiUrl = [
        'loginTokenUrl' => "https://api.ebay.com/identity/v1/oauth2/token"
    ];

    public static string $marketplaceType = 'Ebay';

    /**
     * @throws TransportExceptionInterface
     * @throws ServerExceptionInterface
     * @throws RedirectionExceptionInterface
     * @throws ClientExceptionInterface
     * @throws Exception
     */
    protected function getConsentRequest(): void
    {
        $response = $this->httpClient->request('POST', self::$apiUrl['loginTokenUrl'], [
            'headers' => [
                'Content-Type' => 'application/x-www-form-urlencoded',
                'Authorization' => 'Basic ' . base64_encode("{$this->marketplace->getEbayClientId()}:{$this->marketplace->getEbayClientSecret()}")
            ],
            'form_params' => [
                'grant_type' => 'authorization_code',
                'code' => $this->marketplace->getEbayAuthCode(),
                'redirect_uri' => $this->marketplace->getEbayRuName()
            ]
        ]);
        print_r($response->getStatusCode());
        print_r($response->getContent());
        /*$response = $this->httpClient->request('GET', static::$apiUrl['loginTokenUrl'], [
            'headers' => [
                'client_id' => $this->marketplace->getEbayClientId(),
                'redirect_uri' => $this->marketplace->getRedirectUri(),
                'response_type' => 'code',
                'scope' => "https://api.ebay.com/oauth/api_scope https://api.ebay.com/oauth/api_scope/sell.marketing.readonly https://api.ebay.com/oauth/api_scope/sell.marketing https://api.ebay.com/oauth/api_scope/sell.inventory.readonly https://api.ebay.com/oauth/api_scope/sell.inventory https://api.ebay.com/oauth/api_scope/sell.account.readonly https://api.ebay.com/oauth/api_scope/sell.account https://api.ebay.com/oauth/api_scope/sell.fulfillment.readonly https://api.ebay.com/oauth/api_scope/sell.fulfillment https://api.ebay.com/oauth/api_scope/sell.analytics.readonly https://api.ebay.com/oauth/api_scope/sell.finances https://api.ebay.com/oauth/api_scope/sell.payment.dispute https://api.ebay.com/oauth/api_scope/commerce.identity.readonly https://api.ebay.com/oauth/api_scope/sell.reputation https://api.ebay.com/oauth/api_scope/sell.reputation.readonly https://api.ebay.com/oauth/api_scope/commerce.notification.subscription https://api.ebay.com/oauth/api_scope/commerce.notification.subscription.readonly https://api.ebay.com/oauth/api_scope/sell.stores https://api.ebay.com/oauth/api_scope/sell.stores.readonly"
            ]
        ]);
        if ($response->getStatusCode() !== 200) {
            throw new Exception('Failed Ebay login');
        }
        print_r($response->getContent());*/
    }


    /**
     * @throws Exception|TransportExceptionInterface
     */
    public function download(bool $forceDownload = false): void
    {
     //   $this->prepareToken();
        $this->getConsentRequest();
    }

    public function downloadInventory(): void
    {

    }

    public function downloadOrders(): void
    {
    }
    
    protected function getImage($listing, $mainListing) 
    {
        
    }

    public function import($updateFlag, $importFlag): void
    {
        
    }

    public function setInventory(VariantProduct $listing, int $targetValue, $sku = null, $country = null): void
    {

    }

    public function setPrice(VariantProduct $listing,string $targetPrice, $targetCurrency = null, $sku = null, $country = null): void
    {

    }

}