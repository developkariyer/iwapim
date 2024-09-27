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



#[AsCommand(
    name: 'app:wisersell',
    description: 'connect wisersell api'
)]

class WisersellCommand extends AbstractCommand{
   
    private $listings = [];
    protected function configure() {

        $this
            ->addOption('category', null, InputOption::VALUE_NONE, 'Category add wisersell')
            ->addOption('product', null, InputOption::VALUE_NONE, 'Product add wisersell')

            ;
    }
    protected function execute(InputInterface $input, OutputInterface $output): int{
        
        if ($input->getOption('category')) {
            $this->addCategoryByIwapim();
        }
        if($input->getOption('product')){
            $this->addProductByIwapim();
        }
        $token = $this->getAccessToken();

        $this->controlWisersellProduct();
        return Command::SUCCESS;
    }
    protected function getAccessToken(){
        $token_file = PIMCORE_PROJECT_ROOT."/tmp/wisersell_access_token.json";
        if (file_exists($token_file) && filesize($token_file) > 0) {
            echo "Token file exists.\n";
            $file_contents = file_get_contents($token_file);
            $token = json_decode($file_contents, true);
            if ($token === null || !isset($token['token'])) {
                echo "Invalid token file content. Fetching new token...\n";
                $this->fetchToken(); 
            } elseif ($this->isTokenExpired($token['token'])) {
                echo "Token expired. Fetching new token...\n";
                $this->fetchToken(); 
            } else {
                echo "Bearer Token: " . $token['token'] . "\n";
            }
        } else {
            echo "Token file not found or empty. Fetching new token...\n";
            $this->fetchToken();
        }
        $file_contents = file_get_contents($token_file);
        $token = json_decode($file_contents, true);
        return $token['token'];
    }
    protected function fetchToken(){
        $url = "https://dev2.wisersell.com/restapi/token"; 
        $data = [
            "email" => $_ENV['WISERSELL_DEV_USER'],
            "password" => $_ENV['WISERSELL_DEV_PASSWORD']
        ];
        $client = HttpClient::create();
        $response = $client->request('POST', $url, [
            'json' => $data,
            'headers' => [
                'Content-Type' => 'application/json',
                'Accept' => 'application/json',
            ],
        ]);
        $statusCode = $response->getStatusCode();
        if ($statusCode === 200) {
            $result = $response->toArray(); 
            if (isset($result['token'])) {
                echo "Bearer Token: " . $result['token'] . "\n";
                $tokenFile = PIMCORE_PROJECT_ROOT . "/tmp/wisersell_access_token.json";
                if (file_exists($tokenFile)) {
                    unlink($tokenFile);
                    echo "Old token file deleted.\n";
                }
                file_put_contents($tokenFile, json_encode(['token' => $result['token']], JSON_PRETTY_PRINT));
                echo "New token saved to file.\n";
            } else {
                echo "Failed to get bearer token. Response: " . json_encode($result) . "\n";
            }
        } else {
            echo "Failed to make request. HTTP Status Code: $statusCode\n";
        }
    }
    protected function isTokenExpired($token){
        $tokenParts = explode('.', $token);
        if (count($tokenParts) === 3) {
            $payload = json_decode(base64_decode($tokenParts[1]), true);
            if (isset($payload['exp'])) {
                return ($payload['exp'] < time());
            }
        }
        return true;
    }
    protected function productSearch($token,$data){
        $url = "https://dev2.wisersell.com/restapi/product/search"; 
        $client = HttpClient::create();
        $response = $client->request('POST', $url, [
            'json' => $data,
            'headers' => [
                'Content-Type' => 'application/json',
                'Accept' => 'application/json',
                'Authorization' => 'Bearer ' . $token,
            ],
        ]);
        $statusCode = $response->getStatusCode();
        if ($statusCode === 200) {
            $responseContent = $response->getContent();
            echo "Response: " . $responseContent . "\n";
            $result = $response->toArray();
            echo "Result: " . print_r($result, true) . "\n";
            return $result;
        } else {
            echo "Request failed. HTTP Status Code: $statusCode\n";
        }
    }
    protected function getCategories($token){
        $url = "https://dev2.wisersell.com/restapi/category";
        $client = HttpClient::create();
        $response = $client->request('GET', $url, [
        'headers' => [
            'Content-Type' => 'application/json',
            'Accept' => 'application/json',
            'Authorization' => 'Bearer ' . $token,
        ],
        ]);
        $statusCode = $response->getStatusCode();
        if ($statusCode === 200) {
            $responseContent = $response->getContent();
            echo "Response: " . $responseContent . "\n";
            $result = $response->toArray();
            echo "Result: " . print_r($result, true) . "\n";
            return $result;
        } else {
            echo "Request failed. HTTP Status Code: $statusCode\n";
        }
    }
    protected function addCategory($token,$categories){
        $url = "https://dev2.wisersell.com/restapi/category"; 
        $data = array_map(function($category) {
            return ["name" => $category];
        }, $categories);
        $client = HttpClient::create();
        $response = $client->request('POST', $url, [
            'json' => $data,
            'headers' => [
                'Content-Type' => 'application/json',
                'Accept' => 'application/json',
                'Authorization' => 'Bearer ' . $token,
            ],
        ]);
        $statusCode = $response->getStatusCode();
        if ($statusCode === 200) {
            $responseContent = $response->getContent();
            echo "Response: " . $responseContent . "\n";
            $result = $response->toArray();
            echo "Result: " . print_r($result, true) . "\n";
            return $result;
        } else {
            echo "Request failed. HTTP Status Code: $statusCode\n";
        }
    }
    protected function addProduct($token,$data){
        $url = "https://dev2.wisersell.com/restapi/product"; 
        $client = HttpClient::create();
        $response = $client->request('POST', $url, [
            'json' => $data,
            'headers' => [
                'Content-Type' => 'application/json',
                'Accept' => 'application/json',
                'Authorization' => 'Bearer ' . $token,
            ],
        ]);
        $statusCode = $response->getStatusCode();
        if ($statusCode === 200) {
            $responseContent = $response->getContent();
            echo "Response: " . $responseContent . "\n";
            $result = $response->toArray();
            echo "Result: " . print_r($result, true) . "\n";
            return $result;
        } else {
            echo "Request failed. HTTP Status Code: $statusCode\n";
        }
    }
    protected function updateProduct($token,$data,$id){
        $client = Httpclient::create();
        $response = $client->request('PUT', 'https://dev2.wisersell.com/restapi/product/'.$id, [
            'json' => $data,
            'headers' => [
                'Content-Type' => 'application/json',
                'Accept' => 'application/json',
                'Authorization' => 'Bearer ' . $token,
            ],
        ]);
        $statusCode = $response->getStatusCode();
        if ($statusCode === 200) {
            $responseContent = $response->getContent();
            echo "Response: " . $responseContent . "\n";
        } else {
            echo "Request failed. HTTP Status Code: $statusCode\n";
        }
    }
    protected function productControl($token,$key){
        $searchData = [
            "code"=>$key,
            "page"=> 0,
            "pageSize"=> 10,
        ];
        $response = $this->productSearch($token,$searchData);
        return $response;
    }
    protected function categoryControl($token, $data) {
        $apiCategories = $this->getCategories($token);
        $apiCategoryMap = [];
        foreach ($apiCategories as $apiCategory) {
            $apiCategoryMap[$apiCategory["name"]] = $apiCategory["id"];
        }
        $listingObject = new Category\Listing();
        $categories = $listingObject->load(); 
        $pimcoreCategoryMap = [];
        foreach ($categories as $pimcoreCategory) {
            $pimcoreCategoryMap[$pimcoreCategory->getCategory()] = $pimcoreCategory;
        }
        $newCategories = [];
        foreach ($data as $categoryName) {
            if (isset($apiCategoryMap[$categoryName])) {
                $categoryId = $apiCategoryMap[$categoryName];
                if (isset($pimcoreCategoryMap[$categoryName])) {
                    $pimcoreCategory = $pimcoreCategoryMap[$categoryName];
                    $pimcoreCategory->setWisersellCategoryId($categoryId);
                    $pimcoreCategory->save();
                    echo "Category updated: " . $categoryName . "\n";
                }
            } else {
                echo "New Category Detected: $categoryName\n";
                $newCategories[] = $categoryName;
            }
        }
        return $newCategories;
    }

    protected function addCategoryByIwapim(){
        $token = $this->getAccessToken();
        sleep(3);
        $listingObject = new Category\Listing();
        $categories = $listingObject->load();
        $data = [];
        foreach ($categories as $category) {
            $data[] = $category->getCategory();
        }
        $newCategories = $this->categoryControl($token,$data);    
        sleep(3);
        if(!empty($newCategories)){
            $result = $this->addCategory($token, $newCategories);
            foreach ($result as $wisersellCategory) {
                foreach ($categories as $category) {
                    if ($category->getCategory() === $wisersellCategory['name']) {
                        $category->setWisersellCategoryId($wisersellCategory['id']);
                        $category->save();
                        echo "Category Saved: " . $category->getCategory() . "\n";
                        break;
                    }
                }
            }
        }    
    }
    protected function addProductByIwapim(){
        $token = $this->getAccessToken();
        sleep(3);
        $listingCategories = new Category\Listing();
        $listingCategories->setUnpublished(false);
        $categories = $listingCategories->load();
        $listingObject = new Product\Listing();
        $listingObject->setUnpublished(false);
        $listingObject->setCondition("iwasku IS NOT NULL AND iwasku != ? AND (wisersellId IS NULL OR wisersellId = ?)", ['', '']);
        $pageSize = 10;
        $offset = 0;
        while (true) {
            $listingObject->setLimit($pageSize);
            $listingObject->setOffset($offset);
            $products = $listingObject->load();
            if (empty($products)) {
                break;
            }
            echo "\nProcessed {$offset} ";
            $offset += $pageSize;
            foreach ($products as $product) {
                if ($product->level()!=1) continue;
                $iwasku = $product->getInheritedField("iwasku");
                sleep(3);
                $response = $this->productControl($token,$iwasku);
                if($response['count']===0) {
                    $productName = $product->getInheritedField("name"); 
                    $categoryName = $product->getInheritedField("productCategory");
                    $categoryId = null;
                    foreach($categories as $category){
                        if($category->getCategory() == $categoryName){
                            $categoryId = $category->getWisersellCategoryId();
                            break;
                        }
                    }
                    if($categoryId==null) continue;
                    $variationSize = $product->getInheritedField("variationSize") ?? null;
                    $variationColor = $product->getInheritedField("variationColor") ?? null;
                    $width = $product->getInheritedField("packageDimension1") ?? null;
                    $length = $product->getInheritedField("packageDimension2") ?? null;
                    $height = $product->getInheritedField("packageDimension3") ?? null;
                    $weight = $product->getInheritedField("packageWeight") ?? null;
                    $extraData = [
                        [
                            "variationSize" => $variationSize,
                            "variationColor" => $variationColor
                        ]
                    ];
                    $productData = [
                        [
                            "name" => $productName,
                            "code" => $iwasku,
                            "categoryId" => $categoryId,
                            "weight" => $weight,
                            "width" => $width,
                            "length" => $length,
                            "height" => $height,
                            "extradata"=> $extraData,
                            "subproducts" => []
                        ]
                    ];
                    sleep(2);
                    $result = $this->addProduct($token, $productData);
                    if(isset($result[0]['id'])){
                        $wisersellId = $result[0]['id'];
                        try {
                            $product->setWisersellId($wisersellId); 
                            $product->setWisersellJson(json_encode($result));
                            $product->save();
                            echo "WisersellId updated successfully: " . $wisersellId;
                        } catch (Exception $e) {
                            echo "Error occurred while updating WisersellId: " . $e->getMessage();
                        }
                        echo "New Product added successfully\n";
                    } else {
                        echo "'id' field not found or is empty in the API response.";
                    }
                }
                else {
                    echo "\n\n\n!!!!!!!!!!!!!!UPDATED PRODUCT!!!!!!!!!!!!!!!!!!!!!!\n\n\n";
                    $wisersellId = $response['rows'][0]['id'];
                    try {
                        $product->setWisersellId($wisersellId); 
                        $product->setWisersellJson(json_encode($response));
                        $product->save();
                        echo "WisersellId updated successfully: " . $wisersellId;
                    } catch (Exception $e) {
                        echo "Error occurred while updating WisersellId: " . $e->getMessage();
                    }
                }
            }
        }
    }
    protected function downloadWisersellProduct(){
        $filenamejson =  PIMCORE_PROJECT_ROOT. '/tmp/wisersell.json';
        if ( file_exists($filenamejson) && filemtime($filenamejson) > time() - 86400) {
            $contentJson = file_get_contents($filenamejson);
            $this->listings = json_decode($contentJson, true);          
            echo "Using cached data ";
        }
        else {
            $token = $this->getAccessToken();
            $this->listings = [];
            $page = 0;
            $pageSize = 3;
            $searchData = [
                "page" => $page,
                "pageSize" => $pageSize
            ];
            $response = $this->productSearch($token,$searchData);
            sleep(2);
            $this->listings = $response['rows'];
            while ($response['count'] > 0) {
                $page++;
                $searchData = [
                    "page" => $page,
                    "pageSize" => $pageSize
                ];
                $response = $this->productSearch($token,$searchData);
                sleep(2);
                $this->listings = array_merge($this->listings, $response['rows']);
                if(count($response['rows'])<$pageSize)
                    break;
            }  
            file_put_contents($filenamejson, json_encode($this->listings));
        }
        $jsonListings = json_encode($this->listings);
        file_put_contents($filenamejson, $jsonListings);
        echo "count listings: ".count($this->listings)."\n";
    }
    protected function controlWisersellProduct(){
        $this->downloadWisersellProduct();
        
        $listingObject = new Product\Listing();
        $listingObject->setUnpublished(false);
        $pageSize = 50;
        $offset = 0;
        while (true) {
            $listingObject->setLimit($pageSize);
            $listingObject->setOffset($offset);
            $products = $listingObject->load();
            if (empty($products)) {
                break;
            }
            echo "\nProcessed {$offset} ";
            $offset += $pageSize;
            foreach ($products as $product) {
                $iwasku = $product->getInheritedField("iwasku");
                foreach ($this->listings as $listing ) {
                    if ($listing['code'] === $iwasku) {
                        echo "Product found: " . $iwasku . "\n";
                        break;
                    }
                }
            }
        }


        return null;
    }




}
