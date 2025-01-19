<?php

namespace App\Connector\Wisersell;

use Random\RandomException;
use Symfony\Component\HttpClient\HttpClient;
use Symfony\Component\HttpClient\ScopingHttpClient;
use App\Utils\Utility;
use Exception;
use Symfony\Contracts\HttpClient\Exception\ClientExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\DecodingExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\RedirectionExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\ServerExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;
use Symfony\Contracts\HttpClient\ResponseInterface;


class Connector
{
    public mixed $env = '';
    public static array $apiUrl = [
        'productSearch' => 'product/search',
        'category' => 'category',
        'product'=> 'product',
        'store' => 'store',
        'listingSearch' => 'listing/search',
        'listing' => 'listing'
    ];
    private HttpClientInterface $httpClient;
    private array $wisersellCredentials;
    public mixed $wisersellToken = null;

    public ?StoreSyncService $storeSyncService = null;
    public ?CategorySyncService $categorySyncService = null;
    public ?ProductSyncService $productSyncService = null;
    public ?ListingSyncService $listingSyncService = null;

    /**
     * @throws TransportExceptionInterface
     * @throws ServerExceptionInterface
     * @throws RedirectionExceptionInterface
     * @throws DecodingExceptionInterface
     * @throws ClientExceptionInterface
     */
    public function __construct($env = 'prod')
    {
        $this->httpClient = HttpClient::create();
        $this->wisersellCredentials = match ($env) {
            'prod' => [
                'apiServer' => 'https://www.wisersell.com/restapi/',
                'email' => $_ENV['WISERSELL_PROD_USER'],
                'password' => $_ENV['WISERSELL_PROD_PASSWORD'],
            ],
            default => [
                'apiServer' => 'https://dev2.wisersell.com/restapi/',
                'email' => $_ENV['WISERSELL_DEV_USER'],
                'password' => $_ENV['WISERSELL_DEV_PASSWORD'],
            ],
        };
        $this->env = $env;
        $this->prepareToken();
        $this->storeSyncService = new StoreSyncService($this);
        $this->categorySyncService = new CategorySyncService($this);
        $this->productSyncService = new ProductSyncService($this);
        $this->listingSyncService = new ListingSyncService($this);
    }

    /**
     * @throws TransportExceptionInterface
     * @throws ServerExceptionInterface
     * @throws RedirectionExceptionInterface
     * @throws DecodingExceptionInterface
     * @throws ClientExceptionInterface
     */
    protected function prepareToken(): void
    {
        if (!empty($this->wisersellToken) && Utility::checkJwtTokenValidity($this->wisersellToken)) {
            return;
        }
        $this->wisersellToken = $this->getAccessToken();
        $this->httpClient = ScopingHttpClient::forBaseUri(
            $this->httpClient,
            $this->wisersellCredentials['apiServer'],
            [
                'headers' => [
                    'Authorization' => "Bearer {$this->wisersellToken}",
                    'Accept' => 'application/json',
                    'Content-Type' => 'application/json',
                ],
            ]
        ); 
    }

    /**
     * @throws TransportExceptionInterface
     * @throws ServerExceptionInterface
     * @throws RedirectionExceptionInterface
     * @throws DecodingExceptionInterface
     * @throws ClientExceptionInterface|RandomException
     */
    protected function getAccessToken()
    {
        $token = json_decode(Utility::getCustomCache('wisersell_access_token.json', PIMCORE_PROJECT_ROOT . '/tmp'), true);
        if (Utility::checkJwtTokenValidity($token['token'] ?? '')) {
            echo "Token valid\n";
            return $token['token'];
        }
        echo "Token file not found or expired. Fetching new token...\n";
        return $this->fetchNewAccessToken();
    }

    /**
     * @throws RedirectionExceptionInterface
     * @throws DecodingExceptionInterface
     * @throws ClientExceptionInterface
     * @throws TransportExceptionInterface
     * @throws ServerExceptionInterface
     * @throws Exception
     */
    protected function fetchNewAccessToken()
    {
        $response = $this->httpClient->request(
            'POST',
            $this->wisersellCredentials['apiServer'].'token', 
            [
                'json' => [
                    "email" => $this->wisersellCredentials['email'],
                    "password" => $this->wisersellCredentials['password'],
                ],
                'headers' => [
                    'Content-Type' => 'application/json',
                    'Accept' => 'application/json',
                ],
            ]
        );
        if ($response->getStatusCode() !== 200) {
            throw new Exception("Failed to get bearer token. HTTP Status Code: {$response->getContent()}");
        }
        $result = $response->toArray(); 
        if (empty($result['token'])) {
            throw new Exception("Failed to get bearer token. Response: " . json_encode($result));
        }
        Utility::setCustomCache('wisersell_access_token.json', PIMCORE_PROJECT_ROOT . '/tmp', json_encode($result));
        echo "New token saved to file.\n";
        return $result['token'];
    }

    /**
     * @throws RedirectionExceptionInterface
     * @throws ClientExceptionInterface
     * @throws TransportExceptionInterface
     * @throws ServerExceptionInterface|DecodingExceptionInterface
     * @throws Exception
     */
    public function request($apiEndPoint, $type, $parameter = '', $json = []): string|ResponseInterface
    {
        echo "Requesting: {$apiEndPoint} {$type} {$parameter} in time ".time();
        flush();
        $this->prepareToken();
        file_put_contents(PIMCORE_PROJECT_ROOT . '/var/log/wisersell.log', "=> ".date('Y-m-d H:i:s').", {$type}, {$apiEndPoint}/{$parameter}, ".json_encode($json)."\n", FILE_APPEND);
        $response = $this->httpClient->request($type, "{$apiEndPoint}/{$parameter}", ['json' => $json]);
        file_put_contents(PIMCORE_PROJECT_ROOT . '/var/log/wisersell.log', "<= ".date('Y-m-d H:i:s').", {$response->getStatusCode()}, {$response->getContent()}\n", FILE_APPEND);
        if (str_contains($apiEndPoint, 'listing')) {
            usleep(500000);
        } else {
            usleep(2000000);
        }
        switch ($response->getStatusCode()) {
            case 401:
            case 502:
                echo " Token expired. Fetching new token...\n";
                $this->wisersellToken = $this->fetchNewAccessToken();
                $response = $this->httpClient->request($type, $apiEndPoint . $parameter, ['json' => $json]);
                sleep(2);
                break;
            case 200:
                echo " Success (200)\n";
                break;
            default:
                echo " Failed to get response. HTTP Status Code: {$response->getStatusCode()}\n";
                return '';
        }
        if ($response->getStatusCode() == 200) {
            return $response;
        }
        throw new Exception("Failed. HTTP Status Code: {$response->getContent()}");
    }

}