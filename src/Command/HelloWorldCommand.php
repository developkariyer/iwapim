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
    public function getReturnsFiles($dir) {
        $returnsFiles = [];
        foreach (glob($dir . '*/', GLOB_ONLYDIR) as $marketplaceDir) {
            $marketplaceName = basename($marketplaceDir);
            $returnsFilePath = $marketplaceDir . 'RETURNS.json';
            if (file_exists($returnsFilePath)) {
                $jsonData = json_decode(file_get_contents($returnsFilePath), true);
                switch ($marketplaceName) {
                    case 'BolIwa':
                        $returnsFiles[$marketplaceName] = $this->processBol($jsonData);
                        break;
                    default:
                        break;
                }
            }
        }
        return $returnsFiles;
    }

    public function processBol($jsonData)
    {
        $output = [];
        foreach ($jsonData as $index => $item) {
            if (isset($item['returnItems'])) {
                foreach ($item['returnItems'] as $returnItem) {
                    if (!isset($returnItem['ean'])) {
                        echo "EAN not found in return item\n";
                        continue;
                    }
                    $ean = $returnItem['ean'];
                    $variantObject = VariantProduct::findOneByField('ean', $ean, $unpublished = true);
                    if ($variantObject) {
                        $mainProductObjectArray = $variantObject->getMainProduct();
                    }
                    if(!$mainProductObjectArray) {
                        return;
                    }
                    $mainProductObject = reset($mainProductObjectArray);
                    if ($mainProductObject instanceof Product) {
                        $iwasku =  $mainProductObject->getInheritedField('Iwasku');
                        $path = $mainProductObject->getFullPath();
                        $parts = explode('/', trim($path, '/'));
                        $variantName = array_pop($parts);
                        $parentName = array_pop($parts);
                        $newData = [
                            'date' => $item['registrationDateTime'],
                            'iwasku' => $iwasku,
                            'variantName' => $variantName,
                            'parentName' => $parentName,
                            'mainReason' => $returnItem['returnReason']['mainReason'] ?? '',
                            'detailReason' => $returnItem['returnReason']['detailedReason'] ?? '',
                            'status' => $returnItem['handled'],
                            'date' => $existingData['date'] ?? '',
                            "quantity" => $returnItem['expectedQuantity'],
                            'json' => $jsonData
                        ];
                        $output[$index] = $newData;
                    }
                }
            }
        }
        return $output;
    }



    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $directory = PIMCORE_PROJECT_ROOT . '/tmp/marketplaces/';
        $returnsData = $this->getReturnsFiles($directory);
        $mergedJsonPath = PIMCORE_PROJECT_ROOT . '/tmp/merged_returns.json';
        file_put_contents($mergedJsonPath, json_encode($returnsData, JSON_PRETTY_PRINT));
        return Command::SUCCESS;
    }
}
