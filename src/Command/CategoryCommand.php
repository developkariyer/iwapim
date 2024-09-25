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
        $pageSize = 1;
        $offset = 0;
        while(true){
            $products = $listingObject->load($pageSize, $offset);
            if(count($products) == 0){
                break;
            }
            foreach($products as $product){
                $output->writeln($product->getProductCategory());
            }
            $offset += $pageSize;
        }
    }
}