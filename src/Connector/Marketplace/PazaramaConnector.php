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
                    ]
                ]);
                if ($response->getStatusCode() !== 200) {
                    throw new \Exception("Failed to get offers from Pazarama (Approved = $approvedStatus)");
                }
                $responseArray = $response->toArray();
                $data = $responseArray['data'];
                foreach ($data as &$item) {
                    $item['listingStatus'] = $approvedStatus;
                }
                unset($item);
                $dataCount = count($data);
                echo "Approved = {$approvedStatus} | Page: {$page} | Data Count: {$dataCount}" . PHP_EOL;
                $page++;
                $this->listings = array_merge($this->listings, $data);
            } while ($dataCount === $size);
        }
        $index = 1;
        $listingCount = count($this->listings);
        foreach ($this->listings as &$listing) {
            if (is_array($listing) && isset($listing['code'])) {
                echo "Processing: {$index}/{$listingCount} Code: {$listing['code']}" . PHP_EOL;
                $code = $listing['code'];
                $productDetail = $this->getProductDetail($code);
                $listing['detail'] = $productDetail;
            } else {
                echo "Skipping invalid listing at index {$index}" . PHP_EOL;
            }
            $index++;
        }
        unset($listing);
        $this->putListingsToCache();
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
            echo "($index/$total) Processing Listing {$listing['code']}:{$listing['name']} ...";
            $parent = Utility::checkSetPath($marketplaceFolder);
            if ($listing['detail']['groupCode']) {
                $parent = Utility::checkSetPath(
                    Utility::sanitizeVariable($listing['detail']['groupCode']),
                    $parent
                );
            }
            $url = $this->getPazaramaUrlLink($listing['name'], $listing['code'], $listing['brandName']);
            VariantProduct::addUpdateVariant(
                variant: [
                    'imageUrl' => Utility::getCachedImage($listing['detail']['images'][0]['imageUrl']) ?? '',
                    'urlLink' =>  $this->getUrlLink($url) ?? '',
                    'salePrice' => $listing['salePrice'] ?? 0,
                    'saleCurrency' =>  $this->marketplace->getCurrency(),
                    'title' => $listing['detail']['displayName'] ?? '',
                    'quantity' => $listing['detail']['stockCount'] ?? 0,
                    'attributes' => $listing['name']  ?? '',
                    'uniqueMarketplaceId' =>  $listing['code'] ?? '',
                    'apiResponseJson' => json_encode($listing, JSON_PRETTY_PRINT),
                    'published' => $listing['listingStatus'] === 'true' ? 1 : 0,
                    'sku' => $listing['stockCode'] ?? '',
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

    private function getPazaramaUrlLink($title, $code, $brand): string
    {
        $title = mb_strtolower($title, 'UTF-8');
        $turkish = ['ç', 'ğ', 'ı', 'ö', 'ş', 'ü'];
        $english = ['c', 'g', 'i', 'o', 's', 'u'];
        $title = str_replace($turkish, $english, $title);
        $title = preg_replace('/[^a-z0-9\s-]/', '', $title);
        $title = preg_replace('/[\s]+/', '-', trim($title));
        $brand = mb_strtolower($brand, 'UTF-8');
        $brand = str_replace($turkish, $english, $brand);
        $brand = preg_replace('/[^a-z0-9\s-]/', '', $brand);
        $brand = preg_replace('/[\s]+/', '-', trim($brand));
        $url = "https://www.pazarama.com/{$title}-p-{$code}?magaza={$brand}";
        return $url;
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