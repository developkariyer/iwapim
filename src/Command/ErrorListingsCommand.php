<?php

namespace App\Command;

use Pimcore\Console\AbstractCommand;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Input\InputOption;
use Pimcore\Model\DataObject\Folder;
use Pimcore\Model\DataObject\VariantProduct\Listing as VariantListing; 
use App\Utils\Utility;

#[AsCommand(
    name: 'app:errorlistings',
    description: 'List all error listings'
)]

class ErrorListingsCommand extends AbstractCommand
{
    protected function configure() 
    {
        $this
            ->addOption('notconnected',null, InputOption::VALUE_NONE, '')
            ->addOption('multiconnected',null, InputOption::VALUE_NONE, '')
            ;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        if ($input->getOption('notconnected')) {
            $this->notConnectedListings();
        }
        if ($input->getOption('multiconnected')) {
            $this->multiConnectedListings();
        }
        //$this->unpublishListings();
        return Command::SUCCESS;
    }

    private function unpublishListings()
    {
        $variantObject = new VariantListing();
        $pageSize = 50;
        $offset = 0;
        $variantObject->setLimit($pageSize);
        $variantObject->setUnpublished(true);      

        
        while (true) {
            $variantObject->setOffset($offset);
            $results = $variantObject->load();
            if (empty($results)) {
                break;
            }
            echo "Offset $offset to ".($offset+$pageSize)."\n";
            $offset += $pageSize;
            foreach ($results as $object) {
                $marketplace = $object->getMarketplace();
                $marketplaceKey = $marketplace->getKey();
                if (!$object->isPublished()) {
                    $object->setParent(Utility::checkSetPath("_Pasif",Utility::checkSetPath($marketplaceKey,Utility::checkSetPath("Pazaryerleri"))));
                    $object->save();
                }
            }
        }
    }


    private function notConnectedListings()
    {
        $filePath = PIMCORE_PROJECT_ROOT . '/tmp/notconnectedlistings.txt';
        $variantObject = new VariantListing();
        $pageSize = 50;
        $offset = 0;
        $variantObject->setLimit($pageSize);
        $variantObject->setUnpublished(false);
        while (true) {
            $variantObject->setOffset($offset);
            $results = $variantObject->load();
            if (empty($results)) {
                break;
            }
            echo "Offset $offset to ".($offset+$pageSize)."\n";
            $offset += $pageSize;
            foreach ($results as $object) {
                $mainProduct = $object->getMainProduct();
                if (!$mainProduct) {
                    $message = "Processing {$object->getId()}\n";   
                    echo $message;
                    file_put_contents($filePath, $message, FILE_APPEND);
                }
            }
        }
    }

    private function multiConnectedListings()
    {
        $filePath = PIMCORE_PROJECT_ROOT . '/tmp/multiconnectedlistings.txt';
        $variantObject = new VariantListing();
        $pageSize = 50;
        $offset = 0;
        $variantObject->setLimit($pageSize);
        $variantObject->setUnpublished(false);
        while (true) {
            $variantObject->setOffset($offset);
            $results = $variantObject->load();
            if (empty($results)) {
                break;
            }
            echo "Offset $offset to ".($offset+$pageSize)."\n";
            $offset += $pageSize;
            foreach ($results as $object) {
                $mainProduct = $object->getMainProduct();
                if (!$mainProduct) {
                    continue;
                }
                $mainProductCount = count($mainProduct);
                if ($mainProductCount > 1) {
                    $message = "Processing {$object->getId()}... Main product count: {$mainProductCount}\n";   
                    echo $message;
                    file_put_contents($filePath, $message, FILE_APPEND);
                }
            }
        }
    }

}