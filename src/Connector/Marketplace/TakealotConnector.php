<?php

namespace App\Connector\Marketplace;

use Doctrine\DBAL\Exception;
use Pimcore\Model\DataObject\VariantProduct;
use App\Utils\Utility;
use Pimcore\Model\Element\DuplicateFullPathException;
use Random\RandomException;
use Symfony\Component\HttpClient\ScopingHttpClient;
use Symfony\Contracts\HttpClient\Exception\ClientExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\DecodingExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\RedirectionExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\ServerExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;

class TakealotConnector extends MarketplaceConnectorAbstract
{
    private static array $apiUrl = [
        'offers' => "offers/",
        'orders' => "sales/"
    ];

    public static string $marketplaceType = 'Takealot';

    public function __construct($marketplace)
    {
        parent::__construct($marketplace);
        $this->httpClient = ScopingHttpClient::forBaseUri($this->httpClient, 'https://seller-api.takealot.com/v2/', [
            'headers' => [
                'Authorization' =>' Key ' . $this->marketplace->getTakealotKey()
            ],
        ]);
    }

    /**
     * @throws TransportExceptionInterface
     * @throws ServerExceptionInterface
     * @throws RedirectionExceptionInterface
     * @throws DecodingExceptionInterface
     * @throws ClientExceptionInterface|RandomException
     */
    public function download(bool $forceDownload = false): void
    {
        if (!$forceDownload && $this->getListingsFromCache()) {
            echo "Using cached listings\n";
            return;
        }
        $page = 1;
        $size = 100;
        $this->listings = [];
        do {
            $response = $this->httpClient->request('GET', static::$apiUrl['offers'], [
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
        $this->putListingsToCache();
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

    public function getAttributes ($title)
    {
        $pos = strpos($title, '-');
        return ($pos !== false) ? trim(substr($title, $pos + 1)) : '';
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
                    'attributes' => $this->getAttributes($listing['title']) ?? '',
                    'quantity' => $listing['stock_at_takealot_total'] ?? 0,
                    'uniqueMarketplaceId' => $listing['tsin_id'] ?? '',
                    'apiResponseJson' => json_encode($listing, JSON_PRETTY_PRINT),
                    'published' => $listing['status'] === 'Buyable',
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
        $page = 1;
        $size = 100;
        do {
            $response = $this->httpClient->request('GET', static::$apiUrl['orders'], [
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
            try {
                $data = $response->toArray();
                $orders = $data['sales'];
                foreach ($orders as $order) {
                    $sqlInsertMarketplaceOrder = "
                            INSERT INTO iwa_marketplace_orders (marketplace_id, order_id, json) 
                            VALUES (:marketplace_id, :order_id, :json) ON DUPLICATE KEY UPDATE json = VALUES(json)";
                    Utility::executeSql($sqlInsertMarketplaceOrder, [
                        'marketplace_id' => $this->marketplace->getId(),
                        'order_id' => $order['order_id'],
                        'json' => json_encode($order)
                    ]);
                }
            } catch (\Exception $e) {
                echo "Error: " . $e->getMessage() . "\n";
            }
            echo "Page: " . $page . " ";
            $page++;
            echo ".";
            sleep(1);
        } while ($data['page_summary'] === $size);
    }

    public function downloadReturns()
    {
        $sql = 'SELECT * FROM `iwa_marketplace_orders_line_items` WHERE marketplace_type = "Takealot" AND fulfillments_status = "Returned"';
        $returnOrders = Utility::fetchFromSql($sql);
        print_r(json_encode($returnOrders));
    }

    public function downloadInventory(): void
    {
        $this->downloadReturns();
    }

    /**
     * @param VariantProduct $listing
     * @param int $targetValue
     * @param null $sku
     * @param null $country
     * @throws ClientExceptionInterface
     * @throws DecodingExceptionInterface
     * @throws Exception
     * @throws RedirectionExceptionInterface
     * @throws ServerExceptionInterface
     * @throws TransportExceptionInterface
     * @throws RandomException
     */
    public function setInventory(VariantProduct $listing, int $targetValue, $sku = null, $country = null): void
    {
        $offerId = json_decode($listing->jsonRead('apiResponseJson'), true)['offer_id'];
        if (empty($offerId)) {
            echo "Offer ID not found\n";
            return;
        }
        $request = [
            'query' => [
                'identifier' => $offerId
            ],
            'json' => [
                'leadtime_stock' => [
                    'merchant_warehouse_id' => "000000",
                    'quantity' => $targetValue
                ]
            ]
        ];
        $this->setInventoryPrice($request,"SETINVENTORY_{$offerId}.json");
    }

    /**
     * @param VariantProduct $listing
     * @param string $targetPrice
     * @param null $targetCurrency
     * @param null $sku
     * @param null $country
     * @throws ClientExceptionInterface
     * @throws DecodingExceptionInterface
     * @throws Exception
     * @throws RedirectionExceptionInterface
     * @throws ServerExceptionInterface
     * @throws TransportExceptionInterface
     * @throws RandomException
     */
    public function setPrice(VariantProduct $listing, string $targetPrice, $targetCurrency = null, $sku = null, $country = null): void
    {
        if (empty($targetPrice)) {
            echo "Error: Price cannot be null\n";
            return;
        }
        if (empty($targetCurrency)) {
            $targetCurrency = $listing->getSaleCurrency();
        }
        $finalPrice = $this->convertCurrency($targetPrice, $targetCurrency, $listing->getSaleCurrency());
        if (empty($finalPrice)) {
            echo "Error: Currency conversion failed\n";
            return;
        }
        $offerId = json_decode($listing->jsonRead('apiResponseJson'), true)['offer_id'];
        if (empty($offerId)) {
            echo "Offer ID not found\n";
            return;
        }
        $request = [
            'query' => [
                'identifier' => $offerId
            ],
            'json' => [
                'selling_price' => $finalPrice
            ]
        ];
        $this->setInventoryPrice($request, "SETPRICE_{$offerId}.json");
    }

    /**
     * @throws TransportExceptionInterface
     * @throws ServerExceptionInterface
     * @throws RedirectionExceptionInterface
     * @throws DecodingExceptionInterface
     * @throws ClientExceptionInterface|RandomException
     */
    public function setInventoryPrice($request, $cacheName): void
    {
        $response = $this->httpClient->request('PATCH', static::$apiUrl['offers'], [
            'query' => $request['query'],
            'json' => $request['json']
        ]);
        $statusCode = $response->getStatusCode();
        if ($statusCode !== 200) {
            echo "Error: $statusCode\n";
            return;
        }
        $data = $response->toArray();
        $this->putToCache($cacheName, ['request' => $request, 'response' => $data]);
    }

}