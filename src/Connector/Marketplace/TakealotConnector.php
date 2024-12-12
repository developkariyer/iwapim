<?php

namespace App\Connector\Marketplace;

use Doctrine\DBAL\Exception;
use Pimcore\Model\DataObject\VariantProduct;
use App\Utils\Utility;
use Pimcore\Model\Element\DuplicateFullPathException;
use Symfony\Component\HttpClient\HttpClient;
use Symfony\Contracts\HttpClient\Exception\ClientExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\DecodingExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\RedirectionExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\ServerExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;

class TakealotConnector extends MarketplaceConnectorAbstract
{
    private static array $apiUrl = [
        'offers' => "https://seller-api.takealot.com/v2/offers/",
        'orders' => "https://seller-api.takealot.com/v2/sales"
    ];

    public static $marketplaceType = 'Takealot';

    /**
     * @throws TransportExceptionInterface
     * @throws ServerExceptionInterface
     * @throws RedirectionExceptionInterface
     * @throws DecodingExceptionInterface
     * @throws ClientExceptionInterface
     */
    public function download($forceDownload = false): void
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

    public function createUrlLink($url,$title): string
    {
        $titleParts = explode('-', $title);
        $size = "";
        $colour_variant = "";
        $lastPart = trim($titleParts[count($titleParts) - 1]);
        if (count($titleParts) >= 3) {
            if (str_contains($lastPart, 'cm')) {
                $size = $lastPart;
                $size = trim($size);
                $size = str_replace(' ', '+', $size);
                $colour_variant = trim($titleParts[count($titleParts) - 2]);
            } else {
                $colour_variant = $lastPart;
            }
            $colour_variant = trim($colour_variant);
            $colour_variant = str_replace(' ', '+', $colour_variant);
        }
        else {
            if (str_contains($lastPart, 'cm')) {
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

    public function getParentId ($url): string
    {   
        $urlParts = explode('/', $url);
        return $urlParts[count($urlParts) - 1];
    }

    /**
     * @throws DuplicateFullPathException
     * @throws \Exception
     */
    public function import($updateFlag, $importFlag): void
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

    /**
     * @throws TransportExceptionInterface
     * @throws ServerExceptionInterface
     * @throws RedirectionExceptionInterface
     * @throws DecodingExceptionInterface
     * @throws ClientExceptionInterface
     */
    public function downloadOrders(): void // Does not contain a modifydate field
    {
        $db = \Pimcore\Db::get();
        $page = 1;
        $size = 100;
        do {
            $response = $this->httpClient->request('GET', static::$apiUrl['orders'], [
                'headers' => [
                    'Authorization' =>' Key ' . $this->marketplace->getTakealotKey()
                ],
                'query' => [
                    'page_number' => $page,
                    'page_size' => $size
                ]
            ]);
            $statusCode = $response->getStatusCode();
            print_r($response->getContent());
            if ($statusCode !== 200) {
                echo "Error: $statusCode\n";
                break;
            }
            try {
                $data = $response->toArray();
                $orders = $data['sales'];
                $db->beginTransaction();
                foreach ($orders as $order) {
                    $db->executeStatement(
                        "INSERT INTO iwa_marketplace_orders (marketplace_id, order_id, json) VALUES (?, ?, ?) ON DUPLICATE KEY UPDATE json = VALUES(json)",
                        [
                            $this->marketplace->getId(),
                            $order['order_id'],
                            json_encode($order)
                        ]
                    );
                }
                $db->commit();
            } catch (\Exception $e) {
                $db->rollBack();
                echo "Error: " . $e->getMessage() . "\n";
            }
            echo "Page: " . $page . " ";
            $page++;
            echo ".";
            sleep(1);
        } while ($data['page_summary'] === $size);
    }
    
    public function downloadInventory()
    {

    }

    /**
     * @throws TransportExceptionInterface
     * @throws Exception
     */
    public function setInventory(VariantProduct $listing, int $targetValue, $sku = null, $country = null): void
    {
        if (!$listing instanceof VariantProduct) {
            echo "Listing is not a VariantProduct\n";
            return;
        }
        $offerId = json_decode($listing->jsonRead('apiResponseJson'), true)['offer_id'];
        if (empty($offerId)) {
            echo "Offer ID not found\n";
            return;
        }
        $response = $this->httpClient->request('PATCH', static::$apiUrl['offers'], [
            'headers' => [
                'Authorization' =>' Key ' . $this->marketplace->getTakealotKey()
            ],
            'query' => [
                'identifier' => $offerId
            ],
            'json' => [
                'leadtime_stock' => [
                    'merchant_warehouse_id' => "000000",
                    'quantity' => $targetValue
                ]
            ]
        ]);
        $statusCode = $response->getStatusCode();
        if ($statusCode !== 200) {
            echo "Error: $statusCode\n";
            return;
        }
        $data = $response->toArray();
        $date = date('Y-m-d H:i:s');
        $filename = "{$offerId}-$date.json";
        Utility::setCustomCache($filename, PIMCORE_PROJECT_ROOT . "/tmp/marketplaces/" . urlencode($this->marketplace->getKey()) . '/SetInventory', $data);
    }

    /**
     * @throws Exception
     * @throws TransportExceptionInterface
     */
    public function setPrice(VariantProduct $listing, string $targetPrice, $targetCurrency = null, $sku = null, $country = null): void
    {
        if (!$listing instanceof VariantProduct) {
            echo "Listing is not a VariantProduct\n";
            return;
        }
        if ($targetPrice === null) {
            echo "Error: Price cannot be null\n";
            return;
        }
        if ($targetCurrency === null) {
            $targetCurrency = $listing->getSaleCurrency();
        }
        $finalPrice = $this->convertCurrency($targetPrice, $targetCurrency, $listing->getSaleCurrency());
        if ($finalPrice === null) {
            echo "Error: Currency conversion failed\n";
            return;
        }
        $offerId = json_decode($listing->jsonRead('apiResponseJson'), true)['offer_id'];
        if (empty($offerId)) {
            echo "Offer ID not found\n";
            return;
        }
        $response = $this->httpClient->request('PATCH', static::$apiUrl['offers'], [
            'headers' => [
                'Authorization' =>' Key ' . $this->marketplace->getTakealotKey()
            ],
            'query' => [
                'identifier' => $offerId
            ],
            'json' => [
                'selling_price' => $finalPrice
            ]
        ]);
        $statusCode = $response->getStatusCode();
        if ($statusCode !== 200) {
            echo "Error: $statusCode\n";
            return;
        }
        $data = $response->toArray();
        $date = date('Y-m-d H:i:s');
        $filename = "{$offerId}-$date.json";
        Utility::setCustomCache($filename, PIMCORE_PROJECT_ROOT . "/tmp/marketplaces/" . urlencode($this->marketplace->getKey()) . '/SetPrice', $data);
    }

}