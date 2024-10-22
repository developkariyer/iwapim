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
        //$this->transferOrdersFromShopifyOrderTable();

        // $values = $this->fetchValues();
        // $index = 0;
        // echo "\n";
        // foreach ($values as $row) {
        //     $index++;
        //     if (!($index % 100)) echo "\rProcessing $index of " . count($values) . "\r";
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
        $marketplaceList = Marketplace::getMarketplaceList();
        $marketplaceListWithIds[] = [];
        foreach ($marketplaceList as $marketplace) {
            $marketplaceListWithIds[$marketplace->getId()] = $marketplace->getMarketplaceType();
        }
        $db = \Pimcore\Db::get();
        $sql = "SELECT DISTINCT marketplace_id FROM iwa_marketplace_orders";
        $marketplaceIds = $db->fetchAllAssociative($sql);
        foreach ($marketplaceIds as $marketplaceId) {
            $id = $marketplaceId['marketplace_id']; 

            if (isset($marketplaceListWithIds[$id])) {
                $marketplaceType = $marketplaceListWithIds[$id];
                echo "Marketplace ID: $id - Type: $marketplaceType\n";
                $result = match ($marketplaceType) {
                    'Shopify' => $this->transferOrdersFromShopifyOrderTable($id,$marketplaceType),
                    'Trendyol' => $this->transferOrdersTrendyol($id,$marketplaceType),
                };





            }
        }

    }

    protected static function transferOrdersTrendyol($marketPlaceId,$marketplaceType)
    {
        $trendyolSql = "
            INSERT INTO iwa_marketplace_orders_line_items (
            marketplace_type, marketplace_key, product_code, parent_product_code, product_type,
            created_at, closed_at, order_id, product_id, variant_id, price, currency, quantity,
            vendor, variant_title, total_discount, referring_site, landing_site, subtotal_price,
            shipping_country, shipping_province, shipping_city, shipping_company, shipping_country_code,
            total_price, source_name, fulfillments_id, fulfillments_status, tracking_company,
            discount_code, discount_code_type, discount_value, discount_value_type,current_USD,current_EUR)
            SELECT
                '$marketplaceType',
                NULL AS marketplace_key,
                NULL AS product_code,
                NULL AS parent_product_code,
                NULL AS product_type,
                FROM_UNIXTIME(JSON_UNQUOTE(JSON_EXTRACT(json, '$.orderDate')) / 1000) AS created_at,
                FROM_UNIXTIME(JSON_UNQUOTE(JSON_EXTRACT(json, '$.lastModifiedDate')) / 1000) AS closed_at,         
                JSON_UNQUOTE(JSON_EXTRACT(json, '$.orderNumber')) AS order_id,
                JSON_UNQUOTE(JSON_EXTRACT(line_item.value, '$.id')) AS product_id,
                JSON_UNQUOTE(JSON_EXTRACT(line_item.value, '$.productCode')) AS variant_id,
                JSON_UNQUOTE(JSON_EXTRACT(line_item.value, '$.price')) AS price,
                JSON_UNQUOTE(JSON_EXTRACT(json, '$.currencyCode')) AS currency,        
                JSON_UNQUOTE(JSON_EXTRACT(line_item.value, '$.quantity')) AS quantity,
                JSON_UNQUOTE(JSON_EXTRACT(line_item.value, '$.merchantId')) AS vendor,
                JSON_UNQUOTE(JSON_EXTRACT(line_item.value, '$.productName')) AS variant_title,
                JSON_UNQUOTE(JSON_EXTRACT(json, '$.totalDiscount')) AS total_discount,
                NULL AS referring_site,
                NULL AS landing_site,
                NULL AS subtotal_price,  
                NULL AS shipping_country,
                NULL AS shipping_province,
                JSON_UNQUOTE(JSON_EXTRACT(json, '$.shipmentAddress.city')) AS shipping_city,
                JSON_UNQUOTE(JSON_EXTRACT(json, '$.commercial')) AS shipping_company,
                JSON_UNQUOTE(JSON_EXTRACT(json, '$.shipmentAddress.countryCode')) AS shipping_country_code,
                JSON_UNQUOTE(JSON_EXTRACT(json, '$.totalPrice')) AS total_price,
                NULL AS source_name,
                NULL AS fulfillments_id,
                JSON_UNQUOTE(JSON_EXTRACT(json, '$.status')) AS fulfillments_status,
                JSON_UNQUOTE(JSON_EXTRACT(json, '$.cargoProviderName')) AS tracking_company,
                NULL AS discount_code,
                NULL AS discount_code_type,
                JSON_UNQUOTE(JSON_EXTRACT(json, '$.totalDiscount')) AS discount_value,
                NULL AS discount_value_type,
                NULL AS current_USD,
                NULL AS current_EUR
            FROM
                iwa_marketplace_orders
                CROSS JOIN JSON_TABLE(json, '$.lines[*]' COLUMNS (
                    value JSON PATH '$'
                )) AS line_item
            WHERE
                JSON_UNQUOTE(JSON_EXTRACT(line_item.value, '$.productCode')) IS NOT NULL
                AND JSON_UNQUOTE(JSON_EXTRACT(line_item.value, '$.productCode')) != 'null'
                AND JSON_UNQUOTE(JSON_EXTRACT(line_item.value, '$.productCode')) != ''
                AND CAST(JSON_UNQUOTE(JSON_EXTRACT(line_item.value, '$.productCode')) AS UNSIGNED) > 0
                AND marketplace_id = $marketPlaceId
			ON DUPLICATE KEY UPDATE
                marketplace_type = VALUES(marketplace_type),
                marketplace_key = VALUES(marketplace_key),
                product_code = VALUES(product_code),
                parent_product_code = VALUES(parent_product_code),
                product_type = VALUES(product_type),
                created_at = VALUES(created_at),
                closed_at = VALUES(closed_at),
                product_id = VALUES(product_id),
                variant_id = VALUES(variant_id),
                price = VALUES(price),
                currency = VALUES(currency),
                quantity = VALUES(quantity),
                vendor = VALUES(vendor),
                variant_title = VALUES(variant_title),
                total_discount = VALUES(total_discount),
                referring_site = VALUES(referring_site),
                landing_site = VALUES(landing_site),
                subtotal_price = VALUES(subtotal_price),
                shipping_country = VALUES(shipping_country),
                shipping_province = VALUES(shipping_province),
                shipping_city = VALUES(shipping_city),
                shipping_company = VALUES(shipping_company),
                shipping_country_code = VALUES(shipping_country_code),
                total_price = VALUES(total_price),
                source_name = VALUES(source_name),
                fulfillments_id = VALUES(fulfillments_id),
                fulfillments_status = VALUES(fulfillments_status),
                tracking_company = VALUES(tracking_company),
                discount_code = VALUES(discount_code),
                discount_code_type = VALUES(discount_code_type),
                discount_value = VALUES(discount_value),
                discount_value_type = VALUES(discount_value_type),
                current_USD = VALUES(current_USD),
                current_EUR = VALUES(current_EUR);
        ";
        $db = \Pimcore\Db::get();
        $db->query($trendyolSql);
    }

    protected static function transferOrdersFromShopifyOrderTable($marketPlaceId,$marketplaceType)
    {
        $shopifySql = "
            INSERT INTO iwa_marketplace_orders_line_items (
                marketplace_type, marketplace_key, product_code, parent_product_code, product_type,
                created_at, closed_at, order_id, product_id, variant_id, price, currency, quantity,
                vendor, variant_title, total_discount, referring_site, landing_site, subtotal_price,
                shipping_country, shipping_province, shipping_city, shipping_company, shipping_country_code,
                total_price, source_name, fulfillments_id, fulfillments_status, tracking_company,
                discount_code, discount_code_type, discount_value, discount_value_type, current_USD,
                current_EUR
            )
            SELECT
                '$marketplaceType',
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
                COALESCE(LEFT(JSON_UNQUOTE(JSON_EXTRACT(json, '$.landing_site')), 255), NULL) AS landing_site,
                JSON_UNQUOTE(JSON_EXTRACT(json, '$.subtotal_price')) AS subtotal_price,  
                JSON_UNQUOTE(JSON_EXTRACT(json, '$.shipping_address.country')) AS shipping_country,
                JSON_UNQUOTE(JSON_EXTRACT(json, '$.shipping_address.province')) AS shipping_province,
                JSON_UNQUOTE(JSON_EXTRACT(json, '$.shipping_address.city')) AS shipping_city,
                JSON_UNQUOTE(JSON_EXTRACT(json, '$.shipping_address.company')) AS shipping_company,
                JSON_UNQUOTE(JSON_EXTRACT(json, '$.shipping_address.country_code')) AS shipping_country_code,
                JSON_UNQUOTE(JSON_EXTRACT(json, '$.total_price')) AS total_price,
                JSON_UNQUOTE(JSON_EXTRACT(json, '$.source_name')) AS source_name,
                COALESCE(JSON_UNQUOTE(JSON_EXTRACT(fulfillments.value, '$.id')), NULL) AS fulfillments_id,
                COALESCE(JSON_UNQUOTE(JSON_EXTRACT(fulfillments.value, '$.status')), NULL) AS fulfillments_status,
                COALESCE(JSON_UNQUOTE(JSON_EXTRACT(fulfillments.value, '$.tracking_company')), NULL) AS tracking_company,
                COALESCE(JSON_UNQUOTE(JSON_EXTRACT(discount_application.value, '$.code')), NULL) AS discount_code,
                COALESCE(JSON_UNQUOTE(JSON_EXTRACT(discount_application.value, '$.type')), NULL) AS discount_code_type,
                COALESCE(JSON_UNQUOTE(JSON_EXTRACT(discount_application.value, '$.value')), NULL) AS discount_value,
                COALESCE(JSON_UNQUOTE(JSON_EXTRACT(discount_application.value, '$.value_type')), NULL) AS discount_value_type,
                NULL AS current_USD,
                NULL AS current_EUR
            FROM
                iwa_marketplace_orders
                CROSS JOIN JSON_TABLE(json, '$.line_items[*]' COLUMNS ( value JSON PATH '$' )) AS line_item
                LEFT JOIN JSON_TABLE(json, '$.fulfillments[*]' COLUMNS ( value JSON PATH '$' )) AS fulfillments ON TRUE
                LEFT JOIN JSON_TABLE(json, '$.discount_applications[*]' COLUMNS ( value JSON PATH '$' )) AS discount_application ON TRUE
            WHERE
                JSON_UNQUOTE(JSON_EXTRACT(line_item.value, '$.variant_id')) IS NOT NULL
                AND JSON_UNQUOTE(JSON_EXTRACT(line_item.value, '$.variant_id')) != 'null'
                AND JSON_UNQUOTE(JSON_EXTRACT(line_item.value, '$.variant_id')) != ''
                AND CAST(JSON_UNQUOTE(JSON_EXTRACT(line_item.value, '$.variant_id')) AS UNSIGNED) > 0
                AND marketplace_id = $marketPlaceId
            ON DUPLICATE KEY UPDATE
                marketplace_type = VALUES(marketplace_type),
                marketplace_key = VALUES(marketplace_key),
                product_code = VALUES(product_code),
                parent_product_code = VALUES(parent_product_code),
                product_type = VALUES(product_type),
                created_at = VALUES(created_at),
                closed_at = VALUES(closed_at),
                product_id = VALUES(product_id),
                variant_id = VALUES(variant_id),
                price = VALUES(price),
                currency = VALUES(currency),
                quantity = VALUES(quantity),
                vendor = VALUES(vendor),
                variant_title = VALUES(variant_title),
                total_discount = VALUES(total_discount),
                referring_site = VALUES(referring_site),
                landing_site = VALUES(landing_site),
                subtotal_price = VALUES(subtotal_price),
                shipping_country = VALUES(shipping_country),
                shipping_province = VALUES(shipping_province),
                shipping_city = VALUES(shipping_city),
                shipping_company = VALUES(shipping_company),
                shipping_country_code = VALUES(shipping_country_code),
                total_price = VALUES(total_price),
                source_name = VALUES(source_name),
                fulfillments_id = VALUES(fulfillments_id),
                fulfillments_status = VALUES(fulfillments_status),
                tracking_company = VALUES(tracking_company),
                discount_code = VALUES(discount_code),
                discount_code_type = VALUES(discount_code_type),
                discount_value = VALUES(discount_value),
                discount_value_type = VALUES(discount_value_type),
                current_USD = VALUES(current_USD),
                current_EUR = VALUES(current_EUR);
                ";
        $db = \Pimcore\Db::get();
        $db->query($shopifySql);
    }

    protected static function fetchValues()
    {
        $db = \Pimcore\Db::get();
        echo "Fetching variant IDs from Shopify line_items\n";
        // $sql = "
        // SELECT 
        //     iwa_marketplace_orders_line_items.variant_id AS variant_id,
        //     iwa_marketplace_orders_line_items.created_at AS created_at
        // FROM 
        //     iwa_marketplace_orders_line_items";
        $sql = "
            SELECT 
                DISTINCT variant_id
            FROM
                iwa_marketplace_orders_line_items";

        $values = $db->fetchAllAssociative($sql); 
        return $values;
    }

   
    protected static function prepareOrderTable($uniqueMarketplaceId,$marketplaceType)
    {
        $variantObject = match ($marketplaceType) {
            'Shopify' =>  VariantProduct::findOneByField('uniqueMarketplaceId', $uniqueMarketplaceId),
            'Trendyol' =>  VariantProduct::findOneByField('uniqueMarketplaceId', $uniqueMarketplaceId)
        };
        if(!$variantObject) {
            echo "VariantProduct with uniqueMarketplaceId $uniqueMarketplaceId not found\n";
            return;
        }

        $marketplace = $variantObject->getMarketplace();
        if (!$marketplace instanceof Marketplace) {
            echo "Marketplace not found for VariantProduct with uniqueMarketplaceId $uniqueMarketplaceId\n";
            return;
        }

        $marketplaceKey = $marketplace->getKey(); // field 1
        if (!$marketplaceKey) {
            echo "Marketplace key is required for adding/updating VariantProduct with uniqueMarketplaceId $uniqueMarketplaceId\n";
        }

        $mainProductObjectArray = $variantObject->getMainProduct(); // [] veya null
        if(!$mainProductObjectArray) {
            echo "Main product not found for VariantProduct with uniqueMarketplaceId $uniqueMarketplaceId\n";
            return;
        }

        $mainProductObject = reset($mainProductObjectArray);
        if ($mainProductObject instanceof Product) {
            $productCode = $mainProductObject->getProductCode(); //field 2
            if (!$productCode) {
                echo "Product code is required for adding/updating VariantProduct with uniqueMarketplaceId $uniqueMarketplaceId\n";
                return;
            }
            if ($mainProductObject->level() == 1) {
                $parent = $mainProductObject->getParent();
                if(!$parent) {
                    echo "Parent is required for adding/updating VariantProduct with uniqueMarketplaceId $uniqueMarketplaceId\n";
                    return;
                }
                $parentProductCode = $parent->getProductCode(); // field 3
                if (!$parentProductCode) {
                    echo "Parent product code is required for adding/updating VariantProduct with uniqueMarketplaceId $uniqueMarketplaceId\n";
                    return;
                }
            } else {
                echo "Main product is not a parent product for VariantProduct with uniqueMarketplaceId $uniqueMarketplaceId\n";
                return;
            }
            $productIdentifier = $mainProductObject->getInheritedField('ProductIdentifier');
            if (!$productIdentifier) {
                echo "Product identifier is required for adding/updating VariantProduct with uniqueMarketplaceId $uniqueMarketplaceId\n";
                return;
            }
            $productType = strtok($productIdentifier,'-'); // field 4
            self::insertIntoTable($uniqueMarketplaceId,$marketplaceKey, $productCode, $parentProductCode, $productType);
        }
    }

    protected static function insertIntoTable($uniqueMarketplaceId,$marketplaceKey, $productCode, $parentProductCode, $productType)
    {
        $db = \Pimcore\Db::get();
        $sql = "UPDATE iwa_marketplace_orders_line_items
            SET marketplace_key = ?, product_code = ?, parent_product_code = ?, product_type =?
            WHERE variant_id = $uniqueMarketplaceId;
            ";
        $stmt = $db->prepare($sql);
        $stmt->execute([$marketplaceKey, $productCode, $parentProductCode, $productType]);
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
}