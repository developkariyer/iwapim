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
        'loginTokenUrl' => "https://api.ebay.com/identity/v1/oauth2/token",
        'authorizeUrl' => "https://auth.ebay.com/oauth2/authorize",
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
        /*try {
            $response = $this->httpClient->request('POST', self::$apiUrl['loginTokenUrl'], [
                'headers' => [
                    'Content-Type' => 'application/x-www-form-urlencoded',
                    'Authorization' => 'Basic ' . base64_encode("{$this->marketplace->getEbayClientId()}:{$this->marketplace->getEbayClientSecret()}")
                ],
                'data' => [
                    'grant_type' => 'authorization_code',
                    'code' => $this->marketplace->getEbayAuthCode(),
                    'redirect_uri' => $this->marketplace->getEbayRuName()
                ]
            ]);

            echo "HTTP Status Code: " . $response->getStatusCode() . "\n";
            echo "Response: " . $response->getContent() . "\n";

        } catch (\Exception $e) {
            echo "Hata: " . $e->getMessage() . "\n";
            echo "Hata Kodu: " . $e->getCode() . "\n";
        }*/

        $authorizeUrl = "https://auth.sandbox.ebay.com/oauth2/authorize?" . http_build_query([
                "client_id" => $this->marketplace->getEbayClientId(),
                "redirect_uri" => $this->marketplace->getEbayRuName(),
                "response_type" => "code",
                "scope" => "https://api.ebay.com/oauth/api_scope https://api.ebay.com/oauth/api_scope/sell.inventory",
            ]);

        header("Location: " . $authorizeUrl);
        exit;
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