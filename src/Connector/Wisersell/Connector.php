<?php

namespace App\Connector\Wisersell;

use Symfony\Component\HttpClient\HttpClient;
use Symfony\Component\HttpClient\ScopingHttpClient;
use App\Connector\Wisersell\ProductSyncService;
use App\Connector\Wisersell\CategorySyncService;
use App\Connector\Wisersell\ListingSyncService;
use App\Connector\Wisersell\StoreSyncService;
use App\Utils\Utility;
use Exception;


class Connector
{
    public $env = '';
    public static $apiUrl = [
        'productSearch' => 'product/search',
        'category' => 'category',
        'product'=> 'product',
        'store' => 'store',
        'listingSearch' => 'listing/search',
        'listing' => 'listing/'
    ];
    private $httpClient = null;
    private $wisersellCredentials = null;
    public $wisersellToken = null;

    public $storeSyncService = null;
    public $categorySyncService = null;
    public $productSyncService = null;
    public $listingSyncService = null;

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

    protected function prepareToken()
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

    public function request($apiEndPoint, $type, $parameter = '', $json = [])
    {
        echo "Requesting: {$apiEndPoint} {$type} {$parameter} in time ".time()."\n";
        flush();
        $this->prepareToken();
        $response = $this->httpClient->request($type, $apiEndPoint . $parameter, ['json' => $json]);
        sleep(2);
        switch ($response->getStatusCode()) {
            case 401:
                $this->wisersellToken = $this->fetchNewAccessToken();
                $response = $this->httpClient->request($type, $apiEndPoint . $parameter, ['json' => $json]);
                sleep(2);
                break;
            case 200:
                break;
            default:
                echo "Failed to get response. HTTP Status Code: {$response->getStatusCode()}\n";
                return '';
        }
        if ($response->getStatusCode() == 200) {
            return $response;
        }
    }

}