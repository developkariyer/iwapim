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
        $eans = [
            "179922" => "8684089419667 ",
            "179921" => "8684089419575 ",
            "179920" => "8684089419544 ",
            "179919" => "8684089419483 ",
            "179918" => "8684089419445 ",
            "179917" => "8684089419384 ",
            "179916" => "8684089419315 ",
            "179915" => "8684089419285 ",
            "179914" => "8684089419223 ",
            "179913" => "8684089419193 ",
            "179911" => "8684089419001",
            "179910" => "8684089418998 ",
            "179909" => "8684089418981 ",
            "179908" => "8684089418967 ",
            "179907" => "8684089418905 ",
            "179906" => "8684089418684 ",
            "179904" => "8684089418653",
            "179903" => "8684089418592 ",
            "179902" => "8684089418561 ",
            "179900" => "8684089418516 ",
            "179898" => "8684089418417 ",
            "179897" => "8684089418349 ",
            "179896" => "8684089418318 ",
            "179895" => "8684089418257 ",
            "179894" => "8684089418240 ",
            "179893" => "8684089418219 ",
            "179891" => "8684089418158",
            "179890" => "8684089418110 ",
            "179889" => "8684089418042 ",
            "179888" => "8684089418004 ",
            "179887" => "8684089417939 ",
            "178551" => "8684089418974 ",
            "178550" => "8684089418943",
            "178549" => "8684089418783",
            "178548" => "8684089418769",
            "178547" => "8684089418738",
            "178538" => "8684089418585",
            "178537" => "8684089418578",
            "178536" => "8684089418554",
            "178535" => "8684089418547",
            "178534" => "8684089418530",
            "178533" => "8684089418509",
            "178531" => "8684089418479",
            "178530" => "8684089418455",
            "178529" => "8684089418448",
            "178528" => "8684089418431",
            "178527" => "8684089418332",
            "178526" => "8684089418325",
            "178524" => "8684089418301",
            "178523" => "8684089418288",
            "178522" => "8684089418264",
            "178521" => "8684089418233",
            "178520" => "8684089418226",
            "178519" => "8684089418202",
            "178517" => "8684089418196",
            "178516" => "8684089418172",
            "178515" => "8684089418141",
            "178514" => "8684089418073",
            "178513" => "8684089418028",
            "178512" => "8684089417977",
            "178511" => "8684089417953",
            "178510" => "8684089417915",
            "178509" => "8684089417885",
            "177032" => "8684089418189",
            "177030" => "8684089418165",
            "177029" => "8684089418134",
            "177028" => "8684089418080",
            "177027" => "8684089418035",
            "177025" => "8684089417991",
            "176874" => "8684089417502",
            "176873" => "8684089417496",
            "176872" => "8684089417489",
            "176871" => "8684089417472",
            "175485" => "8684089419735",
            "175484" => "8684089419704",
            "175482" => "8684089419636",
            "175481" => "8684089419520",
            "175480" => "8684089419414",
            "175479" => "8684089419353",
            "175477" => "8684089419339",
            "175476" => "8684089418950",
            "175475" => "8684089418882",
            "175474" => "8684089418844",
            "175473" => "8684089418820",
            "175472" => "8684089418806",
            "175471" => "8684089418752",
            "175470" => "8684089418714",
            "175468" => "8684089418660",
            "175467" => "8684089418639",
            "175466" => "8684089418608",
            "175448" => "8684089418011",
            "175447" => "8684089417984",
            "175446" => "8684089417960",
            "175445" => "8684089417946",
            "175444" => "8684089417922",
            "174015" => "8684089419728",
            "174014" => "8684089419698",
            "174012" => "8684089419674",
            "174011" => "8684089419643",
            "174010" => "8684089419605",
            "174009" => "8684089419582",
            "174008" => "8684089419407",
            "174007" => "8684089419377",
            "174006" => "8684089419124",
            "174005" => "8684089419070",
            "173996" => "8684089418936",
            "173995" => "8684089418929",
            "173994" => "8684089418912",
            "173993" => "8684089418899",
            "173992" => "8684089418875",
            "173991" => "8684089418868",
            "173990" => "8684089418851",
            "173989" => "8684089418837",
            "173986" => "8684089418813",
            "173985" => "8684089418790",
            "173984" => "8684089418776",
            "173983" => "8684089418745",
            "173978" => "8684089418721",
            "173977" => "8684089418707",
            "173976" => "8684089418691",
            "173975" => "8684089418677",
            "173974" => "8684089418646",
            "173973" => "8684089418622",
            "173972" => "8684089418615",
            "173971" => "8684089418523",
            "173954" => "8684089418493",
            "173953" => "8684089418486",
            "173952" => "8684089418462",
            "173951" => "8684089418424",
            "173950" => "8684089418400",
            "173949" => "8684089418394",
            "173947" => "8684089418387",
            "173946" => "8684089418370",
            "173945" => "8684089418363",
            "173944" => "8684089418356",
            "173943" => "8684089418295",
            "173942" => "8684089418271",
            "173939" => "8684089418127",
            "173938" => "8684089418103",
            "173937" => "8684089418097",
            "173936" => "8684089418066",
            "173935" => "8684089418059",
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
