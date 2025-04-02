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

        //$product = Product::findByField('iwasku', 'CA03300XW85K');
        //$product->checkStickerFnsku();

        $product = Product::findByField('iwasku', 'CA03300XW85K');
        $variants = $product->getListingItems();
        foreach ($variants as $variant) {
            $marketplace = $variant->getMarketplace();
            print_r($marketplace);
            break;
            echo $variant->getMa() . "\n";
        }

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
