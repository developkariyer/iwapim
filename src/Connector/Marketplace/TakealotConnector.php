<?php

namespace App\Connector\Marketplace;

use Pimcore\Model\DataObject\VariantProduct;
use App\Utils\Utility;
use Symfony\Component\HttpClient\HttpClient;

class TakealotConnector extends MarketplaceConnectorAbstract
{
    private static $apiUrl = [
        'offers' => "https://seller-api.takealot.com/v2/offers/",
        'orders' => "https://seller-api.takealot.com/v2/sales/summary",
    ];
    
    public static $marketplaceType = 'Takealot';
    
    public function download($forceDownload = false)
    {
        $this->listings = json_decode(Utility::getCustomCache('LISTINGS.json', PIMCORE_PROJECT_ROOT. "/tmp/marketplaces/".urlencode($this->marketplace->getKey())), true);
        if (!(empty($this->listings) || $forceDownload)) {
            echo "Using cached listings\n";
            return;
        }
        $page = 1;
        $size = 100;
        $this->listings = [];
        do {
            $response = $this->httpClient->request('GET', static::$apiUrl['offers'], [
                'headers' => [
                    'Authorization' =>' Key ' . $this->marketplace->getTakealotKey()
                ],
                'query' => [
                    'page_number' => $page,
                    'page_size' => $size
                ]
            ]);
            $statusCode = $response->getStatusCode();
            if ($statusCode !== 200) {
                echo "Error: $statusCode\n";
                break;
            }
            $data = $response->toArray();
            $products = $data['offers'];
            $this->listings = array_merge($this->listings, $products);
            echo "Page: " . $page . " ";
            $page++;
            echo ".";
            sleep(1);  
        } while ($data['total_results'] === $size);
        if (empty($this->listings)) {
            echo "Failed to download listings\n";
            return;
        }
        Utility::setCustomCache('LISTINGS.json', PIMCORE_PROJECT_ROOT. "/tmp/marketplaces/".urlencode($this->marketplace->getKey()), json_encode($this->listings));
    }

    public function createUrlLink($url,$title)
    {
        $titleParts = explode('-', $title);
        $size = "";
        $colour_variant = "";
        if (count($titleParts) >= 3) {
            $lastPart = trim($titleParts[count($titleParts) - 1]);
            if (strpos($lastPart, 'cm') !== false) {
                $size = $lastPart;
                $size = trim($size);
                $size = str_replace(' ', '+', $size);
                $colour_variant = trim($titleParts[count($titleParts) - 2]);
                $colour_variant = trim($colour_variant);
                $colour_variant = str_replace(' ', '+', $colour_variant);
            } else {
                $colour_variant = $lastPart;
                $colour_variant = trim($colour_variant);
                $colour_variant = str_replace(' ', '+', $colour_variant);
            }
        }
        else {
            $lastPart = trim($titleParts[count($titleParts) - 1]);
            if (strpos($lastPart, 'cm') !== false) {
                $size = $lastPart;
                $size = trim($size);
                $size = str_replace(' ', '+', $size);
            } else {
                $colour_variant = $lastPart;
                $colour_variant = trim($colour_variant);
                $colour_variant = str_replace(' ', '+', $colour_variant);
            }
        }

        $newUrl = $url . "?";
        if ($colour_variant !== "") {
            $newUrl .= "colour_variant=".$colour_variant;
        }
        if ($size !== "" and $colour_variant !== "") {
            $newUrl .= "&size=".$size;
        }
        if ($size !== "" and $colour_variant === "") {
            $newUrl .= "size=".$size;
        }
        return $newUrl;
    }

    public function getParentId ($url)
    {   
        $urlParts = explode('/', $url);
        $lastPart = $urlParts[count($urlParts) - 1];
        return $lastPart;
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
            echo "($index/$total) Processing Listing {$listing['sku']}:{$listing['title']} ...";
            $parent = Utility::checkSetPath($marketplaceFolder);
            if (!empty($listing['offer_url'])) {
                $parent = Utility::checkSetPath(
                    Utility::sanitizeVariable($this->getParentId($listing['offer_url'])),
                    $parent
                );
            }
            VariantProduct::addUpdateVariant(
                variant: [
                    'imageUrl' => Utility::getCachedImage($listing['image_url']),
                    'urlLink' => $this->getUrlLink($this->createUrlLink($listing['offer_url'], $listing['title'])),
                    'salePrice' => $listing['selling_price'] ?? 0,
                    'saleCurrency' => 'ZAR',
                    'title' => $listing['title'] ?? '',
                    'attributes' => $listing['title'] ?? '',
                    'uniqueMarketplaceId' => $listing['tsin_id'] ?? '',
                    'apiResponseJson' => json_encode($listing, JSON_PRETTY_PRINT),
                    'published' => $listing['status'] === 'Buyable' ? true : false,
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
        $response = $this->httpClient->request('GET', static::$apiUrl['orders'], [
            'headers' => [
                'Authorization' =>' Key ' . $this->marketplace->getTakealotKey()
            ]
        ]);
        print_r($response->toArray());
        
    }
    
    public function downloadInventory()
    {

    }

}