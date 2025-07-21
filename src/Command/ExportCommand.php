<?php

namespace App\Command;

use Pimcore\Console\AbstractCommand;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Input\InputOption;
use Pimcore\Model\DataObject\Product\Listing as ProductListing;

#[AsCommand(
    name: 'app:export',
    description: 'Export json'
)]

class ExportCommand extends AbstractCommand
{
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $productListing = new ProductListing();
        $productListing->setLimit(10);
        $productListing->setOffset(0);
        $products = $productListing->load();
        foreach ($products as $product) {
            $level = $product->getProductLevel();
            $productKey = $product->getKey();
            echo  $productKey."\n";
            echo  $level."\n";


        }


        return Command::SUCCESS;
    }

}