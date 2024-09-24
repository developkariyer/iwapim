<?php

namespace App\Command;

use Pimcore\Console\AbstractCommand;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Input\InputOption;
use Pimcore\Model\DataObject\Product;
use App\Model\DataObject\VariantProduct;


#[AsCommand(
    name: 'app:wisersell',
    description: 'Get product info'
)]

class WisersellCommand extends AbstractCommand
{

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $listingObject = new Product\Listing();
        // $products->setCondition(
        //     "iwasku IS NOT NULL AND iwasku != ? AND (wisersellId IS NULL OR wisersellId = ?) AND o_published = ?",
        //     ['', '', 1]);
        $listingObject->setLimit(10);
        $listingObject->setOffset(50);
        $products = $listingObject->load();
        foreach ($products as $product) {
            //var_dump($product);
            echo $product;
        }

        echo "Done\n";
       
        return Command::SUCCESS;
    }
}