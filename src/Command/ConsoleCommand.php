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
            "B093CBSDGN" => "8684089427754",
            "B092TMMHYH" => "8684089409156",
            "B093C92SY6" => "8684089429796",
            "B08B59SY53" => "8684089445970",
            "B08LYLCKBR" => "8684089430464",
            "B08B59YMQJ" => "8684089463479",
            "B092TMR5YH" => "8684089439948",
            "B08DXVSVZB" => "8684089408401",
            "B08DXV7R41" => "8684089408388",
            "B08PFH8BQP" => "8684089446014",
            "B093C7MTX5" => "8684089427730",
            "B0927L985P" => "8684089430433",
            "B08B5BVRFR" => "8684089463462",
            "B08B5BJMR5" => "8684089463417",
            "B08VKYNPRN" => "8684089400085",
            "B089561CWS" => "8684089409026",
            "B08953Y6VL" => "8684089409033",
            "B08LYR3YSX" => "8684089430495",
            "B08LC74GHZ" => "8684089422858",
            "B08DXVLTNZ" => "8684089408395",
            "B08DXTQV3C" => "8684089408555",
            "B092QYSPQH" => "8684089440111",
            "B08B5BSSWG" => "8684089445949",
            "B093C6TN1M" => "8684089427747",
            "B0926LLTRS" => "8684089430396",
            "B092TLRBHM" => "8684089439955",
            "B08B5B1HRD" => "8684089463486",
            "B092TLKPWF" => "8684089439931",
            "B092SZL3FP" => "8684089449619",
            "B093C8TQMF" => "8684089429772",
            "B08LC6GZRG" => "8684089422377",
            "B093C89G8B" => "8684089429802",
            "B08953VZXF" => "8684089463363",
            "B08DXTG457" => "8684089408562",
            "B08B5BDSBZ" => "8684089463493",
            "B08LC7WCGP" => "8684089422872",
            "B08B59DWX8" => "8684089463431",
            "B08952Y7R5" => "8684089449947",
            "B08LC68KRN" => "8684089422896",
            "B08956SP95" => "8684089463301",
            "B08B5BFK2L" => "8684089445987",
            "B093C91QG9" => "8684089429727",
            "B08DXV76JM" => "8684089432437",
            "B093C6TN1M" => "8684089427747",
            "B08B5BBGSB" => "8684089445963",
            "B08955ZGYC" => "8684089409002",
            "B08B5C1RQ6" => "8684089463400",
            "B093C9J6GV" => "8684089429758",
            "B08LC6WQLP" => "8684089422834",
            "B08B5C2G1K" => "8684089445956",
            "B08954MB7F" => "8684089449954",
            "B08LC663SW" => "8684089422537",
            "B08LC8H67L" => "8684089422803",
            "B093C8TQMF" => "8684089429772",
            "B092QYWHTT" => "8684089440104",
            "B0895653DD" => "8684089463370",
            "B08LC8GW58" => "8684089422452",
            "B08956PXLL" => "8684089463325",
            "B08953NHTT" => "8684089463356",
            "B08LC5KSXB" => "8684089422667",
            "B0926LLTRS" => "8684089430396",
            "B089553ZZ3" => "8684089463295",
            "B08B59K24J" => "8684089463387",
            "B08LC8GW58" => "8684089422452",
            "B08DXTG457" => "8684089408562",
            "B092R148T6" => "8684089440098",
            "B08LC6D34G" => "8684089422919",
            "B08954DMSW" => "8684089463288",
            "B089552FL8" => "8684089463318",
            "B092TMR5YH" => "8684089439948",
            "B08954Q71D" => "8684089449961",
            "B08954K2MX" => "8684089449923",
            "B08B5B6YSK" => "8684089446007",
            "B08B59YMQJ" => "8684089463479",
            "B089551FG3" => "8684089463271",
            "B08B5BKKTR" => "8684089463424",
            "B089554L1L" => "8684089463349",
            "B08B5BBGSB" => "8684089445963",
            "B08PFJ6JCK" => "8684089445994",
            "B08LYLCKBR" => "8684089430464",
            "B08LYR3YSX" => "8684089430495",
            "B0927L985P" => "8684089430433",
            "B08LC68KRN" => "8684089422896",
            "B08B5B6YSK" => "8684089446007",
            "B08LC6GZRG" => "8684089422377",
            "B092QYSPQH" => "8684089440111",
            "B092QYWHTT" => "8684089440104",
            "B092TMMHYH" => "8684089409156",
            "B092TLRBHM" => "8684089439955",
            "B092TLKPWF" => "8684089439931",
            "B08LC6XTJ9" => "8684089422292",
            "B08LC6D34G" => "8684089422919",
            "B08LC74GHZ" => "8684089422858",
            "B08LC7P5C9" => "8684089422742",
            "B08DXVSVZB" => "8684089408401",
            "B08B5BDSBZ" => "8684089463493",
            "B08B5BSSWG" => "8684089445949",
            "B08DXV76JM" => "8684089432437",
            "B08B59SY53" => "8684089445970",
            "B08VKYNPRN" => "8684089400085",
            "B08LC6WQLP" => "8684089422834",
            "B08B5C2G1K" => "8684089445956",
            "B08955ZGYC" => "8684089409002",
            "B08954K2MX" => "8684089449923",
            "B08954DMSW" => "8684089463288",
            "B089552FL8" => "8684089463318",
            "B0895653DD" => "8684089463370",
            "B08956PXLL" => "8684089463325",
            "B08956SP95" => "8684089463301",
            "B089553ZZ3" => "8684089463295",
            "B089557KPT" => "8684089449978",
            "B08953Y6VL" => "8684089409033",
            "B08954Q71D" => "8684089449961",
            "B08954D9YL" => "8684089463332",
            "B08953NHTT" => "8684089463356",
            "B089554L1L" => "8684089463349",
            "B08954MB7F" => "8684089449954",
            "B089561CWS" => "8684089409026",
            "B08952Y7R5" => "8684089449947",
            "B08B5BDSBZ" => "8684089463493",
            "B08B5B1HRD" => "8684089463486",
            "B08B5B1HRD" => "8684089463486",
            "B08B5BJMR5" => "8684089463417",
            "B08B59DWX8" => "8684089463431",
            "B08B5BKKTR" => "8684089463424",
            "B08B5C1RQ6" => "8684089463400",
            "B08B59K24J" => "8684089463387",
            "B08B5BSSWG" => "8684089445949",
            "B08B5C2G1K" => "8684089445956",
            "B08B59SY53" => "8684089445970",
            "B08B5BFK2L" => "8684089445987",
            "B08B5BBGSB" => "8684089445963",
            "B08B5B6YSK" => "8684089446007",
            "B08DXVLTNZ" => "8684089408395",
            "B08PFH8BQP" => "8684089446014",
            "B089557KPT" => "8684089449978",
            "B08B5BFK2L" => "8684089445987",
            "B08B5BVRFR" => "8684089463462",
            "B08945DSWR" => "8684089418561",
            "B08945DSWR" => "8684089418561",
            "B093CBSDGN" => "8684089427754",
            "B093C7MTX5" => "8684089427730",
            "B093C89G8B" => "8684089429802",
            "B093C91QG9" => "8684089429727",
            "B093C92SY6" => "8684089429796",
            "B092SZL3FP" => "8684089449619",
            "B092T1Z7G5" => "8684089449589",
            "B08DXTQV3C" => "8684089408555",
            "B08DXV7R41" => "8684089408388",
            "B092R148T6" => "8684089440098",
            "B08LC6XTJ9" => "8684089422292",
            "B08LC7WCGP" => "8684089422872",
            "B08LC8H67L" => "8684089422803",
            "B08LC7P5C9" => "8684089422742",
            "B08LC5KSXB" => "8684089422667",
            "B08LC663SW" => "8684089422537",
            "B089551FG3" => "8684089463271",
            "B08954D9YL" => "8684089463332",        ];
        $amazonConnectors = [
            'AU' => new AmazonConnector(Marketplace::getByPath('/Ayarlar/Pazaryerleri/Amazon/AmazonAU')),
            'UK' => new AmazonConnector(Marketplace::getByPath('/Ayarlar/Pazaryerleri/Amazon/AmazonUK')),
            'US' => new AmazonConnector(Marketplace::getByPath('/Ayarlar/Pazaryerleri/Amazon/AmazonUS')),
            'CA' => new AmazonConnector(Marketplace::getByPath('/Ayarlar/Pazaryerleri/Amazon/AmazonCA')),
            'JP' => new AmazonConnector(Marketplace::getByPath('/Ayarlar/Pazaryerleri/Amazon/AmazonJP')),
        ];

        $listingObject = new VariantProduct\Listing();
        $pageSize = 50;
        $offset = 10000;
        $listingObject->setLimit($pageSize);
        $index = $offset;
        $newline = false;
        while (true) {
            $index++;
            $listingObject->setOffset($offset);
            $variantProducts = $listingObject->load();
            echo "$offset   \r";
            $newline = true;
            $offset += $pageSize;
            foreach ($variantProducts as $variantProduct) {
                $asin = $variantProduct->getUniqueMarketplaceId();
                if (!isset($targetList[$asin])) {
                    continue;
                }
                $amazonListings = $variantProduct->getAmazonMarketplace();
                $ean = $targetList[$asin];
                if ($newline) {
                    echo "\n";
                    $newline = false;
                }
                echo "Processing $asin ($ean)...\n";
                foreach ($amazonListings as $amazonListing) {
                    $currentEan = $amazonListing->getEan();
                    $sku = $amazonListing->getSku();
                    $country = $amazonListing->getMarketplaceId();
                    if ($currentEan === $ean) {
                        echo "  Skipping $country => $sku ($ean correct)...\n";
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
                        } catch (\Saloon\Exceptions\Request\Statuses\ForbiddenException) {
                            echo "    Unauthorized EAN [$ean]\n";
                            break;
                        } catch (Exception $e) {
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
