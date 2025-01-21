<?php

namespace App\Command;

use App\Connector\Marketplace\ShopifyConnector;
use Carbon\Carbon;
use Doctrine\DBAL\Exception;
use Pimcore\Console\AbstractCommand;
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
use App\Connector\Marketplace\Amazon\Constants as AmazonConstants;
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

    protected static function getJwtRemainingTime($jwt): int
    {
        $jwt = explode('.', $jwt);
        $jwt = json_decode(base64_decode($jwt[1]), true);
        return $jwt['exp'] - time();
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
            23978 => new ShopifyConnector(Marketplace::getById(23978)),/*
            84124 => new ShopifyConnector(Marketplace::getById(84124)),
            108415 => new ShopifyConnector(Marketplace::getById(108415)),
            108416 => new ShopifyConnector(Marketplace::getById(108416)),
            108417 => new ShopifyConnector(Marketplace::getById(108417)),
            108418 => new ShopifyConnector(Marketplace::getById(108418)),
            108419 => new ShopifyConnector(Marketplace::getById(108419)),*/
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
                echo "\rProcessing $index {$listing->getId()} ";
                if ($listing->getMarketplace()->getMarketplaceType() !== 'Shopify') {
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
        $eans = [
            "239153" => "8684089410701",
            "239152" => "8684089410688",
            "239151" => "8684089411692",
            "239150" => "8684089410695",
            "239148" => "8684089411685",
            "180387" => "8684089416468",
            "180386" => "8684089416239",
            "180385" => "8684089416123",
            "180384" => "8684089416369",
            "180383" => "8684089416536",
            "180382" => "8684089416277",
            "180381" => "8684089416161",
            "180380" => "8684089416420",
            "180373" => "8684089417328",
            "180372" => "8684089417236",
            "180371" => "8684089417182",
            "180370" => "8684089417281",
            "180369" => "8684089417359",
            "180368" => "8684089417250",
            "180367" => "8684089417212",
            "180366" => "8684089417304",
            "179886" => "8684089417908 ",
            "179884" => "8684089417816 ",
            "179883" => "8684089417809 ",
            "179882" => "8684089417786 ",
            "179881" => "8684089417762 ",
            "179880" => "8684089417700 ",
            "179780" => "8684089417137",
            "179779" => "8684089416895",
            "179778" => "8684089416703",
            "179777" => "8684089416987",
            "179776" => "8684089417021",
            "179775" => "8684089416765",
            "179774" => "8684089416574",
            "179773" => "8684089416932",
            "178508" => "8684089417793",
            "178507" => "8684089417779",
            "178506" => "8684089417755",
            "178505" => "8684089417748",
            "178504" => "8684089417731",
            "178095" => "8684089416468",
            "178094" => "8684089416536",
            "178093" => "8684089416369",
            "178092" => "8684089416420",
            "178091" => "8684089416239",
            "178090" => "8684089416277",
            "178089" => "8684089416123",
            "178088" => "8684089416161",
            "178087" => "8684089417021",
            "178086" => "8684089417137",
            "178085" => "8684089416932",
            "178084" => "8684089416987",
            "178083" => "8684089416765",
            "178082" => "8684089416895",
            "178081" => "8684089416574",
            "178080" => "8684089416703",
            "178079" => "8684089417328",
            "178078" => "8684089417359",
            "178077" => "8684089417281",
            "178076" => "8684089417304",
            "178075" => "8684089417236",
            "178074" => "8684089417250",
            "178073" => "8684089417182",
            "178072" => "8684089417212",
            "177024" => "8684089417892",
            "177023" => "8684089417861",
            "177022" => "8684089417830",
            "177015" => "8684089417656",
            "177011" => "8684089417649",
            "177000" => "8684089417632",
            "176999" => "8684089417625",
            "176993" => "8684089417618",
            "176989" => "8684089417601",
            "176978" => "8684089417595",
            "176977" => "8684089417588",
            "176971" => "8684089417571",
            "176967" => "8684089417564",
            "176956" => "8684089417557",
            "176953" => "8684089417540",
            "176952" => "8684089417533",
            "176951" => "8684089417526",
            "176950" => "8684089417519",
            "176948" => "8684089417502",
            "176947" => "8684089417496",
            "176946" => "8684089417489",
            "176945" => "8684089417472",
            "176870" => "8684089417465",
            "176869" => "8684089417441",
            "176868" => "8684089417403",
            "176867" => "8684089417267",
            "175443" => "8684089417878",
            "175442" => "8684089417847",
            "175441" => "8684089417823",
            "175439" => "8684089415997",
            "175438" => "8684089416024",
            "175437" => "8684089415836",
            "175434" => "8684089416055",
            "175433" => "8684089415935",
            "175432" => "8684089417724",
            "175431" => "8684089417717",
            "175430" => "8684089417694",
            "175429" => "8684089417687",
            "175428" => "8684089417670",
            "175427" => "8684089417663",
            "175225" => "8684089415997",
            "175224" => "8684089415836",
            "175223" => "8684089416024",
            "175220" => "8684089415935",
            "175219" => "8684089416055",
            "173934" => "8684089417854",
        ];

        $connector = new ShopifyConnector(Marketplace::getById(23978));
        $carbon7daysAgo = Carbon::now()->subDays(7);
        foreach ($eans as $id => $ean) {
            echo "$id $ean\n";
            $variantProduct = VariantProduct::getById($id);
            if (!$variantProduct || $variantProduct->getLastUpdate() < $carbon7daysAgo) {
                echo "VariantProduct $id not found or too old\n";
                continue;
            }
            $connector->setBarcode($variantProduct, $ean);
        }
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
