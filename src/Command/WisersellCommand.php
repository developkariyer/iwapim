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


#[AsCommand(
    name: 'app:wisersell',
    description: 'Get product info'
)]

class WisersellCommand extends AbstractCommand
{

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $this->getAccessToken();
        // $listingObject = new Product\Listing();
        // $listingObject->setUnpublished(false);
        // $listingObject->setCondition("iwasku IS NOT NULL AND iwasku != ? AND (wisersellId IS NULL OR wisersellId = ?)", ['', '']);
        // $pageSize = 50;
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
        //             echo "\n iwasku değeri: " . $product->getIwasku();
        //             get access token
        //             search
        //             if found
        //                 update product
        //             else
        //                 create product
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
            echo "Result: " . $result;
            // if (isset($result['taken'])) {
            //     echo "Bearer Token: " . $result['taken'];
            //     $token_file = "/var/www/iwapim/tmp/wisersell_access_token.json";
            //     if (file_exists($token_file)) {
            //         unlink($token_file); 
            //         echo "Old token file deleted.\n";
            //     }
            //     file_put_contents($token_file, json_encode(['token' => $result['taken']], JSON_PRETTY_PRINT));
            //     echo "New token saved to file.\n";
            // } else {
            //     echo "Failed to get bearer token. Response: " . $response;
            // }
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
}