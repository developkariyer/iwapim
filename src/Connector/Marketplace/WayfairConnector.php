<?php

namespace App\Connector\Marketplace;

use App\Utils\Utility;
use Exception;
use Pimcore\Db;
use Pimcore\Model\DataObject\VariantProduct;
use Symfony\Contracts\HttpClient\Exception\ClientExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\DecodingExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\RedirectionExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\ServerExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;

class WayfairConnector extends MarketplaceConnectorAbstract
{
    private static array $apiUrl = [
        'oauth' => 'https://sso.auth.wayfair.com/oauth/token',
        'catalog' => 'https://api.wayfair.com/v1/supplier-catalog-api/graphql',
        'url' => 'https://sandbox.api.wayfair.com/v1/graphql',
        'returnOrder' => 'https://api.wayfair.io/v1/supplier-order-api/graphql'
    ];
    public static string $marketplaceType = 'Wayfair';

    /**
     * @throws RedirectionExceptionInterface
     * @throws DecodingExceptionInterface
     * @throws ClientExceptionInterface
     * @throws TransportExceptionInterface
     * @throws ServerExceptionInterface
     */
    public function prepareToken(): void
    {
        if ($this->marketplace->getKey() == 'Wayfair') {
            return;
        }
        try {
            $response = $this->httpClient->request('POST', static::$apiUrl['oauth'],[
                'headers' => [
                    'content-type' => 'application/json',
                    'cache-control' => 'no-cache'
                ],
                'json' => [
                    'grant_type' => 'client_credentials',
                    'client_id' => $this->marketplace->getWayfairClientIdProd(),
                    'client_secret' => $this->marketplace->getWayfairSecretKeyProd(),
                    'audience' => 'https://sandbox.api.wayfair.com/'
                ]
            ]);
            if ($response->getStatusCode() !== 200) {
                throw new Exception('Failed to get token: ' . $response->getContent(false));
            }
            $data = $response->toArray();
            $this->marketplace->setWayfairAccessTokenProd($data['access_token']);
            $this->marketplace->save();
        } catch(Exception $e) {
            echo 'Error: ' . $e->getMessage();
        }
    }

    /**
     * @throws RedirectionExceptionInterface
     * @throws DecodingExceptionInterface
     * @throws ClientExceptionInterface
     * @throws TransportExceptionInterface
     * @throws ServerExceptionInterface
     */
    public function download(bool $forceDownload = false): void
    {
        /*echo "Downloading Wayfair...\n";
        $this->prepareToken();
        echo "Token is valid. Proceeding with download...\n";
        $query = <<<GRAPHQL
        query supplierCatalog(
            \$supplierId: Int!,
             \$paginationOptions: PaginationOptions
        ) {
            supplierCatalog(
                supplierId: \$supplierId
                paginationOptions: \$paginationOptions
            ) {
                supplierId
                products {
                    productId
                    upc
                }
            }
        }
        GRAPHQL;
        $variables = [
            'supplierId' => 194115,
            'paginationOptions' => [
                'page' => 1,
                'pageSize' => 10
            ]
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
        print_r($response->getStatusCode());
        print_r($response->getContent());*/
    }

    /**
     * @throws RedirectionExceptionInterface
     * @throws ClientExceptionInterface
     * @throws TransportExceptionInterface
     * @throws ServerExceptionInterface
     * @throws Exception|DecodingExceptionInterface
     */
    public function downloadOrders(): void
    {
        if ($this->marketplace->getKey() == 'Wayfair') {
            return;
        }
        //$this->prepareToken();
        //$this->queryOpenOrdersSandbox();
        //$this->getDropshipOrdersSandbox();
        //$this->acceptDropshipOrdersSandbox('TEST_75743408', '3SIRAAK94CMBLACK');
        $this->sendShipmentSandbox(233890, 'TEST_80769714', '3SIRAAK94CMBLACK');

//        try {
//            $sqlLastUpdatedAt = "
//                    SELECT DATE_FORMAT(
//                        GREATEST(
//                            IFNULL(
//                                MAX(STR_TO_DATE(JSON_UNQUOTE(JSON_EXTRACT(json, '$.poDate')), '%Y-%m-%d')),
//                                STR_TO_DATE(:default_date, '%Y-%m-%dT%H:%i:%sZ')
//                            ),
//                            STR_TO_DATE(:default_date, '%Y-%m-%dT%H:%i:%sZ')
//                        ),
//                        '%Y-%m-%dT00:00:00Z'
//                    ) AS lastUpdatedAt
//                    FROM iwa_marketplace_orders
//                    WHERE marketplace_id = :marketplace_id;
//                ";
//
//            $result = Utility::fetchFromSql($sqlLastUpdatedAt, [
//                'marketplace_id' => $this->marketplace->getId(),
//                'default_date' => '2024-05-01T00:00:00Z'
//            ]);
//            $lastUpdatedAt = $result[0]['lastUpdatedAt'];
//        } catch (\Exception $e) {
//            echo "Error: " . $e->getMessage() . "\n";
//        }
//        echo "Last update: " . $lastUpdatedAt . "\n";
//
//        $this->prepareToken();
//        $db = Db::get();
//        $limit = 200;
//        do {
//            $query = <<<GRAPHQL
//            query getDropshipPurchaseOrders {
//                getDropshipPurchaseOrders(
//                    limit: $limit,
//                    sortOrder: ASC,
//                    fromDate: "$lastUpdatedAt"
//                ) {
//                    poNumber,
//                    poDate,
//                    estimatedShipDate,
//                    customerName,
//                    customerAddress1,
//                    customerAddress2,
//                    customerCity,
//                    customerState,
//                    customerPostalCode,
//                    orderType,
//                    shippingInfo {
//                        shipSpeed,
//                        carrierCode
//                    },
//                    packingSlipUrl,
//                    warehouse {
//                        id,
//                        name
//                    },
//                    products {
//                        partNumber,
//                        quantity,
//                        price,
//                        isCancelled,
//                        event {
//                            startDate,
//                            endDate
//                        }
//                    }
//                }
//            }
//            GRAPHQL;
//            $response = $this->httpClient->request('POST',static::$apiUrl['url'], [
//                'headers' => [
//                    'Authorization' => 'Bearer ' . $this->marketplace->getWayfairAccessTokenProd(),
//                    'Content-Type' => 'application/json'
//                ],
//                'json' => ['query' => $query]
//            ]);
//            if ($response->getStatusCode() !== 200) {
//                throw new Exception('Failed to get orders: ' . $response->getContent(false));
//            }
//            try {
//                $data = $response->toArray();
//                $orders = $data['data']['getDropshipPurchaseOrders'];
//                $ordersCount = count($orders);
//                $lastDate = $orders[$ordersCount - 1]['poDate'];
//                $db->beginTransaction();
//                foreach ($orders as $order) {
//                    $db->executeStatement(
//                        "INSERT INTO iwa_marketplace_orders (marketplace_id, order_id, json) VALUES (?, ?, ?) ON DUPLICATE KEY UPDATE json = VALUES(json)",
//                        [
//                            $this->marketplace->getId(),
//                            $order['poNumber'],
//                            json_encode($order)
//                        ]
//                    );
//                }
//                $db->commit();
//            } catch (Exception $e) {
//                $db->rollBack();
//                echo "Error: " . $e->getMessage() . "\n";
//            }
//            echo "From date: $lastUpdatedAt\n";
//            echo "Orders downloaded: $ordersCount\n";
//            $lastUpdatedAt = $lastDate;
//        }while($ordersCount === $limit);
    }

    public function testEndpoint()
    {
        $response = $this->httpClient->request('GET', 'https://sandbox.api.wayfair.com/v1/demo/clock',[
            'headers' => [
                'Authorization' => 'Bearer ' . $this->marketplace->getWayfairAccessTokenProd(),
                'Content-Type' => 'application/json'
            ]
        ]);
        if ($response->getStatusCode() !== 200) {
            throw new \Exception('Failed to test endpoint: ' . $response->getContent(false));
        }
        print_r($response->getContent());
    }

    public function getDropshipOrdersSandbox()
    {
        $query = <<<GRAPHQL
        query getDropshipPurchaseOrders {
            getDropshipPurchaseOrders(
                limit: 500,
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
        $response = $this->httpClient->request('POST',static::$apiUrl['url'], [
            'headers' => [
                'Authorization' => 'Bearer ' . $this->marketplace->getWayfairAccessTokenProd(),
                'Content-Type' => 'application/json'
            ],
            'json' => ['query' => $query]
        ]);

        if ($response->getStatusCode() !== 200) {
            throw new \Exception('Failed to get orders: ' . $response->getContent(false));
        }
        print_r($response->getContent());
    }

    public function acceptDropshipOrdersSandbox($poNumber, $partNumber)
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
            'poNumber' => $poNumber,
            'shipSpeed' => 'GROUND',
            'lineItems' => [
                [
                    'partNumber' => $partNumber,
                    'quantity' => 1,
                    'unitPrice' => 9.99,
                    'estimatedShipDate' => '2025-07-26 05:01:13.000000 +00:00',
                ]
            ],
        ];
        $response = $this->httpClient->request('POST',static::$apiUrl['url'], [
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

    public function sendShipmentSandbox($supplierId, $poNumber, $partNumber, )
    {
        $query = <<<GRAPHQL
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
                'poNumber' => $poNumber,
                'supplierId' => $supplierId,
                'packageCount' => 1,
                'weight' => 184,
                'volume' => 22986.958176,
                'carrierCode' => 'FDEG',
                'shipSpeed' => 'GROUND',
                'trackingNumber' => '210123456789',
                'shipDate' => '2025-07-26 05:01:13.000000 +00:00',
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
                        'partNumber' => $partNumber,
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
        $response = $this->httpClient->request('POST',static::$apiUrl['url'], [
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

    public function downloadReturns(): void
    {
        $sql = "SELECT * FROM `iwa_marketplace_orders_line_items` WHERE marketplace_type = 'Wayfair' and is_canceled = 'cancelled'";
        $returnOrders = Utility::fetchFromSql($sql, []);
        foreach ($returnOrders as $return) {
            $sqlInsertMarketplaceReturn = "
                            INSERT INTO iwa_marketplace_returns (marketplace_id, return_id, json) 
                            VALUES (:marketplace_id, :return_id, :json) ON DUPLICATE KEY UPDATE json = VALUES(json)";
            Utility::executeSql($sqlInsertMarketplaceReturn, [
                'marketplace_id' => $this->marketplace->getId(),
                'return_id' => $return['order_id'],
                'json' => json_encode($return)
            ]);
            echo "Inserting order: " . $return['order_id'] . "\n";
        }
    }

    public function import($updateFlag, $importFlag): void
    {

    }

    public function downloadInventory(): void
    {

    }

    public function setInventory(VariantProduct $listing, int $targetValue, $sku = null, $country = null): void
    {

    }

    public function setPrice(VariantProduct $listing,string $targetPrice, $targetCurrency = null, $sku = null, $country = null): void
    {

    }

}