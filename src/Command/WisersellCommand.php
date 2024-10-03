<?php

namespace App\Command;

use Pimcore\Console\AbstractCommand;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Input\InputOption;
use Pimcore\Model\DataObject\Product;
use App\Model\DataObject\VariantProduct;
use Pimcore\Model\DataObject\Category;
use Symfony\Component\HttpClient\HttpClient;
use Symfony\Component\HttpClient\ScopingHttpClient;
use App\Utils\Utility;
use Exception;


#[AsCommand(
    name: 'app:wisersell',
    description: 'connect wisersell api'
)]

class WisersellCommand extends AbstractCommand
{
    private $wisersellListings = [];
    protected $wisersellProducts = [];
    private $iwapimListings = [];
    private static $apiServer = 'https://dev2.wisersell.com/restapi/';
    private static $apiUrl = [
        'productSearch' => 'product/search',
        'category' => 'category',
        'product'=> 'product',
    ];
    private $httpClient = null;
    protected $categoryList = [];
    protected $wisersellToken = null;

    protected function configure() 
    {
        $this
            ->addOption('category', null, InputOption::VALUE_NONE, 'Category add wisersell')
            ->addOption('product', null, InputOption::VALUE_NONE, 'Product add wisersell')
            ->addOption('download', null, InputOption::VALUE_NONE, 'Force download of wisersell products')
            ;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $this->httpClient = HttpClient::create();
        $forceDownload = $input->getOption('download', false);
        if ($input->getOption('category')) {
            $this->syncCategories();
        }
        if($input->getOption('product')){
            $this->syncProducts($forceDownload);
        }
        return Command::SUCCESS;
    }

    protected function syncCategories()
    {
        echo "Syncing Categories...\n";
        $wisersellCategories = $this->getWisersellCategories();
        $pimCategories = $this->getPimCategories();
        foreach ($wisersellCategories as $wisersellCategory) {
            echo "Processing {$wisersellCategory['name']}... ";
            if (isset($pimCategories[$wisersellCategory['name']])) {
                $pimCategory = $pimCategories[$wisersellCategory['name']];
                if ($pimCategory->getWisersellCategoryId() != $wisersellCategory['id']) {
                    $pimCategory->setWisersellCategoryId($wisersellCategory['id']);
                    echo "Updated PIM... ";
                    $pimCategory->save();
                }
                unset($pimCategories[$wisersellCategory['name']]);
                echo "Done\n";
                continue;
            } 
            echo "Adding to PIM... ";
            $category = new Category();
            $category->setKey($wisersellCategory['name']);
            $category->setParent(Utility::checkSetPath('Kategoriler', Utility::checkSetPath('Ayarlar')));
            $category->setCategory($wisersellCategory['name']);
            $category->setWisersellCategoryId($wisersellCategory['id']);
            $category->save();
            echo "Done\n";
        }
        foreach ($pimCategories as $pimCategory) {
            echo "Adding to {$pimCategory->getCategory()} to Wisersell... ";
            $response = $this->addCategoryToWisersell($pimCategory->getCategory());
            if (isset($response[0]['id'])) {
                $pimCategory->setWisersellCategoryId($response[0]['id']);
                $pimCategory->save();
            } else {
                echo "Failed to add category to Wisersell: " . json_encode($response) . "\n";
            }
            echo "Done\n";
        }
    }

    protected function loadWisersellProducts($forceDownload = false)
    {
        $this->wisersellProducts = json_decode(Utility::getCustomCache('wisersell_products.json', PIMCORE_PROJECT_ROOT . '/tmp'), true);
        if (!(empty($this->wisersellProducts) || $forceDownload)) {
            echo "Loaded Wisersell Products from cache\n";
            return;
        }
        $wisersellProducts = [];
        $pageSize = 100;
        $page = 0;
        do {
            $response = $this->getWisersellProduct([
                "page" => $page,
                "pageSize" => $pageSize
            ]);
            $wisersellProducts = array_merge($wisersellProducts, $response['rows']);
            $page++;
            echo "Loaded ".($page*$pageSize)." products from Wisersell\n";
        } while (count($response['rows']) == $pageSize);
        $this->wisersellProducts = [];
        foreach ($wisersellProducts as $product) {
            $this->wisersellProducts[$product['id']] = $product;
        }
        Utility::setCustomCache('wisersell_products.json', PIMCORE_PROJECT_ROOT . '/tmp', json_encode($this->wisersellProducts));
        echo "Loaded ".count($this->wisersellProducts)." products from Wisersell\n";
        sleep(1);
    }

    protected function searchIwaskuInWisersellProducts($iwasku) {
        foreach ($this->wisersellProducts as $product) {
            if ($product['code'] === $iwasku) {
                return $product['id'];
            }
        }
        return null;
    }

    protected function syncProducts($forceDownload = false)
    {
        $this->syncCategories();
        $this->loadWisersellProducts($forceDownload);
        echo "Syncing Products...\n";

        $listingObject = new Product\Listing();
        $listingObject->setUnpublished(false);
        $listingObject->setCondition("iwasku IS NOT NULL AND iwasku != ''");
        $pageSize = 50;
        $offset = 0;
        $productBucket = [];
        $subProductBucket = [];
        while (true) {
            $listingObject->setLimit($pageSize);
            $listingObject->setOffset($offset);
            $products = $listingObject->load();
            if (empty($products)) {
                break;
            }
            $offset += $pageSize;
            foreach ($products as $product) {
                if ($product->level() != 1) {
                    continue;
                }
                echo "Processing {$product->getIwasku()}... ";
                if ($id = $this->searchIwaskuInWisersellProducts($product->getIwasku())) {
                    echo "Found in Wisersell... ";
                    if ($id != $product->getWisersellId()) {
                        $product->setWisersellId($id);
                        $product->setWisersellJson(json_encode($this->wisersellProducts[$id]));
                        $product->save();
                        echo "Updated PIM... ";
                    }
                    unset($this->wisersellProducts[$id]);
                    echo "Done\n";
                    continue;
                }
                if (count($product->getBundleProducts())) {
                    $subProductBucket[] = $product;
                    echo "Added to subProductBucket\n";
                    continue;
                }
                $productBucket[$product->getIwasku()] = $product;
                echo "Added to productBucket\n";
                if (count($productBucket) >= 50) {
                    //$this->addProductBucketToWisersell($productBucket);
                    $productBucket = [];
                }
            }
            echo "\nProcessed {$offset} ";
        }
        if (!empty($productBucket)) {
            //$this->addProductBucketToWisersell($productBucket);
        }
        foreach ($this->wisersellProducts as $wisersellProduct) {
            echo "Adding Wisersell Product {$wisersellProduct['name']} to PIM... ";
            $product = new Product();
            $product->setPublished(false);
            $product->setParent(242819); // Wisersell Error Product!!!!
            $product->setKey($wisersellProduct['name']);
            $product->setDescription(json_encode($wisersellProduct, JSON_PRETTY_PRINT));
            $product->setWisersellJson(json_encode($wisersellProduct));
            $product->setWisersellId($wisersellProduct['id']);
            $product->save();
            echo "Done\n";
        }
    }

    protected function addProductBucketToWisersell($productBucket)
    {
        $this->getPimCategories();
        $productData = [];
        foreach ($productBucket as $product) {
            $category = $this->categoryList[$product->getInheritedField('productCategory')] ?? $this->categoryList['Diğer'];
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
        }
        $result = $this->addProduct($productData);
        foreach ($result as $response) {
            if (isset($response['id']) && isset($productBucket[$response['code']])) {
                $productBucket[$response['code']]->setWisersellId($response['id']);
                $productBucket[$response['code']]->setWisersellJson(json_encode($response));
                $productBucket[$response['code']]->save();
            }
        }
        echo "Added ".count($result)." products to Wisersell\n";
    }

    protected function prepareToken()
    {
        if (!empty($this->wisersellToken) && Utility::checkJwtTokenValidity($this->wisersellToken)) {
            return;
        }
        $token = $this->getAccessToken();
        $this->wisersellToken = $token;
        $this->httpClient = ScopingHttpClient::forBaseUri($this->httpClient, static::$apiServer, [
            'headers' => [
                'Authorization' => "Bearer $token",
                'Accept' => 'application/json',
                'Content-Type' => 'application/json',
            ],
        ]); 
    }

    protected function getAccessToken()
    {
        $token = json_decode(Utility::getCustomCache('wisersell_access_token.json', PIMCORE_PROJECT_ROOT . '/tmp'), true);
        if (Utility::checkJwtTokenValidity($token['token'] ?? '')) {
            echo "Bearer Token: " . $token['token'] . "\n";
            return $token['token'];
        }
        echo "Token file not found or empty. Fetching new token...\n";
        return $this->fetchToken();
    }

    protected function fetchToken()
    {
        $url = "https://dev2.wisersell.com/restapi/token"; 
        $client = HttpClient::create();
        $response = $client->request('POST', $url, [
            'json' => [
                "email" => $_ENV['WISERSELL_DEV_USER'],
                "password" => $_ENV['WISERSELL_DEV_PASSWORD']
            ],
            'headers' => [
                'Content-Type' => 'application/json',
                'Accept' => 'application/json',
            ],
        ]);
        if ($response->getStatusCode() !== 200) {
            throw new Exception("Failed to get bearer token. HTTP Status Code: {$response->getContent()}");
        }
        $result = $response->toArray(); 
        if (empty($result['token'])) {
            throw new Exception("Failed to get bearer token. Response: " . json_encode($result));
        }
        echo "Bearer Token: " . $result['token'] . "\n";
        Utility::setCustomCache('wisersell_access_token.json', PIMCORE_PROJECT_ROOT . '/tmp', json_encode($result));
        echo "New token saved to file.\n";
        return $result['token'];
    }

    protected function addProduct($data)
    {
        $result = $this->request(self::$apiUrl['product'], 'POST', '', $data);
        return $result->toArray();
    }

    protected function request($apiEndPoint, $type, $parameter, $json = [])
    {
        $this->prepareToken();
        $response = $this->httpClient->request($type, $apiEndPoint . $parameter, ['json' => $json]);
        if ($response->getStatusCode() !== 200) {
            echo "Failed to {$type} {$apiEndPoint}{$parameter}:".$response->getContent()."\n";
            return null;
        }
        usleep(1100000);
        return $response;
    }

    protected function getPimCategories()
    {
        $listingObject = new Category\Listing();
        $listingObject->setUnpublished(true);
        $categories = $listingObject->load();
        $this->categoryList = [];
        foreach ($categories as $category) {
            $this->categoryList[$category->getCategory()] = $category;
        }
        return $this->categoryList;
    }

    protected function getWisersellCategories()
    {
        $result = $this->request(self::$apiUrl['category'], 'GET', '');
        return $result->toArray(); // array of ['id', 'name']
    }

    protected function addCategoryToWisersell($category)
    {
        $result = $this->request(self::$apiUrl['category'], 'POST', '', [['name' => $category]]);
        return $result->toArray();
    }

    protected function getWisersellProduct($data)
    {
        $result = $this->request(self::$apiUrl['productSearch'], 'POST', '', $data);
        return $result->toArray();
    }

}
