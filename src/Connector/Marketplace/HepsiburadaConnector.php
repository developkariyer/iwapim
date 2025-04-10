<?php

namespace App\Connector\Marketplace;

use App\Model\DataObject\VariantProduct;
use App\Utils\Utility;
use Exception;
use Pimcore\Model\Element\DuplicateFullPathException;
use Random\RandomException;
use Symfony\Contracts\HttpClient\Exception\ClientExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\DecodingExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\RedirectionExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\ServerExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;

class HepsiburadaConnector extends MarketplaceConnectorAbstract
{
    public static string $marketplaceType = 'Hepsiburada';

    /**
     * @throws TransportExceptionInterface
     * @throws ServerExceptionInterface
     * @throws RedirectionExceptionInterface
     * @throws DecodingExceptionInterface
     * @throws ClientExceptionInterface|RandomException
     */
    public function download(bool $forceDownload = false): void
    {
        if (!$forceDownload && $this->getListingsFromCache()) {
            echo "Using cached listings\n";
            return;
        }
        $offset = 0;
        $limit = 10;
        $this->listings = [];
        do {
            $response = $this->httpClient->request('GET', "https://listing-external.hepsiburada.com/listings/merchantid/{$this->marketplace->getSellerId()}", [
                'headers' => [
                    'Authorization' => 'Basic ' . base64_encode($this->marketplace->getSellerId() . ':' . $this->marketplace->getServiceKey()),
                    "User-Agent" => "colorfullworlds_dev",
                    'Accept' => 'application/json',
                    'Content-Type' => 'application/json'
                ],
                'query' => [
                    'offset' => $offset,
                    'limit' => $limit
                ]
            ]);
            $statusCode = $response->getStatusCode();
            if ($statusCode !== 200) {
                echo "Error: $statusCode\n";
                break;
            }
            $data = $response->toArray();
            $products = $data['listings'];
            $this->listings = array_merge($this->listings, $products);
            $totalItems = $data['totalCount'];
            echo "Offset: " . $offset . " " . count($this->listings) . " ";
            echo "Total Items: " . $totalItems . "\n";
            echo "Count: " . count($this->listings) . "\n";
            $offset += $limit;
        } while (count($this->listings) < $totalItems);
        if (empty($this->listings)) {
            echo "Failed to download listings\n";
            return;
        }
        $this->downloadAttributes();
        $this->putListingsToCache();
    }

    /**
     * @throws TransportExceptionInterface
     * @throws ServerExceptionInterface
     * @throws RedirectionExceptionInterface
     * @throws DecodingExceptionInterface
     * @throws ClientExceptionInterface
     */
    protected function getProduct($hbSku): array
    {
        $page = 0;
        $size = 1;
        $response = $this->httpClient->request('GET', "https://mpop.hepsiburada.com/product/api/products/all-products-of-merchant/{$this->marketplace->getSellerId()}", [
            'headers' => [
                'Authorization' => 'Basic ' . base64_encode($this->marketplace->getSellerId() . ':' . $this->marketplace->getServiceKey()),
                "User-Agent" => "colorfullworlds_dev",
                'Accept' => 'application/json',
                'Content-Type' => 'application/json'
            ],
            'query' => [
                'page' => $page,
                'size' => $size,
                'hbSku' => $hbSku
            ]
        ]);
        $statusCode = $response->getStatusCode();
        if ($statusCode !== 200) {
            echo "Error: $statusCode\n";
            return [];
        }
        return $response->toArray();
    }

    /**
     * @throws TransportExceptionInterface
     * @throws ServerExceptionInterface
     * @throws RedirectionExceptionInterface
     * @throws DecodingExceptionInterface
     * @throws ClientExceptionInterface
     */
    public function downloadAttributes(): void
    {
        echo "Downloading Attributes\n";
        foreach ($this->listings as &$listing) {
            $response = $this->getProduct($listing['hepsiburadaSku']);
            if (empty($response)) {
                echo "Failed to get product\n";
                continue;
            }
            if (isset($response['data'][0])) {
                $listing['attributes'] = $response['data'][0];
            } else {
                $listing['attributes'] = []; 
            }
        }
        echo "Attributes Downloaded\n";
    }

    protected function getAttributes($variantTypeAttributes): string
    {
        $attributeString = "";
        foreach ($variantTypeAttributes as $attribute) {
            $attributeString .= $attribute['value'] . "-";
        }
        return rtrim($attributeString, "-");
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

        foreach ($this->listings as $listing) {
            echo "($index/$total) Processing Listing {$listing['merchantSku']} ...";
            $parent = Utility::checkSetPath($marketplaceFolder);
            if (!empty($listing['attributes']['variantGroupId'])) {
                $parent = Utility::checkSetPath(
                    Utility::sanitizeVariable($listing['attributes']['variantGroupId']),
                    $parent
                );
            }
            VariantProduct::addUpdateVariant(
                variant: [
                    'imageUrl' => Utility::getCachedImage($listing['attributes']['images'][0]) ?? '',
                    'urlLink' => $this->getUrlLink("https://www.hepsiburada.com/-p-" . $listing['hepsiburadaSku']) ?? '',
                    'salePrice' => $listing['price'] ?? 0,
                    'saleCurrency' => $this->marketplace->getCurrency(),
                    'title' =>  $listing['attributes']['productName']  ?? '',
                    'attributes' => $this->getAttributes($listing['attributes']['variantTypeAttributes']) ?? '',
                    'quantity' => $listing['availableStock'] ?? 0,
                    'uniqueMarketplaceId' => $listing['hepsiburadaSku'] ?? '',
                    'apiResponseJson' => json_encode($listing, JSON_PRETTY_PRINT),
                    'published' => $listing['isSalable'],
                    'sku' => $listing['merchantSku'] ?? ''
                ],
                importFlag: $importFlag,
                updateFlag: $updateFlag,
                marketplace: $this->marketplace,
                parent: $parent
            );
            echo "OK\n";
            $index++;
        }  
    }

    /**
     * @throws DecodingExceptionInterface
     * @throws ClientExceptionInterface
     * @throws ServerExceptionInterface
     * @throws \Doctrine\DBAL\Exception
     * @throws RedirectionExceptionInterface
     * @throws TransportExceptionInterface|RandomException
     */
    public function setInventory(VariantProduct $listing, int $targetValue, $sku = null, $country = null, $locationId = null): void
    {
        $attributes = json_decode($listing->jsonRead('apiResponseJson'), true)['attributes'];
        $hbsku = $attributes['hbSku'];
        $merchantSku = $attributes['merchantSku'];
        if (empty($hbsku) || empty($merchantSku)) {
            echo "Failed to get inventory item id for {$listing->getKey()}\n";
            return;
        }
        $body = [
            'hepsiburadaSku' => $hbsku,
            'merchantSku' => $merchantSku,
            'availableStock' => $targetValue
        ];
        $response = $this->httpClient->request('POST', "https://listing-external.hepsiburada.com/listings/merchantid/{$this->marketplace->getSellerId()}/stock-uploads", [
            'headers' => [
                'authorization' => 'Basic ' . base64_encode($this->marketplace->getSellerId() . ':' . $this->marketplace->getServiceKey()),
                'User-Agent' => "colorfullworlds_dev",
                'accept' => 'application/json',
                'content-type' => 'application/*+json'
            ],
            'body' => json_encode($body)
        ]);
        $statusCode = $response->getStatusCode();
        if ($statusCode !== 200) {
            echo "Error: $statusCode\n";
            return;
        }
        $data = $response->toArray();
        $combinedData = [
            'inventory' => $data,
            'batchRequestResult' => $this->getBatchRequestResult($data['id'],"stock-uploads"),
        ];
        echo "Inventory set\n";
        $filename = "SETINVENTORY_{$hbsku}.json";
        $this->putToCache($filename, ['request'=>$body, 'response'=>$combinedData]);
    }

    /**
     * @throws RedirectionExceptionInterface
     * @throws DecodingExceptionInterface
     * @throws ClientExceptionInterface
     * @throws TransportExceptionInterface
     * @throws ServerExceptionInterface
     * @throws \Doctrine\DBAL\Exception
     * @throws RandomException
     */
    public function setPrice(VariantProduct $listing, string $targetPrice, $targetCurrency = null, $sku = null, $country = null): void
    {
        if (empty($targetPrice)) {
            echo "Error: Price cannot be null\n";
            return;
        }
        if ($targetCurrency === null) {
            $targetCurrency = $listing->getSaleCurrency();
            if ($targetCurrency === "TRY") {
                $targetCurrency = "TL";
            }
        }
        $finalPrice = $this->convertCurrency($targetPrice, $targetCurrency, $targetCurrency);
        if (empty($finalPrice)) {
            echo "Error: Currency conversion failed\n";
            return;
       }
        $attributes = json_decode($listing->jsonRead('apiResponseJson'), true)['attributes'];
        $hbsku = $attributes['hbSku'];
        $merchantSku = $attributes['merchantSku'];
        if (empty($hbsku) || empty($merchantSku)) {
            echo "Failed to get inventory item id for {$listing->getKey()}\n";
            return;
        }
        $body = [
            'hepsiburadaSku' => $hbsku,
            'merchantSku' => $merchantSku,
            'price' => (float) $finalPrice
        ];
        $response = $this->httpClient->request('POST', "https://listing-external.hepsiburada.com/listings/merchantid/{$this->marketplace->getSellerId()}/price-uploads", [
            'headers' => [
                'authorization' => 'Basic ' . base64_encode($this->marketplace->getSellerId() . ':' . $this->marketplace->getServiceKey()),
                'User-Agent' => "colorfullworlds_dev",
                'accept' => 'application/json',
                'content-type' => 'application/*+json'
            ],
            'body' => json_encode($body)
        ]); 
        $statusCode = $response->getStatusCode();
        if ($statusCode !== 200) {
            echo "Error: $statusCode\n";
            return;
        }
        echo "Price set\n";
        $data = $response->toArray();
        $combinedData = [
            'price' => $data,
            'batchRequestResult' => $this->getBatchRequestResult($data['id'],"price-uploads"),
        ];
        $filename = "SETPRICE_{$hbsku}.json";
        $this->putToCache($filename, ['request'=>$body, 'response'=>$combinedData]);
    }

    /**
     * @throws TransportExceptionInterface
     * @throws ServerExceptionInterface
     * @throws RedirectionExceptionInterface
     * @throws DecodingExceptionInterface
     * @throws ClientExceptionInterface
     */
    public function getBatchRequestResult($id, $type): array
    {
        $response = $this->httpClient->request('GET', "https://listing-external.hepsiburada.com/listings/merchantid/{$this->marketplace->getSellerId()}/{$type}/id/{$id}", [
            'headers' => [
                'Authorization' => 'Basic ' . base64_encode($this->marketplace->getSellerId() . ':' . $this->marketplace->getServiceKey()),
                "User-Agent" => "colorfullworlds_dev",
                'Accept' => 'application/json',
                'Content-Type' => 'application/json'
            ]
        ]);
        $statusCode = $response->getStatusCode();
        if ($statusCode !== 200) {
            echo "Error: $statusCode\n";
            return [];
        }
        return $response->toArray();
    }

    public function detailOrder($orderNumber)
    {
        $response = $this->httpClient->request('GET', "https://oms-external.hepsiburada.com/orders/merchantid/{$this->marketplace->getSellerId()}/ordernumber/" . $orderNumber, [
            'headers' => [
                'Authorization' => 'Basic ' . base64_encode($this->marketplace->getSellerId() . ':' . $this->marketplace->getServiceKey()),
                "User-Agent" => "colorfullworlds_dev",
                'Accept' => 'application/json',
                'Content-Type' => 'application/json'
            ]
        ]);
        if ($response->getStatusCode() === 200) {
            return $response->toArray();
        }
    }

    public function downloadOrders(): void
    {
        $orders = [];
        $offset = 0;
        $limit = 10;
        do {
            $response = $this->httpClient->request('GET', "https://oms-external.hepsiburada.com/packages/merchantid/{$this->marketplace->getSellerId()}/delivered", [
                'headers' => [
                    'Authorization' => 'Basic ' . base64_encode($this->marketplace->getSellerId() . ':' . $this->marketplace->getServiceKey()),
                    "User-Agent" => "colorfullworlds_dev",
                    'Accept' => 'application/json',
                    'Content-Type' => 'application/json'
                ],
                'query' => [
                    'offset' => $offset,
                    'limit' => $limit
                ]
            ]);
            $statusCode = $response->getStatusCode();
            if ($statusCode !== 200) {
                echo "Error: $statusCode\n";
                break;
            }
            $data = $response->toArray();
            $order = $data['items'] ?? [];
            $orders = array_merge($orders, $order);
            $totalItems = $data['totalCount'];
            echo "Offset: " . $offset . " " . $offset . " ";
            echo "Total Items: " . $totalItems . "\n";
            echo "Count: " . count($orders) . "\n";
            $offset += $limit;
        } while (count($orders) < $totalItems);
        foreach ($orders as &$order) {
            $orderNumber = $order['OrderNumber'];
            $order['detail'] = $this->detailOrder($orderNumber);
            $sqlInsertMarketplaceOrder = "
                            INSERT INTO iwa_marketplace_orders (marketplace_id, order_id, json) 
                            VALUES (:marketplace_id, :order_id, :json) ON DUPLICATE KEY UPDATE json = VALUES(json)";
            Utility::executeSql($sqlInsertMarketplaceOrder, [
                'marketplace_id' => $this->marketplace->getId(),
                'order_id' => $order['OrderNumber'],
                'json' => json_encode($order)
            ]);
            sleep(0.2);
        }
    }
    public function downloadReturns(): void
    {
        $returns = [];
        $offset = 0;
        $limit = 10;
        do {
            $response = $this->httpClient->request('GET', "https://oms-external.hepsiburada.com/orders/merchantid/{$this->marketplace->getSellerId()}/cancelled}", [
                'headers' => [
                    'Authorization' => 'Basic ' . base64_encode($this->marketplace->getSellerId() . ':' . $this->marketplace->getServiceKey()),
                    "User-Agent" => "colorfullworlds_dev",
                    'Accept' => 'application/json',
                    'Content-Type' => 'application/json'
                ],
                'query' => [
                    'offset' => $offset,
                    'limit' => $limit
                ]
            ]);
            $statusCode = $response->getStatusCode();
            if ($statusCode !== 200) {
                echo "Error: $statusCode\n";
                break;
            }
            $data = $response->toArray();
            $return = $data['items'] ?? [];
            $returns = array_merge($returns, $return);
            $totalItems = $data['totalCount'];
            echo "Offset: " . $offset . " " . $offset . " ";
            echo "Total Items: " . $totalItems . "\n";
            echo "Count: " . count($returns) . "\n";
            $offset += $limit;
        } while (count($returns) < $totalItems);
        foreach ($returns as $return) {
            $sqlInsertMarketplaceReturn = "
                            INSERT INTO iwa_marketplace_returns (marketplace_id, return_id, json) 
                            VALUES (:marketplace_id, :return_id, :json) ON DUPLICATE KEY UPDATE json = VALUES(json)";
            Utility::executeSql($sqlInsertMarketplaceReturn, [
                'marketplace_id' => $this->marketplace->getId(),
                'return_id' => $return['orderNumber'],
                'json' => json_encode($return)
            ]);
            echo "Inserting RETURN: " . $return['orderNumber'] . "\n";
        }
    }
    
    public function downloadInventory(): void
    {

    }

}