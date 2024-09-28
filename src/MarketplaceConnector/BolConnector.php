<?php

namespace App\MarketplaceConnector;

use Symfony\Component\HttpClient\HttpClient;
use Symfony\Component\HttpClient\ScopingHttpClient;

use Pimcore\Model\DataObject\Marketplace;
use Pimcore\Model\DataObject\VariantProduct;

use App\Utils\Utility;
use App\MarketplaceConnector\MarketplaceConnectorAbstract;


class BolConnector extends MarketplaceConnectorAbstract
{
    private $listingsInfo = [];
    private static $loginTokenUrl = "https://login.bol.com/token?grant_type=client_credentials";
    private static $offerExportUrl = "/retailer/offers/export/"; // https://api.bol.com
    private static $processStatusUrl = "/shared/process-status/";
    private static $productsUrl  = "/retailer/products/";
    private static $catalogProductsUrl = "/retailer/content/catalog-products/";
    private static $commissionUrl = "/retailer/commission/";
    private static $insightsForecastUrl = "/retailer/insights/sales-forecast/";

    private $httpClient = null;
    public static $marketplaceType = 'Bol.com';

    public function __construct(Marketplace $marketplace)
    {
        parent::__construct($marketplace);
        $this->httpClient = HttpClient::create();
        $this->prepareToken();
    }

    protected function setHttpClientAuthorization()
    {
        $this->httpClient = ScopingHttpClient::forBaseUri($this->httpClient, 'https://api.bol.com/', [
            'headers' => [
                'Authorization' => 'Bearer ' . $this->marketplace->getBolJwtToken(),
                'Accept' => 'application/vnd.retailer.v10+json',
                'Content-Type' => 'application/vnd.retailer.v10+json'
            ],
        ]);         
    }

    protected function prepareToken()
    {
        if (!Utility::checkJwtTokenValidity($this->marketplace->getBolJwtToken())) {
            $response = $this->httpClient->request('POST', static::$loginTokenUrl, [
                'headers' => [
                    'Authorization' => 'Basic ' . base64_encode("{$this->marketplace->getBolClientId()}:{$this->marketplace->getBolSecret()}"),
                    'Accept' => 'application/json'
                ]
            ]);
            if ($response->getStatusCode() !== 200) {
                throw new \Exception('Failed to get JWT token from Bol.com');
            }
            $decodedResponse = json_decode($response->getContent(), true);
            $this->marketplace->setBolJwtToken($decodedResponse['access_token']);
            $this->setHttpClientAuthorization();
        } 
    }

    protected function requestOfferReport()
    {
        $this->prepareToken();
        $response = $this->httpClient->request('POST', static::$offerExportUrl, ['json' => ['format' => 'CSV']]);
        if ($response->getStatusCode() !== 202) {
            throw new \Exception('Failed to get offer report from Bol.com');
        }
        $decodedResponse = json_decode($response->getContent(), true);
        if ($decodedResponse['status'] !== 'SUCCESS' && $decodedResponse['status'] !== 'PENDING') {
            throw new \Exception('Failed to get offer report from Bol.com');
        }
        return $decodedResponse;
    }

    protected function reportStatus($decodedResponse)
    {
        $status = $decodedResponse['status'] === 'SUCCESS';
        $statusUrl = $decodedResponse['links'][0]['href'] ?? static::$processStatusUrl . ($decodedResponse['processStatusId'] ?? '');

        while (!$status) {
            echo "  Waiting for report...\n";
            sleep(2);
            $response = $this->httpClient->request('GET', $statusUrl);
            if ($response->getStatusCode() !== 200) {
                throw new \Exception('Failed to get offer report from Bol.com');
            }
            $decodedResponse = json_decode($response->getContent(), true);
            switch ($decodedResponse['status'] ?? '') {
                case 'SUCCESS':
                    $status = true;
                    break;
                case 'PENDING':
                    $status = false;
                    break;
                default: throw new \Exception('Failed to get offer report from Bol.com: '. $response->getContent());
            }
        }
        if (empty($decodedResponse['entityId'])) {
            throw new \Exception('Failed to get offer report from Bol.com.');
        }
        return $decodedResponse['entityId'] ?? [];
    }

    protected function downloadOfferReport($forceDownload = false)
    {
        $report = Utility::getCustomCache('OFFERS_EXPORT_REPORT.cvs', PIMCORE_PROJECT_ROOT. "/tmp/marketplaces/{$this->marketplace->getKey()}");
        if ($report === false || $forceDownload) {
            echo "Requesting offer report from Bol.com\n";
            $entityId = $this->reportStatus($this->requestOfferReport());
            echo static::$offerExportUrl . $entityId ." \n";
            $response = $this->httpClient->request('GET', static::$offerExportUrl . $entityId, ['headers' => ['Accept' => 'application/vnd.retailer.v10+csv']]);
            if ($response->getStatusCode() !== 200) {
                throw new \Exception('Failed to get offer report from Bol.com:'.$response->getContent());
            }
            $report = $response->getContent();
            Utility::setCustomCache('OFFERS_EXPORT_REPORT.cvs', PIMCORE_PROJECT_ROOT. "/tmp/marketplaces/{$this->marketplace->getKey()}", $report);
        } else {
            echo "Using cached report\n";
        }
        return $report;
    }

    protected function downloadExtra($apiEndPoint, $type, $parameter, $query = [])
    {
        $response = $this->httpClient->request($type, $apiEndPoint . $parameter, ['query' => $query]);
        if ($response->getStatusCode() !== 200) {
            echo "Failed to {$type} {$apiEndPoint}{$parameter}:".$response->getContent()."\n";
            return null;
        }
        echo "{$apiEndPoint}{$parameter} ";
        return json_decode($response->getContent(), true);
    }

    protected function downloadForecast($offerId)
    {   // not used for the moment
        $response = $this->httpClient->request('GET', static::$insightsForecastUrl, ['query' => ['offer-id' => $offerId, 'weeks-ahead' => 6]]);
        if ($response->getStatusCode() !== 200) {
            echo "Failed to get forecast for $offerId:".$response->getContent()."\n";
            return null;
        }
        echo "Forecast ";
        return json_decode($response->getContent(), true);
    }

    protected function getListings($report)
    {
        $sleep = 200000;
        $rows = array_map('str_getcsv', explode("\n", trim($report)));
        $headers = array_shift($rows);
        $this->listings = [];
        $totalCount = count($rows);
        $index = 0;
        foreach ($rows as $row) {
            $index++;
            if (count($row) === count($headers)) {
                $rowData = array_combine($headers, $row);
                $ean = $rowData['ean'];
                echo "($index/$totalCount) Downloading $ean ";
                $this->listings[$ean] = $rowData;
                $this->listings[$ean]['catalog'] = $this->downloadExtra(static::$catalogProductsUrl, 'GET', $ean);
                usleep($sleep);
                $this->listings[$ean]['assets'] = $this->downloadExtra(static::$productsUrl, 'GET', "$ean/assets", ['usage' => 'IMAGE']);
                usleep($sleep);
                $this->listings[$ean]['placement'] = $this->downloadExtra(static::$productsUrl, 'GET', "$ean/placement");
                usleep($sleep);
                $this->listings[$ean]['commission'] = $this->downloadExtra(static::$commissionUrl, 'GET', $ean, ['condition' => 'NEW', 'unit-price' => $rowData['bundlePricesPrice']]);
                usleep($sleep);
                $this->listings[$ean]['product-ids'] = $this->downloadExtra(static::$productsUrl, 'GET', "$ean/product-ids");
                usleep($sleep);
                Utility::setCustomCache("EAN_{$ean}.json", PIMCORE_PROJECT_ROOT. "/tmp/marketplaces/{$this->marketplace->getKey()}", json_encode($this->listings[$ean]));
                usleep($sleep);
                echo "OK\n";
                usleep($sleep);
            }
        }
    }

    public function download($forceDownload = false)
    {
        $this->listings = json_decode(Utility::getCustomCache('BOL_LISTINGS.json', PIMCORE_PROJECT_ROOT. "/tmp/marketplaces/{$this->marketplace->getKey()}"), true);
        if (empty($this->listings) || $forceDownload) {
            $this->getListings($this->downloadOfferReport($forceDownload));
            Utility::setCustomCache('BOL_LISTINGS.json', PIMCORE_PROJECT_ROOT. "/tmp/marketplaces/{$this->marketplace->getKey()}", json_encode($this->listings));
        } else {
            echo "Using cached listings\n";
        }
        return;
    }

    private function addProductInfo($url,$urlEnd="",$headers,$method,$postData=null,$boolEan=false,$processStatusId=null,$entityId=null,$csv=false)
    {
        $this->getAccessToken();
        $control = false;
        foreach ($headers as &$header) {
            if (strpos($header, 'Authorization:') === 0) {
                $header = 'Authorization: Bearer ' . $this->accessToken;
                echo "Anahtar Guncellendi\n";
                break; 
            }
        }
        if(count($this->listings) === 0) {
            $this->listings[] = [];
            $control = true;
        }
        $firstElementSkipped = false;
        foreach ($this->listings as &$listing) {
            if (!$firstElementSkipped && $boolEan) {
                $firstElementSkipped = true; 
                continue;
            }
            $urlFunction = "";
            if ($boolEan) $urlFunction = $url.$listing["ean"].$urlEnd;
            elseif ($processStatusId) $urlFunction = $url.$processStatusId;
            elseif ($entityId) $urlFunction = $url.$entityId;
            else $urlFunction = $url;
    
            $ch = curl_init($urlFunction);
            curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

            // Post metodunu kontrol et
            if ($method === "POST") {
                curl_setopt($ch, CURLOPT_POST, true);
                curl_setopt($ch, CURLOPT_POSTFIELDS, $postData);
            }
            $response = curl_exec($ch);
            $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            if ($csv) {
                $filename = 'tmp/'.urlencode($this->marketplace->getKey()).'.csv';
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
                break;
            }
            if (curl_errno($ch)) {
                echo 'Curl error: ' . curl_error($ch);
            } else {
                $listing[$urlFunction] = json_decode($response, true);
            }
            curl_close($ch);
            sleep(1);
            if($control) break;
        }
    }

    private function singleCurl($url,$urlEnd="",$ean)
    {
        $this->getAccessToken();
        $headers = [
            'Authorization: Bearer ' . $this->accessToken,
            'Accept: application/vnd.retailer.v10+json',
            'Content-Type: application/vnd.retailer.v10+json'
        ];
        $urlFunction = $url.$ean.$urlEnd;
        $ch = curl_init($urlFunction);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        if (curl_errno($ch)) {
            echo 'Curl error: ' . curl_error($ch);
        } else {
            $decoded_response = json_decode($response, true);   
            if ($decoded_response) {
                return $decoded_response;
            } else {
                echo "Yanit cozumlenemedi veya gecersiz: $decoded_response\n";
                return null;
            }                
        }

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

    private function parseListing() 
    {
        $first = true;
        foreach ($this->listings as $listing) {
            if ($first) { 
                $first = false; 
                continue;
            }
            $imageUrl= "";
            $categoryName = "";
            $title = "";
            foreach ($listing[$this->productsUrl . $listing["ean"] . "/assets"]["assets"][0]["variants"] as $variant) 
            {
                if ($variant["size"] === "medium") {
                    $imageUrl = $variant["url"];
                    break;
                }
            }
            // Category Name 
            $placementKey = $this->productsUrl . $listing["ean"] . "/placement";
            if (isset($listing[$placementKey]["categories"]) && !empty($listing[$placementKey]["categories"])) {
                $categoryName = $listing[$placementKey]["categories"][0]["categoryName"];
            }
            else{
                $decoded_response = $this->singleCurl($this->productsUrl,"/placement",$listing["ean"]);
                if(isset($decoded_response['categories']) && is_array($decoded_response['categories'])) {
                    foreach ($decoded_response['categories'] as $category) {
                        if (isset($category['categoryName'])) {
                            echo 'Guncellenmis Category Name: ' . $category['categoryName'] . "\n";
                            $categoryName = $category['categoryName'];
                            break; 
                        }
                    }
                }
            }

            $titleKey = $this->productDetailUrl . $listing["ean"];
            if (isset($listing[$titleKey]["attributes"]) && !empty($listing[$titleKey]["attributes"])) {
                foreach ($listing[$titleKey]["attributes"] as $attribute) {
                    if ($attribute["id"] === "Title") {
                        $title = $attribute["values"][0]["value"];
                        break;
                    }
                }
            }

            // Bol Product Id
            $bolProductId = $listing[$this->productsUrl . $listing["ean"] . "/product-ids"]["bolProductId"];
            if (empty($bolProductId)) {
                $decoded_response = $this->singleCurl($this->productsUrl,"/product-ids",$listing["ean"]);
                $bolProductId = $decoded_response["bolProductId"];
                echo 'Guncellenmis Bol Product Id: ' . $decoded_response["bolProductId"] . "\n";
                
            } 

            $this->listingsInfo[] = [
                "ean" => $listing["ean"],
                "bolProductId" => $bolProductId,
                "bundlePricesPrice" => $listing["bundlePricesPrice"],
                "published" => $listing[$this->productDetailUrl.$listing["ean"]]["published"],
                "url" => $listing[$this->productsUrl.$listing["ean"]."/placement"]["url"],
                "categoryName" => $categoryName,
                "imageUrl" => $imageUrl,
                "title" => $title,
                "attributes" => $listing[$this->productDetailUrl.$listing["ean"]]["attributes"]
            ];

        }

    }

    public function import($updateFlag, $importFlag)
    {
        echo "import";
        $this->parseListing();
        if (empty($this->listingsInfo)) {
            echo "Nothing to import\n";
        }
        $marketplaceFolder = Utility::checkSetPath(
            Utility::sanitizeVariable($this->marketplace->getKey(), 190),
            Utility::checkSetPath('Pazaryerleri')
        );
        $total = count($this->listingsInfo);
        $index = 0;
    
        $families = [
            'Tasnif-Edilmemiş' => []
        ];

        echo "Re-generating family trees...\n";
        $first = true;
        foreach ($this->listingsInfo as $listing) {
            if ($first) { 
                $first = false; 
                continue;
            }
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

            foreach ($listings as $listing) 
            {
                echo "Listing {$listing['listing']['bolProductId']}:{$listing['listing']['ean']} ...";
  
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
                        'imageUrl' => $this->getCachedImage($listing['imageUrl'] ?? ''),
                        'urlLink' => $this->getUrlLink($listing["url"] ?? ''),
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

    public function downloadInventory()
    {

    }

}