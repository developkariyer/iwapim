<?php

namespace App\Command;

use Pimcore\Console\AbstractCommand;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Input\InputOption;
use Pimcore\Model\DataObject\VariantProduct\Listing as VariantListing; 


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
        return Command::SUCCESS;
    }

    private function multiConnectedListings()
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
                $mainProduct = $object->getMainProduct();
                if (!$mainProduct) {
                    echo "Main product not found for variant product: " .$id;
                    continue;
                }
                $mainProductCount = count($mainProduct);
                if ($mainProductCount > 1) {
                    echo "Processing {$object->getId()}... ";
                    echo "Main product count: {$mainProductCount}\n";   
                }
            }
        }
    }

}