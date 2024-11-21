<?php

namespace App\Command;

use Pimcore\Console\AbstractCommand;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Input\InputOption;
use Pimcore\Model\DataObject\Marketplace;
use Pimcore\Model\DataObject\Product;
use App\Model\DataObject\VariantProduct;
use PhpOffice\PhpSpreadsheet\IOFactory;
use App\Utils\Utility;


#[AsCommand(
    name: 'app:prepare-table'
)]

class PrepareOrderTableCommand extends AbstractCommand
{
    private $marketplaceListWithIds = [];

    protected function configure() 
    {
        $this
            ->addOption('transfer',null, InputOption::VALUE_NONE, 'Transfer iwa_marketplace_orders to iwa_marketplace_orders_line_items')
            ->addOption('variant',null, InputOption::VALUE_NONE, 'Process variant order data find main product')
            ->addOption('updateCoin',null, InputOption::VALUE_NONE, 'Update current coin')
            ->addOption('extraColumns',null, InputOption::VALUE_NONE, 'Insert extra columns')
            ;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        if($input->getOption('transfer')) {
            $this->transferOrders();
        }

        if($input->getOption('variant')) {
            $this->processVariantOrderData();
        }

        // ERRORR!!!!!!!
        if($input->getOption('updateCoin')) {
            $this->updateCurrentCoin();
        }

        if($input->getOption('extraColumns')) {
            $this->extraColumns();
        }
        $this->calculatePrice();
        return Command::SUCCESS;
    }
    
    protected function extraColumns()
    {
        $this->insertClosedAtDiff();
        $this->discountValue();
        $this->isfullfilled();
        $this->productCount();
        $this->calculatePrice();
        $this->countryCode();
        $this->parseUrl();  
        $this->productQuantity();
        $this->usaCode();
    }
        
    protected function transferOrders()
    {
        if (empty($this->marketplaceListWithIds)) {
            $this->marketplaceList();
        }
        $db = \Pimcore\Db::get();
        $sql = "SELECT DISTINCT marketplace_id FROM iwa_marketplace_orders";
        $marketplaceIds = $db->fetchAllAssociative($sql);
        foreach ($marketplaceIds as $marketplaceId) {
            $id = $marketplaceId['marketplace_id']; 
            if (isset($this->marketplaceListWithIds[$id])) {
                $marketplaceType = $this->marketplaceListWithIds[$id];
                echo "Marketplace ID: $id - Type: $marketplaceType\n";
                $result = match ($marketplaceType) {
                    'Shopify' => $this->transferOrdersFromShopifyOrderTable($id,$marketplaceType),
                    'Trendyol' => $this->transferOrdersTrendyol($id,$marketplaceType),
                    'Bol.com' => $this->transferOrdersFromBolcomOrderTable($id,$marketplaceType),
                    'Etsy' => $this->transferOrdersEtsy($id,$marketplaceType)
                };
                echo "Complated: $marketplaceType\n";
            }
        }

    }

    protected static function transferOrdersEtsy($marketPlaceId,$marketplaceType)
    {
        $etsySql = "
            INSERT INTO iwa_marketplace_orders_line_items (
            marketplace_type, marketplace_key, product_code, parent_product_code, product_type,
            created_at, closed_at, order_id, product_id, variant_id, sku, price, currency, quantity,
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
                FROM_UNIXTIME(CAST(JSON_UNQUOTE(JSON_EXTRACT(json, '$.created_timestamp')) AS UNSIGNED)) AS created_at,
                FROM_UNIXTIME(CAST(JSON_UNQUOTE(JSON_EXTRACT(json, '$.updated_timestamp')) AS UNSIGNED)) AS closed_at,         
                order_id AS order_id,
                JSON_UNQUOTE(JSON_EXTRACT(line_item.value, '$.listing_id')) AS product_id,
                JSON_UNQUOTE(JSON_EXTRACT(line_item.value, '$.product_id')) AS variant_id,
                JSON_UNQUOTE(JSON_EXTRACT(line_item.value, '$.sku')) AS sku,
                JSON_UNQUOTE(JSON_EXTRACT(line_item.value, '$.price.amount')) / JSON_UNQUOTE(JSON_EXTRACT(line_item.value, '$.price.divisor')) AS price,
                JSON_UNQUOTE(JSON_EXTRACT(line_item.value, '$.price.currency_code')) AS currency,        
                JSON_UNQUOTE(JSON_EXTRACT(line_item.value, '$.quantity')) AS quantity,
                JSON_UNQUOTE(JSON_EXTRACT(json, '$.seller_email')) AS vendor,
                JSON_UNQUOTE(JSON_EXTRACT(line_item.value, '$.title')) AS variant_title,
                JSON_UNQUOTE(JSON_EXTRACT(json, '$.discount_amt.amount')) / JSON_UNQUOTE(JSON_EXTRACT(json, '$.discount_amt.divisor'))  AS total_discount,
                NULL AS referring_site,
                NULL AS landing_site,
                JSON_UNQUOTE(JSON_EXTRACT(json, '$.subtotal.amount')) / JSON_UNQUOTE(JSON_EXTRACT(json, '$.subtotal.divisor'))  AS subtotal_price,  
                JSON_UNQUOTE(JSON_EXTRACT(json, '$.country_iso'))  AS shipping_country,
                NULL AS shipping_province,
                JSON_UNQUOTE(JSON_EXTRACT(json, '$.city')) AS shipping_city,
                NULL AS shipping_company,
                JSON_UNQUOTE(JSON_EXTRACT(json, '$.country_iso')) AS shipping_country_code,
                JSON_UNQUOTE(JSON_EXTRACT(json, '$.total_price.amount')) / JSON_UNQUOTE(JSON_EXTRACT(json, '$.total_price.divisor'))  AS total_price,  
                NULL AS source_name,
                NULL AS fulfillments_id,
                JSON_UNQUOTE(JSON_EXTRACT(json, '$.status')) AS fulfillments_status,
                JSON_UNQUOTE(JSON_EXTRACT(shipments.value, '$.carrier_name')) AS tracking_company,
                NULL AS discount_code,
                NULL AS discount_code_type,
                JSON_UNQUOTE(JSON_EXTRACT(line_item.value, '$.shop_coupon')) AS discount_value,
                NULL AS discount_value_type,
                NULL AS current_USD,
                NULL AS current_EUR
            FROM
                iwa_marketplace_orders
                CROSS JOIN JSON_TABLE(json, '$.transactions[*]' COLUMNS (
                    value JSON PATH '$'
                )) AS line_item
                CROSS JOIN JSON_TABLE(json, '$.shipments[*]' COLUMNS (
                    value JSON PATH '$'
                )) AS shipments
            WHERE
                JSON_UNQUOTE(JSON_EXTRACT(line_item.value, '$.product_id')) IS NOT NULL
                AND JSON_UNQUOTE(JSON_EXTRACT(line_item.value, '$.product_id')) != 'null'
                AND JSON_UNQUOTE(JSON_EXTRACT(line_item.value, '$.product_id')) != ''
                AND marketplace_id = $marketPlaceId
			ON DUPLICATE KEY UPDATE
                marketplace_type = VALUES(marketplace_type),
                created_at = VALUES(created_at),
                closed_at = VALUES(closed_at),
                product_id = VALUES(product_id),
                variant_id = VALUES(variant_id),
                sku = VALUES(sku),
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
        try {
            $db = \Pimcore\Db::get();
            $db->query($etsySql);
        } catch (\Exception $e) {
            echo "Error: " . $e->getMessage();
        }
    }

    protected static function transferOrdersTrendyol($marketPlaceId,$marketplaceType)
    {
        $trendyolSql = "
            INSERT INTO iwa_marketplace_orders_line_items (
            marketplace_type, marketplace_key, product_code, parent_product_code, product_type,
            created_at, closed_at, order_id, product_id, variant_id, sku, price, currency, quantity,
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
                JSON_UNQUOTE(JSON_EXTRACT(line_item.value, '$.sku')) AS sku,
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
                created_at = VALUES(created_at),
                closed_at = VALUES(closed_at),
                product_id = VALUES(product_id),
                variant_id = VALUES(variant_id),
                sku = VALUES(sku),
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
        try {
            $db = \Pimcore\Db::get();
            $db->query($trendyolSql);
        } catch (\Exception $e) {
            echo "Error: " . $e->getMessage();
        }
    }

    protected static function transferOrdersFromShopifyOrderTable($marketPlaceId,$marketplaceType)
    {
        $shopifySql = "
            INSERT INTO iwa_marketplace_orders_line_items (
                marketplace_type, marketplace_key, product_code, parent_product_code, product_type,
                created_at, closed_at, order_id, product_id, variant_id, sku, price, currency, quantity,
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
                JSON_UNQUOTE(JSON_EXTRACT(line_item.value, '$.sku')) AS sku,
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
                created_at = VALUES(created_at),
                closed_at = VALUES(closed_at),
                product_id = VALUES(product_id),
                variant_id = VALUES(variant_id),
                sku = VALUES(sku),
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
        try {
            $db = \Pimcore\Db::get();
            $db->query($shopifySql);
        } catch (\Exception $e) {
            echo "Error: " . $e->getMessage();
        }
    }

    protected static function transferOrdersFromBolcomOrderTable($marketPlaceId,$marketplaceType)
    {
        $bolcomSql = "
        INSERT INTO iwa_marketplace_orders_line_items (
            marketplace_type, marketplace_key, product_code, parent_product_code, product_type,
            created_at, closed_at, order_id, product_id, variant_id, sku, price, currency, quantity,
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
            JSON_UNQUOTE(JSON_EXTRACT(json, '$.orderPlacedDateTime')) AS created_at,
            JSON_UNQUOTE(JSON_EXTRACT(json, '$.orderPlacedDateTime')) AS closed_at,               
            JSON_UNQUOTE(JSON_EXTRACT(json, '$.orderId')) AS order_id,
            JSON_UNQUOTE(JSON_EXTRACT(order_item_detail.value, '$.product.ean')) AS product_id,
            JSON_UNQUOTE(JSON_EXTRACT(order_item_detail.value, '$.product.bolProductId')) AS variant_id,
            NULL AS sku,
            JSON_UNQUOTE(JSON_EXTRACT(order_item_detail.value, '$.unitPrice')) AS price,
            'EUR' AS currency,        
            JSON_UNQUOTE(JSON_EXTRACT(order_item_detail.value, '$.quantity')) AS quantity,
            JSON_UNQUOTE(JSON_EXTRACT(order_item_detail.value, '$.offer.offerId')) AS vendor,
            JSON_UNQUOTE(JSON_EXTRACT(order_item_detail.value, '$.product.title')) AS variant_title,
            NULL AS total_discount,
            NULL AS referring_site,
            NULL AS landing_site,
            NULL AS subtotal_price,  
            NULL AS shipping_country,
            NULL AS shipping_province,
            JSON_UNQUOTE(JSON_EXTRACT(json, '$.orderDetail.shipmentDetails.city')) AS shipping_city,
            NULL AS shipping_company,
            JSON_UNQUOTE(JSON_EXTRACT(json, '$.orderDetail.shipmentDetails.countryCode')) AS shipping_country_code,
            NULL AS total_price,
            NULL AS source_name,
            NULL AS fulfillments_id,
            COALESCE(JSON_UNQUOTE(JSON_EXTRACT(order_item.value, '$.fulfilmentStatus')), NULL) AS fulfillments_status,
            NULL AS tracking_company,
            NULL AS discount_code,
            NULL AS discount_code_type,
            NULL AS discount_value,
            NULL AS discount_value_type,
            NULL AS current_USD,
            NULL AS current_EUR
        FROM
            iwa_marketplace_orders
            CROSS JOIN JSON_TABLE(json, '$.orderItems[*]' COLUMNS ( value JSON PATH '$' )) AS order_item
            CROSS JOIN JSON_TABLE(json, '$.orderDetail.orderItems[*]' COLUMNS ( value JSON PATH '$' )) AS order_item_detail
        WHERE
            JSON_UNQUOTE(JSON_EXTRACT(order_item_detail.value, '$.product.bolProductId')) IS NOT NULL
            AND JSON_UNQUOTE(JSON_EXTRACT(order_item_detail.value, '$.product.bolProductId')) != 'null'
            AND JSON_UNQUOTE(JSON_EXTRACT(order_item_detail.value, '$.product.bolProductId')) != ''
            AND CAST(JSON_UNQUOTE(JSON_EXTRACT(order_item_detail.value, '$.product.bolProductId')) AS UNSIGNED) > 0
            AND marketplace_id = $marketPlaceId
        ON DUPLICATE KEY UPDATE
            marketplace_type = VALUES(marketplace_type),
            created_at = VALUES(created_at),
            closed_at = VALUES(closed_at),
            product_id = VALUES(product_id),
            variant_id = VALUES(variant_id),
            sku = VALUES(sku),
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
        try {
            $db = \Pimcore\Db::get();
            $db->query($bolcomSql);
        } catch (\Exception $e) {
            echo "Error: " . $e->getMessage();
        }
    }

    protected function processVariantOrderData()
    {
        if (empty($this->marketplaceListWithIds)) {
            $this->marketplaceList();
        }
        $marketplaceTypes = array_values(array_unique($this->marketplaceListWithIds));
        foreach ($marketplaceTypes as $marketplaceType) {
            $values = $this->fetchVariantInfo($marketplaceType);
            $index = 0;
            foreach ($values as $row) {
                $index++;
                if (!($index % 100)) echo "\rProcessing $index of " . count($values) . "\r";
                $this->prepareOrderTable($row['variant_id'],$row['product_id'], $row['sku'],$marketplaceType);
            }
            
        }
    }

    protected static function fetchVariantInfo($marketplaceType)
    {
        $db = \Pimcore\Db::get();
        $sql = "
            SELECT 
                DISTINCT variant_id,
                product_id,
                sku
            FROM
                iwa_marketplace_orders_line_items
            WHERE 
                marketplace_type = '$marketplaceType'
            ";
        $values = $db->fetchAllAssociative($sql); 
        return $values;
    }

    protected static function getTrendyolVariantProduct($uniqueMarketplaceId)
    {
        $sql = "
            SELECT object_id
            FROM iwa_json_store
            WHERE field_name = 'apiResponseJson'
            AND JSON_UNQUOTE(JSON_EXTRACT(json_data, '$.productCode')) = ?
            LIMIT 1;
        ";
        $db = \Pimcore\Db::get();
        $result = $db->fetchAllAssociative($sql, [$uniqueMarketplaceId]);
        $objectId = $result[0]['object_id'] ?? null;
        if ($objectId) {
            return VariantProduct::getById($objectId);
        }
        echo "VariantProduct with uniqueMarketplaceId $uniqueMarketplaceId not found\n";
        return null;
    }

    protected static function getShopifyVariantProduct($uniqueMarketplaceId, $productId, $sku)
    {
        $variantProduct = VariantProduct::findOneByField('uniqueMarketplaceId', $uniqueMarketplaceId,$unpublished = true);
        if ($variantProduct) {
            return $variantProduct;
        }
        /*$variantProduct = VariantProduct::findOneByField('uniqueMarketplaceId', $uniqueMarketplaceId);
        if ($variantProduct) {
            return $variantProduct;
        }
        $sql = "
            SELECT object_id
            FROM iwa_json_store
            WHERE (field_name = 'apiResponseJson' AND JSON_UNQUOTE(JSON_EXTRACT(json_data, '$.product_id')) = ?)
            LIMIT 1;
        ";
        $db = \Pimcore\Db::get();
        $result = $db->fetchAllAssociative($sql, [$productId]);
        $objectId = $result[0]['object_id'] ?? null;
        if ($objectId) {
            return VariantProduct::getById($objectId);
        }
        return null;*/

        /*$variantProduct = VariantProduct::findOneByField('uniqueMarketplaceId', $uniqueMarketplaceId,$unpublished = true);
        if ($variantProduct) {
            return $variantProduct;
        }
        $sql = "
            SELECT object_id
            FROM iwa_json_store
            WHERE field_name = 'apiResponseJson'
            AND JSON_UNQUOTE(JSON_EXTRACT(json_data, '$.product_id')) = ?
            LIMIT 1; ";
        $db = \Pimcore\Db::get();
        $result = $db->fetchAllAssociative($sql, [$productId]);
        $objectId = $result[0]['object_id'] ?? null;
        $randomObject = VariantProduct::getById($objectId);
        if (!$randomObject) {
            return null;
        }
        $mainProductObjectArray = $randomObject->getMainProduct();
        if (!$mainProductObjectArray) {
            return null;
        }
        $mainProductObject = reset($mainProductObjectArray);
        if (!$mainProductObject instanceof Product) {
            return null;
        }
        $marketplaceFolder = Utility::checkSetPath(
            Utility::sanitizeVariable($randomObject->getMarketplace()->getKey(), 190),
            Utility::checkSetPath('Pazaryerleri')
        );
        $path = Utility::sanitizeVariable('Diger');
        $parent = Utility::checkSetPath($path, $marketplaceFolder);
        $parent = Utility::checkSetPath(Utility::sanitizeVariable($productId), $parent);
        $newVariantProduct  = VariantProduct::addUpdateVariant(
            variant: [
                'imageUrl' =>  '',
                'urlLink' =>  null,
                'salePrice' => 0,
                'saleCurrency' => '',
                'title' => $variantTitle,
                'attributes' => '',
                'uniqueMarketplaceId' => $uniqueMarketplaceId,
                'apiResponseJson' => '',
                'published' => false,
                'sku' => '',
            ],
            importFlag: true,
            updateFlag: true,
            marketplace: $randomObject->getMarketplace(),
            parent: $parent
        );
        if (!$newVariantProduct) {
            return null;
        }
        try {
            $result = $mainProductObject->addVariant($newVariantProduct);
            echo "Result: $result\n";
        } catch (\Throwable $e) {
            echo "Error: {$e->getMessage()}\n";
            return null;
        }
        return $newVariantProduct;*/
    }

    protected static function getBolcomVariantProduct($uniqueMarketplaceId)
    {
        $variantProduct = VariantProduct::findOneByField('uniqueMarketplaceId', $uniqueMarketplaceId,$unpublished = true);
        if ($variantProduct) {
            return $variantProduct;
        }
        $sql = "
            SELECT object_id
            FROM iwa_json_store
            WHERE field_name = 'apiResponseJson'
            AND JSON_UNQUOTE(JSON_EXTRACT(json_data, '$.\"product-ids\".bolProductId')) = ?
            LIMIT 1;";
        
        $db = \Pimcore\Db::get();
        $result = $db->fetchAllAssociative($sql, [$uniqueMarketplaceId]);
        $objectId = $result[0]['object_id'] ?? null;
        if ($objectId) {
            return VariantProduct::getById($objectId);
        }
        echo "VariantProduct with uniqueMarketplaceId $uniqueMarketplaceId not found\n";
        return null;
    }

    protected static function getEtsyVariantProduct($uniqueMarketplaceId)
    {
        $variantProduct = VariantProduct::findOneByField('uniqueMarketplaceId', $uniqueMarketplaceId,$unpublished = true);
        if ($variantProduct) {
            return $variantProduct;
        }
    }

    protected static function prepareOrderTable($uniqueMarketplaceId, $productId, $sku, $marketplaceType)
    {
        $variantObject = match ($marketplaceType) {
            'Shopify' => self::getShopifyVariantProduct($uniqueMarketplaceId, $productId, $sku),
            'Trendyol' => self::getTrendyolVariantProduct($uniqueMarketplaceId),
            'Bol.com' => self::getBolcomVariantProduct($uniqueMarketplaceId),
            'Etsy' => self::getEtsyVariantProduct($uniqueMarketplaceId),
            default => null,
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
            self::insertIntoTable($uniqueMarketplaceId,$marketplaceKey, $productCode, $parentProductCode, $productType, $marketplaceType);
        }
    }

    protected static function insertIntoTable($uniqueMarketplaceId,$marketplaceKey, $productCode, $parentProductCode, $productType, $marketplaceType)
    {
        $db = \Pimcore\Db::get();
        $sql = "UPDATE iwa_marketplace_orders_line_items
            SET marketplace_key = ?, product_code = ?, parent_product_code = ?, product_type =?
            WHERE variant_id = $uniqueMarketplaceId AND marketplace_type= ?;
            ";
        $stmt = $db->prepare($sql);
        $stmt->execute([$marketplaceKey, $productCode, $parentProductCode, $productType, $marketplaceType]);
    }
   
    protected function marketplaceList()
    {
        $marketplaceList = Marketplace::getMarketplaceList();
        foreach ($marketplaceList as $marketplace) {
            $this->marketplaceListWithIds[$marketplace->getId()] = $marketplace->getMarketplaceType();
        }
    }

    protected static function exchangeCoin()
    {
        /*$filePath = PIMCORE_PROJECT_ROOT. '/src/Command/EVDS.xlsx';
        $spreadsheet = IOFactory::load($filePath);
        $worksheet = $spreadsheet->getActiveSheet();
        $data = $worksheet->toArray();
        $db = \Pimcore\Db::get();
        foreach ($data as $row) {
            // Excel'deki her satırdaki verilere karşılık gelen sütunları alalım
            $date = $row[0];  // Tarih
            $currency = $row[1];  // Döviz türü (USD, EUR, vb.)
            $value = $row[2];  // Döviz değeri
        
            // SQL sorgusunu hazırlayın (veritabanına eklemek için)
            $sql = "
            INSERT INTO iwa_currency_history (date, currency, value)
            VALUES (:date, :currency, :value)
            ";
        
            // Sorguyu hazırlayın
            $stmt = $db->prepare($sql);
        
            // Parametreleri bind edin
            $stmt->bindParam(':date', $date);
            $stmt->bindParam(':currency', $currency);
            $stmt->bindParam(':value', $value);
        
            // Sorguyu çalıştırın
            $stmt->execute();
        }*/
    }

    protected static function updateCurrentCoin()
    {
        /*$db = \Pimcore\Db::get();
        $sql = "
        UPDATE iwa_marketplace_orders_line_items AS orders
        JOIN iwa_currency_historyy AS history
        ON DATE(orders.created_at) = history.date
        SET orders.current_USD = history.usd, 
            orders.current_EUR = history.eur
        WHERE DATE(orders.created_at) = history.date;
        ";
        $stmt = $db->prepare($sql);
        $stmt->execute();*/

        /*
        $coins = self::exchangeCoin();
        $db = \Pimcore\Db::get();
        $sql = "
        UPDATE iwa_marketplace_orders_line_items
        SET current_USD = ?, current_EUR = ?
        WHERE DATE(created_at) = ?
        ";
        $stmt = $db->prepare($sql);
        foreach ($coins as $date => $coin) {
            $dateTime = \DateTime::createFromFormat('Y-m-d', $date);
            if ($dateTime && $dateTime->format('Y-m-d') === $date) {
                $stmt->execute([$coin['usd'], $coin['euro'], $date]);
            }
        }*/
    }

    protected function insertClosedAtDiff()
    {
        $db = \Pimcore\Db::get();
        $sql = "
        UPDATE iwa_marketplace_orders_line_items
        SET completion_day = DATEDIFF(DATE(closed_at), DATE(created_at))
        WHERE DATE(closed_at) IS NOT NULL;
        ";
        $stmt = $db->prepare($sql);
        $stmt->execute();
    }

    protected function discountValue()
    {
        $db = \Pimcore\Db::get();
        $sql = "
        UPDATE iwa_marketplace_orders_line_items
        SET has_discount = 
        CASE 
            WHEN discount_value IS NOT NULL AND discount_value <> 0.00 THEN TRUE
            ELSE FALSE
        END;
        ";
        $stmt = $db->prepare($sql);
        $stmt->execute();
    }

    protected function isfullfilled()
    {
        $db = \Pimcore\Db::get();
        $sql = "
        UPDATE iwa_marketplace_orders_line_items
        SET is_fulfilled = 
        CASE 
            WHEN fulfillments_status = 'success' 
            OR fulfillments_status = 'Delivered'
            OR fulfillments_status = 'HANDLED'
            THEN TRUE
            ELSE FALSE
        END;
        ";
        $stmt = $db->prepare($sql);
        $stmt->execute();
    }

    protected function productCount()
    {
        $db = \Pimcore\Db::get();
        $sql = "
            UPDATE iwa_marketplace_orders_line_items AS orders
            JOIN (
                SELECT order_id, COUNT(*) AS product_count
                FROM iwa_marketplace_orders_line_items
                GROUP BY order_id
            ) AS order_counts
            ON orders.order_id = order_counts.order_id
            SET orders.product_count = order_counts.product_count;
        ";
        $stmt = $db->prepare($sql);
        $stmt->execute();
    }

    protected function productQuantity()
    {
        $db = \Pimcore\Db::get();
        $sql = "
            UPDATE iwa_marketplace_orders_line_items AS orders
            JOIN (
                SELECT 
                    order_id, 
                    SUM(quantity) AS total_quantity
                FROM 
                    iwa_marketplace_orders_line_items
                GROUP BY 
                    order_id
            ) AS order_totals ON orders.order_id = order_totals.order_id
            SET orders.total_quantity = order_totals.total_quantity;
        ";
        $stmt = $db->prepare($sql);
        $stmt->execute();
    }

    protected function calculatePrice()
    {
        $db = \Pimcore\Db::get();
        $sql = "
            SELECT 
                DISTINCT currency,
                DATE(created_at)
            FROM
                iwa_marketplace_orders_line_items;
        ";
        $results = $db->fetchAllAssociative($sql);
        echo "Start: Updating currency rates...\n";
        foreach ($results as $row) {
            $currency = $row['currency'];
            $date = $row['DATE(created_at)'];
            echo "Processing... Currency: $currency, Date: $date\n";
            /*$sql = "
                SELECT 
                    value
                FROM 
                    iwa_currency_history
                WHERE 
                    currency = '$currency'
                    AND DATE(date) <= '$date'
                ORDER BY 
                    ABS(TIMESTAMPDIFF(DAY, DATE(date), '$date')) ASC
                LIMIT 1;
            ";
            $result = $db->fetchOne($sql);
            if ($result) {
                echo "Currency rate found: $result\n";
            } else {
                echo "Currency rate not found, currency: $currency, date: $date\n";
            }
            $date = date('Y-m-d', strtotime($date));
            echo "Date: $date\n";
            $updateSql = "
            UPDATE iwa_marketplace_orders_line_items
            SET currency_rate = $result
            DATE_FORMAT(created_at, '%Y-%m-%d')  = '$date'
             ";
            
            echo "Updating... $updateSql\n";
            try {
                $affectedRows = $db->executeStatement($updateSql);
                echo "Rows affected: $affectedRows\n";
                echo "Update successful\n";
            } catch (Exception $e) {
                echo "Error occurred: " . $e->getMessage() . "\n";
            }
            $sqlCheckRate = "
                SELECT currency_rate 
                FROM iwa_marketplace_orders_line_items
                WHERE DATE_FORMAT(created_at, '%Y-%m-%d')  = '$date'
            ";
            $currentRate = $db->fetchOne($sqlCheckRate);
            echo "Current currency rate for $date: $currentRate\n";*/
        }
        echo "All processes completed.\n";
        /*$db = \Pimcore\Db::get();
        $sql = "
        UPDATE iwa_marketplace_orders_line_items AS items
        JOIN iwa_currency_history AS currency_history
            ON items.currency = currency_history.currency
            AND DATE(items.created_at) = DATE(currency_history.date) 
        JOIN iwa_currency_history AS usd_history
            ON usd_history.currency = 'USD'
            AND DATE(usd_history.date) = DATE(currency_history.date)
        SET 
            items.product_price_usd = CASE 
                WHEN items.currency = 'USD' THEN items.price
                ELSE ROUND((items.price / NULLIF(usd_history.value, 0)), 2)
            END,
                items.total_price_usd = CASE 
                    WHEN items.currency = 'USD' THEN items.total_price
                    ELSE ROUND((items.total_price / NULLIF(usd_history.value, 0)), 2)
            END;
        ";
        $stmt = $db->prepare($sql);
        $stmt->execute();
        try {
            $stmt = $db->prepare($sql);
            $stmt->execute();
            $affectedRows = $stmt->rowCount();
            echo "SQL Query executed successfully. Affected rows: " . $affectedRows;
        } catch (\Exception $e) {
            echo "Error: " . $e->getMessage();
        }*/
    }

    protected function countryCode()
    {
        $db = \Pimcore\Db::get();
        $sql = "
        UPDATE iwa_marketplace_orders_line_items
        SET shipping_country = 'Turkey'
        WHERE shipping_country_code = 'TR';
        ";
        $stmt = $db->prepare($sql);
        $stmt->execute();
    }

    protected function parseUrl()
    {
        $tldList = [
            'com', 'org', 'net', 'gov', 'm', 'io', 'I', 'co', 'uk',
            'de', 'lens', 'search', 'pay', 'tv', 'nl', 'au', 'ca', 'lm', 'sg',
            'at', 'nz', 'in', 'tt', 'dk', 'es', 'no', 'se', 'ae', 'hk',
            'sa', 'us', 'ie', 'be', 'pk', 'ro', 'co', 'il', 'hu', 'fi',
            'pa', 't', 'm', 'io', 'cse', 'az', 'new', 'tr', 'web', 'cz', 'gm',
            'ua', 'www', 'fr', 'gr', 'ch', 'pt', 'pl', 'rs', 'bg', 'hr','l','it','m','lm','pay'
        ];
        $db = \Pimcore\Db::get();
        $sql = "
            SELECT DISTINCT referring_site 
            FROM iwa_marketplace_orders_line_items 
            WHERE referring_site IS NOT NULL 
            AND referring_site != '' 
            AND referring_site != 'null'
            ";
        $results = $db->fetchAllAssociative($sql); 
        foreach ($results as $row) {
            $referringSite = $row['referring_site'];
            $parsedUrl = parse_url($referringSite);
            if (isset($parsedUrl['host'])) {
                $host = $parsedUrl['host'];
                $domainParts = explode('.', $host);
                while (!empty($domainParts) && in_array(end($domainParts), $tldList)) {
                    array_pop($domainParts); 
                }
                while (!empty($domainParts) && in_array(reset($domainParts), $tldList)) {
                    array_shift($domainParts); 
                }
                $domain = implode('.', $domainParts);
                $domain = preg_replace('/^www\./', '', $domain);
                $domain = strtolower($domain);
                $updateQuery = "
                    UPDATE iwa_marketplace_orders_line_items 
                    SET referring_site_domain = ?
                    WHERE referring_site = ?
                ";
                $stmt = $db->prepare($updateQuery);
                $stmt->execute([$domain,$row['referring_site']]);
            }
        }
    }

    protected function usaCode()
    {
        $isoCodes = [
            'Alabama' => 'US-AL',
            'Alaska' =>'US-AK',
            'Arizona' =>'US-AZ',
            'Arkansas' =>'US-AR',
            'California' =>'US-CA',
            'Colorado' =>'US-CO',
            'Connecticut' =>'US-CT',
            'Delaware' =>'US-DE',
            'Florida' =>'US-FL',
            'Georgia' =>'US-GA',
            'Hawaii' =>'US-HI',
            'Idaho' =>'US-ID',
            'Illinois' =>'US-IL',
            'Indiana' =>'US-IN',
            'Iowa' =>'US-IA',
            'Kansas' =>'US-KS',
            'Kentucky' =>'US-KY',
            'Louisiana' =>'US-LA',
            'Maine' =>'US-ME',
            'Maryland' =>'US-MD',
            'Massachusetts' =>'US-MA',
            'Michigan' =>'US-MI',
            'Minnesota' =>'US-MN',
            'Mississippi' =>'US-MS',
            'Missouri' =>'US-MO',
            'Montana' =>'US-MT',
            'Nebraska' =>'US-NE',
            'Nevada' =>'US-NV',
            'New Hampshire' =>'US-NH',
            'New Jersey' =>'US-NJ',
            'New Mexico' =>'US-NM',
            'New York' =>'US-NY',
            'North Carolina' =>'US-NC',
            'North Dakota' =>'US-ND',
            'Ohio' =>'US-OH',
            'Oklahoma' =>'US-OK',
            'Oregon' =>'US-OR',
            'Pennsylvania' =>'US-PA',
            'Rhode Island' =>'US-RI',
            'South Carolina' =>'US-SC',
            'South Dakota' =>'US-SD',
            'Tennessee' =>'US-TN',
            'Texas' =>'US-TX',
            'Utah' =>'US-UT',
            'Vermont' =>'US-VT',
            'Virginia' =>'US-VA',
            'Washington' =>'US-WA',
            'West Virginia' =>'US-WV',
            'Wisconsin' =>'US-WI',
            'Wyoming' =>'US-WY',
            'American Samoa' =>'US-AS',
            'Guam' =>'US-GU',
            'Northern Mariana Islands' =>'US-MP',
            'Puerto Rico' =>'US-PR',
            'United States Minor Outlying Islands' =>'US-UM',
            'Virgin Islands, U.S.' =>'US-VI',
            'District of Columbia' =>'US-DC',
        ];
        $db = \Pimcore\Db::get();
        $sql = "
            SELECT DISTINCT shipping_province 
            FROM iwa_marketplace_orders_line_items 
            WHERE shipping_province IS NOT NULL 
            AND shipping_province != '' 
            AND shipping_province != 'null'
            ";
        $results = $db->fetchAllAssociative($sql);

        foreach ($results as $result) {
            $shippingProvince = $result['shipping_province'];
            if (isset($isoCodes[$shippingProvince])) {
                $provinceCode = $isoCodes[$shippingProvince];
                $updateSql = "
                    UPDATE iwa_marketplace_orders_line_items 
                    SET province_code = :province_code 
                    WHERE shipping_province = :shipping_province
                ";
                $db->executeStatement($updateSql, [
                    'province_code' => $provinceCode,
                    'shipping_province' => $shippingProvince
                ]);
            }
        }
    }
}