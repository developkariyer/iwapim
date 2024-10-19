<?php

namespace App\Command;

use Pimcore\Console\AbstractCommand;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Pimcore\Model\DataObject\Marketplace;
use Pimcore\Model\DataObject\Product;

use App\Model\DataObject\VariantProduct;

use PhpOffice\PhpSpreadsheet\IOFactory;


#[AsCommand(
    name: 'app:prepare-table',
    description: 'Imports products from Shopify sites!'
)]

class PrepareTableCommand extends AbstractCommand
{
    // protected function configure() 
    // {
    //     $this
    //         ->addOption('prepare',null, InputOption::VALUE_NONE, 'Prepare table')
    //         ;
    // }
    
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        // echo "Transferring orders from Shopify order table\n";
        // $this->transferOrdersFromShopifyOrderTable();

        // $values = $this->fetchValues();
        // $index = 0;
        // echo "\n";
        // foreach ($values as $row) {
        //     $index++;
        //     if (!($index % 100)) echo "\rProcessing $index of " . count($values) . "                            \r";
        //     $this->prepareOrderTable($row['variant_id']);
        // }
        
        // $values = $this->fetchValues();
        // $coins = $this->exchangeCoin();

        // $this->updateCurrentCoin($coins);
        $this->transferOrders();
        return Command::SUCCESS;
    }

    protected function transferOrders()
    {
        $marketplaceList = Marketplace::getMarketplaceListAsArrayKeys();
        // $db = \Pimcore\Db::get();
        // $sql = "SELECT DISTINCT marketplace_id FROM iwa_marketplace_orders";
        // $marketplaceIds = $db->fetchAllAssociative($sql);
        echo $marketplaceList;
    }

    protected static function transferOrdersFromShopifyOrderTable()
    {
        $shopifySql = "INSERT IGNORE INTO iwa_marketplace_orders_line_items (
            marketplace_type, marketplace_key, product_code, parent_product_code, product_type,
            created_at, closed_at, order_id, product_id, variant_id, price, currency, quantity,
            vendor, variant_title, total_discount, referring_site, landing_site, subtotal_price,
            shipping_country, shipping_province, shipping_city, shipping_company, shipping_country_code,
            total_price, source_name, fulfillments_id, fulfillments_status, tracking_company,
            discount_code, discount_code_type, discount_value, discount_value_type,current_USD,current_EUR,created_date,total_price_tl,subtotal_price_tl)
        SELECT
            'Shopify' AS marketplace_type,
            NULL AS marketplace_key,
            NULL AS product_code,
            NULL AS parent_product_code,
            NULL AS product_type,
            JSON_UNQUOTE(JSON_EXTRACT(json, '$.created_at')) AS created_at,
            JSON_UNQUOTE(JSON_EXTRACT(json, '$.closed_at')) AS closed_at,               
            JSON_UNQUOTE(JSON_EXTRACT(json, '$.id')) AS order_id,
            JSON_UNQUOTE(JSON_EXTRACT(line_item.value, '$.product_id')) AS product_id,
            JSON_UNQUOTE(JSON_EXTRACT(line_item.value, '$.variant_id')) AS variant_id,
            JSON_UNQUOTE(JSON_EXTRACT(line_item.value, '$.price')) AS price,
            JSON_UNQUOTE(JSON_EXTRACT(json, '$.currency')) AS currency,        
            JSON_UNQUOTE(JSON_EXTRACT(line_item.value, '$.quantity')) AS quantity,
            JSON_UNQUOTE(JSON_EXTRACT(line_item.value, '$.vendor')) AS vendor,
            JSON_UNQUOTE(JSON_EXTRACT(line_item.value, '$.variant_title')) AS variant_title,
            JSON_UNQUOTE(JSON_EXTRACT(line_item.value, '$.total_discount')) AS total_discount,
            JSON_UNQUOTE(JSON_EXTRACT(json, '$.referring_site')) AS referring_site,
            JSON_UNQUOTE(JSON_EXTRACT(json, '$.landing_site')) AS landing_site,
            JSON_UNQUOTE(JSON_EXTRACT(json, '$.subtotal_price')) AS subtotal_price,  
            JSON_UNQUOTE(JSON_EXTRACT(json, '$.shipping_address.country')) AS shipping_country,
            JSON_UNQUOTE(JSON_EXTRACT(json, '$.shipping_address.province')) AS shipping_province,
            JSON_UNQUOTE(JSON_EXTRACT(json, '$.shipping_address.city')) AS shipping_city,
            JSON_UNQUOTE(JSON_EXTRACT(json, '$.shipping_address.company')) AS shipping_company,
            JSON_UNQUOTE(JSON_EXTRACT(json, '$.shipping_address.country_code')) AS shipping_country_code,
            JSON_UNQUOTE(JSON_EXTRACT(json, '$.total_price')) AS total_price,
            JSON_UNQUOTE(JSON_EXTRACT(json, '$.source_name')) AS source_name,
            JSON_UNQUOTE(JSON_EXTRACT(fulfillments.value, '$.id')) AS fulfillments_id,
            JSON_UNQUOTE(JSON_EXTRACT(fulfillments.value, '$.status')) AS fulfillments_status,
            JSON_UNQUOTE(JSON_EXTRACT(fulfillments.value, '$.tracking_company')) AS tracking_company,
            JSON_UNQUOTE(JSON_EXTRACT(discount_application.value, '$.code')) AS discount_code,
            JSON_UNQUOTE(JSON_EXTRACT(discount_application.value, '$.type')) AS discount_code_type,
            JSON_UNQUOTE(JSON_EXTRACT(discount_application.value, '$.value')) AS discount_value,
            JSON_UNQUOTE(JSON_EXTRACT(discount_application.value, '$.value_type')) AS discount_value_type,
            NULL AS current_USD,
            NULL AS current_EUR,
            NULL AS created_date,
            NULL AS total_price_tl,
            NULL AS subtotal_price_tl
        FROM
            iwa_shopify_orders
            CROSS JOIN JSON_TABLE(json, '$.line_items[*]' COLUMNS (
                value JSON PATH '$'
            )) AS line_item
            CROSS JOIN JSON_TABLE(json, '$.fulfillments[*]' COLUMNS (
                value JSON PATH '$'
            )) AS fulfillments
            CROSS JOIN JSON_TABLE(json, '$.discount_applications[*]' COLUMNS (
                value JSON PATH '$'
            )) AS discount_application
        WHERE
            JSON_UNQUOTE(JSON_EXTRACT(line_item.value, '$.variant_id')) IS NOT NULL
            AND JSON_UNQUOTE(JSON_EXTRACT(line_item.value, '$.variant_id')) != 'null'
            AND JSON_UNQUOTE(JSON_EXTRACT(line_item.value, '$.variant_id')) != ''
            AND CAST(JSON_UNQUOTE(JSON_EXTRACT(line_item.value, '$.variant_id')) AS UNSIGNED) > 0;
        ";
        $db = \Pimcore\Db::get();
        $db->query($shopifySql);
    }

    protected static function fetchValues()
    {
        $db = \Pimcore\Db::get();
        echo "Fetching variant IDs from Shopify line_items\n";
        $sql = "
        SELECT 
            iwa_marketplace_orders_line_items.variant_id AS variant_id,
            iwa_marketplace_orders_line_items.created_at AS created_at
        FROM 
            iwa_marketplace_orders_line_items";

        $values = $db->fetchAllAssociative($sql); 
        return $values;
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
            $tarih = $row[0] ?? null; 
            $usd = $row[1] ?? null; 
            $euro = $row[2] ?? null;   
        
            if ($tarih !== null) {
                $dateParts = explode('-', $tarih);
                if (count($dateParts) === 3) {
                    [$gun, $ay, $yil] = $dateParts;
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
            self::insertIntoTable($uniqueMarketplaceId,$marketplaceKey, $productCode, $parentProductCode, $productType);
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