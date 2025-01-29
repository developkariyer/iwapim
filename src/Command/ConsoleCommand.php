<?php

namespace App\Command;

use App\Connector\Marketplace\ShopifyConnector;
use Carbon\Carbon;
use Doctrine\DBAL\Exception;
use Pimcore\Console\AbstractCommand;
use Pimcore\Model\Notification\Service\NotificationService;
use Random\RandomException;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Input\InputOption;
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

    protected static function getJwtRemainingTime($jwt): int
    {
        $jwt = explode('.', $jwt);
        $jwt = json_decode(base64_decode($jwt[1]), true);
        return $jwt['exp'] - time();
    }

    /**
     * @throws Exception
     */
    public function sendTestNotification(): void
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
    public function setShopifySku(): void
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

        $carbonYesterday = Carbon::now()->subDays(1);
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
    public function setShopifyBarcode(): void
    {   //  $this->setShopifyBarcode();
        $listingObject = new Product\Listing();
        $listingObject->setUnpublished(false);
        $listingObject->filterByEanGtin('868%', 'LIKE');
        $listingObject->setOrderKey('iwasku');
        $pageSize = 5;
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
                echo "\rProcessing $index {$product->getId()} ";
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


/*
        foreach ($eans as $id => $ean) {
            echo "$id $ean\n";
            $variantProduct = VariantProduct::getById($id);
            if (!$variantProduct || $variantProduct->getLastUpdate() < $carbon7daysAgo) {
                echo "VariantProduct $id not found or too old\n";
                continue;
            }
            $connector->setBarcode($variantProduct, $ean);
        }*/
    }

    public function connectAmazonUs()
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

    public function deleteFr()
    {   // $this->deleteFr();
        $db = \Pimcore\Db::get();
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

    public function addMatteToVariantColors()
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

    public function getAmazonInfo()
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
        //$amazon = new AmazonConnector(Marketplace::getById(149795)); // US: 149795  UK: 200568
        //$amazon->patchCustom(sku: "CA_41_Burgundy_XL", country: "US", attribute: "item_type_keyword", operation: "delete", value: "");

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
            } catch (\Throwable $e) {
                $outputCaptured = ob_get_clean();
                if (!empty($outputCaptured)) {
                    $io->writeln($outputCaptured);
                }
                $io->error($e->getMessage());
            }
        }
    }
}
