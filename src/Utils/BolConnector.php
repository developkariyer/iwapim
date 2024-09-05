<?php

namespace App\Utils;

use Pimcore\Model\DataObject\Marketplace;
use Pimcore\Model\DataObject\Data\Link;
use Pimcore\Model\DataObject\VariantProduct;

use App\Utils\Utility;

class BolConnector implements MarketplaceConnectorInterface
{
    private $marketplace = null;
    private $listings = [];
    private $accessToken = "";

    public function __construct(Marketplace $marketplace)
    {
        if (!$marketplace instanceof Marketplace ||
            !$marketplace->getPublished() ||
            $marketplace->getMarketplaceType() !== 'Bol.com' ||
            empty($marketplace->getBolClientId()) ||
            empty($marketplace->getBolSecret())
        ) {
            throw new \Exception("Marketplace is not published, is not Amazon or credentials are empty");
        }
        $this->marketplace = $marketplace;
    }

    // Create Access Token
    private function getAccessToken()
    {
        $apiUrl = "https://login.bol.com/token?grant_type=client_credentials";
        $credentials = $this->marketplace->getBolClientId() . ':' . $this->marketplace->getBolSecret();
        $encoded_credentials = base64_encode($credentials);

        // Headers
        $headers = [
            'Authorization: Basic ' . $encoded_credentials,
            'Accept: application/json'
        ];

        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, $apiUrl);
        curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($curl, CURLOPT_POST, true);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);

        $response = curl_exec($curl);

        if (curl_errno($curl)) {
            echo 'Curl error: ' . curl_error($curl);
        } else {
            $decoded_response = json_decode($response, true);

            // access token
            if (isset($decoded_response['access_token'])) {
                echo 'Access Token: ' . $decoded_response['access_token'];
                $this->accessToken = $decoded_response['access_token'];
            } else {
                echo 'Anahtar bulunamadi!';
            }
        }
        curl_close($curl);
    }

    // GET all offers return => .csv
    public function download($forceDownload = false)
    {

        $filename = 'tmp/'.urlencode($this->marketplace->getKey()).'.csv';
        $filenamejson = 'tmp/'.urlencode($this->marketplace->getKey()).'.json';

        if (!$forceDownload && file_exists($filename) && file_exists($filenamejson) && filemtime($filename) > time() - 86400) {
            $contentJson = file_get_contents($filenamejson);
            $this->listings = json_decode($contentJson, true);
            echo "Using cached data ";
        } else {   
            $this->listings = [];
            $processStatusApi = "";
            $entityId = "";
            $this->getAccessToken();
            $headers = [
                'Authorization: Bearer ' . $this->accessToken,
                'Accept: application/vnd.retailer.v10+json',
                'Content-Type: application/vnd.retailer.v10+json'
            ];
    
            // POST verisi
            $postData = json_encode(['format' => 'CSV']);
    
            // cURL isteği oluştur
            $ch = curl_init("https://api.bol.com/retailer/offers/export");
            curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $postData);

            $response = curl_exec($ch);
            $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    
            if ($httpCode == 202) {
                $data = json_decode($response, true);
                $processStatusApi = $data['processStatusId'] ?? null;
            }
            
            echo "Process Status Api: $processStatusApi";
            // cURL işlemi kapat
            curl_close($ch);

            // process status control
            $status = '';

            do {
                // cURL oturumu başlat
                $ch = curl_init("https://api.bol.com/shared/process-status/" . $processStatusApi);
                curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            
                // İstek gönder
                $response = curl_exec($ch);
                $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
                if (curl_errno($ch)) {
                    echo 'Curl error: ' . curl_error($ch);
                    break; 
                } else {
                    $data = json_decode($response, true);
                    $status = $data['status'] ?? ''; 
                    $entityId = $data['entityId'] ?? null;
                    echo "Status: $status, Entity Id: $entityId\n";
                }
                curl_close($ch);
                if ($status !== 'SUCCESS') {
                    exit;
                }
            } while ($status !== 'SUCCESS');

            
            // get offers 
            $headers = [
                'Authorization: Bearer ' . $this->accessToken,
                'Accept: application/vnd.retailer.v9+csv'
            ];
            $ch = curl_init("https://api.bol.com/retailer/offers/export/".$entityId);
            curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        
            // istek gonder
            $response = curl_exec($ch);
            $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            if (curl_errno($ch)) {
                echo 'Curl error: ' . curl_error($ch);
            } else {
                echo $response;
                file_put_contents($filename, $response);
                $rows = array_map('str_getcsv', explode("\n", trim($response))); 
                if (!empty($rows)) {

                    $headers = array_shift($rows);
            
                    foreach ($rows as $row) {
                        if (count($row) === count($headers)) { 
                            $this->listings[] = array_combine($headers, $row);
                        }
                    }
                }
            }
            curl_close($ch);  
            $this->addProductId();
            $this->addProductDetail();
            $this->addProductAssets();
            $this->addProductPlacement();
        }
        $jsonListings = json_encode($this->listings);
        file_put_contents($filenamejson, $jsonListings);

        echo "count listings: ".count($this->listings);
        
        return  count($this->listings);
    }


    // Get catalog product details by EAN
    private function addProductDetail()
    {
        $this->getAccessToken();

        foreach ($this->listings as &$listing) 
        {
            $ean = $listing["ean"];
            $headers = [
                'Authorization: Bearer ' . $this->accessToken,
                'Accept: application/vnd.retailer.v10+json',
            ];
    
            $ch = curl_init("https://api.bol.com/retailer/content/catalog-products/".$ean);
            curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

            $response = curl_exec($ch);
            $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    
            if (curl_errno($ch)) {
                echo 'Curl error: ' . curl_error($ch);
                exit;
            } else {
                $decoded_response = json_decode($response, true);
                if (isset($decoded_response["published"])) {
                    $listing["published"] = $decoded_response["published"];
                    
                } else {
                    echo 'published Anahtar bulunamadi veya eksik!' . "\n";
                    $listing["published"] = null; 
                }
                if (isset($decoded_response['gpc']['chunkId'])) {
                    $chunkId = $decoded_response['gpc']['chunkId'];
                    $listing["chunkId"] = $chunkId;
                } else {
                    $listing["chunkId"] = null;
                }
                $listing["attributes"] = $decoded_response["attributes"];
                if (isset($decoded_response['attributes']) && is_array($decoded_response['attributes'])) {
                    foreach ($decoded_response['attributes'] as $attribute) {
                        if (isset($attribute['id']) && $attribute['id'] === 'Title' && isset($attribute['values'][0]['value'])) {
                            $listing['title'] = $attribute['values'][0]['value'];
                            echo 'Title: ' . $listing['title'] . "\n";
                            break;
                        }
                    }
                } 
                echo $listing['title'];
            }
            curl_close($ch);
            sleep(1);
        }

    }
    // Get product placement
    private function addProductPlacement()
    {
        $this->getAccessToken();

        foreach ($this->listings as &$listing) 
        {
            $ean = $listing["ean"];
            $headers = [
                'Authorization: Bearer ' . $this->accessToken,
                'Accept: application/vnd.retailer.v10+json',
            ];
    
           
            $ch = curl_init("https://api.bol.com/retailer/products/".$ean."/placement");
            curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

            $response = curl_exec($ch);
            $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            
            if (curl_errno($ch)) {
                echo 'Curl error: ' . curl_error($ch);
            } else {
                $decoded_response = json_decode($response, true);                
                if (isset($decoded_response["url"])) {
                    $listing["url"] = $decoded_response["url"];
                    
                } else {
                    echo 'URL Anahtar bulunamadi veya eksik!' . "\n";
                    $listing["url"] = null; 
                }
                if (isset($decoded_response['categories']) && is_array($decoded_response['categories'])) {
                    foreach ($decoded_response['categories'] as $category) {
                        if (isset($category['categoryName'])) {
                            echo 'Category Name: ' . $category['categoryName'] . "\n";
                            $listing["categoryName"] = $category['categoryName'];
                            break; // İlk bulunan categoryName'i alıp döngüden çıkabilirsiniz
                        }
                    }
                } else {
                    $listing["categoryName"] = "";
                }
                echo $listing["url"];
            }
            curl_close($ch);
            sleep(1);
        
        }
    }
    // Get product assets
    private function addProductAssets()
    {
   
        $this->getAccessToken();

        foreach ($this->listings as &$listing) 
        {
            $ean = $listing["ean"];
            $headers = [
                'Authorization: Bearer ' . $this->accessToken,
                'Accept: application/vnd.retailer.v10+json',
            ];
    
           
            $ch = curl_init("https://api.bol.com/retailer/products/".$ean."/assets");
            curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

            $response = curl_exec($ch);
            $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            if (curl_errno($ch)) {
                echo 'Curl error: ' . curl_error($ch);
            } else {
                echo $response;
                $decoded_response = json_decode($response, true);  
                $url_count = 1;
                if (isset($decoded_response['assets']) && is_array($decoded_response['assets'])) {
                    foreach ($decoded_response['assets'] as $asset) {
                        if (isset($asset['variants']) && is_array($asset['variants'])) {
                            foreach ($asset['variants'] as $variant) {    
                                if (isset($variant['url'])) {
                                    echo 'Image URL'.$url_count.': ' . $variant['url'] . PHP_EOL;
                                    $listing["imageUrl" . $url_count] = $variant['url'];
                                    $url_count++; 
                                }
                            }
                        } else {
                            echo 'Variants bulunamadi.';
                        }
                    }
                } else {
                    echo 'Assets bulunamadi.';
                }
            }
            
            sleep(1);
        
        }
    }
    // Get Product ids By EAN return =>  bolProductId
    private function addProductId()
    {
        $this->getAccessToken();

        foreach ($this->listings as &$listing) 
        {
            $ean = $listing["ean"];
            $headers = [
                'Authorization: Bearer ' . $this->accessToken,
                'Accept: application/vnd.retailer.v10+json',
            ];
    
           
            $ch = curl_init("https://api.bol.com/retailer/products/".$ean."/product-ids");
            curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

            $response = curl_exec($ch);
            $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    
            if (curl_errno($ch)) {
                echo 'Curl error: ' . curl_error($ch);
            } else {
                $decoded_response = json_decode($response, true);                
                if (isset($decoded_response["bolProductId"])) {
                    $listing["bolProductId"] = $decoded_response["bolProductId"];
                    echo 'bolProductId: ' . $listing["bolProductId"] . "\n";
                } else {
                    echo 'bolProductId Anahtar bulunamadi veya eksik!' . "\n";
                    $listing["bolProductId"] = null;
                }
            }
            curl_close($ch);
            sleep(1);
        }
    }

    private function getVariants()
    {
        $this->getAccessToken();

        foreach ($this->listings as &$listing) 
        {
            $ean = $listing["ean"];
            $headers = [
                'Authorization: Bearer ' . $this->accessToken,
                'Accept-Language: nl',
                'Accept: application/json'
            ];
            
            $baseUrl = "https://api.bol.com/marketing/catalog/v1/products/" . $ean . "/variants";
            $queryParams = [
                'country-code' => 'NL',
            ];
            

            $url = $baseUrl . '?' . http_build_query($queryParams);
            $ch = curl_init($url); 
            
            curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            
            $response = curl_exec($ch);
            $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            
            curl_close($ch);  
    
            if (curl_errno($ch)) {
                echo 'cURL error: ' . curl_error($ch);
            } else {

                if ($httpCode === 200) {
                    echo $response; 
                } else {
                    echo "Request failed with HTTP code: " . $httpCode; 
                    echo "\nResponse: " . $response; 
                }
            }
            sleep(1);
        }

    }

    private function getUrlLink($listing) {
        $l = new Link();
        $l->setPath($listing["url"] ?? '');
        return $l;
    }

    private function getImage($listing) {
        $image = $listing['imageUrl1'] ?? '';
        if (!empty($image)) {
            // $imageAsset = Utility::findImageByName("Trendyol_".str_replace(["https:", "/", ".", "_", "jpg"], '', $image).".jpg");
            // if ($imageAsset) {
            //     $image = "https://mesa.iwa.web.tr/var/assets/".str_replace(" ", "%20", $imageAsset->getFullPath());
            // }
            return new \Pimcore\Model\DataObject\Data\ExternalImage($image);
        }
        return null;
    }

    private function getAttributes($listing) {
        if (!empty($listing['attributes']) && is_array($listing['attributes'])) {
            $values = array_filter(array_map(function($value) {
                return is_string($value) ? str_replace(' ', '', $value) : '';
            }, $listing['attributes']));
            
            if (!empty($values)) {
                return implode('-', $values);
            }
        }
        return '';
    }

    

    public function import($updateFlag, $importFlag)
    {

        echo "import";
        if (empty($this->listings)) {
            echo "Nothing to import\n";
        }
        $marketplaceFolder = Utility::checkSetPath(
            Utility::sanitizeVariable($this->marketplace->getKey(), 190),
            Utility::checkSetPath('Pazaryerleri')
        );
        $total = count($this->listings);
        $index = 0;

        $families = [
            'Tasnif-Edilmemiş' => []
        ];

        echo "Re-generating family trees...\n";
        foreach ($this->listings as $listing) {
            $family = "Tasnif-Edilmemiş";
            $attributeDetails = $this->getAttributes($listing);
            $attributeArray = array_map(function($attribute) {
                return $attribute['values'][0]['value'] ?? null;
            }, array_column($listing['attributes'], null, 'id'));
            if (!empty($attributeArray['Family Name'])) {
                $family = $attributeArray['Family Name'];
                $attributeDetails = trim(substr($listing['title'], strlen($attributeArray['Family Name'])));
            }
            if (!isset($families[$family])) {
                $families[$family] = [];
            }
            $families[$family][] = [
                'attributes' => $attributeDetails,
                'listing' => $listing,
            ];
        }

        foreach ($families as $family => $listings) {
            echo "Processing Family $family ...\n";
            foreach ($listings as $listing) {
                echo "    Listing {$listing['listing']['bolProductId']}:{$listing['listing']['ean']} ...";
                if ($family === 'Tasnif-Edilmemiş') {
                    $familyFolder = Utility::checkSetPath(
                        Utility::sanitizeVariable($listing['listing']['categoryName'] ?? 'Tasnif-Edilmemiş'),
                        $marketplaceFolder
                    );
                    $title = $listing['listing']['title'] ?? '';
                } else {
                    $familyFolder = Utility::checkSetPath(
                        Utility::sanitizeVariable($family),
                        Utility::checkSetPath(
                            Utility::sanitizeVariable($listing['listing']['categoryName'] ?? 'Tasnif-Edilmemiş'),
                            $marketplaceFolder
                        )
                    );
                    $title = "({$family}) " . ($listing['listing']['title'] ?? '');
                }
                VariantProduct::addUpdateVariant(
                    variant: [
                        'imageUrl' => $this->getImage($listing['listing']),
                        'urlLink' => $this->getUrlLink($listing['listing']),
                        'salePrice' => $listing['listing']['bundlePricesPrice'] ?? 0,
                        'saleCurrency' => 'EUR',
                        'title' => $title,
                        'attributes' => $listing['attributes'],
                        'uniqueMarketplaceId' => $listing['listing']['bolProductId'] ?? '',
                        'apiResponseJson' => json_encode($listing['listing']),
                        'published' => $listing['listing']['published'],
                    ],
                    importFlag: $importFlag,
                    updateFlag: $updateFlag,
                    marketplace: $this->marketplace,
                    parent: $familyFolder
                );
                echo "OK\n";
                $index++;
            }
        }

/*
        foreach ($this->listings as $listing) {
            echo "($index/$total) Processing Listing {$listing['bolProductId']}:{$listing['title']} ...";

            $path = Utility::sanitizeVariable($listing['categoryName'] ?? 'Tasnif-Edilmemiş');
            $parent = Utility::checkSetPath($path, $marketplaceFolder);
            if ($listing['bolProductId']) {
                $parent = Utility::checkSetPath(Utility::sanitizeVariable($listing['bolProductId']), $parent);
            }
            VariantProduct::addUpdateVariant(
                variant: [
                    'imageUrl' => $this->getImage($listing),
                    'urlLink' => $this->getUrlLink($listing),
                    'salePrice' => $listing['bundlePricesPrice'] ?? 0,
                    'saleCurrency' => 'TL',
                    'title' => $listing['title'] ?? '',
                    'attributes' => $this->getAttributes($listing),
                    'uniqueMarketplaceId' => $listing['id'] ?? '',
                    'apiResponseJson' => json_encode($listing, JSON_PRETTY_PRINT),
                    'published' => $listing['published'],
                ],
                importFlag: $importFlag,
                updateFlag: $updateFlag,
                marketplace: $this->marketplace,
                parent: $parent
            );
            echo "OK\n";
            $index++;
        }*/
    }

    public function downloadInventory()
    {

    }


    public function downloadOrders()
    {
    //     $this->getAccessToken();

    //     foreach ($this->listings as &$listing) 
    //     {
    //         $ean = $listing["ean"];
    //         $headers = [
    //             'Authorization: Bearer ' . $this->accessToken,
    //             'Accept: application/vnd.retailer.v10+json',
    //             'Content-Type: application/vnd.retailer.v10+json'
    //         ];

    //         $apiUrl = "https://api.bol.com/retailer/orders";
    //         $params = [
    //             "status" => "ALL",
    //             "fulfilment-method" => "ALL",
    //             "latest-change-date" => "2024-08-28"
    //         ];
            
    //         $urlWithParams = $apiUrl . '?' . http_build_query($params);
            
    //         $ch = curl_init($urlWithParams);
    //         curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
    //         curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

    //         $response = curl_exec($ch);
    //         $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            
    //         if (curl_errno($ch)) {
    //             echo 'Curl error: ' . curl_error($ch);
    //         } else {
    //             //$decoded_response = json_decode($response, true);                
    //             echo $response;
    //         }
    //         curl_close($ch);
    //         sleep(1);
        
    //     }

    }

}