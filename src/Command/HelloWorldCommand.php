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
                    case 'TrendyolCfw':
                    case 'TrendyolIwa':
                    case 'TrendyolFurni':
                        $returnsFiles[$marketplaceName] = $this->processTrendyol($jsonData);
                        break;
                    default:
                        break;
                }
            }
        }
        return $returnsFiles;
    }

    public function processTrendyol($jsonData)
    {
        $output = [];
        foreach ($jsonData as $index => $item) {
            $timestamp = $item['orderDate'];
            $order_id = $item['orderNumber'];
            $date = date('Y-m-d H:i:s', $timestamp);
            echo "DATE: $date\n ";
            if (isset($item['items'])) {
                foreach ($item['items'] as $item) {
                    $productId = $item['orderLine']['id'];
                    echo $order_id . "\t" . $productId . "\n";
                    $sql = "select iwasku, variant_name, parent_name, quantity from iwa_marketplace_orders_line_items where product_id = ? and order_id = ?";
                    $data = Utility::fetchFromSql($sql, [$productId, $order_id]);
                    echo "Iwasku: " . $data[0]['iwasku'] . "\n";
                   /* foreach ($item['claimItems'] as $claimItem) {
                        $newData = [
                            'date' => $date,
                            'iwasku' => $data[0]['iwasku'],
                            'variantName' => $data[0]['variant_name'],
                            'parentName' => $data[0]['parent_name'],
                            'mainReason' => $claimItem['trendyolClaimItemReason']['name'] ?? '',
                            'detailReason' => $claimItem['trendyolClaimItemReason']['externalReasonId'] ?? '',
                            'status' => $claimItem['claimItemStatus']['name'] ?? '',
                            'reasonCode' => $claimItem['trendyolClaimItemReason']['code'] ?? '',
                            'customerNote' => $claimItem['note'] ?? '',
                            'date' => $existingData['date'] ?? '',
                            'quantity' => $data[0]['quantity'],
                            'json' => json_encode($item)
                        ];
                        $output[$index] = $newData;
                    }*/
                }
            }
        }
        return $output;
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
                            'reasonCode' => '',
                            'customerCode' => '',
                            'date' => $existingData['date'] ?? '',
                            "quantity" => $returnItem['expectedQuantity'],
                            'json' => json_encode($item)
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
