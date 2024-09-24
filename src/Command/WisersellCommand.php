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
        $listingObject->setUnpublished(false);
        $listingObject->setCondition("iwasku IS NOT NULL AND iwasku != ? AND (wisersellId IS NULL OR wisersellId = ?)", ['', '']);
        $pageSize = 50;
        $offset = 0;

        while (true) {
            $listingObject->setLimit($pageSize);
            $listingObject->setOffset($offset);
            $products = $listingObject->load();
            if (empty($products)) {
                break;
            }
          
            echo "\nProcessed {$offset} ";
            $offset += $pageSize;
            // foreach ($products as $product) {
            //     //var_dump($product);
            //     $product->checkIwasku(true);
            //     echo $product->getIwasku() . "\n";
            // }

            foreach ($products as $product) {
                // iwasku alanını kontrol et
                if (!empty($product->getIwasku())) {
                    echo "iwasku değeri: " . $product->getIwasku(); // iwasku değeri mevcutsa yazdır
                } else {
                    echo "iwasku değeri boş."; // iwasku değeri boşsa
                }
            }
        }
        return Command::SUCCESS;
    }
}