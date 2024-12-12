<?php

namespace App\Connector\Marketplace;

use Pimcore\Model\DataObject\VariantProduct;
use App\Utils\Utility;
use Symfony\Component\HttpClient\HttpClient;
use Symfony\Contracts\HttpClient\Exception\ClientExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\RedirectionExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\ServerExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;

class WayfairConnector extends MarketplaceConnectorAbstract
{
    private static array $apiUrl = [
        'oauth' => 'https://sso.auth.wayfair.com/oauth/token',
        'sandbox' => 'https://sandbox.api.wayfair.com/v1/graphql',
        'catalog' => 'https://api.wayfair.io/v1/supplier-catalog-api/graphql'
    ];
    public static $marketplaceType = 'Wayfair';
    public static $expires_in;

    public function prepareTokenSanbox()
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

    public function prepareTokenProd()
    {
        try {
            $response = $this->httpClient->request('POST', static::$apiUrl['oauth'],[
                'headers' => [
                    'content-type' => 'application/json'
                ],
                'json' => [
                    'grant_type' => 'client_credentials',
                    'client_id' => $this->marketplace->getWayfairClientIdProd(),
                    'client_secret' => $this->marketplace->getWayfairSecretKeyProd(),
                    'audience' => 'https://api.wayfair.com/'
                ]
            ]);
            if ($response->getStatusCode() !== 200) {
                throw new \Exception('Failed to get token: ' . $response->getContent(false));
            }
            $data = $response->toArray();
            static::$expires_in = time() + $data['expires_in'];
            $this->marketplace->setWayfairAccessTokenProd($data['access_token']);
            $this->marketplace->save();
        } catch(\Exception $e) {
            echo 'Error: ' . $e->getMessage();
        }
    }

    /*public function sandboxTestings()
    {
        if (!isset(static::$expires_in) || time() >= static::$expires_in) {
            $this->prepareTokenSanbox();
        }
        $this->acceptDropshipOrdersSandbox();
        $this->testEndpoint();
        $this->getDropshipOrdersSandbox();  
        $this->sendShipmentSandbox();
        $this->saveInventorySandbox();
        $this->getListingSandbox();
    }*/

    public function download($forceDownload = false)
    {
        if (!isset(static::$expires_in) || time() >= static::$expires_in) {
            $this->prepareTokenProd();
        }
        echo "Token is valid. Proceeding with download...\n";
        $query = <<<GRAPHQL
        query supplierCatalog(
            \$supplierId: Int!,
            \$paginationOptions: PaginationOptions
        ) {
            supplierCatalog(
                supplierId: \$supplierId,
                paginationOptions: \$paginationOptions
            ) {
                pageInfo {
                    page
                    pageSize
                }
            }
        }
        GRAPHQL;
        $variables = [
            'supplierId' => 194115, 
            'paginationOptions' => [
                'page' => 1,  
                'pageSize' => 10
            ],
        ];
        $response = $this->httpClient->request('POST',static::$apiUrl['catalog'], [
            'headers' => [
                'Authorization' => 'Bearer ' . $this->marketplace->getWayfairAccessTokenProd(),
                'Content-Type' => 'application/json'
            ],
            'json' => [
                'query' => $query,
                'variables' => $variables
            ]
        ]);
        if ($response->getStatusCode() !== 200) {
            throw new \Exception('Failed to get orders: ' . $response->getContent(false));
        }
        print_r($response->getContent());
    }

    public function testEndpoint()
    {
        $response = $this->httpClient->request('GET', 'https://sandbox.api.wayfair.com/v1/demo/clock',[
            'headers' => [
                'Authorization' => 'Bearer ' . $this->marketplace->getWayfairAccessToken(),
                'Content-Type' => 'application/json'
            ]
        ]);
        if ($response->getStatusCode() !== 200) {
            throw new \Exception('Failed to test endpoint: ' . $response->getContent(false));
        }
        print_r($response->getContent());
    }

    public function getListingSandbox()
    {
        $query = <<<GRAPHQL
        query supplierCatalog(
            \$supplierId: Int!,
            \$paginationOptions: PaginationOptions
        ) {
            supplierCatalog(
                supplierId: \$supplierId,
                paginationOptions: \$paginationOptions
            ) {
                supplierId
                pageInfo {
                    currentPage
                    totalPages
                    totalRecords
                }
                products {
                    id
                    name
                    description
                    price
                    availability
                }
            }
        }
        GRAPHQL;

        $variables = [
            'supplierId' => 194115, 
            'paginationOptions' => [
                'page' => 1,  
                'pageSize' => 10
            ],
        ];
        $response = $this->httpClient->request('POST',static::$apiUrl['catalog'], [
            'headers' => [
                'Authorization' => 'Bearer ' . $this->marketplace->getWayfairAccessToken(),
                'Content-Type' => 'application/json'
            ],
            'json' => [
                'query' => $query,
                'variables' => $variables
            ]
        ]);
        if ($response->getStatusCode() !== 200) {
            throw new \Exception('Failed to get orders: ' . $response->getContent(false));
        }
        print_r($response->getContent());
    }

    public function saveInventorySandbox()
    {
        $query = <<<GRAPHQL
        mutation inventory(\$inventory: [inventoryInput]!) {
            inventory {
                save(
                    inventory: \$inventory,
                    feed_kind: TRUE_UP
                ) {
                    handle,
                    submittedAt,
                    errors {
                        key,
                        message
                    }
                }
            }
        }
        GRAPHQL;
        $supplierPartNumbers  = [
            'AHM61GRAY',
            'AHM61KAZ',
            'TUANA2XL',
            'TUANALARGE',
            'TUANAMED',
            'TUANAXL',
            'AHM68',
            'AHM89ROUNDPASTEL',
            'AHM90KAREPASTEL',
            'AHM91 C SEHPA BLACK',
            'AHM91 C SEHPA BROWN',
            'AHM91 C SEHPA WHITE',
            'AHM68RAVEN',
            'AHM69EARTHCORE',
            'AHM69BABYLON',
            'AHM69SILVERSTONE',
            'AHM69SANDSTONE',
            'AHM69SHADOWSTONE',
            'AHM69GEMSTONE',
            'AHM69VERMILION',
            'SRIYANTRA',
            'MANDALALARGEBLACK',
            'MANDALALARGECOPPER',
            'MANDALALARGEGOLD',
            'MANDALALARGESILVER',
            'MANDALAMEDIUMBLACK',
            'MANDALAMEDIUMCOPPER',
            'MANDALAMEDIUMGOLD',
            'MANDALAMEDIUMSILVER',
            'MANDALASMALLBLACK',
            'MANDALASMALLCOPPER',
            'MANDALASMALLGOLD',
            'MANDALASMALLSILVER',
            'DOGHOUSERCTNGLSYH',
            'DOGHOUSEMDUMBLCK',
            'DOGHOUSEMDUMWHTE',
            'DOGHOUSESMLLWHTE',
            'IM210MOONANDSTAR',
            'IM7IGOSAKSHINY',
            'IM7IGOSAKSHINYXL',
            'IM210MOONSTARMDM',
            'IM210MOONSTARLRG',
            'BASMALA3SZ4CLRLB',
            'BASMALA3SZ4CLRLC',
            'BASMALA3SZ4CLRLG',
            'BASMALA3SZ4CLRLS',
            'BASMALA3SZ4CLRMB',
            'BASMALA3SZ4CLRMC',
            'BASMALA3SZ4CLRMG',
            'BASMALA3SZ4CLRMS',
            'BASMALA3SZ4CLRSB',
            'BASMALA3SZ4CLRSC',
            'BASMALA3SZ4CLRSG',
            'BASMALA3SZ4CLRSS',
            '2S2CLRMTLAK3STRLB',
            '2S2CLRMTLAK3STRLG',
            '2S2CLRMTLAK3STRMB',
            '2S2CLRMTLAK3STRMG',
            'TAWHID2S4CLRLGB',
            'TAWHID2S4CLRLGC',
            'TAWHID2S4CLRLGG',
            'TAWHID2S4CLRLGS',
            'TAWHID2S4CLRMDB',
            'TAWHID2S4CLRMDC',
            'TAWHID2S4CLRMDG',
            'TAWHID2S4CLRMDS',
            '2S4CMASHLLHTBLAB',
            '2S4CMASHLLHTBLAC',
            '2S4CMASHLLHTBLAG',
            '2S4CMASHLLHTBLAS',
            '2S4CMASHLLHTBMDB',
            '2S4CMASHLLHTBMDC',
            '2S4CMASHLLHTBMDG',
            '2S4CMASHLLHTBMDS',
            'WLNTNGHTSTND',
            'MONTESRI4BAY',
            '2SIZEWLLMNTDESK',
            '4PARAKMED',
            'NEWMAPBETULMED',
            'WLLMNTDESK114X61',
            'WLNTNGHTSTNDMEDIUM',
            'WOODEN4PARAKLARGE',
            'WOODEN4PARAKXL',
            'NEWMAPBETUL2XL',
            'NEWMAPBETULXL',
            'NEWMAPBETULARGE',
            'NEWMAPLBROWNXL',
            'NEWMAPLBROWLARGE',
            'NEWMAPLBROWNMED',
            'NEWMAPLBROWN2XL',
            '4KULKUFICINGOLDS70',
            'AKFLKNASWHITEGOLDS',
            'PALESTINEMAPUV47',
            'PALESTINEWOODEN40',
            '4DIAMONDBLACKGLASS',
            '4KULKUFICINGOLDL120',
            '4KULKUFICINGOLDM90',
            '4KULKUFICINGOLDXL150',
            '4DIAMONDWHITEGLASS',
            'PALESTINEMAPUV67',
            'PALESTINEMAPUV90',
            'PALESTINEWOODEN60',
            'PALESTINEWOODEN90',
            'AKFLKNASBLACKGOLDSMALL',
            'AKFLKNASWHITEGOLDL',
            'AKFLKNASWHITEGOLDM',
            'AKFLKNASWHITEIGOSL',
            'AKFLKNASWHITEIGOSM',
            'AKFLKNASBLACKGOLDLARGE',
            'AKFLKNASBLACKGOLDMEDIUM',
            'AKFLKNASBLACKIGOSLARGE',
            'AKFLKNASBLACKIGOSMEDIUM',
            'AKFLKNASBLACKIGOSSMALL',
            'AKFLKNASWHITEIGOSS',
            'IQRABOOKENDGOLD',
            'WOODENTRAYTABLEBROWN',
            'WOODENTRAYTABLEBETUL',
            'WOODENTRAYTABLETUANA',
            'AHM61CREAM',
            'EPOXYTABLE116X63',
            'AHM91CSEHPA_SETOF2BROWN',
            'AHM91CSEHPA_SETOF2BLACK',
            'AHM91CSEHPA_SETOF2WHITE',
            'EPOXYTABLE116X76',
            'KAYINCHAIR_BLACK',
            'IQRABOOKENDBLACK',
            '1234567001',
            '1234567002',
            'AHM260001',
            'CM410002',
            'CM410003',
            'CM410004',
            'CM410005',
            'AHM-5BLUE',
            'AHM-5GREEN',
            'AHM-4',
            'AHM2CHESNUTS',
            'AHM-3 BLACK',
            'CHOCOLATEUPUL1006',
            'LBROWNUPUL1006',
            'UPUL1015LARGE',
            'UPUL1015MEDIUM',
            'NTABLESBLACK',
            'AHM260002BLACK',
            'AHM260002BROWN',
            'AHM260002NATURAL',
            'AHM45-Smallsize',
            'AHM45-Medsize',
            'AHM47WHITE',
            'AHM47BLACK',
            'AHM64-WALNUT-SMALL',
            'AHM64-WALNUT-MED',
            'AHM8-MDF BROWN',
            'AHM8-MDF WHITE',
            'AHM-31'
        ];
        $inventory = [];
        foreach ($supplierPartNumbers as $partNumber) {
            $inventory[] = [
                'supplierId' => 194115,
                'supplierPartNumber' => $partNumber,
                'quantityOnHand' => 5,
                'quantityBackordered' => 10,
                'quantityOnOrder' => 2,
                'itemNextAvailabilityDate' => '2024-12-03T00:00:00+00:00',
                'discontinued' => false,
                'productNameAndOptions' => 'My Awesome Product',
            ];
        }
        $variables = [
            'inventory' => $inventory
        ];
        $response = $this->httpClient->request('POST',static::$apiUrl['orders'], [
            'headers' => [
                'Authorization' => 'Bearer ' . $this->marketplace->getWayfairAccessToken(),
                'Content-Type' => 'application/json'
            ],
            'json' => ['query' => $query,
            'variables' => $variables]
        ]);

        if ($response->getStatusCode() !== 200) {
            throw new \Exception('Failed to get orders: ' . $response->getContent(false));
        }
        print_r($response->getContent());
    }

    public function sendShipmentSandbox()
    {
        $query =  $query = <<<GRAPHQL
        mutation shipment(\$notice: ShipNoticeInput!) {
            purchaseOrders {
                shipment(notice: \$notice) {
                    handle,
                    submittedAt,
                    errors {
                        key,
                        message
                    }
                }
            }
        }
        GRAPHQL;
        $variables = [
            'notice' => [
                'poNumber' => 'TEST_23082207',
                'supplierId' => 194115,
                'packageCount' => 1,
                'weight' => 184,
                'volume' => 22986.958176,
                'carrierCode' => 'FDEG',
                'shipSpeed' => 'GROUND',
                'trackingNumber' => '210123456789',
                'shipDate' => '2024-12-03 08:53:33.000000 +00:00',
                'sourceAddress' => [
                    'name' => 'John Smith',
                    'streetAddress1' => '123 Test Street',
                    'streetAddress2' => '# 2',
                    'city' => 'Boston',
                    'state' => 'MA',
                    'postalCode' => '02116',
                    'country' => 'US',
                ],
                'destinationAddress' => [
                    'name' => 'John Smith',
                    'streetAddress1' => '123 Test Street',
                    'streetAddress2' => '# 2',
                    'city' => 'Boston',
                    'state' => 'MA',
                    'postalCode' => '02116',
                    'country' => 'USA',
                ],
                'largeParcelShipments' => [
                    [
                        'partNumber' => '4KULKUFICINGOLDS70',
                        'packages' => [
                            [
                                'code' => [
                                    'type' => 'TRACKING_NUMBER',
                                    'value' => '210123456781',
                                ],
                                'weight' => 150,
                            ],
                        ],
                    ]
                ]
            ],
        ];
        $response = $this->httpClient->request('POST',static::$apiUrl['orders'], [
            'headers' => [
                'Authorization' => 'Bearer ' . $this->marketplace->getWayfairAccessToken(),
                'Content-Type' => 'application/json'
            ],
            'json' => [
                'query' => $query,
                'variables' => $variables
            ]
        ]);

        if ($response->getStatusCode() !== 200) {
            throw new \Exception('Failed to get orders: ' . $response->getContent(false));
        }
        print_r($response->getContent());
    }

    public function acceptDropshipOrdersSandbox()
    {
        $query = <<<GRAPHQL
        mutation acceptOrder(\$poNumber: String!, \$shipSpeed: ShipSpeed!, \$lineItems: [AcceptedLineItemInput!]!) {
            purchaseOrders {
                accept(
                    poNumber: \$poNumber,
                    shipSpeed: \$shipSpeed,
                    lineItems: \$lineItems
                ) {
                    handle,
                    submittedAt,
                    errors {
                        key,
                        message
                    }
                }
            }
        }
        GRAPHQL;
        $variables = [
            'poNumber' => 'TEST_23082207',
            'shipSpeed' => 'GROUND',
            'lineItems' => [
                [
                    'partNumber' => '4KULKUFICINGOLDS70',
                    'quantity' => 1,
                    'unitPrice' => 9.99,
                    'estimatedShipDate' => '2024-12-05 08:53:33.000000 +00:00',
                ]
            ],
        ];
        $response = $this->httpClient->request('POST',static::$apiUrl['orders'], [
            'headers' => [
                'Authorization' => 'Bearer ' . $this->marketplace->getWayfairAccessToken(),
                'Content-Type' => 'application/json'
            ],
            'json' => [
                'query' => $query,
                'variables' => $variables
            ]
        ]);

        if ($response->getStatusCode() !== 200) {
            throw new \Exception('Failed to get orders: ' . $response->getContent(false));
        }
        print_r($response->getContent());
    }

    public function getDropshipOrdersSandbox()
    {
        $query = <<<GRAPHQL
        query getDropshipPurchaseOrders {
            getDropshipPurchaseOrders(
                limit: 500,
                hasResponse: false,
                sortOrder: DESC
            ) {
                poNumber,
                poDate,
                estimatedShipDate,
                customerName,
                customerAddress1,
                customerAddress2,
                customerCity,
                customerState,
                customerPostalCode,
                orderType,
                shippingInfo {
                    shipSpeed,
                    carrierCode
                },
                packingSlipUrl,
                warehouse {
                    id,
                    name
                },
                products {
                    partNumber,
                    quantity,
                    price,
                    event {
                        startDate,
                        endDate
                    }
                }
            }
        }
        GRAPHQL;
        $response = $this->httpClient->request('POST',static::$apiUrl['orders'], [
            'headers' => [
                'Authorization' => 'Bearer ' . $this->marketplace->getWayfairAccessToken(),
                'Content-Type' => 'application/json'
            ],
            'json' => ['query' => $query]
        ]);

        if ($response->getStatusCode() !== 200) {
            throw new \Exception('Failed to get orders: ' . $response->getContent(false));
        }
        print_r($response->getContent());
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

    public function setInventory(VariantProduct $listing, int $targetValue, $sku = null, $country = null)
    {

    }

    public function setPrice(VariantProduct $listing,string $targetPrice, $targetCurrency = null, $sku = null, $country = null)
    {

    }

}