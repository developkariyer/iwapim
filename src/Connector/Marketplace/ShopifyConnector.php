<?php

namespace App\Connector\Marketplace;

use App\Utils\Utility;
use Doctrine\DBAL\Exception;
use Pimcore\Model\DataObject\Data\ExternalImage;
use Pimcore\Model\DataObject\VariantProduct;
use Pimcore\Model\Element\DuplicateFullPathException;
use Symfony\Contracts\HttpClient\Exception\ClientExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\RedirectionExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\ServerExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;

class ShopifyConnector  extends MarketplaceConnectorAbstract
{
    public static string $marketplaceType = 'Shopify';

    private string $graphqlUrl = PIMCORE_PROJECT_ROOT . '/src/GraphQL/Shopify/';

    private string $apiUrl;

    public function __construct($marketplace)
    {
        parent::__construct($marketplace);
        $this->apiUrl = trim($this->marketplace->getApiUrl(), characters: "/ \n\r\t");
        if (empty($this->apiUrl)) {
            throw new \Exception("API URL is not set for Shopify marketplace {$this->marketplace->getKey()}");
        }
        if (!str_contains($this->apiUrl, 'https://')) {
            $this->apiUrl = "https://{$this->apiUrl}/admin/api/2024-07";
        }
    }

    /**
     * @throws RedirectionExceptionInterface
     * @throws ClientExceptionInterface
     * @throws TransportExceptionInterface
     * @throws ServerExceptionInterface
     */
    public function getFromShopifyApiGraphql($method, $data, $key = null): ?array
    {
        echo "Getting from Shopify GraphQL\n";
        $allData = [];
        $cursor = null;
        $totalCount = 0;
        $allData[$key] = [];
        do {
            $data['variables']['cursor'] = $cursor;
            $headersToApi = [
                'json' => $data,
                'headers' => [
                    'X-Shopify-Access-Token' => $this->marketplace->getAccessToken(),
                    'Content-Type' => 'application/json'
                ]
            ];
            while (true) {
                try {
                    $response = $this->httpClient->request($method, $this->apiUrl . '/graphql.json', $headersToApi);
                    $newData = json_decode($response->getContent(), true);
                    echo "Cost Info: " . json_encode($newData['extensions']['cost']) . PHP_EOL;
                    if ($newData['extensions']['cost']['throttleStatus']['currentlyAvailable'] < $newData['extensions']['cost']['actualQueryCost'] ) {
                        $restoreRate =  $this->rateLimitCalculate($newData['extensions']) ?? 5;
                        echo "Rate limit exceeded, waiting for {$restoreRate} seconds..." . PHP_EOL;
                        sleep($restoreRate);
                        continue;
                    }
                    $allData[$key] = array_merge($allData[$key] ?? [], $newData['data'][$key]['nodes'] ?? []);
                    break;
                } catch (\Exception $e) {
                    echo "Request Error: " . $e->getMessage() . PHP_EOL;
                    break;
                }
            }
            $itemsCount = count($newData['data'][$key]['nodes'] ?? []);
            $totalCount += $itemsCount;
            echo "$key Count: $totalCount\n";
            $pageInfo = $newData['data'][$key]['pageInfo'] ?? null;
            $cursor = $pageInfo['endCursor'] ?? null;
            $hasNextPage = $pageInfo['hasNextPage'] ?? false;
        } while ($hasNextPage);
        return $allData;
    }

    public function rateLimitCalculate($extensions): Int
    {
        $actualQueryCost = $extensions['cost']['actualQueryCost'];
        $currentlyAvailable = $extensions['cost']['throttleStatus']['currentlyAvailable'];
        $restoreRate = $extensions['cost']['throttleStatus']['restoreRate'];
        $waitTime = ceil(($actualQueryCost - $currentlyAvailable) / $restoreRate) + 1;
        return max($waitTime, 10);
    }

    /**
     * @throws TransportExceptionInterface
     * @throws ServerExceptionInterface
     * @throws RedirectionExceptionInterface
     * @throws ClientExceptionInterface
     */
    public function download($forceDownload = false): void
    {
        echo "GraphQL download\n";
        if ($this->getListingsFromCache()) {
            echo "Using cached listings\n";
            return;
        }
        $query = [
            'query' => file_get_contents($this->graphqlUrl . 'downloadListing.graphql'),
            'variables' => [
                'numProducts' => 50,
                'cursor' => null
            ]
        ];
        $this->listings = $this->getFromShopifyApiGraphql('POST', $query, 'products');
        if (empty($this->listings)) {
            echo "Failed to download listings\n";
            return;
        }
        $this->putListingsToCache();
    }


    /**
     * @throws TransportExceptionInterface
     * @throws ServerExceptionInterface
     * @throws RedirectionExceptionInterface
     * @throws ClientExceptionInterface
     * @throws Exception
     */
    public function downloadOrders(): void
    {
        $result = Utility::fetchFromSqlFile(parent::SQL_PATH . 'Shopify/select_last_updated_at.sql', [
            'marketplace_id' => $this->marketplace->getId()
        ]);
        $lastUpdatedAt = $result[0]['lastUpdatedAt'];
        echo  "Last updated at: $lastUpdatedAt\n";
        $filter = 'updated_at:>=' . (string) $lastUpdatedAt;
        $query = [
            'query' => file_get_contents($this->graphqlUrl . 'downloadOrders.graphql'),
            'variables' => [
                'numOrders' => 50,
                'cursor' => null,
                'filter' => $filter
            ]
        ];
        $orders = $this->getFromShopifyApiGraphql('POST', $query, 'orders');
        foreach ($orders['orders'] as $order) {
            Utility::executeSqlFile(parent::SQL_PATH . 'insert_marketplace_orders.sql', [
                'marketplace_id' => $this->marketplace->getId(),
                'order_id' => $order['id'],
                'json' => json_encode($order)
            ]);
        }
    }

    /**
     * @throws TransportExceptionInterface
     * @throws ServerExceptionInterface
     * @throws RedirectionExceptionInterface
     * @throws ClientExceptionInterface
     */
    public function downloadInventory()
    {
        $inventory = $this->getFromCache('INVENTORY.json');
        if (!empty($inventory)) {
            echo "Using cached inventory\n";
            return;
        }
        $query = [
            'query' => file_get_contents($this->graphqlUrl . 'downloadInventory.graphql'),
            'variables' => [
                'numItems' => 50,
                'cursor' => null
            ]
        ];
        $inventories = $this->getFromShopifyApiGraphql('POST', $query, 'inventoryItems');
        if (empty($inventories)) {
            echo "Failed to download inventory\n";
            return;
        }
        $this->putToCache('INVENTORY.json', $inventories);
    }

    /**
     * @throws DuplicateFullPathException
     */
    public function import($updateFlag, $importFlag)
    {
        $this->listings = [];
        $this->listings = $this->getFromCache("LISTINGS.json");

        $marketplaceFolder = Utility::checkSetPath(
            Utility::sanitizeVariable('Test11' . $this->marketplace->getKey(), 190),
            Utility::checkSetPath('Pazaryerleri')
        );
        $total = count($this->listings);
        $index = 0;
        foreach ($this->listings['products'] as $mainListing) {
            echo "($index/$total) Processing Listing {$mainListing['id']}:{$mainListing['title']} ...\n";
            $parent = Utility::checkSetPath(
                Utility::sanitizeVariable($mainListing['productType'] ?? 'Tasnif-Edilmemiş'),
                $marketplaceFolder
            );
            if (!empty($mainListing['title'])) {
                $parent = Utility::checkSetPath(
                    Utility::sanitizeVariable($mainListing['title']),
                    $parent
                );
            }
            if (($mainListing['status'] ?? 'ACTIVE') !== 'ACTIVE') {
                $parent = Utility::checkSetPath(
                    Utility::sanitizeVariable('_Pasif'),
                    $marketplaceFolder
                );
            }
            $parentResponseJson = $mainListing;
            if (isset($parentResponseJson['variants']['nodes'])) {
                unset($parentResponseJson['variants']['nodes']);
            }
            foreach ($mainListing['variants']['nodes'] as $listing) {
                echo $this->marketplace->getMarketplaceUrl().'products/'.($mainListing['handle'] ?? '').'/?variant='.($listing['id'] ?? '');
                try{
                    VariantProduct::addUpdateVariant(
                        variant: [
                            'imageUrl' => $this->getImage($listing, $mainListing) ?? '',
                            'urlLink' => $this->getUrlLink($this->marketplace->getMarketplaceUrl().'products/'.($mainListing['handle'] ?? '').'/?variant='.($listing['id'] ?? '')),
                            'salePrice' =>   $listing['price'] ?? '',
                            'saleCurrency' =>   $this->marketplace->getCurrency(),
                            'attributes' =>   $listing['title'] ?? '',
                            'title' =>  ($mainListing['title'] ?? '').($listing['title'] ?? ''),
                            'quantity' => $listing['inventory_quantity'] ?? 0,
                            'uniqueMarketplaceId' =>  basename($listing['id'] ?? ''),
                            'apiResponseJson' =>  json_encode($listing),
                            'parentResponseJson' => json_encode($parentResponseJson),
                            'published' =>  ($mainListing['status'] ?? 'ACTIVE') === 'ACTIVE',
                            'sku' =>   $listing['sku'] ?? '',
                            'ean' =>   $listing['barcode'] ?? '',
                        ],
                        importFlag: $importFlag,
                        updateFlag: $updateFlag,
                        marketplace: $this->marketplace,
                        parent: $parent
                    );
                    echo "v";
                } catch (\Exception $e) {
                    echo "Error: " . $e->getMessage() . "\n";
                    echo "Sku: " . $listing['sku'] ?? '' . "\n";
                    echo "ERRROR VARIANT: \n";
                }
            }
            echo "OK\n";
            $index++;
        }
    }

    protected function getImage($listing, $mainListing): ?ExternalImage
    {
        $lastImage = '';
        $images = $mainListing['media']['nodes'] ?? [];
        foreach ($images as $img) {
            $imageId = $listing['image']['id'] ?? null;
            $imgId = $img['id'] ?? null;
            if ($imageId === null || $imgId === null) {
                continue;
            }
            if ( basename($imgId) === basename($imageId)) {
                return Utility::getCachedImage($img['preview']['image']['url'] ?? '');
            }

            if (empty($lastImage)) {
                $lastImage = Utility::getCachedImage($img['preview']['image']['url'] ?? '');
            }
        }
        return $lastImage;
    }

    public function setInventory(VariantProduct $listing, int $targetValue, $sku = null, $country = null)
    {
        // TODO: Implement setInventory() method.
    }

    public function setPrice(VariantProduct $listing, string $targetPrice, $targetCurrency = null, $sku = null, $country = null)
    {
        // TODO: Implement setPrice() method.
    }
}