<?php

namespace App\Command;

use Pimcore\Console\AbstractCommand;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Input\InputOption;
use Pimcore\Model\DataObject\Marketplace;
use App\Connector\Marketplace\Amazon\Connector as AmazonConnector;
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
            ->addOption('notconnected',null, InputOption::VALUE_NONE, 'List listings without main product')
            ->addOption('multiconnected',null, InputOption::VALUE_NONE, 'List listings with multiple main products')
            ->addOption('unpublish',null, InputOption::VALUE_NONE, 'Move unpublished listings to _Pasif folders')
            ->addOption('updatecount',null, InputOption::VALUE_NONE, 'Update main product count')
            ->addOption('amazon-safety', null, InputOption::VALUE_NONE, 'Set gpsr_safety_attestation to TRUE and set dsa_responsible_person_email to responsible@iwaconcept.com')
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
        if ($input->getOption('unpublish')) {
            $this->unpublishListings();
        }
        if ($input->getOption('updatecount')) {
            $this->updateMainProductCounts();
        }
        if ($input->getOption('amazon-safety')) {
            $this->amazonSafetyFix();
        }
        return Command::SUCCESS;
    }

    private function amazonSafetyFix()
    {
        $db = \Pimcore\Db::get();
        $amazonConnector = new AmazonConnector(Marketplace::getById(200568)); // UK Amazon
        $amazonEuMarkets = ['DE', 'FR', 'IT', 'ES', 'NL', 'BE', 'SE', 'PL'];
        $euMarketsPlaceholder = implode("','", $amazonEuMarkets);
        $query = "
            SELECT marketplaceId, sku
            FROM object_collection_AmazonMarketplace_varyantproduct
            WHERE fieldname = 'amazonMarketplace' 
                AND status <> 'Active' 
                AND marketplaceId IN ('$euMarketsPlaceholder')
            ORDER BY marketplaceId, sku
        ";
        $skulist = $db->fetchAllAssociative($query);
        $total = count($skulist);
        $index = 0;
        foreach ($skulist as $sku) {
            $index++;
            echo "\rProcessing $index/$total ";
            $country = $sku['marketplaceId'];
            if (!in_array($country, $amazonEuMarkets)) {
                continue;
            }
            $sku = $sku['sku'];
            echo " $country $sku ";
            try {
                $amazonConnector->utilsHelper->patchListing($sku, $country);
            } catch (\Exception $e) {
                echo "Error: ".$e->getMessage()."\n";
            }
        }
        echo "\nFinished\n";
    }

    public function amazonInfo()
    {
        
    }

    private function updateMainProductCounts()
    {
        $variantObject = new VariantListing();
        $pageSize = 5;
        $offset = 0;
        $variantObject->setLimit($pageSize);
        $variantObject->setUnpublished(true);
        $index = 0;      
        while (true) {
            $variantObject->setOffset($offset);
            $results = $variantObject->load();
            if (empty($results)) {
                break;
            }
            $offset += $pageSize;
            foreach ($results as $object) {
                $index++;
                echo "\rProcessing $index {$object->getId()}";
                $mainProduct = $object->getMainProduct();
                $mainProductCount = $object->getCountMainProduct();
                $objectDirty = false;
                if (is_null($mainProductCount) || count($mainProduct) != $mainProductCount) {
                    $object->setCountMainProduct(count($mainProduct));
                    $objectDirty = true;
                }
                if ($objectDirty) {
                    $object->save();
                    echo " updated\n";
                }
            }
        }
        echo "\nFinished\n";
    }

    private function unpublishListings()
    {
        $variantObject = new VariantListing();
        $pageSize = 5;
        $offset = 0;
        $variantObject->setLimit($pageSize);
        $variantObject->setUnpublished(true);
        $index = 0;      
        while (true) {
            $variantObject->setOffset($offset);
            $results = $variantObject->load();
            if (empty($results)) {
                break;
            }
            $offset += $pageSize;
            foreach ($results as $object) {
                $index++;
                echo "\rProcessing $index {$object->getId()}";
                $marketplace = $object->getMarketplace();
                $marketplaceKey = $marketplace->getKey();
                if (!$object->isPublished()) {
                    if ($object->getParent()->getKey() === "_Pasif") {
                        continue;
                    }
                    echo " moving to _Pasif\n";
                    $object->setParent(Utility::checkSetPath("_Pasif",Utility::checkSetPath($marketplaceKey,Utility::checkSetPath("Pazaryerleri"))));
                    $object->save();
                }
            }
        }
        echo "\nFinished\n";
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