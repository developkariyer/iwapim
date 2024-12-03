<?php

namespace App\Connector\Marketplace;

use Pimcore\Model\DataObject\VariantProduct;
use App\Utils\Utility;
use Symfony\Component\HttpClient\HttpClient;

class WayfairConnector extends MarketplaceConnectorAbstract
{
    private static $apiUrl = [
        'oauth' => 'https://sso.auth.wayfair.com/oauth/token',
        'orders' => 'https://sandbox.api.wayfair.com/v1/graphql',
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
        $this->acceptDropshipOrdersSandbox();
        //$this->testEndpoint();
        //$this->getDropshipOrdersSandbox();  
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
                limit: 10,
                hasResponse: true,
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
   
}