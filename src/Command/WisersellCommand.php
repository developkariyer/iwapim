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



#[AsCommand(
    name: 'app:wisersell',
    description: 'connect wisersell api'
)]

class WisersellCommand extends AbstractCommand{

    protected function configure()
    {
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
        
        $listingCategories = new Category\Listing();
        $listingCategories->setUnpublished(false);
        $categories = $listingCategories->load();

        $listingObject = new Product\Listing();
        $listingObject->setUnpublished(false);
        $listingObject->setCondition("iwasku IS NOT NULL AND iwasku != ? AND (wisersellId IS NULL OR wisersellId = ?)", ['', '']);
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
                if ($product->level()==1) continue;
                $iwasku = $product->getInheritedField("iwasku");
                $categoryName = $product->getProductCategory();
                $categoryId = null;
                foreach($categories as $category){
                    if($category->getCategory() == $categoryName){
                        $categoryId = $category->getWisersellCategoryId();
                    }
                }
                echo "IWASKU: $iwasku\n";
                echo "Category Name: $categoryName\n";
                echo "Category ID: " . ($categoryId !== null ? $categoryId : 'Not found') . "\n";
                echo "--------------------\n";
            }
        }




        // sleep(2);
        // $searchData = [
        //     "code"=>"IA00500MRVE9",
        //     "page"=> 0,
        //     "pageSize"=> 10,
        // ];
        // $response = $this->productSearch($token,$searchData);
        // $decodedResponse = json_decode($response, true);
        // $id = $decodedResponse["rows"][0]['id'];

        // $productData = [
        //     [
        //         "name" => "Cam1",
        //         "code" => "AXXXXXX",
        //         "categoryId" => 256
        //     ]
        // ];
        // $this->addProduct($token, $productData);
        // sleep(5);
        // $this->productSearch($token);

        // $this->deleteCategory($token, 257);
        // sleep(5);
        // $this->getCategories($token);

        // $productData = [
        //     [
        //         "id" => 182,
        //         "name" => "Cam1 Edited",
        //         "code" => "Edited Code",
        //         "categoryId" => 256
        //     ]
        // ];
        // $this->updateProduct($token, $productData);
        // sleep(5);
        // $this->productSearch($token);


        //**********************************/
        //**search wisersell for iwasku*****/
        //**add product to wisersell*****/
        //**********************************/
        


        // $listingObject = new Product\Listing();
        // $listingObject->setUnpublished(false);
        // $listingObject->setCondition("iwasku IS NOT NULL AND iwasku != ? AND (wisersellId IS NULL OR wisersellId = ?)", ['', '']);
        // $pageSize = 1;
        // $offset = 0;
                                                            
        // while (true) {
        //     $listingObject->setLimit($pageSize);
        //     $listingObject->setOffset($offset);
        //     $products = $listingObject->load();
        //     if (empty($products)) {
        //         break;
        //     }
        //     echo "\nProcessed {$offset} ";
        //     $offset += $pageSize;
        //     foreach ($products as $product) {
        //         echo "\n iwasku değeri: " . $product->getInheritedField("iwasku");
        //         $token = $this->getAccessToken();
        //         sleep(4);
        //         // $searchData = [
        //         //     "code"=>$product->getInheritedField("iwasku"),
        //         //     "page"=> 0,
        //         //     "pageSize"=> 10,
        //         // ];
        //         //$response = $this->productSearch($token,$searchData);
        //         // $decodedResponse = json_decode($response, true);
        //         // if (isset($decodedResponse["rows"][0]['id']) && !empty($decodedResponse["rows"][0]['id'])) {
        //         //     $wisersellId = $decodedResponse["rows"][0]['id'];
        //         //     try {
        //         //         $product->setWisersellId($wisersellId); 
        //         //         $product->setWisersellJson($response);
        //         //         $product->save();
        //         //         echo "WisersellId updated successfully: " . $wisersellId;
        //         //     } catch (Exception $e) {
        //         //         echo "Error occurred while updating WisersellId: " . $e->getMessage();
        //         //     }
        //         // } else {
        //         //     echo "'id' field not found or is empty in the API response.";
        //         // }
                

        //         // $productData = [
        //         //     [
        //         //         "name" => $product->getInheritedField("name"),
        //         //         "code" => $product->getInheritedField("iwasku"),
        //         //         "categoryId" => 256
        //         //     ]
        //         // ];
        //         // $this->addProduct($token, $productData);

        //     }
        // }
        return Command::SUCCESS;
    }
    protected function getAccessToken(){
        $token_file = "/var/www/iwapim/tmp/wisersell_access_token.json";
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
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Content-Type: application/json',
            'Accept: application/json'
        ]);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
        $response = curl_exec($ch);
        if ($response === false) {
            $error = curl_error($ch);
            echo "cURL Error: $error";
        } else {
            $result = json_decode($response, true);
            if (isset($result['token'])) {
                echo "Bearer Token: " . $result['token'] . "\n";
                $token_file = "/var/www/iwapim/tmp/wisersell_access_token.json";
                if (file_exists($token_file)) {
                    unlink($token_file);
                    echo "Old token file deleted.\n";
                }
                file_put_contents($token_file, json_encode(['token' => $result['token']], JSON_PRETTY_PRINT));
                echo "New token saved to file.\n";
            } else {
                echo "Failed to get bearer token. Response: " . $response . "\n";
            }
        } 
        curl_close($ch);
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
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Content-Type: application/json',
            'Accept: application/json',
            'Authorization: Bearer ' . $token
        ]);
        
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
        $response = curl_exec($ch);
        if ($response === false) {
            $error = curl_error($ch);
            echo "cURL Error: $error";
        } else {
            echo "Response: " . $response . "\n";
            $result = json_decode($response, true);
            echo "Result: " . print_r($result, true) . "\n";
        }
        return $response;
    }
    protected function getCategories($token){
        $url = "https://dev2.wisersell.com/restapi/category"; 
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Content-Type: application/json',
            'Accept: application/json',
            'Authorization: Bearer ' . $token
        ]);
        $response = curl_exec($ch);
        if ($response === false) {
            $error = curl_error($ch);
            echo "cURL Error: $error";
        } else {
            echo "Response: " . $response . "\n";
            $result = json_decode($response, true);
            echo "Result: " . print_r($result, true) . "\n";
        }
    }
    protected function addCategory($token,$categories){
        $url = "https://dev2.wisersell.com/restapi/category"; 
        $data = array_map(function($category) {
            return ["name" => $category];
        }, $categories);
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Content-Type: application/json',
            'Accept: application/json',
            'Authorization: Bearer ' . $token
        ]);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
        $response = curl_exec($ch);
        if ($response === false) {
            $error = curl_error($ch);
            echo "cURL Error: $error";
        } else {
            echo "Response: " . $response . "\n";
            $result = json_decode($response, true);
            return $result;
            echo "Result: " . print_r($result, true) . "\n";
        }
    }
    protected function deleteCategory($token,$categoryId){
        $url = "https://dev2.wisersell.com/restapi/category/". $categoryId; 
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "DELETE");
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Content-Type: application/json',
            'Accept: application/json',
            'Authorization: Bearer ' . $token
        ]);
        $response = curl_exec($ch);
        if ($response === false) {
            $error = curl_error($ch);
            echo "cURL Error: $error";
        } else {
            echo "Response: " . $response . "\n";
            $result = json_decode($response, true);
            echo "Result: " . print_r($result, true) . "\n";
        }
    }
    protected function addProduct($token,$data){
        $url = "https://dev2.wisersell.com/restapi/product"; 
        
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Content-Type: application/json',
            'Accept: application/json',
            'Authorization: Bearer ' . $token
        ]);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
        $response = curl_exec($ch);
        if ($response === false) {
            $error = curl_error($ch);
            echo "cURL Error: $error";
        } else {
            echo "Response: " . $response . "\n";
            $result = json_decode($response, true);
            echo "Result: " . print_r($result, true) . "\n";
        }
    }
    protected function updateProduct($token,$data){
        $url = "https://dev2.wisersell.com/restapi/product"; 
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "PUT");
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Content-Type: application/json',
            'Accept: application/json',
            'Authorization: Bearer ' . $token
        ]);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
        $response = curl_exec($ch);
        if ($response === false) {
            $error = curl_error($ch);
            echo "cURL Error: $error";
        } else {
            echo "Response: " . $response . "\n";
            $result = json_decode($response, true);
            echo "Result: " . print_r($result, true) . "\n";
        }
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
        $result = $this->addCategory($token, $data);
        foreach ($result as $wisersellCategory) {
            foreach ($categories as $category) {
                if ($category->getCategory() === $wisersellCategory['name']) {
                    $category->setWisersellCategoryId($wisersellCategory['id']);
                    $category->save();
                    break;
                }
            }
        }
    }
    protected function addProductByIwapim(){

        // urunu gerekli filtrelere gore getir
        // urunun iwasku bilgisini al
        // iwasku bilgisi ile wisersellde urun ara
        // urun varsa urunun id sini al iwapim tarafinda kaydet donen urun bilgisini json olarak ve wisersellid olarak kaydet
        // urun yoksa: 
                // urunun category bilgisini al
                // urunun category bilgisine gore wiserselldeki category id yi bul
                // urunun diger bilgileri al
                // urunu wisersellde olustur
                // olusturulan urunun id sini iwapim tarafinda kaydet donnen urun bilgisini json olarak kaydet
        
                

        




        //$token = $this->getAccessToken();
        //sleep(3);

        $listingCategories = new Category\Listing();
        $listingCategories->setUnpublished(false);
        $categories = $listingCategories->load();

        $listingObject = new Product\Listing();
        $listingObject->setUnpublished(false);
        $listingObject->setCondition("iwasku IS NOT NULL AND iwasku != ? AND (wisersellId IS NULL OR wisersellId = ?)", ['', '']);
        $listingObject->setLimit(5);
        $products = $listingObject->load();
        foreach ($products as $product){
            if ($product->level()==1) continue;
            $iwasku = $product->getInheritedField("iwasku");
            $categoryName = $product->getProductCategory();
            $categoryId = null;
            foreach($categories as $category){
                if($category->getCategory() == $categoryName){
                    $categoryId = $category->getWisersellCategoryId();
                }
            }
            echo "IWASKU: $iwasku\n";
            echo "Category Name: $categoryName\n";
            echo "Category ID: " . ($categoryId !== null ? $categoryId : 'Not found') . "\n";
            echo "--------------------\n";

        }


    }
}