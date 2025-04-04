<?php

namespace App\Command;

use App\Model\DataObject\VariantProduct;
use App\Utils\Utility;
use Doctrine\DBAL\Exception;
use Pimcore\Console\AbstractCommand;
use Pimcore\Db;
use Pimcore\Model\Asset;
use Pimcore\Model\DataObject\Product;
use Pimcore\Model\Element\DuplicateFullPathException;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Pimcore\Model\DataObject\GroupProduct;

#[AsCommand(
    name: 'app:hello-world',
    description: 'Outputs Hello, World!'
)]
class HelloWorldCommand extends AbstractCommand
{

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $stickerEu = Asset::getById(95320);
        if ($stickerEu) {
            echo "Yes";
        }
        else {
            echo "No";
        }
     //  $product = Product::findByField('iwasku', 'SC02400BKRMC');
      // $product->checkStickerFnsku();

        // NEW ALGORITHM
        /*$product = Product::findByField('iwasku', 'SC02400BKRMC');
        $variants = $product->getListingItems();
        $stickerFnskuList = [];
        $notEuArray = ['CA', 'US', 'MX', 'BR', 'SG', 'AU', 'JP'];
        foreach ($variants as $variant) {
            $marketplace = $variant->getMarketplace();
            $marketplacePath = $marketplace->getPath();
            $marketplacePathArray = explode('/', $marketplacePath);
            array_pop($marketplacePathArray);
            $marketplaceType = array_pop($marketplacePathArray);

            if ($marketplaceType === 'Amazon') {
                $amazonMarketplaceCollection = $variant->getAmazonMarketplace();
                $asin = $variant->getUniqueMarketplaceId();

                foreach ($amazonMarketplaceCollection as $amazonMarketplace) {
                    $marketplaceId = $amazonMarketplace->getMarketplaceId();
                    if (in_array($marketplaceId, $notEuArray)) {
                        continue;
                    }

                    $sql = "select * from iwa_inventory where asin = :asin and inventory_type = 'AMAZON_FBA'";
                    $result = Utility::fetchFromSql($sql, ['asin' => $asin]);
                    if (!empty($result)) {
                        if (!isset($stickerFnskuList[$asin])) {
                            $stickerFnskuList[$asin] = [];
                        }
                        foreach ($result as $item) {
                            if (!isset($stickerFnskuList[$asin]['return']) || !isset($stickerFnskuList[$asin]['notReturn'])) {
                                $stickerFnskuList[$asin]['return'] = [];
                                $stickerFnskuList[$asin]['notReturn'] = [];
                            }
                            $returnControl = $item['seller_sku'] ?? '';
                            $fnsku = $item['fnsku'] ?? '';
                            if (!str_starts_with($returnControl, 'amzn.gr')) {
                                if (!in_array($fnsku, $stickerFnskuList[$asin]['notReturn'])) {
                                    $stickerFnskuList[$asin]['notReturn'][] = $fnsku;
                                }
                            }
                            else {
                                if (!in_array($fnsku, $stickerFnskuList[$asin]['return'])) {
                                    $stickerFnskuList[$asin]['return'][] = $fnsku;
                                }
                            }
                        }
                    }
                }
            }
        }
        print_r(json_encode($stickerFnskuList));*/

        //where  inventory type = FBA
        // seller sku != amzn.gr != unexpected
        // Product Class Create FnskuPDF !!!!!!!/////////////////////////////////////////////////////////////////////////////
        /*$product = Product::findByField('iwasku', 'CA03300XW85K');
        $variants = $product->getListingItems();
        $stickerFnskuList = [];
        foreach ($variants as $variant) {
            $marketplace = $variant->getMarketplace();
            $marketplacePath = $marketplace->getPath();
            $marketplacePathArray = explode('/', $marketplacePath);
            array_pop($marketplacePathArray);
            $marketplaceType = array_pop($marketplacePathArray);
            // Control variant marketplace
            if ($marketplaceType === 'Amazon') {
                $amazonMarketplaceCollection = $variant->getAmazonMarketplace();
                $asin = $variant->getUniqueMarketplaceId();
                $notEuArray = ['CA', 'US', 'MX', 'BR', 'SG', 'AU', 'JP'];
                // Amazon MarketplaceCollection Loop
                foreach ($amazonMarketplaceCollection as $amazonMarketplace) {
                    $marketplaceId = $amazonMarketplace->getMarketplaceId();
                    // Control EU
                    if (in_array($marketplaceId, $notEuArray)) {
                        continue;
                    }
                    $fnsku = $amazonMarketplace->getFnsku();
                    if (!isset($fnsku)) {
                        continue;
                    }
                    // add new ASIN list
                    if (!isset($stickerFnskuList[$asin])) {
                        $stickerFnskuList[$asin] = [];
                    }
                    // add new fnsku for asin
                    if (!in_array($fnsku, $stickerFnskuList[$asin])) {
                        $stickerFnskuList[$asin][] = $fnsku;
                    }
                }
            }
        }
        print_r($stickerFnskuList);*/

        ///////////////////////////////////////////////////////////////////////////

       /* $stickerFnskuList = [];
        $variantProducts = VariantProduct::findByField('uniqueMarketplaceId', 'B08B5BJMR5');
        $variantProduct = $variantProducts[0];
        $amazonMarketplaceCollection = $variantProduct->getAmazonMarketplace();
        $asin = $variantProduct->getUniqueMarketplaceId();
        $notEuArray = ['CA', 'US', 'MX', 'BR', 'SG', 'AU', 'JP'];
        foreach ($amazonMarketplaceCollection as $amazonMarketplace) {
            $marketplaceId = $amazonMarketplace->getMarketplaceId();
            if (in_array($marketplaceId, $notEuArray)) {
                continue;
            }
            if ($marketplaceId )
            $fnsku = $amazonMarketplace->getFnsku();
            if (!isset($stickerFnskuList[$asin])) {
                $stickerFnskuList[$asin] = [];
            }
            if (!in_array($fnsku, $stickerFnskuList[$asin])) {
                $stickerFnskuList[$asin][] = $fnsku;
            }
        }
        print_r($stickerFnskuList);*/


       /* if ($product instanceof Product) {
            echo "Finded\n" ;
            $variantProducts =  $product->getListingItems();
            foreach ($variantProducts as $variantProduct) {
                if ($variantProduct instanceof VariantProduct) {
                    if ($variantProduct->getFnsku() !== null) {
                        echo $variantProduct->getFnsku() . "\n";
                    }
                }
            }
        }*/
        return Command::SUCCESS;
    }
}
