<?php

namespace App\Connector\Marketplace;

use Pimcore\Model\DataObject\VariantProduct;
use App\Utils\Utility;
use Symfony\Component\HttpClient\HttpClient;

class WallmartConnector extends MarketplaceConnectorAbstract
{
    private static $apiUrl = [
        'loginTokenUrl' => "https://api-gateway.walmart.com/v3/token",
        'offers' => 'https://marketplace.walmartapis.com/v3/items',
        'item' => 'https://marketplace.walmartapis.com/v3/items/',
        'associations' => 'https://marketplace.walmartapis.com/v3/items/associations'
    ];
    public static string $marketplaceType = 'Wallmart';
    public static $expires_in;
    public static $correlationId;

    function generateCorrelationId() 
    {
        $randomHex = bin2hex(random_bytes(4));
        return substr($randomHex, 0, 4) . '-' . substr($randomHex, 4, 4);
    }

    public function prepareToken()
    {
        static::$correlationId = $this->generateCorrelationId();
        try {
            $response = $this->httpClient->request('POST', static::$apiUrl['loginTokenUrl'], [
                'headers' => [
                    'Accept' => 'application/json',
                    'Authorization' => 'Basic ' . base64_encode("{$this->marketplace->getWallmartClientId()}:{$this->marketplace->getWallmartSecretKey()}"),
                    'WM_QOS.CORRELATION_ID' => static::$correlationId,
                    'Content-Type' => 'application/x-www-form-urlencoded',
                    'WM_SVC.NAME' => 'Walmart Marketplace'
                ],
                'body' => http_build_query([
                    'grant_type' => 'client_credentials'
                ])
            ]);
            if ($response->getStatusCode() !== 200) {
                throw new \Exception('Failed to get token: ' . $response->getContent(false));
            }
            $data = $response->toArray();
            static::$expires_in = time() + $data['expires_in'];
            $this->marketplace->setWallmartAccessToken($data['access_token']);
            $this->marketplace->save();
        } catch (\Exception $e) {
            echo 'Error: ' . $e->getMessage();
        }
    }

    public function download($forceDownload = false)
    {
       if (!isset(static::$expires_in) || time() >= static::$expires_in) {
            $this->prepareToken();
        }
        echo "Token is valid. Proceeding with download...\n";
        $this->listings = json_decode(Utility::getCustomCache('LISTINGS.json', PIMCORE_PROJECT_ROOT. "/tmp/marketplaces/".urlencode($this->marketplace->getKey())), true);
        if (!(empty($this->listings) || $forceDownload)) {
            echo "Using cached listings\n";
            return;
        }
         
        $offset = 0;
        $limit = 20;
        $this->listings = [];
        do {
            $response = $this->httpClient->request('GET',  static::$apiUrl['offers'], [
                'headers' => [
                    'WM_SEC.ACCESS_TOKEN' => $this->marketplace->getWallmartAccessToken(),
                    'WM_QOS.CORRELATION_ID' => static::$correlationId,
                    'WM_SVC.NAME' => 'Walmart Marketplace',
                    'Accept' => 'application/json'
                ],
                'query' => [
                    'limit' => $limit,
                    'offset' => $offset
                ]
            ]);
            $statusCode = $response->getStatusCode();
            if ($statusCode !== 200) {
                echo "Error: $statusCode\n";
                break;
            }
            $data = $response->toArray();
            $products = $data['ItemResponse'];
            $totalItems = $data['totalItems'];
            $this->listings = array_merge($this->listings, $products);
            echo "Offset: " . $offset . " " . count($this->listings) . " ";
            $offset += $limit;
            echo ".";
            sleep(1);  
            echo "Total Items: " . $totalItems . "\n";
            echo "Count: " . count($this->listings) . "\n";
        } while (count($this->listings) < $totalItems);
        if (empty($this->listings)) {
            echo "Failed to download listings\n";
            return;
        }
        Utility::setCustomCache('LISTINGS.json', PIMCORE_PROJECT_ROOT. "/tmp/marketplaces/".urlencode($this->marketplace->getKey()), json_encode($this->listings));
    }

    public function getAnItem($sku)
    {
        $response = $this->httpClient->request('GET', static::$apiUrl['item'] . $sku, [
            'headers' => [
                'WM_SEC.ACCESS_TOKEN' => $this->marketplace->getWallmartAccessToken(),
                'WM_QOS.CORRELATION_ID' => static::$correlationId,
                'WM_SVC.NAME' => 'Walmart Marketplace',
                'Accept' => 'application/json'
            ]
        ]);
        $statusCode = $response->getStatusCode();
        if ($statusCode !== 200) {
            echo "Error: $statusCode\n";
            return;
        }
        $data = $response->toArray();
        print_r($data);
    }

    protected function getAttributes($listing)
    {
        $attributeString = "";
        if (!empty($listing['variantGroupInfo']['groupingAttributes'])) {
            foreach ($listing['variantGroupInfo']['groupingAttributes'] as $attribute) {
                $attributeString .= $attribute['value'] . '-';
            }
        }
        return rtrim($attributeString, '-');
    }

    public function import($updateFlag, $importFlag)
    {
        if (empty($this->listings)) {
            echo "Nothing to import\n";
        }
        $marketplaceFolder = Utility::checkSetPath(
            Utility::sanitizeVariable($this->marketplace->getKey(), 190),
            Utility::checkSetPath('Pazaryerleri')
        );
        $total = count($this->listings);
        $index = 0;
        foreach ($this->listings as $listing) {
            echo "($index/$total) Processing Listing {$listing['sku']}:{$listing['productName']} ...";
            $parent = Utility::checkSetPath($marketplaceFolder);
            if (!empty($listing['variantGroupId'])) {
                $parent = Utility::checkSetPath(
                    Utility::sanitizeVariable($listing['variantGroupId']),
                    $parent
                );
            }
            VariantProduct::addUpdateVariant(
                variant: [
                    'imageUrl' => Utility::getCachedImage($listing['image_url']) ?? '',
                    'urlLink' => $this->getUrlLink("https://www.walmart.com/ip/" . str_replace(' ', '-', $listing['productName']) . "/" . $listing['wpid']) ?? '',
                    'salePrice' => $listing['price']['amount'] ?? 0,
                    'saleCurrency' => 'USD',
                    'title' => $listing['productName'] ?? '',
                    'attributes' => $this->getAttributes($listing) ?? '',
                    'uniqueMarketplaceId' => $listing['wpid'] ?? '',
                    'apiResponseJson' => json_encode($listing, JSON_PRETTY_PRINT),
                    'published' => $listing['publishedStatus'] === 'PUBLISHED' ? true : false,
                    'sku' => $listing['sku'] ?? '',
                ],
                importFlag: $importFlag,
                updateFlag: $updateFlag,
                marketplace: $this->marketplace,
                parent: $parent
            );
            echo "OK\n";
            $index++;
        }    
    }

    public function downloadOrders()
    {
        
    }
    
    public function downloadInventory()
    {

    }
   
}