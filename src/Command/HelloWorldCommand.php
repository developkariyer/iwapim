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
        $product = Product::findByField('iwasku', 'SC04000J0MY1');
        if ($product instanceof Product) {
            echo "Finded" ;
            $variantProducts =  $product->getListingItems();
            foreach ($variantProducts as $variantProduct) {
                print_r($variantProduct->getAmazonMarketplace() . " ");
                print_r($variantProduct->getFnsku());
                echo "\n";
            }

        }

        return Command::SUCCESS;
    }
}
