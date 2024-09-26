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
    name: 'app:category',
    description: 'product category'
)]

class CategoryCommand extends AbstractCommand{
    
    protected function execute(InputInterface $input, OutputInterface $output): int{
        echo "\nStarted";
        $listingObject = new Product\Listing();
        $pageSize = 50;
        $offset = 0;
        
        $categoryMapping = [
            'IM' => 'Metal',
            'CM' => 'Metal',
            'CMS' => 'Metal',
            'IMS' => 'Metal',
            'SC' => 'Cam',
            'SCS' => 'Cam',
            'CT' => 'Cam',
            'IA' => 'Ahsap',
            'IAS' => 'Ahsap',
            'TA' => 'Ahsap',
            'TAS' => 'Ahsap',
            'TT' => 'Ahsap',
            'AHM' => 'Mobilya',
            'CA' => 'Harita',
            'CAS' => 'Harita',
            'CMA' => 'Harita',
            'DS' => 'Alsat',
            'IJ' => 'Taki',
            'KUL' => 'Kulube',
            'IT' => 'Tabletop',
            'ITS' => 'Tabletop',
            'KV' => 'Kanvas',
        ];


        while(true){
            $listingObject->setLimit($pageSize);
            $listingObject->setOffset($offset);
            $products = $listingObject->load();
            if(count($products) == 0){
                break;
            }
            echo "\nProcessed {$offset} ";
            $offset += $pageSize;

            foreach($products as $product){
                $parentPath = $product->getParent();
                $pathParts = explode('/', $parentPath);
                if (isset($pathParts[2])) {
                    $secondIndex = $pathParts[2]; 
                    $productCode = explode('-', $secondIndex)[0];
                    // if($productCode){
                        
    
                    // }
                    echo "\nProduct Code: {$productCode}";


                    //$product->setProductCategory($productCode);
                    
                }
            }
        }
        return Command::SUCCESS;
    }
}