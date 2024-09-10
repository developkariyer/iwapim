<?php

namespace App\Command;

use Pimcore\Console\AbstractCommand;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Pimcore\Model\DataObject\Marketplace;
use Pimcore\Model\DataObject\Product;
use Pimcore\Model\DataObject\Product\Listing;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use App\EventListener\DataObjectListener;
use Symfony\Component\Finder\Finder;
use Pimcore\Model\Asset;
use App\Utils\AmazonConnector;
use App\Utils\ShopifyConnector;
use App\Utils\EtsyConnector;
use App\Utils\TrendyolConnector;
use App\Utils\BolConnector;

use App\Model\DataObject\VariantProduct;

use PhpOffice\PhpSpreadsheet\IOFactory;


#[AsCommand(
    name: 'app:prepare-table',
    description: 'Imports products from Shopify sites!'
)]

class PrepareTableCommand extends AbstractCommand
{
    
    protected static function fetchValues()
    {
        $db = \Pimcore\Db::get();
    
        echo "Fetching variant IDs from Shopify line_items\n";
        $sql = "
        SELECT 
            iwa_marketplace_orders_line_items.variant_id AS variant_id,
            iwa_marketplace_orders_line_items.created_at AS created_at

        FROM 
            iwa_marketplace_orders_line_items
        ";
            
        $stmt = $db->executeQuery($sql);
        $values = $stmt->fetchAllAssociative(); 
        return $values;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $values = $this->fetchValues();
        foreach ($values as $row) {
            $this->prepareOrderTable($row['variant_id']);
        }
        
        $values = $this->fetchValues();
        $coins = $this->exchangeCoin();

        $this->updateCurrentCoin($coins);
        return Command::SUCCESS;
    }

    protected static function exchangeCoin()
    {
        $filePath = '/var/www/iwapim/tmp/EVDS.xlsx';

        $spreadsheet = IOFactory::load($filePath);

        $worksheet = $spreadsheet->getActiveSheet();
        $data = $worksheet->toArray();

        $result = [];

        $previousUsd = null;
        $previousEuro = null;

        foreach ($data as $row) {
            $tarih = isset($row[0]) ? $row[0] : null; 
            $usd = isset($row[1]) ? $row[1] : null; 
            $euro = isset($row[2]) ? $row[2] : null;   
        
            if ($tarih !== null) {
                $dateParts = explode('-', $tarih);
                if (count($dateParts) === 3) {
                    list($gun, $ay, $yil) = $dateParts;
                    $tarih = "$yil-$ay-$gun";
                } 
            }
        
            if ($usd !== null) {
                $previousUsd = $usd;
            } else {
                $usd = $previousUsd;
            }
        
            if ($euro !== null) {
                $previousEuro = $euro;
            } else {
                $euro = $previousEuro;
            }
        
            $result[$tarih] = [
                'usd' => $usd,
                'euro' => $euro
            ];
        }
        foreach ($result as &$item) {
            if (isset($item['tarih'])) {
                $dateParts = explode('-', $item['tarih']);                
                if (count($dateParts) === 3) {
                    list($gun, $ay, $yil) = $dateParts;        
                    $item['tarih'] = "$yil-$ay-$gun";
                } 
            }
        }
        return $result;
    }

    protected static function updateCurrentCoin($coins)
    {
        $db = \Pimcore\Db::get();
        $updateCreatedDateSql = "
            UPDATE iwa_marketplace_orders_line_items
            SET created_date = DATE(created_at);
        ";
        $db->executeUpdate($updateCreatedDateSql);
        $sql = "
        UPDATE iwa_marketplace_orders_line_items
        SET current_USD = ?, current_EUR = ?
        WHERE created_date = ?
        ";
        $stmt = $db->prepare($sql);
        foreach ($coins as $date => $coin) {
            $dateTime = \DateTime::createFromFormat('Y-m-d', $date);
            if ($dateTime && $dateTime->format('Y-m-d') === $date) {
                $stmt->bindValue(1, $coin['usd']);
                $stmt->bindValue(2, $coin['euro']);
                $stmt->bindValue(3, $date);

                $stmt->execute();
            }
        }
        $total_price_TL = "
        UPDATE `iwa_marketplace_orders_line_items` 
        SET 
            total_price_tl = ROUND(total_price * current_USD * 100) / 100,
            subtotal_price_tl = ROUND(subtotal_price * current_USD * 100) / 100
        ";
        $db->executeUpdate($total_price_TL);
        
    }
    protected static function prepareOrderTable($uniqueMarketplaceId)
    {
        $variantObject = VariantProduct::findOneByField('uniqueMarketplaceId', $uniqueMarketplaceId);
        if(!$variantObject) {
            return Command::FAILURE;
        }

        $marketplace = $variantObject->getMarketplace();
        if (!$marketplace instanceof Marketplace) {
            throw new \Exception('Marketplace is required for adding/updating VariantProduct');
        }

        $marketplaceKey = $marketplace->getKey(); // field 1
        if (!$marketplaceKey) {
            throw new \Exception('Marketplace key is required for adding/updating VariantProduct');
        }

        $mainProductObjectArray = $variantObject->getMainProduct(); // [] veya null
        if(!$mainProductObjectArray) {
            return Command::FAILURE;
        }

        $mainProductObject = reset($mainProductObjectArray);
        if ($mainProductObject instanceof Product) {
            $productCode = $mainProductObject->getProductCode(); //field 2
            if (!$productCode) {
                throw new \Exception('Product code is required for adding/updating VariantProduct');
            }
            if ($mainProductObject->level() == 1) {
                $parent = $mainProductObject->getParent();
                $parentProductCode = $parent->getProductCode(); // field 3
                if(!$parent) {
                    throw new \Exception('Parent is required for adding/updating VariantProduct');
                }
            } else {
                throw new \Exception('VariantProduct is misconfigured. Please fix it');
            }
            $productIdentifier = $mainProductObject->getInheritedField('ProductIdentifier');
            if (!$productIdentifier) {
                throw new \Exception('Product identifier is required for adding/updating VariantProduct');
            }
            $productType = strtok($productIdentifier,'-'); // field 4
            PrepareTableCommand::insertIntoTable($uniqueMarketplaceId,$marketplaceKey, $productCode, $parentProductCode, $productType);
        }
    }
    
    protected static function insertIntoTable($uniqueMarketplaceId,$marketplaceKey, $productCode, $parentProductCode, $productType)
    {

        $db = \Pimcore\Db::get();
        $tableName = "iwa_marketplace_orders_line_items";
        $sql = "UPDATE $tableName
            SET marketplace_key = ?, product_code = ?, parent_product_code = ?, product_type =?
            WHERE variant_id = $uniqueMarketplaceId;
            ";

        $stmt = $db->prepare($sql);
        $stmt->execute([$marketplaceKey, $productCode, $parentProductCode, $productType]);
    }
}