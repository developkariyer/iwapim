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

        $sql = "select fnsku from iwa_inventory where seller_sku= :seller_sku limit 1";
        $fnsku = Utility::fetchFromSql($sql, ['seller_sku' => 'TEMPERED32'])[0]['fnsku'];
        echo $fnsku . "\n";

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
