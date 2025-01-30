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

    public function commandSetEanRequires(): void
    {
        $variantProductIdList = "184974
184975
239490
184976
239491
239499
239500
239496
239497
239478
239479
239487
239488
184991
184982
184988
184985
184990
184981
184987
184984
184992
184983
184989
184986
186080
186092
186086
186079
186091
186085
186081
186077
186093
186087
186082
186094
186088
186083
186095
186089
186084
186078
186096
186090
185649
185777
185648
185650
185778
185779
186867
186868
186861
186862
186552
185805
185804
185806
186871
186870
186872
239481
239482
239484
239485
186864
186865
185003
185002
185004
185026
185027
185028
185746
185747
185748
184952
184953
185046
185047
185053
185054
184959
184960
186947
185010
185011
185012
186898
186899
186900
186902
186903
186904
186906
186907
186908
186922
186923
186924
186914
186915
186916
186874
186883
186875
186878
186885
186879
215501
215502
215504
186910
186911
186912
186032
186030
186031
185090
185091
185092
185112
185113
185114
239493
239494
186559
246132
186525
186542
186576
246115
187060
186551
246124
186517
186534
186568
246107
187052
186547
246120
186513
186530
186564
246103
187048
186555
246128
186521
186538
186572
246111
187056
186557
246130
186523
186540
186574
246113
187025
187058
186549
246122
186515
186532
186566
246105
186985
187050
186545
246118
186511
186528
186562
246101
186965
187046
186553
246126
186519
186536
186570
246109
187005
187054
186558
246131
186524
186541
186575
246114
187026
187059
186550
246123
186516
186533
186567
246106
186986
187051
186546
246119
186512
186529
186563
246102
186966
187047
186554
246127
186520
186537
186571
246110
187006
187055
186928
186929
186930
186026
186027
186028
186009
186010
186011
185816
185817
185818
186138
247362
247347
270259
247363
247348
270260
247364
247349
270261
247365
247360
247355
247350
270262
247345
267400
259859
267403
267406
267399
259858
267402
267405
186067
186224
186227
186225
186226
185679
185688
185682
185699
185697
185695
185685
185680
185689
185683
185700
185698
185696
185686
185509
185512
185510
185511
185475
185474
185476
185477
186287
186290
186288
186289
185488
185497
185491
185494
185489
185498
185492
185495
186124
186127
186125
186126
185344
185353
185347
185350
185345
185354
185348
185351
185346
185355
185349
185352
187185
187194
187191
187188
187186
187195
187192
187189
187187
187196
187193
187190
186320
186321
185439
185448
185445
185442
185440
185449
185446
185443
185441
185450
185447
185444
185660
185666
185664
185662
185661
185667
185665
185663
185905
185908
185906
185907
185536
185538
185535
185537
185910
185913
185911
185912
185456
185462
185458
185460
185457
185463
185459
185461
186242
186245
186243
186244
186129
186135
186131
186133
186130
186136
186132
186134
185133
185139
185137
185135
185134
185140
185138
185136
185163
185169
185165
185167
185164
185170
185166
185168
185328
185334
185332
185330
185329
185335
185333
185331
185540
185546
185544
185542
185541
185547
185545
185543
186501
186507
186505
186503
186502
186508
186506
186504
186072
186075
186073
186074
186115
186118
186116
186117
185720
185723
185722
185721
185188
185191
185189
185190
186315
186318
186316
186317
185408
185411
185409
185410
185306
185312
185310
185308
185307
185313
185311
185309
185888
185894
185892
185890
185889
185895
185893
185891
186414
186410
186399
186398
186396
186142
186146
186144
186145
186143
186345
186354
186348
186351
186346
186355
186349
186352
186347
186356
186350
186353
186360
186364
186362
186358
186361
186365
186363
186359
187171
187174
187172
187173
185735
185738
185736
185737
185107
185110
185108
185109
186102
186105
186103
186104
185940
185949
185943
185946
185941
185950
185944
185947
185942
185951
185945
185948
185469
185472
185470
185471
185669
185675
185671
185673
185670
185676
185672
185674
186069
186070
186170
186180
186165
186175
186166
186176
186161
186171
186167
186177
186162
186172
186168
186178
186163
186173
186169
186179
186164
186174
186207
186222
186212
186217
186203
186218
186208
186213
186204
186219
186209
186214
186205
186220
186210
186215
186206
186221
186211
186216
186186
186201
186191
186196
186182
186197
186187
186192
186183
186198
186188
186193
186184
186199
186189
186194
186185
186200
186190
186195
186485
186497
186491
186494
186488
186486
186498
186492
186495
186489
186487
186499
186493
186496
186490
184949
184947
184948
184946
185532
185533
185143
185144
185602
185603
185337
185338
186437
186438
185146
185148
185147
185703
185702
185704
185150
186335
186341
186332
186338
186336
186342
186333
186339
186337
186343
186334
186340
185841
185849
185837
185845
185842
185850
185838
185846
185843
185851
185839
185847
185844
185852
185840
185848
185315
185321
185317
185319
185316
185322
185318
185320
185916
185915
185614
185626
185622
185618
185615
185627
185623
185619
185616
185628
185624
185620
185617
185629
185625
185621
185991
185993
185992
185994
185826
185835
185829
185832
185824
185833
185827
185830
185825
185834
185828
185831
185983
185984
185985
185927
185936
185930
185933
185928
185937
185931
185934
185929
185938
185932
185935
185897
185900
185898
185899
186473
186476
186474
186475
186005
186006
186007
186313
185429
185433
185437
185428
185432
185436
185427
185431
185435
185426
185430
185434
186034
186035
186036
186037
186038
186039
186040
186041
186042
186043
186044
186045
185420
185416
185424
185419
185415
185423
185418
185414
185422
185417
185413
185421
186159
186155
186151
186158
186154
186150
186157
186153
186149
186156
186152
186148
186232
186240
186236
186231
186239
186235
186230
186238
186234
186229
186237
186233
185156
185158
185160
185157
185159
185161
260438
260439
269087
269078
269075
269084
269072
269081
269088
269079
269076
269085
269073
269082
270659
270656
270655
270658
270654
270657
270436
270549
270546
270552
270433
270440
270435
270548
270545
270551
270432
270439
185706
185712
185708
185710
185707
185713
185709
185711
186384
186390
186386
186388
186385
186391
186387
186389
186375
186381
186377
186379
186376
186382
186378
186380
186373
186372
186367
186370
186368
186369
185726
185727
185725
185729
185730
185728
185732
185733
185731
239476
185223
185225
185229
187136
187137
187138
187139
187140
187141
187143
187144
187145
187146
187147
187148
187129
187130
187131
187132
187133
187134
187150
187151
187152
187153
187154
187155
187157
187158
187159
187160
187161
187162
187164
187165
187166
187167
187168
187169
185173";

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
        $listingObject->filterByEanGtin('868%', 'NOT LIKE');
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
                        if (empty($ean)) {
                            continue;
                        }
                        echo "EAN: $ean ";
                        try {
                            $product->setEanGtin($ean);
                            $product->save();
                            break;
                        } catch (\Exception $e) {
                            echo "Error: ".$e->getMessage();
                        }
                    }
                }
                echo "\n";
            }
        }

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
                if (empty($ean)) {
                    continue;
                }
                echo "EAN: $ean ";
                foreach ($product->getListingItems() as $variantProduct) {
                    if ($variantProduct->getLastUpdate() < $carbon3daysAgo) {
                        continue;
                    }
                    $marketplace = $variantProduct->getMarketplace();
                    if (empty($marketplace) || $marketplace->getMarketplaceType() !== 'Shopify') {
                        continue;
                    }
                    echo "\n  {$marketplace->getKey()} ";
                    if ($marketplace->getKey() === 'ShopifyShukranEn') {
                        echo "Skipping";
                        continue;
                    }
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
                            $sku = $amazonListing->getSku();
                            $country = $amazonListing->getMarketplaceId();
                            if (empty($sku)) {
                                continue;
                            }
                            $amazonConnector = match ($country) {
                                'AU' => $amazonConnectors['AU'],
                                'US','MX' => $amazonConnectors['US'],
                                'CA' => $amazonConnectors['CA'],
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
