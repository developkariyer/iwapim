<?php

namespace App\Connector\Marketplace\Ozon;

use App\Utils\Utility;
use Symfony\Contracts\HttpClient\Exception\ClientExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\DecodingExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\RedirectionExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\ServerExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;

class Listings
{
    public Connector $connector;

    const string API_PRODUCT_LIST_URL = "https://api-seller.ozon.ru/v3/product/list";
    const string API_PRODUCT_ATTRIBUTES_URL = "https://api-seller.ozon.ru/v4/product/info/attributes";
    const string API_PRODUCT_INFO_URL = "https://api-seller.ozon.ru/v2/product/info";

    public function __construct(Connector $connector)
    {
        $this->connector = $connector;
    }

    /**
     * @throws TransportExceptionInterface|ServerExceptionInterface|RedirectionExceptionInterface|DecodingExceptionInterface|ClientExceptionInterface
     */
    public function getListings($forceDownload = false): void
    {
        echo "Getting listings\n";
        if (!($forceDownload || $this->connector->getListingsFromCache())) {
            echo "  Using cached copy\n";
            return;
        }
        $this->connector->listings = [];
        $productBucket = [];
        $products = $this->getListingsFromApi();
        $totalCount = count($products);
        $index = 0;
        foreach ($products as $product) {
            $index++;
            echo "  Getting product $index/$totalCount ";
            $productId = $product['product_id'] ?? '';
            if (empty($productId)) {
                echo "No product id\n";
                continue;
            }
            $this->connector->listings[$productId] = $product;
            $this->connector->listings[$productId]['info'] = $this->getProductInfo($product);
            echo " {$this->connector->listings[$productId]['info']['sku']} ";
            $productBucket[] = $productId;
            if (count($productBucket) >= 1000) {
                $this->getProductAttributes($productBucket);
                $productBucket = [];
            }
            echo "\n";
        }
        $this->getProductAttributes($productBucket);
        $this->connector->putListingsToCache();
    }

    /**
     * @throws TransportExceptionInterface|ServerExceptionInterface|RedirectionExceptionInterface|DecodingExceptionInterface|ClientExceptionInterface
     */
    public function getListingsFromApi($visibility = 'ALL'): array
    {
        return $this->connector->getApiMultiPageResponse('POST',  self::API_PRODUCT_LIST_URL, ['visibility' => $visibility]);
    }

    /**
     * @throws TransportExceptionInterface|ServerExceptionInterface|RedirectionExceptionInterface|DecodingExceptionInterface|ClientExceptionInterface
     */
    public function getProductInfo($product): array
    {
        $productId = $product['product_id'];
        $apiResponse = $this->connector->getApiResponse('POST', self::API_PRODUCT_INFO_URL, ['product_id' => $productId]);
        return $apiResponse['result'] ?? [];
    }

    /**
     * @throws TransportExceptionInterface|ServerExceptionInterface|RedirectionExceptionInterface|DecodingExceptionInterface|ClientExceptionInterface
     */
    public function getProductAttributes(array $productBucket): void
    {
        $query = [
            'filter' => [
                'product_id' => $productBucket,
                'visibility' => 'ALL'
            ],
        ];
        $productAttributes = $this->connector->getApiMultiPageResponse('POST', self::API_PRODUCT_ATTRIBUTES_URL, $query);
        foreach ($productAttributes as $product) {
            if (!isset($this->connector->listings[$product['id']])) {
                echo "Product {$product['id']} not found in listings.\n";
                continue;
            }
            $this->connector->listings[$product['id']]['attributes'] = $product;
            echo ".";
        }
    }

}
