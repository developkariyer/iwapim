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
        foreach (glob($dir . '*/', GLOB_ONLYDIR) as $marketplaceDir) {
        $returnsFiles = [];
            $marketplaceName = basename($marketplaceDir);
            $returnsFilePath = $marketplaceDir . 'RETURNS.json';
            if (file_exists($returnsFilePath)) {
                $jsonData = json_decode(file_get_contents($returnsFilePath), true);
                $returnsFiles[$marketplaceName]['json'] = $jsonData;
                match ($marketplaceName) {
                    'BolIwa' => $returnsFiles[$marketplaceName] = $this->processBol($jsonData, $returnsFiles[$marketplaceName]),
                    default => null
                };

            }
        }
        return $returnsFiles;
    }

    public function processBol($jsonData, $existingData)
    {
        echo $jsonData['registrationDateTime'];
        if (isset($jsonData['returnItems'])) {
            echo "ISSET IF";
            foreach ($jsonData['returnItems'] as $returnItem) {
                $ean = $returnItem['ean'];
                $variant = VariantProduct::findOneByField('ean', $ean, $unpublished = true);
                if ($variant) {
                    print_r($variant->getTitle());
                } else {
                    echo "Variant not found for EAN: $ean\n";
                }
                $existingData['date'] = $jsonData['registrationDateTime'] ?? '';
                $existingData['mainReason'] = $returnItem['returnReason']['mainReason'] ?? '';
                $existingData['detailReason'] = $returnItem['returnReason']['detailedReason'] ?? '';
            }
        }
        else {
            echo "NOT ISSET IF";
        }
        return $existingData;
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
