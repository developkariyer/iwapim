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
    ];

    public static string $marketplaceType = 'Ebay';

    private  static $expiresIn = 0;

    /**
     * @throws TransportExceptionInterface
     * @throws ServerExceptionInterface
     * @throws RedirectionExceptionInterface
     * @throws ClientExceptionInterface
     * @throws Exception
     */
    protected function codeToRefreshToken(): void // !! 11.02.2025 expires refresh token 1.5 year
    {
        try {
            $response = $this->httpClient->request('POST', self::$apiUrl['loginTokenUrl'], [
                'headers' => [
                    'Content-Type'  => 'application/x-www-form-urlencoded',
                    'Authorization' => 'Basic ' . base64_encode(
                            "{$this->marketplace->getEbayClientId()}:{$this->marketplace->getEbayClientSecret()}"
                        ),
                ],
                'body' => http_build_query([
                    'grant_type'    => 'authorization_code',
                    'code'          => urldecode($this->marketplace->getEbayAuthCode()),
                    'redirect_uri'  => $this->marketplace->getEbayRuName(),
                ]),
            ]);
            echo "HTTP Status Code: " . $response->getStatusCode() . "\n";
            echo "Response: " . $response->getContent() . "\n";
        } catch (\Symfony\Contracts\HttpClient\Exception\HttpExceptionInterface $e) {
            echo "Error: " . $e->getMessage() . "\n";
            echo "Error Code: " . $e->getCode() . "\n";
            echo "Response Error: " . $e->getResponse()->getContent(false) . "\n";
        } catch (\Exception $e) {
            echo "Unknown Error: " . $e->getMessage() . "\n";
        }
    }

    public function refreshToAccessToken(): void
    {
        $scope = "https://api.ebay.com/oauth/api_scope https://api.ebay.com/oauth/api_scope/sell.marketing.readonly https://api.ebay.com/oauth/api_scope/sell.marketing https://api.ebay.com/oauth/api_scope/sell.inventory.readonly https://api.ebay.com/oauth/api_scope/sell.inventory https://api.ebay.com/oauth/api_scope/sell.account.readonly https://api.ebay.com/oauth/api_scope/sell.account https://api.ebay.com/oauth/api_scope/sell.fulfillment.readonly https://api.ebay.com/oauth/api_scope/sell.fulfillment https://api.ebay.com/oauth/api_scope/sell.analytics.readonly https://api.ebay.com/oauth/api_scope/sell.finances https://api.ebay.com/oauth/api_scope/sell.payment.dispute https://api.ebay.com/oauth/api_scope/commerce.identity.readonly https://api.ebay.com/oauth/api_scope/sell.reputation https://api.ebay.com/oauth/api_scope/sell.reputation.readonly https://api.ebay.com/oauth/api_scope/commerce.notification.subscription https://api.ebay.com/oauth/api_scope/commerce.notification.subscription.readonly https://api.ebay.com/oauth/api_scope/sell.stores https://api.ebay.com/oauth/api_scope/sell.stores.readonly https://api.ebay.com/oauth/scope/sell.edelivery";
        try {
            $response = $this->httpClient->request('POST', self::$apiUrl['loginTokenUrl'], [
                'headers' => [
                    'Content-Type'  => 'application/x-www-form-urlencoded',
                    'Authorization' => 'Basic ' . base64_encode(
                            "{$this->marketplace->getEbayClientId()}:{$this->marketplace->getEbayClientSecret()}"
                        ),
                ],
                'body' => http_build_query([
                    'grant_type'    => 'refresh_token',
                    'refresh_token' => $this->marketplace->getEbayRefreshToken(),
                    'scope'  => $scope
                ]),
            ]);
            echo "HTTP Status Code: " . $response->getStatusCode() . "\n";
            echo "Response: " . $response->getContent() . "\n";
        } catch (\Symfony\Contracts\HttpClient\Exception\HttpExceptionInterface $e) {
            echo "Error: " . $e->getMessage() . "\n";
            echo "Error Code: " . $e->getCode() . "\n";
            echo "Response Error: " . $e->getResponse()->getContent(false) . "\n";
        } catch (\Exception $e) {
            echo "Unknown Error: " . $e->getMessage() . "\n";
        }
        $responseArray  = $response->toArray();
        $accessToken    = $responseArray['access_token'];
        print_r($accessToken);
        static::$expiresIn = $responseArray['expires_in'];
        print_r(static::$expiresIn);
        $this->marketplace->setEbayAccessToken($accessToken);
    }


    /**
     * @throws Exception|TransportExceptionInterface
     */
    public function download(bool $forceDownload = false): void
    {
        // control expiresIn
        //$this->refreshToAccessToken();
        $allData = [];
        $accessToken = $this->marketplace->getEbayAccessToken();
        $startDate = strtotime('2022-04-01');
        $currentDate = time();
        $interval = 120 * 24 * 60 * 60;
        $url = "https://api.ebay.com/ws/api.dll";
        $headers = [
            "X-EBAY-API-COMPATIBILITY-LEVEL: 1349",
            "X-EBAY-API-CALL-NAME: GetSellerList",
            "X-EBAY-API-SITEID: 0",
            "Content-Type: text/xml"
        ];
        do {
            $startTime = gmdate('Y-m-d\TH:i:s\Z', $startDate);
            $endTime = gmdate('Y-m-d\TH:i:s\Z', $startDate + $interval);
            $xmlRequest = '<?xml version="1.0" encoding="utf-8"?>
                <GetSellerListRequest xmlns="urn:ebay:apis:eBLBaseComponents">
                  <RequesterCredentials>
                    <eBayAuthToken>' . $accessToken . '</eBayAuthToken>
                  </RequesterCredentials>
                  <ErrorLanguage>en_US</ErrorLanguage>
                  <WarningLevel>High</WarningLevel>
                  <GranularityLevel>Coarse</GranularityLevel>
                  <StartTimeFrom>' . $startTime . '</StartTimeFrom>
                  <StartTimeTo>' . $endTime . '</StartTimeTo>
                  <IncludeWatchCount>true</IncludeWatchCount>
                  <IncludeVariations>true</IncludeVariations>
                  <SKUArray>true</SKUArray>
                  <Pagination>
                    <EntriesPerPage>200</EntriesPerPage>
                  </Pagination>
                </GetSellerListRequest>';

            try {
                $response = $this->httpClient->request('POST', $url, [
                    'headers' => $headers,
                    'body' => $xmlRequest
                ]);
                $xmlContent = $response->getContent();
                $xmlObject = simplexml_load_string($xmlContent);
                $jsonResponse = json_encode($xmlObject);
                $responseObject = json_decode($jsonResponse);
                if ($responseObject->Ack === 'Failure') {
                    echo "Error: " . $responseObject->Errors[0]->ShortMessage;
                    break;
                }
                foreach ($responseObject->ItemArray->Item as $item) {
                      $allData[] = $item;
                      print_r($item);
                }
                $startDate = $startDate + $interval;
            } catch (\Exception $e) {
                echo 'Hata: ' . $e->getMessage();
                break;
            }
        } while ($startDate < $currentDate);
        echo "\n\n\n\n\n\n";
        echo "------------------------------------------------------------------------------------------------\n";
        print_r(json_encode($allData));
        echo "------------------------------------------------------------------------------------------------\n";
    }

    public function downloadInventory(): void
    {


        /*$url = "https://api.ebay.com/sell/inventory/v1/inventory_item";
        try {
            $response = $this->httpClient->request('GET', $url, [
                'headers' => [
                    'Authorization' => 'Bearer ' . $this->marketplace->getEbayAccessToken(),
                    'Content-Type'  => 'application/json',
                ]
            ]);
            print_r($response);
            echo $response->getStatusCode();
            echo $response->getContent();
        } catch (\Exception $e) {
            echo 'Hata: ' . $e->getMessage();
        }*/
    }

    public function downloadOrders(): void
    {
       /* $url = "https://api.ebay.com/sell/fulfillment/v1/order";
        $response = $this->httpClient->request('GET', $url, [
            'headers' => [
                'Authorization' => 'Bearer ' . $this->marketplace->getEbayAccessToken(),
                'Content-Type'  => 'application/json',
            ],
            'query' => [
                'limit'  => 2,
                'offset' => 0
            ]
        ]);
        print_r($response->getContent());*/
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