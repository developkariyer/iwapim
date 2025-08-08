<?php

namespace App\Command;

use App\Connector\Marketplace\EbayConnector;
use App\Connector\Marketplace\EbayConnector2;
use App\Connector\Marketplace\ShopifyConnector;
use App\Utils\Utility;
use Carbon\Carbon;
use Doctrine\DBAL\Exception;
use JsonException;
use Pimcore\Console\AbstractCommand;
use Pimcore\Db;
use Pimcore\Model\Notification\Service\NotificationService;
use Random\RandomException;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Pimcore\Model\DataObject\Product;
use Pimcore\Model\DataObject\Marketplace;
use Pimcore\Model\DataObject\VariantProduct;
use Pimcore\Model\DataObject\GroupProduct;
use App\Connector\Marketplace\Amazon\Connector as AmazonConnector;
use Symfony\Contracts\HttpClient\Exception\ClientExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\DecodingExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\RedirectionExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\ServerExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;
use Throwable;


#[AsCommand(
    name: 'app:console',
    description: 'Interactive Wisersell Connection Console',
)]
class ConsoleCommand extends AbstractCommand
{
    private ?EbayConnector $ebayConnector;
    private int $ebayBrowseApiCounter;
    private NotificationService $notificationService;



    function __construct(NotificationService $notificationService)
    {
        $this->notificationService = $notificationService;
        $this->ebayConnector = null;
        $this->ebayBrowseApiCounter = 0;
        parent::__construct(self::$defaultName);
    }

    protected function configure(): void
    {
        $methods = get_class_methods($this);
        $methodNames = "";
        foreach ($methods as $method) {
            if (str_starts_with($method, 'command')) {
                $methodNames .= "\n  - $method";
            }
        }
        $this->addArgument('runCommand', InputArgument::OPTIONAL, "If provied, command to execute. Here is a list of allowed commands: $methodNames");
    }


    /**
     * @throws TransportExceptionInterface
     * @throws RandomException
     * @throws ServerExceptionInterface
     * @throws RedirectionExceptionInterface
     * @throws DecodingExceptionInterface
     * @throws ClientExceptionInterface
     * @throws \Exception
     */
    public function commandEbayInventoryItems(): void
    {
        $ebayConnector = new EbayConnector2(Marketplace::getByMarketplaceType('Ebay', 1));
        $response = $ebayConnector->getInventoryItems();
        Utility::setCustomCache('ebay_inventory_items', PIMCORE_PROJECT_ROOT . '/tmp/ebay', json_encode($response, JSON_PRETTY_PRINT));
        print_r($response);
        exit;

    }

    /**
     * @throws RandomException
     * @throws RedirectionExceptionInterface
     * @throws DecodingExceptionInterface
     * @throws ClientExceptionInterface
     * @throws TransportExceptionInterface
     * @throws ServerExceptionInterface
     * @throws \DOMException
     */
    public function commandEbaySellerList(): void
    {
        $ebayConnector = new EbayConnector2(Marketplace::getByMarketplaceType('Ebay', 1));
        $response = $ebayConnector->getSellerList();
        Utility::setCustomCache('ebay_seller_list', PIMCORE_PROJECT_ROOT . '/tmp/ebay', json_encode($response, JSON_PRETTY_PRINT));
        print_r($response);
        exit;
    }

    /**
     * @throws \Exception
     */
    public function commandSearchEbayProduct(string $searchText = ""): array
    {
        if (!$this->ebayConnector) {
            $ebayObject = Marketplace::getByMarketplaceType('Ebay', 1);
            $this->ebayConnector = new EbayConnector($ebayObject);
            $this->ebayConnector->refreshToAccessToken();
        }
        $result = json_decode(Utility::getCustomCache(urlencode($searchText), PIMCORE_PROJECT_ROOT . '/tmp/ebay', 7*86400), true);
        if (empty($result)) {
            if ($this->ebayBrowseApiCounter>5000) {
                return [];
            }
            try {
                $result = $this->ebayConnector->searchProduct($searchText, 1, 20);
            } catch (Throwable $e) {
                $this->ebayConnector->refreshToAccessToken();
                sleep(10);
                return [];
            }
            $this->ebayBrowseApiCounter++;
            Utility::setCustomCache(urlencode($searchText), PIMCORE_PROJECT_ROOT . '/tmp/ebay', json_encode($result, JSON_PRETTY_PRINT));
        }
        return $result;
    }

    /**
     * @throws Exception
     * @throws \Exception
     */
    public function commandGetPrices(): void
    {
        $db = Db::get();
        //$brandCodes = $db->fetchFirstColumn("SELECT brand_code FROM iwa_autoparts_parts WHERE min_price IS NULL AND max_price IS NULL ORDER BY brand_code LIMIT 100000");
        $brandCodes = $db->fetchFirstColumn("SELECT iap.brand_code, min_price, max_price, min(iai.price) AS input_min_price, max(iai.price) AS input_max_price FROM iwa_autoparts_parts iap JOIN iwa_autoparts_inventory iai ON iap.brand_code = iai.brand_code AND iai.price > 0 WHERE ((not iap.min_price > 0) AND (not iap.max_price > 0)) OR (min_price IS NULL AND max_price IS NULL) GROUP BY iap.brand_code, min_price, max_price HAVING MIN(iai.price) > 300 ORDER BY `input_min_price` DESC");
        foreach ($brandCodes as $brandCode) {
            if (empty($brandCode)) {
                continue;
            }
            echo "- $brandCode\n";
            $searchResult = $this->commandSearchEbayProduct($brandCode);
            $code = trim(substr($brandCode, strpos($brandCode, " ") + 1));
            if (empty($searchResult)) {
                continue;
            }
            $minPrice = 0;
            $maxPrice = 0;
            $title = "";
            $image = "";
            foreach ($searchResult['itemSummaries'] ?? [] as $productInfo) {
                if (!empty($productInfo['title'])) {
                    $cleanTitle = preg_replace("/[^a-zA-Z0-9]/", "", $productInfo['title']);
                    if (!str_contains($cleanTitle, $code)) {
                        continue;
                    }
                }
                echo "  - {$productInfo['title']}\n";
                if (empty($title)) {
                    $title = $productInfo['title'];
                }
                if (empty($image) && !empty($productInfo['image']['imageUrl'])) {
                    $image = $productInfo['image']['imageUrl'];
                }
                if (!empty($productInfo['price']['value']) && $productInfo['price']['currency'] === 'USD') {
                    $price = (float)$productInfo['price']['value'];
                    if ($minPrice === 0 || $price < $minPrice) {
                        $minPrice = $price;
                    }
                    if ($price > $maxPrice) {
                        $maxPrice = $price;
                    }
                }
            }
            if (empty($minPrice) || empty($title)) {
                echo "  * EMPTY\n";
                continue;
            }
            echo "  * $minPrice $maxPrice\n";
            $db->executeQuery("UPDATE iwa_autoparts_parts SET min_price = :minPrice, max_price = :maxPrice, title = :title, image = :image WHERE brand_code = :brandCode", [
                'minPrice' => $minPrice,
                'maxPrice' => $maxPrice,
                'title' => $title,
                'image' => $image,
                'brandCode' => $brandCode
            ]);
        }
    }

    protected static function getJwtRemainingTime($jwt): int
    {
        $jwt = explode('.', $jwt);
        $jwt = json_decode(base64_decode($jwt[1]), true);
        return $jwt['exp'] - time();
    }

    /**
     * @throws \Exception
     */
    public function commandTrimProductEans(): void
    {
        $listingObject = new Product\Listing();
        $listingObject->setUnpublished(false);
        $listingObject->filterByEanGtin('%868408%', 'LIKE');
        $pageSize = 14;
        $offset = 0;
        $listingObject->setLimit($pageSize);
        $index = $offset;
        while (true) {
            $listingObject->setOffset($offset);
            $products = $listingObject->load();
            if (empty($products)) {
                break;
            }
            $offset += $pageSize;
            foreach ($products as $product) {
                $index++;
                echo "\rProcessing $index {$product->getId()} {$product->getIwasku()} ";
                $ean = $product->getEanGtin();
                if ($ean === trim($ean)) {
                    continue;
                }
                $product->setEanGtin(trim($ean));
                $product->save();
                echo "Trimmed\n";
            }
        }
    }


    public function commandSetEanRequires(): void
    {
        $variantProductIdList = "184150";

        $variantProductIds = explode("\n", $variantProductIdList);
        foreach ($variantProductIds as $variantProductId) {
            $variantProduct = VariantProduct::getById($variantProductId);
            if (!$variantProduct) {
                continue;
            }
            echo "\rProcessing $variantProductId ";
            $mainProduct = $variantProduct->getMainProduct();
            if (empty($mainProduct)) {
                continue;
            }
            $mainProduct = reset($mainProduct);
            $ean = $mainProduct->getEanGtin();
            $requireEan = $mainProduct->getRequireEan();
            if ($ean || $requireEan) {
                continue;
            }
            echo "Setting EAN Required\n";
            $mainProduct->setRequireEan(true);
            $mainProduct->save();
        }

    }

    public function commandGetEanFromAmazon(): void
    {
        $listingObject = new Product\Listing();
        $listingObject->setUnpublished(false);
        $listingObject->setOrderKey('iwasku');
        $pageSize = 13;
        $offset = 0;
        $listingObject->setLimit($pageSize);
        $index = $offset;
        $carbon3daysAgo = Carbon::now()->subDays(3);
        $connectors = [];
        while (true) {
            $listingObject->setOffset($offset);
            $products = $listingObject->load();
            if (empty($products)) {
                break;
            }
            $offset += $pageSize;
            foreach ($products as $product) {
                $index++;
                echo "\rProcessing $index {$product->getId()} {$product->getIwasku()} ";
                if ($product->getEanGtin()) {
                    continue;
                }
                foreach ($product->getListingItems() as $variantProduct) {
                    if ($variantProduct->getLastUpdate() < $carbon3daysAgo) {
                        continue;
                    }
                    $marketplace = $variantProduct->getMarketplace();
                    if (empty($marketplace) || $marketplace->getMarketplaceType() !== 'Amazon') {
                        continue;
                    }
                    foreach ($variantProduct->getAmazonMarketplace() as $amazonMarketplace) {
                        if ($amazonMarketplace->getLastUpdate() < $carbon3daysAgo) {
                            continue;
                        }
                        $ean = $amazonMarketplace->getEan();
                        if (empty($ean) || !str_starts_with($ean, '868')) {
                            continue;
                        }
                        echo "EAN: $ean ";
                        try {
                            $product->setEanGtin($ean);
                            $product->setRequireEan(false);
                            $product->save();
                            echo "OK\n";
                            break;
                        } catch (\Exception $e) {
                            echo "Error: ".$e->getMessage()."\n";
                        }
                    }
                }
            }
        }

    }

    public function commandAmazonRequireEan(): void
    {
        $carbonYesterday = Carbon::now()->subDays(2);
        $variantObject = new VariantProduct\Listing();
        $pageSize = 16;
        $offset = 0;
        $variantObject->setLimit($pageSize);
        $variantObject->setUnpublished(false);
        $index = $offset;
        while (true) {
            $variantObject->setOffset($offset);
            $results = $variantObject->load();
            if (empty($results)) {
                break;
            }
            $offset += $pageSize;
            foreach ($results as $listing) {
                $index++;
                echo "\rProcessing $index {$listing->getId()} ";
                if ($listing->getMarketplace()->getMarketplaceType() !== 'Amazon') {
                    continue;
                }
                if ($listing->getLastUpdate() < $carbonYesterday) {
                    continue;
                }
                $mainProduct = $listing->getMainProduct();
                if (empty($mainProduct)) {
                    continue;
                }
                $mainProduct = reset($mainProduct);
                if ($mainProduct->getEanGtin() || $mainProduct->getRequireEan()) {
                    continue;
                }
                $hasEan = false;
                $foundEan = '';
                foreach ($listing->getAmazonMarketplace() as $amazonMarketplace) {
                    if ($amazonMarketplace->getLastUpdate() < $carbonYesterday) {
                        continue;
                    }
                    $ean = $amazonMarketplace->getEan();
                    if (str_starts_with($ean, '868')) {
                        $hasEan = true;
                        $foundEan = $ean;
                    }
                }
                if ($hasEan && $foundEan) {
                    echo "Setting EAN: $foundEan\n";
                    try {
                        $mainProduct->setEanGtin($foundEan);
                        $mainProduct->save();
                    } catch (\Exception $e) {
                        echo "Error: ".$e->getMessage()."\n";
                    }
                }
                if (!$hasEan) {
                    echo "Setting EAN Required\n";
                    $mainProduct->setRequireEan(true);
                    $mainProduct->save();
                }
            }
        }
        echo "\nFinished\n";

    }



    /**
     * @throws Exception
     */
    public function commandSendTestNotification(): void
    {   // $this->sendTestNotification();
        // get symfony's notifaction service. But not with new NotificationService() because it will not work
        $this->notificationService->sendToUser(2, 1, 'Test Notification', 'This is a test notification');
    }

    /**
     * @throws TransportExceptionInterface
     * @throws RandomException
     * @throws ServerExceptionInterface
     * @throws RedirectionExceptionInterface
     * @throws ClientExceptionInterface
     * @throws Exception
     * @throws \Exception
     */
    public function commandSetShopifySku(): void
    {   // $this->setShopifySku();

        $shopifyConnectors = [
            23978 => new ShopifyConnector(Marketplace::getById(23978)),
            84124 => new ShopifyConnector(Marketplace::getById(84124)),
            108415 => new ShopifyConnector(Marketplace::getById(108415)),
            108416 => new ShopifyConnector(Marketplace::getById(108416)),
            108417 => new ShopifyConnector(Marketplace::getById(108417)),
            108418 => new ShopifyConnector(Marketplace::getById(108418)),
            108419 => new ShopifyConnector(Marketplace::getById(108419)),
        ];

        $carbonYesterday = Carbon::now()->subDays(2);
        $variantObject = new VariantProduct\Listing();
        $pageSize = 5;
        $offset = 0;
        $variantObject->setLimit($pageSize);
        $variantObject->setUnpublished(false);
        $index = $offset;
        while (true) {
            $variantObject->setOffset($offset);
            $results = $variantObject->load();
            if (empty($results)) {
                break;
            }
            $offset += $pageSize;
            foreach ($results as $listing) {
                $index++;
                echo "\rProcessing $index {$listing->getId()} ";
                if ($listing->getMarketplace()->getMarketplaceType() !== 'Shopify') {
                    continue;
                }
                if ($listing->getLastUpdate() < $carbonYesterday) {
                    continue;
                }
                $marketplaceId = $listing->getMarketplace()->getId();
                $mainProduct = $listing->getMainProduct();
                if (empty($mainProduct)) {
                    echo "No Main Product\n";
                    continue;
                }
                if (is_array($mainProduct)) {
                    $mainProduct = reset($mainProduct);
                }
                if (!$mainProduct instanceof Product) {
                    echo "Main Product is not an instance of Product\n";
                    continue;
                }
                $iwasku = $mainProduct->getIwasku();
                if (empty($iwasku)) {
                    echo "No Iwasku\n";
                    continue;
                }
                $connector = $shopifyConnectors[$marketplaceId] ?? null;
                if (empty($connector)) {
                    //echo "No Connector\n";
                    continue;
                }
                $connector->setSku($listing, $iwasku);
            }
        }
        echo "\nFinished\n";

    }

    /**
     * @throws \Exception
     * @throws TransportExceptionInterface
     */
    public function commandSetShopifyBarcode(): void
    {   //  $this->setShopifyBarcode();
        $listingObject = new Product\Listing();
        $listingObject->setUnpublished(false);
        $listingObject->filterByEanGtin('868%', 'LIKE');
        $listingObject->setOrderKey('iwasku');
        $pageSize = 15;
        $offset = 0;
        $listingObject->setLimit($pageSize);
        $index = $offset;
        $carbon3daysAgo = Carbon::now()->subDays(3);
        $connectors = [];
        while (true) {
            $listingObject->setOffset($offset);
            $products = $listingObject->load();
            if (empty($products)) {
                break;
            }
            $offset += $pageSize;
            foreach ($products as $product) {
                $index++;
                echo "\rProcessing $index {$product->getId()} {$product->getIwasku()} ";
                $ean = $product->getEanGtin();
                echo "EAN: $ean ";
                foreach ($product->getListingItems() as $variantProduct) {
                    if ($variantProduct->getLastUpdate() < $carbon3daysAgo) {
                        continue;
                    }
                    if ($ean === $variantProduct->getEan()) {
                        continue;
                    }
                    $marketplace = $variantProduct->getMarketplace();
                    if (empty($marketplace) || $marketplace->getMarketplaceType() !== 'Shopify') {
                        continue;
                    }
                    echo "\n  {$marketplace->getKey()} ";
                    if (!isset($connectors[$marketplace->getId()])) {
                        $connectors[$marketplace->getId()] = new ShopifyConnector($marketplace);
                    }
                    $maxRetries = 5;
                    $attempt = 0;
                    $success = false;

                    while ($attempt < $maxRetries && !$success) {
                        try {
                            $connectors[$marketplace->getId()]->setBarcode($variantProduct, $ean);
                            $success = true;
                        } catch (\Exception $e) {
                            $attempt++;
                            echo "Error: ".$e->getMessage()." (Attempt $attempt of $maxRetries)";
                            if ($attempt >= $maxRetries) {
                                echo " - Giving up.";
                                break;
                            }
                            sleep(1);
                        }
                    }
                }
                echo "\n";
            }
        }
    }

    /**
     * @throws \Exception
     */
    public function commandDeleteAmazonListings(): void
    {
        $amazonAsinsToDelete = "B09CBR1VLQ
B09PNBNHCS
B09PNBK9JT
B09PNCCFCS
B09TWH3XG5
B09V881ZTL
B09FJ3YDYT
B0BYCYLGMF
B09FJ3S8PP
B0BYD1QY27
B09FJ44F6N
B0BR2WG1Y5
B0B51XB71B
B0BBRG71PY
B0BR2VDMDY
B0BBRGQFJ9
B09KNHY7T5
B09KNHSW8X
B0B9BW2S2V
B0B4KF1Q21";

        $amazonConnectors = [
            'AU' => new AmazonConnector(Marketplace::getByPath('/Ayarlar/Pazaryerleri/Amazon/AmazonAU')),
            'UK' => new AmazonConnector(Marketplace::getByPath('/Ayarlar/Pazaryerleri/Amazon/AmazonUK')),
            'US' => new AmazonConnector(Marketplace::getByPath('/Ayarlar/Pazaryerleri/Amazon/AmazonUS')),
            'CA' => new AmazonConnector(Marketplace::getByPath('/Ayarlar/Pazaryerleri/Amazon/AmazonCA')),
            'JP' => new AmazonConnector(Marketplace::getByPath('/Ayarlar/Pazaryerleri/Amazon/AmazonJP')),
        ];
        foreach (explode("\n", $amazonAsinsToDelete) as $asin) {
            echo "\rProcessing $asin ";
            $listing = VariantProduct::getByUniqueMarketplaceId(trim($asin), 1);
            if (!$listing) {
                echo "Not Found\n";
                continue;
            }
            foreach ($listing->getAmazonMarketplace() as $amazonMarketplace) {
                $carbon3daysAgo = Carbon::now()->subDays(3);
                if ($amazonMarketplace->getLastUpdate() < $carbon3daysAgo) {
                    continue;
                }
                $sku = $amazonMarketplace->getSku();
                $country = $amazonMarketplace->getMarketplaceId();
                $amazonConnector = match ($country) {
                    'AU' => $amazonConnectors['AU'],
                    'US','MX' => $amazonConnectors['US'],
                    'CA' => $amazonConnectors['CA'],
                    'JP' => $amazonConnectors['JP'],
                    default => $amazonConnectors['UK'],
                };
                try {
                    echo "\n  $sku $country ";
                    $amazonConnector->utilsHelper->deleteListing($sku, $country);
                    echo "Deleted\n";
                } catch (\Exception $e) {
                    echo "Error: ".$e->getMessage()."\n";
                }
            }
        }
    }

    /**
     * @throws \Exception
     */
    public function commandSetAmazonEan(): void
    {
        $targetList = [
            "B0B4DLSMN4"=>"8684089408623",
            "B09P16QBBJ"=>"8684089465220",
            "B08B5BJMR5"=>"8684089463417",
            "B09N1DXWYR"=>"8684089411876",
            "B08VKSTRD7"=>"8684089400009",
            "B0B2DZVP8Y"=>"8684089467255",
            "B08B5BBGSB"=>"8684089445963",
            "B09P17657M"=>"8684089465190",
            "B08B59DWX8"=>"8684089463431",
            "B0B2DZ8QBT"=>"8684089467262",
            "B09N1R1CCD"=>"8684089466142",
            "B09D3R4H7W"=>"8684089471405",
            "B09N1DLJ2X"=>"8684089411913",
            "B0B4DR7B7W"=>"8684089407947",
            "B08PFJ6JCK"=>"8684089445994",
            "B08LYLCKBR"=>"8684089430464",
            "B08LYR3YSX"=>"8684089430495",
            "B08LZ3FDM8"=>"8684089430440",
            "B08LZ235C1"=>"8684089430457",
            "B08LYXWDVT"=>"8684089430419",
            "B08LYYX5DD"=>"8684089413603",
            "B0927L985P"=>"8684089430433",
            "B08954Q71D"=>"8684089449961",
            "B08VKSSXN6"=>"8684089407084",
            "B09CQ84YW7"=>"8684089471108",
            "B0B4K419YS"=>"8684089407954",
            "B09N1R3T7T"=>"8684089466111",
            "B09N1F5QKF"=>"8684089412262",
            "B0B2F1TBX6"=>"8684089467279",
            "B09CTXVK1M"=>"8684089471344",
            "B08B5BCV2Z"=>"8684089445932",
            "B0B4DSRFG6"=>"8684089407930",
            "B09P479FGN"=>"8684089466074",
            "B09CTXMKG8"=>"8684089471245",
            "B08B5B6YSK"=>"8684089446007",
            "B089RG8SG6"=>"8684089420571",
            "B0B25MY2YJ"=>"8684089469365",
            "B0926LLTRS"=>"8684089430396",
            "B0B25PXHF6"=>"8684089469334",
            "B09P47G6W2"=>"8684089408845",
            "B09379GT3K"=>"8684089417014",
            "B09CTZ8WGX"=>"8684089471252",
            "B0B77J57MM"=>"8684089428034",
            "B0B2F2V9F5"=>"8684089412217",
            "B0B77H3TZL"=>"8684089428027",
            "B09CV2KXHG"=>"8684089471313",
            "B0B25M1BBS"=>"8684089469358",
            "B09P45ZH4K"=>"8684089408838",
            "B08DXTG457"=>"8684089408562",
            "B0B9BTZ2B1"=>"8684089409637",
            "B0BGP4KZS7"=>"8684089412408",
            "B08B5BF8DT"=>"8684089463455",
            "B0B77GZ6NJ"=>"8684089427976",
            "B09CTYVHZX"=>"8684089471436",
            "B09CTZRHJJ"=>"8684089471443",
            "B0B4DMV3P8"=>"8684089408579",
            "B08B5C1RQ6"=>"8684089463400",
            "B0B2DZ8Z84"=>"8684089412194",
            "B0BR4KNBQD"=>"8684089400900",
            "B0BQYRG6MP"=>"8684089400535",
            "B0BQYMDYCR"=>"8684089400542",
            "B0BRD8P64N"=>"8684089401297",
            "B089QZ7H9C"=>"8684089420557",
            "B089S4DFVS"=>"8684089449770",
            "B0B25P922C"=>"8684089469372",
            "B08B59K24J"=>"8684089463387",
            "B09WRFRP51"=>"8684089411777",
            "B0B77G547N"=>"8684089428058",
            "B09CTXZD1Z"=>"8684089471276",
            "B08VKYNPRN"=>"8684089400085",
            "B08DXVSVZB"=>"8684089408401",
            "B09P47N84Y"=>"8684089466098",
            "B0B2F1CNCH"=>"8684089467224",
            "B094GY5BSC"=>"8684089412576",
            "B09CTZ7JKQ"=>"8684089471238",
            "B08B5BDSBZ"=>"8684089463493",
            "B09P16WW2Z"=>"8684089465206",
            "B08956VHQF"=>"8684089449930",
            "B09N1BDBY1"=>"8684089412279",
            "B08LY9R1BF"=>"8684089416949",
            "B08LYZFFMN"=>"8684089416734",
            "B08LYR876B"=>"8684089416970",
            "B08LYY5X76"=>"8684089416604",
            "B09CV17NGY"=>"8684089471375",
            "B0B9BTVZJS"=>"8684089463929",
            "B0B77FWP81"=>"8684089428041",
            "B09D3PMNPD"=>"8684089471412",
            "B09N1QMZX5"=>"8684089466128",
            "B09P46YYVW"=>"8684089465152",
            "B09P45KTYP"=>"8684089465176",
            "B09P477P6K"=>"8684089465183",
            "B09P46363Z"=>"8684089465169",
            "B0B3YB76GL"=>"8684089412095",
            "B09N1BHHQ6"=>"8684089412811",
            "B09WRPW41N"=>"8684089470279",
            "B09WRTV5VR"=>"8684089401891",
            "B09WRP6TRG"=>"8684089470262",
            "B09P41PFWV"=>"8684089465114",
            "B09P43HVF1"=>"8684089465138",
            "B09P41KW2T"=>"8684089465145",
            "B09P43JYBP"=>"8684089465121",
            "B09N1BKDWS"=>"8684089412286",
            "B08PFH8BQP"=>"8684089446014",
            "B0B4DND2Q9"=>"8684089406544",
            "B089S5GL9M"=>"8684089445055",
            "B0B77FXLWB"=>"8684089428065",
            "B09D3QZVMJ"=>"8684089471399",
            "B09LD6R24M"=>"8684089437425",
            "B09CTZ4M9T"=>"8684089471283",
            "B08B5BSSWG"=>"8684089445949",
            "B0BBCVGKLK"=>"8684089402638",
            "B08CSTBZP9"=>"8684089471061",
            "B09CC1PN1V"=>"8684089400115",
            "B09CBKDS7N"=>"8684089400054",
            "B09CBMDXF6"=>"8684089400108",
            "B09CBHTRLN"=>"8684089400092",
            "B09CBGP5K3"=>"8684089400023",
            "B09CBDQCR7"=>"8684089400047",
            "B09CBT5JVK"=>"8684089400122",
            "B09CBX1V17"=>"8684089415072",
            "B09N1NR3CH"=>"8684089466159",
            "B0B2DWH8XT"=>"8684089469662",
            "B0BMHXJ276"=>"8684089425354",
            "B0BMJ713H5"=>"8684089425323",
            "B0BMJT742H"=>"8684089433571",
            "B0BMJC5ZBX"=>"8684089425316",
            "B0BMJLQ6BN"=>"8684089425293",
            "B0BMJV6TD1"=>"8684089425347",
            "B0BMJWVMW6"=>"8684089433588",
            "B0BMJ71S1L"=>"8684089425330",
            "B09N1CWRL2"=>"8684089412828",
            "B09N1CSJB6"=>"8684089412743",
            "B09N1GMPGZ"=>"8684089412781",
            "B08B5BZVQT"=>"8684089463394",
            "B0CCVP5YXK"=>"8684089407510",
            "B0B2F2C397"=>"8684089467231",
            "B08B59R42F"=>"8684089463448",
            "B08DXV76JM"=>"8684089432437",
            "B08B59SY53"=>"8684089445970",
            "B089S51J35"=>"8684089445048",
            "B0BGP3CGTZ"=>"8684089409132",
            "B0B9BXW9TK"=>"8684089412354",
            "B0B4DP5ZXM"=>"8684089408203",
            "B0B4DPMSC6"=>"8684089408210",
            "B08B59YMQJ"=>"8684089463479",
            "B09P45MX8T"=>"8684089466067",
            "B09CV1GYLV"=>"8684089471214",
            "B0B77LNXRR"=>"8684089419841",
            "B0B77LWN8N"=>"8684089428188",
            "B0B77MTWVX"=>"8684089428256",
            "B0B77N5W6L"=>"8684089428263",
            "B0B77KFNC4"=>"8684089428232",
            "B0B77NCHZ1"=>"8684089428195",
            "B0B77P8SMX"=>"8684089428270",
            "B0B77KM4BX"=>"8684089428287",
            "B0B77KSXM9"=>"8684089428249",
            "B0B77L653C"=>"8684089428201",
            "B0B77LD5L5"=>"8684089428225",
            "B0B77LLRJQ"=>"8684089428218",
            "B0B9BWQ23B"=>"8684089412378",
            "B0B4K1TKMP"=>"8684089406414",
            "B0B2DYDX6B"=>"8684089467248",
            "B089S4DQ6Z"=>"8684089445017",
            "B0B5S568DW"=>"8684089469303",
            "B089S55QGN"=>"8684089424678",
            "B089S43ZWN"=>"8684089437364",
            "B089S4157C"=>"8684089449626",
            "B089S3Y93H"=>"8684089437333",
            "B09CTY4N7R"=>"8684089471221",
            "B09C3ZGKQL"=>"8684089439504",
            "B0B5RD7B7Q"=>"8684089467644",
            "B0B5R9G4HL"=>"8684089467682",
            "B0B5RH562D"=>"8684089467675",
            "B0B5RBGW25"=>"8684089467668",
            "B0B5RDJNQW"=>"8684089469310",
            "B0B5RF2XXK"=>"8684089470101",
            "B0B5RFWRTF"=>"8684089470095",
            "B0B5RCSQ53"=>"8684089470156",
            "B0B5RH6J3R"=>"8684089470125",
            "B0B5RCNMR8"=>"8684089470118",
            "B09H665LZJ"=>"8684089423299",
            "B09H67ZXPL"=>"8684089423244",
            "B09H65DKKD"=>"8684089424920",
            "B09H677M6G"=>"8684089423275",
            "B09H67N5DZ"=>"8684089415133",
            "B09H67TQPQ"=>"8684089423312",
            "B09H66ZRKX"=>"8684089423374",
            "B09H67BLZJ"=>"8684089419018",
            "B09H67GLY6"=>"8684089419278",
            "B09H6772Z9"=>"8684089422155",
            "B09H6769S5"=>"8684089416178",
            "B09H665D4C"=>"8684089411548",
            "B09H67T4W6"=>"8684089410183",
            "B09C1YK7PW"=>"8684089439535",
            "B09C1YV6HQ"=>"8684089439559",
            "B09C1YGGKR"=>"8684089439566",
            "B0B4DRY4CZ"=>"8684089406520",
            "B09CTXQLBH"=>"8684089471467",
            "B09BB7K4LP"=>"8684089411210",
            "B09BBGLLLC"=>"8684089411265",
            "B09BBDNDRF"=>"8684089411319",
            "B09KNGMRTC"=>"8684089408722",
            "B09KNHWT4N"=>"8684089451865",
            "B09KNK5BW8"=>"8684089403345",
            "B09KNHX3DJ"=>"8684089416505",
            "B0B4DMDP4H"=>"8684089406537",
            "B09N1Q1QKW"=>"8684089466173",
            "B09N1PH6G1"=>"8684089466104",
            "B08B5B1HRD"=>"8684089463486",
            "B0B3YBHMFG"=>"8684089412026",
            "B0B3Y9CWQF"=>"8684089412033",
            "B0B3Y9MMCP"=>"8684089413795",
            "B0B3Y86BJL"=>"8684089412316",
            "B09CV1X9Y9"=>"8684089471474",
            "B0B4K2D2PB"=>"8684089408852",
            "B0B9BWD2ZD"=>"8684089409460",
            "B0B9BV5P7Z"=>"8684089412392",
            "B09N1PCW7S"=>"8684089466166",
            "B0B25NTXTS"=>"8684089469396",
            "B0B4DMNNVK"=>"8684089404212",
            "B09N19N7XH"=>"8684089412156",
            "B0B25P54VS"=>"8684089469341",
            "B0B77HD4GC"=>"8684089427983",
            "B0B9BVZKF3"=>"8684089464018",
            "B09CTZ8MB7"=>"8684089471290",
            "B09D3QZRQD"=>"8684089471382",
            "B0B77FQ4FC"=>"8684089428072",
            "B0BBCVXFGW"=>"8684089404144",
            "B09CTZMBKK"=>"8684089471351",
            "B09VCH4SN9"=>"8684089427853",
            "B09VCJMCTG"=>"8684089427822",
            "B09VCHVDGP"=>"8684089427877",
            "B09VCJMDYX"=>"8684089427860",
            "B09VCHRRRD"=>"8684089427839",
            "B09VCK5JS9"=>"8684089427846",
            "B09VCHNCH8"=>"8684089427914",
            "B09VCJWBL9"=>"8684089427921",
            "B09VCK82QP"=>"8684089427891",
            "B09VCHM7NM"=>"8684089427907",
            "B09VCJQRNX"=>"8684089427884",
            "B09VCH9473"=>"8684089427938",
            "B08B5C2G1K"=>"8684089445956",
            "B09VLCQSX8"=>"8684089427945",
            "B09VLD5JMB"=>"8684089427952",
            "B09N1HGNCV"=>"8684089412170",
            "B09WRFKXD4"=>"8684089411784",
            "B08955ZGYC"=>"8684089409002",
            "B08954K2MX"=>"8684089449923",
            "B08954DMSW"=>"8684089463288",
            "B089551FG3"=>"8684089463271",
            "B089552FL8"=>"8684089463318",
            "B0895653DD"=>"8684089463370",
            "B08956PXLL"=>"8684089463325",
            "B08956SP95"=>"8684089463301",
            "B089553ZZ3"=>"8684089463295",
            "B089557KPT"=>"8684089449978",
            "B08953Y6VL"=>"8684089409033",
            "B08954D9YL"=>"8684089463332",
            "B08953NHTT"=>"8684089463356",
            "B089554L1L"=>"8684089463349",
            "B08954MB7F"=>"8684089449954",
            "B089561CWS"=>"8684089409026",
            "B08952Y7R5"=>"8684089449947",
            "B09P4784PV"=>"8684089408821",
            "B08B5BKKTR"=>"8684089463424",
            "B08B5BVRFR"=>"8684089463462",
            "B08B5BFK2L"=>"8684089445987",
            "B09P44MWXH"=>"8684089413832",
            "B0BGP394SQ"=>"8684089408487",
            "B09WRG2RW2"=>"8684089401907",
            "B09WRHGC4L"=>"8684089406407",
            "B09L1JPBB1"=>"8684089417564",
            "B09L1HBY5X"=>"8684089411128",
            "B09L1J267Y"=>"8684089407831",
            "B09HR91MQ2"=>"8684089446502",
            "B09HR9P71K"=>"8684089470637",
            "B09HR96LC6"=>"8684089406421",
            "B09HR9QP6H"=>"8684089470620",
            "B09HR913F2"=>"8684089406384",
            "B09HRB5YXP"=>"8684089406391",
            "B09WRQ4T5T"=>"8684089411166",
            "B0982HYXBN"=>"8684089411173",
            "B08DXVLTNZ"=>"8684089408395",
            "B08VBH4GH8"=>"8684089421172",
            "B08VBK6HFW"=>"8684089421196",
            "B08VBK8H7H"=>"8684089421219",
            "B08VC862B5"=>"8684089421189",
            "B08VBR3N6X"=>"8684089421202",
            "B08VC3G87G"=>"8684089421226",
            "B0B4DP3K4T"=>"8684089413771",
            "B0B4DPK34M"=>"8684089404199",
            "B0B77DP6GK"=>"8684089427990",
            "B0B77F1Q6M"=>"8684089428003",
            "B09FJ51F51"=>"8684089401020",
            "B09FJ41TGJ"=>"8684089400986",
            "B09FJ3B9QR"=>"8684089400993",
            "B09FJ4G77G"=>"8684089401037",
            "B09FJ58JQP"=>"8684089412255",
            "B09FJ4KCLY"=>"8684089401006",
            "B09FJ48DPY"=>"8684089411906",
            "B09FJ46G51"=>"8684089400979",
            "B09FJ4QNHN"=>"8684089408234",
            "B09FJ3QV3D"=>"8684089401013",
            "B09FLZ7R4Z"=>"8684089415157",
            "B09FLZZZBL"=>"8684089408296",
            "B09FM1PZY1"=>"8684089415140",
            "B09FM1JH9H"=>"8684089408265",
            "B0B25PKTPG"=>"8684089469402",
            "B09CTZ31HG"=>"8684089471320",
            "B0B25PJVPY"=>"8684089469389",
            "B0B2F1BPVR"=>"8684089412200",
            "B0B4DR2BFL"=>"8684089408197",
            "B09P158PWM"=>"8684089465213",
            "B08945DSWR"=>"8684089418561",
            "B08945C21S"=>"8684089418592",
            "B0B9BX7P23"=>"8684089464032",
            "B093FVX7Q5"=>"8684089427785",
            "B093FV9D9Z"=>"8684089427808",
            "B093FW2YXM"=>"8684089427792",
            "B093FWCRZZ"=>"8684089427815",
            "B09CQ7JX41"=>"8684089471054",
            "B09CQ9F7SN"=>"8684089471078",
            "B09CQBSVBQ"=>"8684089471085",
            "B09CQC2ZX3"=>"8684089471092",
            "B09CQ8T7M8"=>"8684089471115",
            "B09CQ956VC"=>"8684089471047",
            "B09CQ8Z478"=>"8684089471139",
            "B09CQCPHG4"=>"8684089471146",
            "B09CQCJG2G"=>"8684089471153",
            "B09CQCX51Q"=>"8684089471160",
            "B09CQ995MX"=>"8684089471177",
            "B09CQC7XBX"=>"8684089471184",
            "B09CQBVY2Y"=>"8684089471191",
            "B09CQB31MB"=>"8684089471122",
            "B093CBSDGN"=>"8684089427754",
            "B093C7MTX5"=>"8684089427730",
            "B093C6TN1M"=>"8684089427747",
            "B093CBTTQ5"=>"8684089429765",
            "B093C89G8B"=>"8684089429802",
            "B093C91QG9"=>"8684089429727",
            "B093C92SY6"=>"8684089429796",
            "B093C8TQMF"=>"8684089429772",
            "B093CB88RL"=>"8684089429741",
            "B093C9J6GV"=>"8684089429758",
            "B093C9HS1C"=>"8684089429734",
            "B092QRXBPD"=>"8684089431133",
            "B092RMWNSR"=>"8684089449596",
            "B092RQ713X"=>"8684089449565",
            "B092SR21J1"=>"8684089431140",
            "B092STYYM2"=>"8684089449602",
            "B092STB22H"=>"8684089449541",
            "B092SZFVHZ"=>"8684089431164",
            "B092SZL3FP"=>"8684089449619",
            "B092T2GWZ8"=>"8684089449558",
            "B092T2W1DM"=>"8684089431157",
            "B092T2X43W"=>"8684089449572",
            "B092T1Z7G5"=>"8684089449589",
            "B098K281QP"=>"8684089469204",
            "B09N1DMVRS"=>"8684089412835",
            "B08DXTQV3C"=>"8684089408555",
            "B0B77GN8C9"=>"8684089411487",
            "B0B9BTZNRM"=>"8684089463998",
            "B09N1QSPNL"=>"8684089466135",
            "B09CTZ8HY3"=>"8684089471450",
            "B09CTZQJYQ"=>"8684089471207",
            "B0B2DZBP68"=>"8684089412187",
            "B09N1DLVH5"=>"8684089412330",
            "B0B2F3CGKN"=>"8684089412798",
            "B09CTY4KZQ"=>"8684089471368",
            "B0B2F1LGKH"=>"8684089412323",
            "B0B77H9YJN"=>"8684089427969",
            "B09N1D933C"=>"8684089412347",
            "B09CTYHXK6"=>"8684089471269",
            "B0B4DPP3ZW"=>"8684089404205",
            "B09N1HS1JL"=>"8684089412804",
            "B089RG6J89"=>"8684089420595",
            "B09P46SSZK"=>"8684089466081",
            "B09CTYT3DW"=>"8684089471429",
            "B08DXV7R41"=>"8684089408388",
            "B09CTY419G"=>"8684089471337",
            "B0BBVHQKPK"=>"8684089411883",
            "B09CTYCMV1"=>"8684089471306",
            "B09N1GP4TM"=>"8684089411968",
            "B0B5S4G7QD"=>"8684089411999",
            "B0C5JS61TS"=>"8684089407114",
            "B0DW4825BP"=>"8684089435131",
            "B0DW47HXBY"=>"8684089435988",
            "B0DW4745MW"=>"8684089435995",
            "B0DW45YG1C"=>"8684089436053",
            "B0DW471BQG"=>"8684089436107",
            "B0DW46194K"=>"8684089436114",
            "B0DW9CVST6"=>"8684089463936",
            "B0DW98MLQR"=>"8684089463974",
            "B0DW9FMPJF"=>"8684089463950",
            "B0BQSJQSV2"=>"8684089400269",
            "B09WYWFRFK"=>"8684089440753",
            "B0B7NG6WMY"=>"8684089415928",
            "B09RKB9PS2"=>"8684089440166",
            "B098HBFVM8"=>"8684089423381",
            "B097RMSQDT"=>"8684089412439",
            "B0B7JWTGP4"=>"8684089411616",
            "B098H2LHL5"=>"8684089449879",
            "B0B7JTGBMT"=>"8684089409583",
            "B09L1JZHR1"=>"8684089411852",
            "B09RJRDT5Q"=>"8684089440234",
            "B098GZ6MGK"=>"8684089449886",
            "B097RLDP2B"=>"8684089412491",
            "B0B7JVJYHN"=>"8684089412682",
            "B09NW3WX9P"=>"8684089411432",
            "B098GWN1HR"=>"8684089409873",
            "B0999LXM7V"=>"8684089439337",
            "B0999QNS39"=>"8684089411494",
            "B098H3752Z"=>"8684089411579",
            "B098GKS9KT"=>"8684089410442",
            "B098GR69BX"=>"8684089410459",
            "B098GVZMRN"=>"8684089412767",
            "B098GQRB8D"=>"8684089412774",
            "B099QTDLCP"=>"8684089410497",
            "B098GN2F6C"=>"8684089410466",
            "B09NW5X8MY"=>"8684089412651",
            "B09NW3K6Q5"=>"8684089420748",
            "B09MNL81N8"=>"8684089421233",
            "B09MNLQ4N8"=>"8684089410664",
            "B09MNL88CS"=>"8684089410701",
            "B09MNKTQXH"=>"8684089411654",
            "B09MNLF3S8"=>"8684089411661",
            "B09MNLB9S3"=>"8684089410725",
            "B09MNKYTL2"=>"8684089412606",
            "B09MNM2819"=>"8684089410732",
            "B09MNM7NPQ"=>"8684089411678",
            "B09MNLDLC3"=>"8684089412750",
            "B09VLDQKH9"=>"8684089410787",
            "B09VLFZD2K"=>"8684089411708",
            "B09VLFZM25"=>"8684089410794",
            "B09VLJ3GJW"=>"8684089411715",
            "B09VLGCKW4"=>"8684089410800",
            "B09VLDM48V"=>"8684089411722",
            "B09L1HNQSL"=>"8684089411111",
            "B09L1JZ5FK"=>"8684089412446",
            "B09L1HWV7Q"=>"8684089411845",
            "B09HR9LKFN"=>"8684089446588",
            "B09HR8MTDQ"=>"8684089448360",
            "B09HR996ZM"=>"8684089446472",
            "B09HSC4X1M"=>"8684089446632",
            "B09HSFLL7Y"=>"8684089446656",
            "B09JKNTF79"=>"8684089446373",
            "B0983YNGB5"=>"8684089446519",
            "B0B3YFS3PH"=>"8684089412101",
            "B0B3Y923LT"=>"8684089412071",
            "B0B3Y98FSL"=>"8684089412149",
            "B0BQSB37M5"=>"8684089400276",
            "B0DH8HQWM5"=>"8684089414006",
        ];
        $amazonConnectors = [
            'AU' => new AmazonConnector(Marketplace::getByPath('/Ayarlar/Pazaryerleri/Amazon/AmazonAU')),
            'UK' => new AmazonConnector(Marketplace::getByPath('/Ayarlar/Pazaryerleri/Amazon/AmazonUK')),
            'US' => new AmazonConnector(Marketplace::getByPath('/Ayarlar/Pazaryerleri/Amazon/AmazonUS')),
            'CA' => new AmazonConnector(Marketplace::getByPath('/Ayarlar/Pazaryerleri/Amazon/AmazonCA')),
            'JP' => new AmazonConnector(Marketplace::getByPath('/Ayarlar/Pazaryerleri/Amazon/AmazonJP')),
        ];

        $listingObject = new VariantProduct\Listing();
        $pageSize = 50;
        $offset = 0;
        $listingObject->setLimit($pageSize);
        $index = $offset;
        while (true) {
            $index++;
            $listingObject->setOffset($offset);
            $variantProducts = $listingObject->load();
            $offset += $pageSize;
            foreach ($variantProducts as $variantProduct) {
                $asin = $variantProduct->getUniqueMarketplaceId();
                if (!isset($targetList[$asin])) {
                    continue;
                }
                $amazonListings = $variantProduct->getAmazonMarketplace();
                $ean = $targetList[$asin];
                echo "Processing $asin ($ean)...\n";
                foreach ($amazonListings as $amazonListing) {
                    $currentEan = $amazonListing->getEan();
                    $sku = $amazonListing->getSku();
                    $country = $amazonListing->getMarketplaceId();
                    if ($currentEan === $ean) {
                        echo "  Skipping $country => $sku...\n";
                        continue;
                    }
                    echo "  Processing $country => $sku...\n";
                    $amazonConnector = match ($country) {
                        'AU' => $amazonConnectors['AU'],
                        'US','MX' => $amazonConnectors['US'],
                        'CA' => $amazonConnectors['CA'],
                        'JP' => $amazonConnectors['JP'],
                        default => $amazonConnectors['UK'],
                    };
                    $maxRetries = 2;
                    $attempt = 0;
                    $success = false;
                    while ($attempt < $maxRetries && !$success) {
                        try {
                            $amazonConnector->utilsHelper->patchSetEan($sku, $ean, $country);
                            $success = true;
                        } catch (\Exception $e) {
                            $attempt++;
                            echo "    Attempt $attempt failed.\n";
                            if ($attempt >= $maxRetries) {
                                echo "    Skipping $country => $sku...\n";
                                break;
                            }
                            sleep(max(2 ** $attempt, 15));
                        }

                    }
                }

            }

        }


    }




    /**
     * @throws RandomException
     * @throws JsonException
     * @throws \Exception
     */
    public function commandSetAmazonBarcode(): void
    {   //  $this->setAmazonBarcode();
        $amazonConnectors = [
            'AU' => new AmazonConnector(Marketplace::getByPath('/Ayarlar/Pazaryerleri/Amazon/AmazonAU')),
            'UK' => new AmazonConnector(Marketplace::getByPath('/Ayarlar/Pazaryerleri/Amazon/AmazonUK')),
            'US' => new AmazonConnector(Marketplace::getByPath('/Ayarlar/Pazaryerleri/Amazon/AmazonUS')),
            'CA' => new AmazonConnector(Marketplace::getByPath('/Ayarlar/Pazaryerleri/Amazon/AmazonCA')),
            'JP' => new AmazonConnector(Marketplace::getByPath('/Ayarlar/Pazaryerleri/Amazon/AmazonJP')),
        ];

        $listingObject = new Product\Listing();
        $listingObject->setUnpublished(false);
        $listingObject->filterByEanGtin('868%', 'LIKE');
        $listingObject->setOrderKey('iwasku');
        $pageSize = 15;
        $offset = 0;
        $listingObject->setLimit($pageSize);
        $index = $offset;
        $carbon3daysAgo = Carbon::now()->subDays(1);
        $connectors = [];
        while (true) {
            $listingObject->setOffset($offset);
            $products = $listingObject->load();
            if (empty($products)) {
                break;
            }
            $offset += $pageSize;
            foreach ($products as $product) {
                $index++;
                echo "\rProcessing $index {$product->getId()} {$product->getIwasku()} ";
                $ean = $product->getEanGtin();/*
                if (empty($ean)) {
                    continue;
                }
                echo "EAN: $ean ";*/
                foreach ($product->getListingItems() as $variantProduct) {
                    if ($variantProduct->getLastUpdate() < $carbon3daysAgo) {
                        continue;
                    }
                    $marketplace = $variantProduct->getMarketplace();
                    if ($marketplace->getMarketplaceType() === 'Amazon') {
                        $amazonListings = $variantProduct->getAmazonMarketplace();
                        $newline = "\n";
                        foreach ($amazonListings as $amazonListing) {
                            $currentEan = $amazonListing->getEan();
                            if (empty($currentEan)) { //$currentEan === $ean
                                //echo "\n  Amazon: {$marketplace->getKey()} $currentEan SKIPPING\n";
                                continue;
                            }
                            $sku = $amazonListing->getSku();
                            $country = $amazonListing->getMarketplaceId();
                            if (empty($sku)) {
                                continue;
                            }
                            $amazonConnector = match ($country) {
                                'AU' => $amazonConnectors['AU'],
                                'US','MX' => $amazonConnectors['US'],
                                'CA' => $amazonConnectors['CA'],
                                'JP' => $amazonConnectors['JP'],
                                default => $amazonConnectors['UK'],
                            };
                            echo "$newline  Amazon: {$amazonConnector->marketplace->getKey()} $sku $country ";
                            $newline = "";
                            $maxRetries = 2;
                            $attempt = 0;
                            $success = false;
                            while ($attempt < $maxRetries && !$success) {
                                try {
                                    $amazonConnector->utilsHelper->patchSetEan($sku, $ean, $country);
                                    $success = true;
                                } catch (\Exception $e) {
                                    $attempt++;
                                    echo "Attempt $attempt failed: " . $e->getMessage() . "\n";
                                    if ($attempt >= $maxRetries) {
                                        echo "Max retry limit reached. Skipping.\n";
                                        break;
                                    }
                                    sleep(max(2 ** $attempt, 15));
                                }
                            }
                        }
                    }
                }
            }
        }
    }

    /**
     * @throws RandomException
     * @throws JsonException
     * @throws \Exception
     */
    public function commandSetAmazonCountryOfOrigin(): void
    {   //  $this->setAmazonBarcode();
        $amazonConnectors = [
            'AU' => new AmazonConnector(Marketplace::getByPath('/Ayarlar/Pazaryerleri/Amazon/AmazonAU')),
            'UK' => new AmazonConnector(Marketplace::getByPath('/Ayarlar/Pazaryerleri/Amazon/AmazonUK')),
            'US' => new AmazonConnector(Marketplace::getByPath('/Ayarlar/Pazaryerleri/Amazon/AmazonUS')),
            'CA' => new AmazonConnector(Marketplace::getByPath('/Ayarlar/Pazaryerleri/Amazon/AmazonCA')),
            'JP' => new AmazonConnector(Marketplace::getByPath('/Ayarlar/Pazaryerleri/Amazon/AmazonJP')),
        ];

        $listingObject = new VariantProduct\Listing();
        $listingObject->setUnpublished(false);
        $listingObject->filterByUniqueMarketplaceId('B%', 'LIKE');
        $pageSize = 15;
        $offset = 0;
        $listingObject->setLimit($pageSize);
        $index = $offset;
        while (true) {
            $listingObject->setOffset($offset);
            $listings = $listingObject->load();
            if (empty($listings)) {
                break;
            }
            $offset += $pageSize;
            foreach ($listings as $listing) {
                $index++;
                echo "\rProcessing $index {$listing->getId()} ";
                $marketplace = $listing->getMarketplace();
                $marketplaceType = $marketplace->getMarketplaceType();
                if ($marketplaceType !== 'Amazon') {
                    continue;
                }
                $amazonListings = $listing->getAmazonMarketplace();
                $newline = "\n";
                foreach ($amazonListings as $amazonListing) {
                    $currentCountryOfOrigin = $amazonListing->getMadeInTurkiye();
                    $sku = $amazonListing->getSku();
                    $country = $amazonListing->getMarketplaceId();
                    if ($currentCountryOfOrigin === 'TR') {
                        echo "\n  Amazon: {$marketplace->getKey()} $sku $country already set to TR, SKIPPING\n";
                        continue;
                    }
                    if (empty($sku)) {
                        continue;
                    }
                    $amazonConnector = match ($country) {
                        'AU' => $amazonConnectors['AU'],
                        'US', 'MX' => $amazonConnectors['US'],
                        'CA' => $amazonConnectors['CA'],
                        'JP' => $amazonConnectors['JP'],
                        default => $amazonConnectors['UK'],
                    };
                    echo "  ";
                    try {
                        $listingInfos = $amazonConnector->utilsHelper->getInfo($sku, $country, true, true);
                        $listingInfo = $listingInfos['listing'] ?? [];

                        $countryOfOrigin = $listingInfo['attributes']['country_of_origin'][0]['value'] ?? '';
                        $brand = $listingInfo['attributes']['brand'][0]['value'] ?? '';
                        $madeInTurkey = mb_substr_count(mb_strtolower($listingInfo['attributes']['product_description'][0]['value'] ?? ''), 'made in t');
                        $this->dbUpdateAmazonMarketplace($country, $sku, $amazonListing->getListingId(), $countryOfOrigin, $madeInTurkey, $brand);
                        if ($countryOfOrigin === 'TR') {
                            echo "\n  Amazon: {$marketplace->getKey()} $sku $country already set to TR, SAVING and SKIPPING\n";
                            continue;
                        }
                        echo "$newline  Amazon: {$amazonConnector->marketplace->getKey()} $sku $country ";
                        $newline = "";
                        $maxRetries = 3;
                        $attempt = 0;
                        $success = false;/*
                        while ($attempt < $maxRetries && !$success) {
                            try {
                                $amazonConnector->utilsHelper->patchSetCountryOfOrigin($sku, $country);
                                $success = true;
                            } catch (\Exception $e) {
                                $attempt++;
                                echo "Attempt $attempt failed: " . $e->getMessage() . "\n";
                                if ($attempt >= $maxRetries) {
                                    echo "Max retry limit reached. Skipping.\n";
                                    break;
                                }
                                sleep(min(pow(2, $attempt), 15));
                            }
                        }*/
                    } catch (\Exception $e) {
                        echo "Error: ".$e->getMessage()."\n";
                        sleep(1);
                    }
                }
            }
        }
    }

    /**
     * @throws Exception
     */
    private function dbUpdateAmazonMarketplace($country, $sku, $listingId, $countryOfOrigin, $madeInTurkey, $brand): void
    {
        $variables = "";
        $values = [
            'sku' => $sku,
            'country' => $country,
            'listingId' => $listingId,
        ];
        if (!empty($countryOfOrigin)) {
            $variables .= "countryOfOrigin = :countryOfOrigin,";
            $values['countryOfOrigin'] = $countryOfOrigin;
        }
        if (!empty($madeInTurkey)) {
            $variables .= "madeInTurkiye = :madeInTurkey,";
            $values['madeInTurkey'] = $madeInTurkey;
        }
        if (!empty($brand)) {
            $variables .= "brand = :brand,";
            $values['brand'] = $brand;
        }
        if (empty($variables)) {
            return;
        }
        $variables = rtrim($variables, ",");
        echo "  Updating DB using ".json_encode($values)."\n";
        $db = Db::get();
        $query = "UPDATE object_collection_AmazonMarketplace_varyantproduct SET ".$variables." WHERE sku = :sku AND marketplaceId = :country AND listingId = :listingId";
        $db->executeQuery($query, $values);
    }


    /**
     * @throws \Exception
     */
    public function commandConnectAmazonUs(): void
    {   // $this->connectAmazonUs();
        $grp = GroupProduct::getById(267975);
        $grpArray = [];
        $prdListObj = new Product\Listing();
        $prdListObj->setUnpublished(false);
        $prdList = $prdListObj->load();
        foreach ($prdList as $prd) {
            $found = false;
            echo "{$prd->getId()} ";
            $listings = $prd->getListingItems();
            foreach ($listings as $listing) {
                echo "{$listing->getId()} ";
                $amazonMarketplaces = $listing->getAmazonMarketplace() ?? [];
                foreach ($amazonMarketplaces as $amazonMarketplace) {
                    echo "{$amazonMarketplace->getMarketplaceId()} ";
                    if ($amazonMarketplace->getStatus() === 'Active' && $amazonMarketplace->getMarketplaceId() === 'US') {
                        $grpArray[] = $prd;
                        echo "added";
                        $found = true;
                        break;
                    }
                }
                if ($found) {
                    break;
                }
            }
            echo "\n";
        }
        $grp->setProducts($grpArray);
        $grp->save();
    }

    /**
     * @throws Exception
     * @throws \Exception
     */
    public function commandDeleteFrGpsr(): void
    {   // $this->deleteFr();
        $db = Db::get();
        $amazonConnector = new AmazonConnector(Marketplace::getById(200568)); // UK Amazon
        $amazonEuMarkets = ['IT', 'FR']; //['DE', 'FR', 'IT', 'ES', 'NL', 'BE', 'SE', 'PL'];
        $euMarketsPlaceholder = implode("','", $amazonEuMarkets);
        $query = "
            SELECT marketplaceId, sku
            FROM object_collection_AmazonMarketplace_varyantproduct
            WHERE fieldname = 'amazonMarketplace' 
                AND marketplaceId IN ('$euMarketsPlaceholder')
            ORDER BY marketplaceId, sku
        ";
        $skulist = $db->fetchAllAssociative($query);
        $total = count($skulist);
        $index = 0;
        foreach ($skulist as $sku) {
            $index++;
            echo "\rProcessing $index/$total ";
            $country = $sku['marketplaceId'];
            if (!in_array($country, $amazonEuMarkets)) {
                continue;
            }
            $sku = $sku['sku'];
            echo " $country $sku ";
            try {
                $amazonConnector->utilsHelper->patchGPSR($sku, $country);
            } catch (\Exception $e) {
                echo "Error: ".$e->getMessage()."\n";
            }
        }
        echo "\nFinished\n";
    }

    /**
     * @throws \Exception
     */
    public function commandAddMatteToVariantColors(): void
    {   // $this->addMatteToVariantColors();
        $pageSize = 5;
        $offset = 0;
        $listingObject = new Product\Listing();
        $listingObject->setUnpublished(true);
        $listingObject->setLimit($pageSize);
        $index = $offset;
        while (true) {
            $listingObject->setOffset($offset);
            $products = $listingObject->load();
            if (empty($products)) {
                break;
            }
            $offset += $pageSize;
            foreach ($products as $product) {
                $index++;
                echo "\rProcessing $index {$product->getId()} ";
                if ($product->level() < 1 || $product->getInheritedField('productCategory') !== 'IWA Metal') {
                    continue;
                }
                $variationColor = $product->getVariationColor();
                if (in_array($variationColor, ['Gold', 'Copper', 'Silver', 'IGOB', 'IGOS', 'ISOB', 'ISOG'])) {
                    echo "{$product->getInheritedField('productCategory')} {$variationColor} ";
                    $product->setVariationColor("Matte {$variationColor}");
                    $product->save();
                    echo "saved\n";
                    file_put_contents(PIMCORE_PROJECT_ROOT . "/tmp/product_add_matte.log", json_encode([
                        'timestamp' => date('Y-m-d H:i:s'),
                        'product' => $product->getId(),
                        'productCategory' => $product->getInheritedField('productCategory'),
                        'oldVariationColor' => $variationColor,
                        'newVariationColor' => "Matte {$variationColor}"
                    ]) . "\n", FILE_APPEND);
                }
            }
        }
    }

    /**
     * @throws \Exception
     */
    public function commandGetAmazonInfo(): void
    {   // $this->getAmazonInfo();
        
        $amazonConnector = [
            [
                'connector' => new AmazonConnector(Marketplace::getById(200568)), // UK Amazon
                'countries' => ['UK', 'DE', 'FR', 'IT', 'ES', 'NL', 'TR', 'SE', 'PL'],
            ],
            [
                'connector' => new AmazonConnector(Marketplace::getById(149795)), // US Amazon
                'countries' => ['US', 'MX'],
            ],
            [
                'connector' => new AmazonConnector(Marketplace::getById(234692)), // CA Amazon
                'countries' => ['CA'],
            ]
        ];
        $variantObject = new VariantProduct\Listing();
        $pageSize = 5;
        $offset = 0;
        $variantObject->setLimit($pageSize);
        $variantObject->setUnpublished(false);
        $index = $offset;
        while (true) {
            $variantObject->setOffset($offset);
            $results = $variantObject->load();
            if (empty($results)) {
                break;
            }
            $offset += $pageSize;
            foreach ($results as $listing) {
                $index++;
                echo "\rProcessing $index {$listing->getId()}";
                $amazonMarketplaces = $listing->getAmazonMarketplace() ?? [];
                if (empty($amazonMarketplaces)) {
                    continue;
                }
                echo "\n";
                foreach ($amazonMarketplaces as $amazonMarketplace) {
                    $country = $amazonMarketplace->getMarketplaceId();
                    foreach ($amazonConnector as $connector) {
                        if (!in_array($country, $connector['countries'])) {
                            continue;
                        }
                        $sku = $amazonMarketplace->getSku();
                        if (empty($sku)) {
                            continue;
                        }
                        echo " $country:$sku";
                        $connector['connector']->utilsHelper->getInfo($sku, $country);
                    }
                }
                echo "\n";
            }
        }
        echo "\nFinished\n";
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $argCommand = $input->getArgument('runCommand');
        if (!empty($argCommand)) {
            if (!is_array($argCommand)) {
                $argCommand = [$argCommand];
            }
            foreach ($argCommand as $command) {
                $cmd = str_replace(['(', ')'], '', $command);
                if (method_exists($this, $cmd)) {
                    $this->$cmd();
                } else {
                    echo "Command $cmd not found\n";
                }
            }
            return Command::SUCCESS;
        }

        $io = new SymfonyStyle($input, $output);
        $io->title('IWAPIM Interactive Shell');
        $context = [];

        while (true) {
            $command = $io->ask('');
            if (trim($command) === 'exit') {
                $io->success('Goodbye!');
                return 0;
            }
            try {
                $result = eval($command . ';');
                if ($result !== null) {
                    $io->writeln(var_export($result, true));
                }
                echo "\n";
                $context = get_defined_vars();
            } catch (Throwable $e) {
                $outputCaptured = ob_get_clean();
                if (!empty($outputCaptured)) {
                    $io->writeln($outputCaptured);
                }
                $io->error($e->getMessage());
            }
        }
    }
}
