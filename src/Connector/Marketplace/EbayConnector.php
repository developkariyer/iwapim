<?php

namespace App\Connector\Marketplace;

use App\Utils\Utility;
use Exception;
use Pimcore\Model\DataObject\VariantProduct;
use Pimcore\Model\Element\DuplicateFullPathException;
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
        } catch (\Symfony\Contracts\HttpClient\Exception\HttpExceptionInterface $e) {
            echo "Error: " . $e->getMessage() . "\n";
            echo "Error Code: " . $e->getCode() . "\n";
            echo "Response Error: " . $e->getResponse()->getContent(false) . "\n";
        } catch (\Exception $e) {
            echo "Unknown Error: " . $e->getMessage() . "\n";
        }
        $responseArray  = $response->toArray();
        $accessToken    = $responseArray['access_token'];
        static::$expiresIn = $responseArray['expires_in'];
        $this->marketplace->setEbayAccessToken($accessToken);
        $this->marketplace->save();
    }

    /**
     * @throws Exception|TransportExceptionInterface
     */
    public function download(bool $forceDownload = false): void
    {
        // control expiresIn
        //$this->refreshToAccessToken();
        if (!$forceDownload && $this->getListingsFromCache()) {
            echo "Using cached listings\n";
            return;
        }
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
                  <DetailLevel>ReturnAll</DetailLevel> 
                  <StartTimeFrom>' . $startTime . '</StartTimeFrom>
                  <StartTimeTo>' . $endTime . '</StartTimeTo>
                  <IncludeWatchCount>true</IncludeWatchCount>
                  <IncludeVariations>true</IncludeVariations>
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
                if (isset($responseObject->ItemArray->Item)) {
                    foreach ($responseObject->ItemArray->Item as $item) {
                        $this->listings[] = $item;
                    }
                }
                echo "Start Time: " . $startTime . " End Time: " . $endTime . "\n";
                echo "Total Count: " . count($this->listings) . "\n";
                $startDate = $startDate + $interval;
            } catch (\Exception $e) {
                echo 'Hata: ' . $e->getMessage();
                break;
            }
        } while ($startDate < $currentDate);
        $this->listings = json_decode(json_encode($this->listings), true);
        $this->putListingsToCache();
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
        $url = "https://api.ebay.com/sell/fulfillment/v1/order";
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
        print_r($response->getContent());
    }
    
    protected function getImage($listing, $mainListing) 
    {
        
    }

    private function getAttributes($listing): string
    {
        $attributes = "";
        if (isset($listing['VariationSpecifics']['NameValueList'])) {
            $nameValueList = $listing['VariationSpecifics']['NameValueList'];
            if (!is_array($nameValueList) || isset($nameValueList['Name'])) {
                $nameValueList = [$nameValueList];
            }
            foreach ($nameValueList as $item) {
                if (isset($item['Value'])) {
                    $value = is_array($item['Value']) ? implode(', ', $item['Value']) : $item['Value'];
                    $attributes .= $value . " ";
                }
            }
        }
        return trim($attributes);
    }


    /**
     * @throws DuplicateFullPathException
     * @throws Exception
     */
    public function import($updateFlag, $importFlag): void
    {
        if (empty($this->listings)) {
            echo "Nothing to import\n";
        }
        $marketplaceFolder = Utility::checkSetPath(
            Utility::sanitizeVariable($this->marketplace->getKey(), 190),
            Utility::checkSetPath('Pazaryerleri')
        );
        $total = count($this->listings);
        $index = 0;
        foreach ($this->listings as $mainListing) {
            echo "($index/$total) Processing Listing {$mainListing['ItemID']}:{$mainListing['Title']} ...";
            $parent = Utility::checkSetPath(
                Utility::sanitizeVariable($mainListing['PrimaryCategory']['CategoryName'] ?? 'Tasnif-Edilmemiş'),
                $marketplaceFolder
            );
            if (!empty($mainListing['Title'])) {
                $parent = Utility::checkSetPath(
                    Utility::sanitizeVariable($mainListing['Title']),
                    $parent
                );
            }
            if (($mainListing['SellingStatus']['ListingStatus'] ?? 'Active') !== 'Active') {
                $parent = Utility::checkSetPath(
                    Utility::sanitizeVariable('_Pasif'),
                    $marketplaceFolder
                );
            }
            $parentResponseJson = $mainListing;
            if (isset($parentResponseJson['Variations'])) {
                unset($parentResponseJson['Variations']);
            }
            $count = 0;
            $variations = $mainListing['Variations']['Variation'] ?? [null];
            foreach ($variations as $listing) {
                VariantProduct::addUpdateVariant(
                    variant: [
                        'imageUrl' => Utility::getCachedImage($mainListing['PictureDetails']['PictureURL'][0]) ?? '',
                        'urlLink' => $this->getUrlLink($mainListing['ListingDetails']['ViewItemURL']),
                        'salePrice' => $mainListing['SellingStatus']['CurrentPrice'] ?? '',
                        'saleCurrency' => $mainListing['Currency'],
                        'attributes' => $this->getAttributes($listing),
                        'title' => $mainListing['Title'] ?? '',
                        'quantity' => $mainListing['Quantity'] ?? 0,
                        'uniqueMarketplaceId' => $mainListing['ItemID'] . $count ?? '',
                        'apiResponseJson' => json_encode($listing),
                        'parentResponseJson' => json_encode($parentResponseJson),
                        'published' => ($mainListing['SellingStatus']['ListingStatus'] ?? 'Active') === 'Active',
                        'sku' => $listing['SKU'] ?? '',
                        //'ean' => $listing['VariationProductListingDetails']['UPC'] ?? '',
                    ],
                    importFlag: $importFlag,
                    updateFlag: $updateFlag,
                    marketplace: $this->marketplace,
                    parent: $parent
                );
                echo "v";
                $count++;
            }
            echo "OK\n";
            $index++;
            /*echo "MainID: " . $mainListing['ItemID'] . "\n";
            echo "Title: " . $mainListing['Title'] . "\n";
            echo "Product Type: " . $mainListing['PrimaryCategory']['CategoryName'] . "\n";
            echo "Listing Status: " . $mainListing['SellingStatus']['ListingStatus'] . "\n";
            if (isset($mainListing['PictureDetails']['PictureURL'])) {
                echo "PictureURL: " . $mainListing['PictureDetails']['PictureURL'][0] . "\n";
            }
            if (isset($mainListing['ListingDetails']['ViewItemURL'])) {
                echo "ViewItemURL: " . $mainListing['ListingDetails']['ViewItemURL'] . "\n";
            }
            if (isset($mainListing['SellingStatus'])) {
                echo "CurrentPrice: " . $mainListing['SellingStatus']['CurrentPrice'] . "\n";
                echo "Published: " . $mainListing['SellingStatus']['ListingStatus'] . "\n";
            }
            echo "Currency: " . $mainListing['Currency'] . "\n";
            echo "Quantity: " . $mainListing['Quantity'] . "\n";
            if (isset($mainListing['Variations'])) {
                foreach ($mainListing['Variations']['Variation'] as $listing) {
                    if (isset($listing['SKU'])) {
                        echo "SKU: " . $listing['SKU'] . "\n";
                    }
                    if (isset($listing['VariationProductListingDetails']['UPC'])) {
                        echo "UPC: " . $listing['VariationProductListingDetails']['UPC'] . "\n";
                    }

                    echo "Attributes: " . $this->getAttributes($listing) . "\n";
                    echo "---------------------------------------------------------------------------------------------------------------\n";
                }
            }
            echo "---------------------------------------------------------------------------------------------------------------\n";*/
        }
    }

    public function setInventory(VariantProduct $listing, int $targetValue, $sku = null, $country = null): void
    {

    }

    public function setPrice(VariantProduct $listing,string $targetPrice, $targetCurrency = null, $sku = null, $country = null): void
    {

    }

}