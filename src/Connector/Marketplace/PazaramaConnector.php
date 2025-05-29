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
    ];

    public static string $marketplaceType = 'Pazarama';

    protected function prepareToken(): void
    {

        try {
            $response = $this->httpClient->request('POST', static::$apiUrl['loginTokenUrl'], [
                'headers' => [
                    'Authorization' => 'Basic ' . base64_encode("{$this->marketplace->getPazaramaClientId()}:{$this->marketplace->getPazaramaClientSecret()}"),
                    'Accept' => 'application/json'
                ],
                'form_params' => [
                    'grant_type' => 'client_credentials',
                    'scope' => 'merchantgatewayapi.fullaccess'
                ]
            ]);

            // Durum kodunu kontrol et
            $statusCode = $response->getStatusCode();
            echo "Status Code: " . $statusCode . "\n"; // Durum kodunu yazdır

            // Yanıtın içeriğini al ve yazdır
            $responseContent = $response->getContent();
            echo "Response Content: " . $responseContent . "\n"; // Yanıtı yazdır

            // JSON formatında yanıtı işlemek
            $decodedResponse = json_decode($responseContent, true);
            if ($decodedResponse === null) {
                echo "JSON decode hatası: " . json_last_error_msg(); // JSON çözümlemede hata varsa
            } else {
                print_r($decodedResponse);  // JSON verisini yazdır
            }

        } catch (\Exception $e) {
            // Hata durumunda yakalanan mesajı yazdır
            echo "Hata: " . $e->getMessage(); // Hata mesajını yazdır
            echo "Hata kodu: " . $e->getCode(); // Hata kodunu yazdır
            // Eğer hata geçmişi isteniyorsa:
            echo "Stack Trace: " . $e->getTraceAsString(); // Hata izini yazdır
        }
//        print_r($response->getContent());
//        if (!Utility::checkJwtTokenValidity($this->marketplace->getPazaramaAccessToken())) {
//            $response = $this->httpClient->request('POST', static::$apiUrl['loginTokenUrl'], [
//                'headers' => [
//                    'Authorization' => 'Basic ' . base64_encode("{$this->marketplace->getPazaramaClientId()}:{$this->marketplace->getPazaramaClientSecret()}"),
//                    'Accept' => 'application/json'
//                ],
//                'form_params' => [
//                    'grant_type' => 'client_credentials',
//                    'scope' => 'merchantgatewayapi.fullaccess'
//                ]
//            ]);
//            print_r($response->getContent());
//            if ($response->getStatusCode() !== 200) {
//                throw new \Exception('Failed to get JWT token from Bol.com');
//            }
//            $decodedResponse = json_decode($response->getContent(), true);
//            $this->marketplace->setPazaramaAccessToken($decodedResponse['data']['accessToken']);
//            $this->marketplace->save();
//        }
//        $this->httpClient = ScopingHttpClient::forBaseUri($this->httpClient, 'https://isortagimapi.pazarama.com/', [
//            'headers' => [
//                'Authorization' => 'Bearer ' . $this->marketplace->getPazaramaAccessToken(),
//                'Content-Type' => 'application/json'
//            ],
//        ]);
    }

    public function download(bool $forceDownload = false): void
    {
        $this->prepareToken();
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