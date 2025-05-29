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
        'offers' => "product/products"
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
        $this->prepareToken();

        try {
            $response = $this->httpClient->request('GET', static::$apiUrl['offers'], [
                'headers' => [
                    'Authorization' => 'Bearer ' . $this->accessToken,
                    'Accept' => 'application/json'
                ],
                'query' => [
                    'Approved' => true,
                    'page' => 1,
                    'size' => 100
                ]
            ]);

            $statusCode = $response->getStatusCode();
            echo "Status: " . $statusCode . PHP_EOL;

            $data = json_decode($response->getContent(), true);

            print_r($data);

        } catch (\Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface $e) {
            echo "İletişim hatası: " . $e->getMessage();
        } catch (\Symfony\Contracts\HttpClient\Exception\ClientExceptionInterface $e) {
            echo "İstemci hatası (4xx): " . $e->getMessage();
        } catch (\Symfony\Contracts\HttpClient\Exception\ServerExceptionInterface $e) {
            echo "Sunucu hatası (5xx): " . $e->getMessage();
        } catch (\Symfony\Contracts\HttpClient\Exception\RedirectionExceptionInterface $e) {
            echo "Yönlendirme hatası: " . $e->getMessage();
        } catch (\Exception $e) {
            echo "Genel hata: " . $e->getMessage();
        }
        print_r($response->getContent());

        // TODO: Implement download() method.
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