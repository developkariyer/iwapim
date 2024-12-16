<?php

namespace App\Connector\Marketplace\Ozon;

use App\Connector\Marketplace\MarketplaceConnectorAbstract;
use Exception;
use Pimcore\Model\DataObject\Marketplace;
use Pimcore\Model\DataObject\VariantProduct;
use Symfony\Component\HttpKernel\Exception\HttpExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\ClientExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\DecodingExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\RedirectionExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\ServerExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;

class Connector extends MarketplaceConnectorAbstract
{
    public static string $marketplaceType = 'Ozon';

    public Listings $listingsHelper;

    /**
     * @throws Exception
     */
    public function __construct(Marketplace $marketplace)
    {
        parent::__construct($marketplace);
        $this->listingsHelper = new Listings($this);
    }

    /**
     * @throws RedirectionExceptionInterface|DecodingExceptionInterface|ClientExceptionInterface|TransportExceptionInterface|ServerExceptionInterface
     */
    public function getApiResponse($method, $url, $query = []): array
    {
        echo "getApiResponse: $method $url ".json_encode($query)."\n";
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
                return $responseArray['result'] ?? [];
            } catch (DecodingExceptionInterface) {
                echo "Failed to decode response: " . $response->getContent(false) . "\n";
                exit;
                return [];
            }
        } catch (Exception $e) {
            echo "Unexpected error: " . $e->getMessage() . "\n";
            exit;
            return [];
        }
    }


    /**
     * @throws TransportExceptionInterface
     * @throws ServerExceptionInterface
     * @throws RedirectionExceptionInterface
     * @throws DecodingExceptionInterface
     * @throws ClientExceptionInterface
     */
    public function getApiMultiPageResponse($method, $url, $query = [], $itemsKey = 'items'): array
    {
        echo "getApiMultiPageResponse: $method $url ".json_encode($query)."\n";
        $items = [];
        $lastId = null;
        if (empty($query['limit'])) {
            $query['limit'] = 1000;
        }
        do {
            $query['last_id'] = $lastId;
            $result = $this->getApiResponse(
                $method,
                $url,
                $query
            );
            if (empty($result[$itemsKey])) {
                break;
            }
            $items = array_merge($items, $result[$itemsKey]);
            $lastId = $result['last_id'] ?? null;
            $totalCount = $result['total'] ?? 0;
            echo " $totalCount";
        } while ($lastId !== null && count($items) < $totalCount);
        return $items;
    }

    /**
     * @param bool $forceDownload
     * @throws ClientExceptionInterface
     * @throws DecodingExceptionInterface
     * @throws RedirectionExceptionInterface
     * @throws ServerExceptionInterface
     * @throws TransportExceptionInterface
     */
    public function download($forceDownload = false): void
    {
        $this->listingsHelper->getListings($forceDownload);
    }

    public function downloadInventory()
    {
        
    }

    public function downloadOrders()
    {

    }

    public function import($updateFlag, $importFlag)
    {

    }



    public function setInventory(VariantProduct $listing, int $targetValue, $sku = null, $country = null)
    {

    }

    public function setPrice(VariantProduct $listing,string $targetPrice, $targetCurrency = null, $sku = null, $country = null)
    {

    }

}
