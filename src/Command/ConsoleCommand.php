<?php

namespace App\Command;

use App\Connector\Marketplace\ShopifyConnector;
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
    private NotificationService $notificationService;

    function __construct(NotificationService $notificationService)
    {
        $this->notificationService = $notificationService;
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
        $variantProductIdList = "184150
184149
184148
184152
246045
246044
184319
184320
184318
184322
184323
184321
184325
184326
184324
255901
255902
255900
184158
184159
184160
184161
184162
184163
184631
184898
184900
184901
184902
184903
184165
184166
184167
184168
184169
184170
184172
184173
247729
184184
184183
184182
184188
184187
184186
184192
184195
184193
238543
238544
184204
238541
184205
238542
238546
238547
184206
238545
238548
238549
184208
184209
184210
184211
184212
184213
184215
184216
184217
184218
184219
184220
184222
184224
184225
184226
184227
184228
184229
184231
184232
184233
184234
184235
184236
184238
184241
184244
184249
184248
184253
184272
184271
184302
184303
184304
184305
184306
184307
184308
184309
184328
184329
184330
184331
184332
184333
184549
184550
184551
215057
184552
184553
184335
184336
184337
184338
184339
184342
184345
184381
184382
184383
184384
184385
184386
184387
184367
184368
184369
184370
184371
184372
184373
184374
184375
184376
184377
184378
184379
184380
184394
184396
265264
184389
265266
184403
246070
184405
184773
184407
184409
246060
270073
246061
246062
246063
270074
246064
184416
238108
238109
184417
238110
184418
184419
184420
184489
184490
184491
184492
184493
184422
184423
184424
184425
184426
184428
238111
238112
184429
238113
184430
184431
184432
184311
184313
184314
184315
184316
184448
184449
184446
184447
184444
184445
184442
184443
184477
184476
184475
184480
184479
184481
184482
184483
184484
246068
184485
184495
184497
184499
184501
184503
184506
184508
184509
184518
184517
184520
184521
184522
184524
184525
184531
184545
184544
184543
184558
184557
184560
184561
184562
184567
184569
184570
184572
184589
184590
184591
184592
184593
184595
184767
184582
184581
184579
184580
184584
184586
184587
246071
184597
184599
184600
184601
184602
184603
184604
184605
184606
184607
184608
184610
184611
184612
184613
184614
184619
184622
184623
184621
184626
184627
184625
184633
184634
184662
184664
184665
184666
184667
184668
184681
184687
184686
184688
184691
184692
184702
184701
184696
184695
184704
184703
184700
184699
184698
184697
184629
184714
184715
184716
184718
184719
184720
184728
184727
184726
238550
238551
184739
184740
184741
184742
184743
184749
184753
184755
184776
237956
184778
184779
184780
237957
184782
184784
184802
184803
184800
184801
184799
184805
184807
247731
184837
184839
184842
184844
184848
184847
184846
184852
184851
184850
241091
184574
247517
247516
184854
184855
184856
184857
184858
184859
247610
247609
247604
247603
247612
247611
247608
247607
247606
247605
247518
247519
184866
184865
184867
184868
184864
247621
247620
247615
247614
247623
247622
247619
247618
247617
247616
184770
184769
184771
184874
184875
184876
184877
184878
184880
184881
184882
184883
184884
184886
184887
184888
184889
184890
184892
184893
184894
184913
184912";

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
                if (empty($ean)) {
                    continue;
                }
                echo "EAN: $ean ";
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
                            if (!empty($currentEan)) { //$currentEan === $ean
                                echo "\n  Amazon: {$marketplace->getKey()} $currentEan SKIPPING\n";
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
                            $maxRetries = 5;
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
                                    sleep(min(pow(2, $attempt), 15));
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
                        $listingInfos = $amazonConnector->utilsHelper->getInfo($sku, $country, false, true);
                        $listingInfo = $listingInfos['listing'] ?? [];

                        $countryOfOrigin = $listingInfo['attributes']['countryOfOrigin'][0]['value'] ?? '';
                        $brand = $listingInfo['attributes']['brand'][0]['value'] ?? '';
                        $madeInTurkey = mb_substr_count(mb_strtolower($listingInfo['attributes']['productDescription'][0]['value'] ?? ''), 'made in t');
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
