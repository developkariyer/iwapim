<?php

namespace App\Connector\Marketplace\Ozon;

use App\Connector\Marketplace\MarketplaceConnectorAbstract;
use Exception;
use Pimcore\Model\DataObject\Marketplace;
use Pimcore\Model\DataObject\VariantProduct;
use Symfony\Contracts\HttpClient\Exception\ClientExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\DecodingExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\RedirectionExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\ServerExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;

class Connector extends MarketplaceConnectorAbstract
{
    public static string $marketplaceType = 'Ozon';

    public Listings $listingsHelper;
    public Products $productsHelper;

    /**
     * @throws Exception
     */
    public function __construct(Marketplace $marketplace)
    {
        parent::__construct($marketplace);
        $this->listingsHelper = new Listings($this);
        $this->productsHelper = new Products($this);
    }

    /**
     * @throws RedirectionExceptionInterface|DecodingExceptionInterface|ClientExceptionInterface|TransportExceptionInterface|ServerExceptionInterface
     */
    public function getApiResponse($method, $url, $query = [], $returnKey = 'result'): array
    {
        $options = [
            'headers' => [
                'Client-Id' => $this->marketplace->getOzonClientId(),
                'Api-Key' => $this->marketplace->getOzonApiKey(),
            ],
        ];
        if (!empty($query)) {
            if ($method === 'POST' || $method === 'PUT') {
                $options['headers']['Content-Type'] = 'application/json';
                $options['json'] = $query;
            } else {
                $options['query'] = $query;
            }
        }
        try {
            $response = $this->httpClient->request($method, $url, $options);
            $statusCode = $response->getStatusCode();
            if ($statusCode < 200 || $statusCode >= 300) {
                echo "Error: " . json_encode($response->toArray(false)) . "\n";
                return [];
            }
            try {
                $responseArray = $response->toArray();
                return empty($returnKey) ? $responseArray : ($responseArray[$returnKey] ?? []);
            } catch (DecodingExceptionInterface) {
                echo "Failed to decode response: " . $response->getContent(false) . "\n";
                return [];
            }
        } catch (Exception $e) {
            echo "Unexpected error: " . $e->getMessage() . "\n";
            return [];
        }
    }


    /**
     * @throws RedirectionExceptionInterface|DecodingExceptionInterface|ClientExceptionInterface|TransportExceptionInterface|ServerExceptionInterface
     */
    public function getApiMultiPageResponse($method, $url, $query = [], $itemsKey = 'items'): array
    {
        $items = [];
        $lastId = null;
        if (empty($query['limit'])) {
            $query['limit'] = 1000;
        }
        do {
            $query['last_id'] = $lastId;
            $response = $this->getApiResponse(
                $method,
                $url,
                $query
            );
            $result = empty($itemsKey) ? $response : $response[$itemsKey];
            if (empty($result)) {
                break;
            }
            $items = array_merge($items, $result);
            $lastId = $result['last_id'] ?? null;
            $totalCount = $result['total'] ?? 0;
        } while ($lastId !== null && count($items) < $totalCount);
        return $items;
    }

    /**
     * @param false $forceDownload
     * @throws ClientExceptionInterface|DecodingExceptionInterface|RedirectionExceptionInterface|ServerExceptionInterface|TransportExceptionInterface
     */
    public function download(bool $forceDownload = false): void
    {
        $this->listingsHelper->getListings($forceDownload);
    }

    public function downloadInventory(): void
    {
        // Implement downloadInventory() method.
    }

    public function downloadOrders(): void
    {
        // Implement downloadOrders() method.
    }

    /**
     * @throws TransportExceptionInterface|ServerExceptionInterface|RedirectionExceptionInterface|DecodingExceptionInterface|ClientExceptionInterface|\Doctrine\DBAL\Exception
     */
    public function downloadAttributes(): void
    {
        error_reporting(E_ALL);
        ini_set('display_errors', 1);
        $this->productsHelper->getCategoryTreeFromApi();
        $this->productsHelper->getCategoryAttributesFromApi();
        $this->productsHelper->getAttributeValuesFromApi();
    }

    public function import($updateFlag, $importFlag): void
    {
        // Implement import() method.
    }

    public function setInventory(VariantProduct $listing, int $targetValue, $sku = null, $country = null): void
    {
        // Implement setInventory() method.
    }

    public function setPrice(VariantProduct $listing, string $targetPrice, $targetCurrency = null, $sku = null, $country = null): void
    {
        // Implement setPrice() method.
    }

}