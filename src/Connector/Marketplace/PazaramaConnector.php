<?php

namespace App\Connector\Marketplace;

use Doctrine\DBAL\Exception;
use Pimcore\Model\DataObject\Folder;
use Pimcore\Model\DataObject\Product;
use Pimcore\Model\Element\DuplicateFullPathException;
use Random\RandomException;
use Symfony\Component\HttpClient\ScopingHttpClient;
use Pimcore\Model\DataObject\VariantProduct;
use App\Utils\Utility;
use Symfony\Contracts\HttpClient\Exception\ClientExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\DecodingExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\RedirectionExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\ServerExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;

class PazaramaConnector extends MarketplaceConnectorAbstract
{
    private static array $apiUrl = [
        'loginTokenUrl' => "https://isortagimgiris.pazarama.com/connect/token",
        'offers' => "product/products",
        'productDetail' => "product/getProductDetail"
    ];

    public static string $marketplaceType = 'Pazarama';

    protected function prepareToken(): void
    {
        if (!Utility::checkJwtTokenValidity($this->marketplace->getPazaramaAccessToken())) {
            echo "Token is invalid. Regenerating...\n";
            $response = $this->httpClient->request('POST', static::$apiUrl['loginTokenUrl'], [
                'headers' => [
                    'Authorization' => 'Basic ' . base64_encode("{$this->marketplace->getPazaramaClientId()}:{$this->marketplace->getPazaramaClientSecret()}"),
                    'Accept' => 'application/json'
                ],
                'body' => [
                    'grant_type' => 'client_credentials',
                    'scope' => 'merchantgatewayapi.fullaccess'
                ]
            ]);
            if ($response->getStatusCode() !== 200) {
                throw new \Exception('Failed to get JWT token from Bol.com');
            }
            $decodedResponse = json_decode($response->getContent(), true);
            $this->marketplace->setPazaramaAccessToken($decodedResponse['data']['accessToken']);
            $this->marketplace->save();
        }
        echo "Token is valid \n";
        $this->httpClient = ScopingHttpClient::forBaseUri($this->httpClient, 'https://isortagimapi.pazarama.com/', [
            'headers' => [
                'Authorization' => 'Bearer ' . $this->marketplace->getPazaramaAccessToken(),
                'Content-Type' => 'application/json'
            ],
        ]);
    }

    public function download(bool $forceDownload = false): void
    {
        if (!$forceDownload && $this->getListingsFromCache()) {
            echo "Using cached listings\n";
            return;
        }
        echo "Downloading Pazarama...\n";
        $this->prepareToken();
        $page = 1;
        $size = 100;
        $this->listings = [];
        foreach (['true', 'false'] as $approvedStatus) {
            $page = 1;
            $size = 100;
            do {
                $response = $this->httpClient->request('GET', static::$apiUrl['offers'], [
                    'query' => [
                        'Approved' => $approvedStatus,
                        'page' => $page,
                        'size' => $size
                    ],
                    'headers' => [
                        'Authorization' => 'Bearer ' . $this->accessToken,
                        'Accept' => 'application/json'
                    ]
                ]);
                if ($response->getStatusCode() !== 200) {
                    throw new \Exception("Failed to get offers from Pazarama (Approved = $approvedStatus)");
                }
                $responseArray = $response->toArray();
                $data = $responseArray['data'];
                $dataCount = count($data);
                echo "Approved = {$approvedStatus} | Page: {$page} | Data Count: {$dataCount}" . PHP_EOL;
                $page++;
                $this->listings = array_merge($this->listings, $data);
            } while ($dataCount === $size);
        }
        $index = 1;
        $listingCount = count($this->listings);
        foreach ($this->listings as &$listing) {
            echo "Processing: {$index}/{$listingCount} Code: {$listing['code']}" . PHP_EOL;
            $code = $listing['code'];
            $productDetail = $this->getProductDetail($code);
            $listing['detail'] = $productDetail;
            $index++;
        }
        unset($listing);
        $this->putListingsToCache();
        // TODO: Implement download() method.
    }

    private function getProductDetail($code)
    {
        $response = $this->httpClient->request('POST', static::$apiUrl['productDetail'], [
            'json' => [
                'Code' => $code
            ]
        ]);
        if ($response->getStatusCode() !== 200) {
            throw new \Exception('Failed to get product detail from Pazarama');
        }
        $responseArray = $response->toArray();
        return $responseArray['data'];
    }

    public function downloadOrders(): void
    {
        // TODO: Implement downloadOrders() method.
    }

    public function downloadInventory(): void
    {
        // TODO: Implement downloadInventory() method.
    }

    public function downloadReturns(): void
    {
        // TODO: Implement downloadReturns() method.
    }

    public function import($updateFlag, $importFlag): void
    {
        // TODO: Implement import() method.
    }

    public function setInventory(VariantProduct $listing, int $targetValue, $sku = null, $country = null): void
    {
        // TODO: Implement setInventory() method.
    }

    public function setPrice(VariantProduct $listing, string $targetPrice, $targetCurrency = null, $sku = null, $country = null): void
    {
        // TODO: Implement setPrice() method.
    }
}