<?php

namespace App\MarketplaceConnector;

use Symfony\Component\HttpClient\HttpClient;
use Symfony\Component\HttpClient\ScopingHttpClient;
use Pimcore\Model\DataObject\Category;
use App\Utility\Utility;


class WisersellConnector
{
    protected $wisersellProducts = [];
    protected $wisersellListings = [];
    protected $wisersellCategories = [];
    protected $wisersellStores = [];
    protected $pimCategories = [];
    private static $apiUrl = [
        'productSearch' => 'product/search',
        'category' => 'category',
        'product'=> 'product',
        'store' => 'store',
        'listingSearch' => 'listing/search',
        'listing' => 'listing/'
    ];
    private $httpClient = null;
    private $wisersellCredentials = null;
    private $wisersellToken = null;

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
        $this->prepareToken();
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

    public function request($apiEndPoint, $type, $parameter, $json = [])
    {
        $this->prepareToken();
        $response = $this->httpClient->request($type, $apiEndPoint . $parameter, ['json' => $json]);
        usleep(2000000);
        switch ($response->getStatusCode()) {
            case 401:
                $this->wisersellToken = $this->fetchNewAccessToken();
                $response = $this->httpClient->request($type, $apiEndPoint . $parameter, ['json' => $json]);
                usleep(2000000);
                break;
            case 200:
                break;
            default:
                echo "Failed to get response. HTTP Status Code: {$response->getStatusCode()}\n";
                return null;
        }
        if ($response->getStatusCode() == 200) {
            return $response;
        }
    }

    public function getWisersellCategories()
    {
        $response = $this->request(self::$apiUrl['category'], 'GET', '');
        if (empty($response)) {
            return [];
        }
        $this->wisersellCategories = $response->toArray();
        return $this->wisersellCategories; // array of [id, name]
    }

    public function getPimCategories()
    {
        $listingObject = new Category\Listing();
        $listingObject->setUnpublished(true);
        $categories = $listingObject->load();
        $this->pimCategories = [];
        foreach ($categories as $category) {
            $this->pimCategories[$category->getCategory()] = $category;
        }
        return $this->pimCategories;
    }

    public function addCategoryToWisersell($categoryName)
    {
        $response = $this->request(self::$apiUrl['category'], 'POST', '', ['name' => $categoryName]);
        if (empty($response)) {
            return null;
        }
        return $response->toArray();
    }

    public function updateWisersellCategory($categoryId, $categoryName)
    {
        $response = $this->request(self::$apiUrl['category'], 'PUT', "/{$categoryId}", ['name' => $categoryName]);
        if (empty($response)) {
            return null;
        }
        return $response->toArray();
    }

    public function searchWisersellProducts($searchData)
    {
        $searchData['page'] = 0;
        $searchData['pageSize'] = 100;
        $products = $retval = [];
        do {
            $result = $this->request(self::$apiUrl['productSearch'], 'POST', '', $searchData);
            if (empty($result)) {
                break;
            }
            $response = $result->toArray();
            $products = array_merge($products, $response['rows'] ?? []);
            $searchData['page']++;
        } while (count($response['rows']) > 0);
        foreach ($products as $product) {
            if (!empty($product['id'])) {
                $retval[$product['id']] = $product;
            }
        }
        return $retval;
    }

    public function addProductsToWisersell($products)
    {
        if (empty($this->pimCategories)) {
            $this->getPimCategories();
        }
        if (!is_array($products)) {
            $products = [$products];
        }
        $productData = [];
        foreach ($products as $product) {
            if ($product->level() < 1) {
                continue;
            }
            if (empty($product->getIwasku())) {
                if ($product->checkIwasku(true)) {
                    $product->save();
                } else {
                    continue;
                }
            }
            $category = $this->pimCategories[$product->getInheritedField('productCategory')] ?? $this->pimCategories['Diğer'];
            $productData[] = [
                "name" => $product->getInheritedField('name'),
                "code" => $product->getIwasku(),
                "categoryId" => $category->getWisersellCategoryId(),
                "weight" => $product->getInheritedField("packageWeight"),
                "width" => $product->getInheritedField("packageDimension1"),
                "length" => $product->getInheritedField("packageDimension2"),
                "height" => $product->getInheritedField("packageDimension3"),
                "extradata" => [
                    "Size" => $product->getVariationSize(),
                    "Color" => $product->getVariationColor()
                ],
                "subproducts" => []
            ];
            if (count($productData) >= 100) {
                break;
            }
        }
        if (empty($productData)) {
            return null;
        }
        $response = $this->request(self::$apiUrl['product'], 'POST', '', $productData);
        if (empty($response)) {
            return null;
        }
        foreach ($response->toArray() as $wisersellResponse) {
            if (
                isset($wisersellResponse['id']) && 
                isset($wisersellResponse['code']) && 
                isset($products[$wisersellResponse['code']])
            ) {
                $products[$wisersellResponse['code']]->setWisersellId($wisersellResponse['id']);
                $products[$wisersellResponse['code']]->setWisersellJson(json_encode($wisersellResponse));
                $products[$wisersellResponse['code']]->save();
                $this->wisersellProducts[$wisersellResponse['id']] = $wisersellResponse;
            }
        }
    }

    public function getWisersellProducts()
    {
        $this->wisersellProducts = $this->searchWisersellProducts([]);
        return $this->wisersellProducts;
    }



}