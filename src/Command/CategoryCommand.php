<?php

namespace App\Command;

use Pimcore\Console\AbstractCommand;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Input\InputOption;
use Pimcore\Model\DataObject\Product;


#[AsCommand(
    name: 'app:category',
    description: 'product category'
)]

class CategoryCommand extends AbstractCommand{
    protected function execute(InputInterface $input, OutputInterface $output): int{
        $listingObject = new Product\Listing();
        $pageSize = 50;
        $offset = 0;
        while(true){
            $products = $listingObject->load($pageSize, $offset);
            if(count($products) == 0){
                break;
            }
            echo "\nProcessed {$offset} ";
            $offset += $pageSize;
            foreach($products as $product){
                echo $product->getParent();

            }
        }
    }
}