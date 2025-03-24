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
use Pimcore\Db;

class EbayConnector extends MarketplaceConnectorAbstract
{
    private static array $apiUrl = [
        'loginTokenUrl' => "https://api.ebay.com/identity/v1/oauth2/token",
    ];

    public static string $marketplaceType = 'Ebay';

    private static $expiresIn = 0;

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
        } catch (Exception $e) {
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
        } catch (Exception $e) {
            echo "Unknown Error: " . $e->getMessage() . "\n";
        }
        $responseArray  = $response->toArray();
        $accessToken    = $responseArray['access_token'];
        static::$expiresIn = $responseArray['expires_in'];
        $this->marketplace->setEbayAccessToken($accessToken);
        $this->marketplace->save();
    }

    /**
     * @throws TransportExceptionInterface
     * @throws ServerExceptionInterface
     * @throws RedirectionExceptionInterface
     * @throws ClientExceptionInterface
     */
    public function listingDetail($itemId): void
    {
        $url = "https://api.ebay.com/ws/api.dll";
        $accessToken = $this->marketplace->getEbayAccessToken();
        $headers = [
            "X-EBAY-API-COMPATIBILITY-LEVEL: 1395",
            "X-EBAY-API-CALL-NAME: GetItem",
            "X-EBAY-API-SITEID: 0",
            "Content-Type: text/xml"
        ];
        $xmlRequest = '<?xml version="1.0" encoding="utf-8"?>
                <GetItemRequest xmlns="urn:ebay:apis:eBLBaseComponents">
                 <RequesterCredentials>
                    <eBayAuthToken>' . $accessToken . '</eBayAuthToken>
                  </RequesterCredentials>
                  <ItemID>' . $itemId . '</ItemID>
                   <IncludeItemCompatibilityList>true</IncludeItemCompatibilityList>
                  <IncludeItemSpecifics>true</IncludeItemSpecifics>
                  <IncludeVariations>true</IncludeVariations>
                  <ErrorLanguage>en_US</ErrorLanguage>
                </GetItemRequest>';
        $response = $this->httpClient->request('POST', $url, [
            'headers' => $headers,
            'body' => $xmlRequest
        ]);
        $xmlContent = $response->getContent();
        $xmlObject = simplexml_load_string($xmlContent);
        $jsonResponse = json_encode($xmlObject);
        print_r($jsonResponse);
    }

    public function getItemRest($itemId)
    {
        $url = "https://api.ebay.com/buy/browse/v1/item/" . $itemId;
        try {
            $response = $this->httpClient->request('GET', $url, [
                'headers' => [
                    'Authorization' => 'Bearer ' . $this->marketplace->getEbayAccessToken(),
                    'Content-Type'  => 'application/json',
                ]
            ]);
            print_r($response->getContent());

        } catch (Exception $e) {
            echo "Error Type: " . $e->getMessage() . "\n";
        }

        print_r($response->getContent());
    }

    public function getItemByLegacyId($itemId)
    {
        $url = "https://api.ebay.com/buy/browse/v1/item/get_item_by_legacy_id";
        try {
            $response = $this->httpClient->request('GET', $url, [
                'headers' => [
                    'Authorization' => 'Bearer ' . $this->marketplace->getEbayAccessToken(),
                    'Content-Type'  => 'application/json',
                ],
                'query' => [
                    'legacy_item_id'  => $itemId,
                    'fieldgroups' => 'PRODUCT'
                ]
            ]);
            echo "HTTP Status Code: " . $response->getStatusCode() . "\n";
            print_r(json_decode($response->getContent(false), true));

        } catch (Exception $e) {
            echo "HTTP Status Code: " . $e->getStatusCode() . "\n";
            echo "Error Code: " . $e->getErrorCode() . "\n";
            echo "Error Type: " . $e->getMessage() . "\n";
        }

    }

    /**
     * @throws Exception|TransportExceptionInterface
     */
    public function download(bool $forceDownload = false): void
    {
        //$this->getItemRest("334936877779");
        //$this->listingDetail("334936877779");
        //$this->getMyeBaySelling();
        //$this->getItemByLegacyId("334936877779");


        $this->refreshToAccessToken();
        $this->fetchItemAspects("EBAY_US");


        // control expiresIn
       /* $this->refreshToAccessToken();
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
        $this->putListingsToCache();*/
    }

    public function getSellerList()
    {
        $headers = [
            "X-EBAY-API-COMPATIBILITY-LEVEL: 1349",
            "X-EBAY-API-CALL-NAME: GetSellerList",
            "X-EBAY-API-SITEID: 0",
            "Content-Type: text/xml"
        ];
        $accessToken = $this->marketplace->getEbayAccessToken();
        $url = "https://api.ebay.com/ws/api.dll";
        $xmlRequest = '<?xml version="1.0" encoding="utf-8"?>
            <GetSellerListRequest xmlns="urn:ebay:apis:eBLBaseComponents">
                <RequesterCredentials>
                    <eBayAuthToken>' . $accessToken . '</eBayAuthToken>
                </RequesterCredentials>
                <StartTimeFrom>2024-11-01T00:00:00.000Z</StartTimeFrom>
            <StartTimeTo>2025-02-24T23:59:59.000Z</StartTimeTo>
                <IncludeVariations>true</IncludeVariations>
                <IncludeWatchCount>true</IncludeWatchCount>
                <GranularityLevel>Coarse</GranularityLevel>
                <DetailLevel>ReturnAll</DetailLevel>
                <WarningLevel>High</WarningLevel>
                <Pagination>
                    <EntriesPerPage>200</EntriesPerPage>
                    <PageNumber>1</PageNumber>
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
            print_r($jsonResponse);
        } catch (Exception $e) {
            echo 'Hata: ' . $e->getMessage();
        }

    }

    public function getMyeBaySelling()
    {
        $url = "https://api.ebay.com/ws/api.dll";
        $accessToken = $this->marketplace->getEbayAccessToken();
        $headers = [
            "X-EBAY-API-COMPATIBILITY-LEVEL: 1349",
            "X-EBAY-API-CALL-NAME: GetMyeBaySelling",
            "X-EBAY-API-SITEID: 0",
            "Content-Type: text/xml"
        ];
        $xmlRequest = '<?xml version="1.0" encoding="utf-8"?>
<GetMyeBaySellingRequest xmlns="urn:ebay:apis:eBLBaseComponents">
  <RequesterCredentials>
                    <eBayAuthToken>' . $accessToken . '</eBayAuthToken>
                  </RequesterCredentials>
  <ActiveList> ItemListCustomizationType
    <Include>true</Include>
    <IncludeNotes>true</IncludeNotes>
  </ActiveList>
  <HideVariations>false</HideVariations>
  <ScheduledList> ItemListCustomizationType
    <Include>true</Include>
    <IncludeNotes>true</IncludeNotes>
  </ScheduledList>
  <SellingSummary> ItemListCustomizationType
    <Include>true</Include>
  </SellingSummary>
  <SoldList> ItemListCustomizationType
    <Include>true</Include>
    <IncludeNotes>true</IncludeNotes>
  </SoldList>
  <UnsoldList> ItemListCustomizationType
    <Include>true</Include>
    <IncludeNotes>true</IncludeNotes>
  </UnsoldList>
  <DetailLevel>ReturnAll</DetailLevel>
</GetMyeBaySellingRequest>';
        $response = $this->httpClient->request('POST', $url, [
            'headers' => $headers,
            'body' => $xmlRequest
        ]);
        $xmlContent = $response->getContent();
        $xmlObject = simplexml_load_string($xmlContent);
        $jsonResponse = json_encode($xmlObject);
        print_r($jsonResponse);

    }

    public function fetchItemAspects($categoryTreeIdName)
    {
        $categoryTree = $this->getDefaultCategoryTreeId($categoryTreeIdName);
        $categoryTreeId = $categoryTree['categoryTreeId'];
        $categoryTreeVersion = $categoryTree['categoryTreeVersion'];
        $url = "https://api.ebay.com/commerce/taxonomy/v1/category_tree/" . $categoryTreeId . "/fetch_item_aspects";
        try {
            $response = $this->httpClient->request('GET', $url, [
                'headers' => [
                    'Authorization' => 'Bearer ' . $this->marketplace->getEbayAccessToken(),
                    'Content-Type'  => 'application/json',
                ],
                'query' => [
                    'marketplace_id' => $categoryTreeIdName
                ]
            ]);
            $gzipContent = $response->getContent();
            $jsonContent = gzdecode($gzipContent);
            $dataArray = json_decode($jsonContent, true);
            if (isset($dataArray)) {
                foreach ($dataArray['categoryAspects'] as $categoryAspect) {
                    $categoryId = $categoryAspect['category']['categoryId'];
                    $categoryName = $categoryAspect['category']['categoryName'];
                    $categorySql = "INSERT INTO  iwa_ebay_category(category_id, category_name, category_tree_id, category_tree_version, category_tree_name)
                            VALUES (:categoryId, :categoryName, :categoryTreeId, :categoryTreeVersion, :categoryTreeName)";
                    $params = [
                        'categoryId' => $categoryId,
                        'categoryName' => $categoryName,
                        'categoryTreeId' => $categoryTreeId,
                        'categoryTreeVersion' => $categoryTreeVersion,
                        'categoryTreeName' => $categoryTreeIdName
                    ];
                    Utility::executeSql($categorySql, $params);
                    foreach ($categoryAspect['aspects'] as $item) {
                        $categoryAspectSql  = " 
                            INSERT INTO iwa_ebay_category_aspects (category_id, aspect_name, aspect_data_type, aspect_cardinality, aspect_mode, aspect_required, aspect_usage,
                                                                   aspect_enabled_variation, aspect_applicable_to)
                            VALUES (:category_id, :aspect_name, :aspect_data_type, :aspect_cardinality, :aspect_mode, :aspect_required, :aspect_usage,
                                    :aspect_enabled_variation, :aspect_applicable_to)";
                        $params = [
                            'category_id' => $categoryId,
                            'aspect_name' => $item['localizedAspectName'] ?? '',
                            'aspect_data_type' => $item['aspectConstraint']['aspectDataType'] ?? '',
                            'aspect_cardinality' => $item['aspectConstraint']['itemToAspectCardinality'] ?? '',
                            'aspect_mode' => $item['aspectConstraint']['aspectMode'] ?? '',
                            'aspect_required' => $item['aspectConstraint']['aspectRequired'] ?? '',
                            'aspect_usage' => $item['aspectConstraint']['aspectUsage'] ?? '',
                            'aspect_enabled_variation' => $item['aspectConstraint']['aspectEnabledForVariations'] ?? '',
                            'aspect_applicable_to' => json_encode($item['aspectConstraint']['aspectApplicableTo'] ?? '')
                        ];
                        Utility::executeSql($categoryAspectSql, $params);
                        $db = Db::get();
                        $aspectId = $db->lastInsertId();
                        foreach ($item['aspectValues'] as $values) {
                            $valueSql = "INSERT INTO iwa_ebay_category_aspects_values (aspect_id, value) VALUES (:aspect_id, :value)";
                            $params = [
                                'aspect_id' => $aspectId,
                                'value' => $values['localizedValue']
                            ];
                            Utility::executeSql($valueSql, $params);
                        }
                    }
                    echo ".";
                }
            }
        } catch (Exception $e) {
            echo 'Error: ' . $e->getMessage();
        }
    }

    public function getFulFillmentsPolicy()
    {
        $url = "https://api.ebay.com/sell/account/v1/fulfillment_policy";
        try {
            $response = $this->httpClient->request('GET', $url, [
                'headers' => [
                    'Authorization' => 'Bearer ' . $this->marketplace->getEbayAccessToken(),
                    'Content-Type'  => 'application/json',
                    'Content-Language' => 'en-US'
                ],
                'query' => [
                    'marketplace_id' => 'EBAY_US'
                ]
            ]);
            print_r($response->getContent());
        } catch (Exception $e) {
            echo 'Hata: ' . $e->getMessage();
        }

    }

    public function getDefaultCategoryTreeId($marketplaceId)
    {
        //eBay UK (ebay.co.uk)	EBAY_GB
        //eBay US (ebay.com)	EBAY_US
        //eBay Motors US (ebay.com/motors)	EBAY_MOTORS_US
        $url = "https://api.ebay.com/commerce/taxonomy/v1/get_default_category_tree_id";
        try {
            $response = $this->httpClient->request('GET', $url, [
                'headers' => [
                    'Authorization' => 'Bearer ' . $this->marketplace->getEbayAccessToken(),
                    'Content-Type'  => 'application/json',
                ],
                'query' => [
                    'marketplace_id' => $marketplaceId
                ]
            ]);
            if ($response->getStatusCode() == 200) {
                return $response->toArray();
            }
        } catch (Exception $e) {
            echo 'Hata: ' . $e->getMessage();
        }
    }

    public function downloadInventory(): void
    {
        //$this->refreshToAccessToken();
        $url = "https://api.ebay.com/sell/inventory/v1/inventory_item";
        try {
            $response = $this->httpClient->request('GET', $url, [
                'headers' => [
                    'Authorization' => 'Bearer ' . $this->marketplace->getEbayAccessToken(),
                    'Content-Type'  => 'application/json',
                ]
            ]);
            print_r($response->getContent());
        } catch (Exception $e) {
            echo 'Hata: ' . $e->getMessage();
        }
    }

    /**
     * @throws TransportExceptionInterface
     * @throws ServerExceptionInterface
     * @throws RedirectionExceptionInterface
     * @throws ClientExceptionInterface
     */
    public function downloadOrders(): void
    {
        $this->refreshToAccessToken();
        $url = "https://api.ebay.com/sell/fulfillment/v1/order";
        try {
            $sqlLastUpdatedAt = "
                    SELECT COALESCE(MAX(json_extract(json, '$.lastModifiedDate')), '2000-01-01T00:00:00Z') AS lastUpdatedAt
                    FROM iwa_marketplace_orders
                    WHERE marketplace_id = :marketplace_id
                    LIMIT 1;";
            $result = Utility::fetchFromSql($sqlLastUpdatedAt, [
                'marketplace_id' => $this->marketplace->getId()
            ]);
            $lastUpdatedAt = $result[0]['lastUpdatedAt'];
        } catch (\Exception $e) {
            echo "Error: " . $e->getMessage() . "\n";
        }
        echo "Last Updated At: $lastUpdatedAt\n";
        $offset = 0;
        $limit = 200;
        $orderCount = 0;
        do{
            $response = $this->httpClient->request('GET', $url, [
                'headers' => [
                    'Authorization' => 'Bearer ' . $this->marketplace->getEbayAccessToken(),
                    'Content-Type'  => 'application/json',
                ],
                'query' => [
                    'limit'  => $limit,
                    'offset' => $offset,
                    'filter' =>  ['creationDate:['  . $lastUpdatedAt . ']' ]
                ]
            ]);
            $statusCode = $response->getStatusCode();
            if ($statusCode !== 200) {
                echo "Error: $statusCode\n";
                break;
            }
            try {
                $data = $response->toArray();
                $orders = $data['orders'];
                $orderCount += count($orders);
                foreach ($orders as $order) {
                    $sqlInsertMarketplaceOrder = "
                            INSERT INTO iwa_marketplace_orders (marketplace_id, order_id, json)
                            VALUES (:marketplace_id, :order_id, :json) ON DUPLICATE KEY UPDATE json = VALUES(json)";
                    Utility::executeSql($sqlInsertMarketplaceOrder, [
                        'marketplace_id' => $this->marketplace->getId(),
                        'order_id' => $order['orderId'],
                        'json' => json_encode($order)
                    ]);
                }
                $totalElements = $data['total'];
                $count = count($orders);
                $offset += $limit;
                echo "-----------------------------\n";
                echo "Total Elements: $totalElements\n";
                echo "Items on this page: $count\n";
                echo "-----------------------------\n";
            } catch (\Exception $e) {
                echo "Error: " . $e->getMessage() . "\n";
            }
        }while($orderCount < $totalElements);
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
            echo $mainListing['ListingDetails']['ViewItemURL'] . "\n";
            foreach ($variations as $listing) {
                $imageUrl = $mainListing['PictureDetails']['PictureURL'][0] ?? '';
                 VariantProduct::addUpdateVariant(
                     variant: [
                         'imageUrl' => Utility::getCachedImage(is_array($imageUrl) ? $imageUrl[0] : $imageUrl) ?? '',
                         'urlLink' => $this->getUrlLink($mainListing['ListingDetails']['ViewItemURL']),
                         'salePrice' => $mainListing['SellingStatus']['CurrentPrice'] ?? '',
                         'saleCurrency' => $mainListing['Currency'],
                         'attributes' => $this->getAttributes($listing),
                         'title' => $mainListing['Title'] ?? '',
                         'quantity' => $mainListing['Quantity'] ?? 0,
                         'uniqueMarketplaceId' => $mainListing['ItemID'] ,
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
        }
    }

    public function setInventory(VariantProduct $listing, int $targetValue, $sku = null, $country = null): void
    {

    }

    public function setPrice(VariantProduct $listing,string $targetPrice, $targetCurrency = null, $sku = null, $country = null): void
    {

    }

    /**
     * @throws RedirectionExceptionInterface
     * @throws ClientExceptionInterface
     */
    public function searchProduct(string $searchText, int $page = 1, int $limit = 10): array
    {
        $url = "https://api.ebay.com/buy/browse/v1/item_summary/search";
        try {
            $response = $this->httpClient->request('GET', $url, [
                'headers' => [
                    'Authorization' => 'Bearer ' . $this->marketplace->getEbayAccessToken(),
                    'Content-Type'  => 'application/json',
                ],
                'query' => [
                    'q'     => $searchText,
                    'limit' => $limit,
                    'offset'=> ($page - 1) * $limit
                ]
            ]);
            return json_decode($response->getContent(false), true);
        } catch (ClientExceptionInterface|ServerExceptionInterface|RedirectionExceptionInterface|TransportExceptionInterface|Exception $e) {
            echo "Error: " . $e->getMessage() . "\n";
            return [];
        }
    }

    public function downloadReturns(): void
    {
        // TODO: Implement downloadReturns() method.
    }
}