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
    name: 'app:prepare-order-table',
    description: 'Prepare orderItems table from orders table',
)]


class PrepareOrderTableCommand extends AbstractCommand
{
    private array $marketplaceListWithIds = [];

    protected function configure(): void
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

        if($input->getOption('updateCoin')) {
            $this->currencyRate();
            $this->calculatePrice();
        }

        if($input->getOption('extraColumns')) {
            $this->extraColumns();
        }
        return Command::SUCCESS;
    }
    
    protected function extraColumns(): void
    {
        echo "Set Marketplace key\n";
        $this->setMarketplaceKey();
        echo "Complated Marketplace key\n";
        echo "Calculating is Parse URL\n";
        $this->parseUrl(); 
        echo "Complated Parse URL\n";
        echo "Calculating Closed At Diff\n";
        $this->insertClosedAtDiff();
        echo "Complated Closed At Diff\n";
        echo "Calculating is Discount\n";
        $this->discountValue();
        echo "Complated is Discount\n";
        echo "Calculating is Country Name\n";
        $this->countryCodes();
        echo "Complated is Country Name\n";
        echo "Calculating USA Code\n";
        $this->usaCode();
        echo "Complated USA Code\n";
        echo "Calculating Bolcom Total Price\n";
        $this->bolcomTotalPrice();
        echo "Complated Bolcom Total Price\n";
        echo "Fix Bolcom Orders\n";
        $this->bolcomFixOrders();
        echo "Complated Fix Bolcom Orders\n";
        echo "Calculating is Cancelled\n";
        $this->isCancelled();
        echo "Complated is Cancelled\n";
    }
        
    protected function transferOrders(): void
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
                    'Shopify' => $this->transferOrdersFromShopify($id,$marketplaceType),
                    'Trendyol' => $this->transferOrdersTrendyol($id,$marketplaceType),
                    'Bol.com' => $this->transferOrdersFromBolcom($id,$marketplaceType),
                    'Etsy' => $this->transferOrdersEtsy($id,$marketplaceType),
                    'Amazon' => $this->transferOrdersAmazon($id,$marketplaceType),
                    'Takealot' => $this->transferOrdersTakealot($id,$marketplaceType),
                    'Wallmart' => $this->transferOrdersWallmart($id,$marketplaceType),
                    'Ciceksepeti' => $this->transferOrdersCiceksepeti($id,$marketplaceType),
                    default => null,
                };
                echo "Complated: $marketplaceType\n";
            }
        }
    }

    protected function transferOrdersAmazon($marketPlaceId, $marketplaceType): void
    {
        $amazonSql = "
            INSERT INTO iwa_marketplace_orders_line_items (
            marketplace_type, marketplace_id, created_at, closed_at, order_id, product_id, variant_id, price, currency, quantity, variant_title, 
            total_discount, shipping_city, shipping_country_code, province_code, total_price, fulfillments_status,tracking_company)
            SELECT
                '$marketplaceType',
                '$marketPlaceId',
                JSON_UNQUOTE(JSON_EXTRACT(json, '$.PurchaseDate')) AS created_at,
                JSON_UNQUOTE(JSON_EXTRACT(json, '$.LastUpdateDate')) AS closed_at,                     
                order_id AS order_id,
                JSON_UNQUOTE(JSON_EXTRACT(line_item.value, '$.OrderItemId')) AS product_id,
                JSON_UNQUOTE(JSON_EXTRACT(line_item.value, '$.ASIN')) AS variant_id,
                JSON_UNQUOTE(JSON_EXTRACT(line_item.value, '$.ItemPrice.Amount')) AS price,
                JSON_UNQUOTE(JSON_EXTRACT(line_item.value, '$.ItemPrice.CurrencyCode')) AS currency,        
                JSON_UNQUOTE(JSON_EXTRACT(line_item.value, '$.QuantityOrdered')) AS quantity,
                JSON_UNQUOTE(JSON_EXTRACT(line_item.value, '$.Title')) AS variant_title,
                (CAST(JSON_UNQUOTE(JSON_EXTRACT(line_item.value, '$.PromotionDiscount.Amount')) AS DECIMAL(10,2)) + 
                CAST(JSON_UNQUOTE(JSON_EXTRACT(line_item.value, '$.PromotionDiscountTax.Amount')) AS DECIMAL(10,2))) AS total_discount,
                JSON_UNQUOTE(JSON_EXTRACT(json, '$.ShippingAddress.City')) as shipping_city,
                JSON_UNQUOTE(JSON_EXTRACT(json, '$.ShippingAddress.CountryCode')) AS shipping_country_code,
               CONCAT(
                    JSON_UNQUOTE(JSON_EXTRACT(json, '$.ShippingAddress.CountryCode')),
                    '-',
                    JSON_UNQUOTE(JSON_EXTRACT(json, '$.ShippingAddress.StateOrRegion'))
                ) AS province_code,
                JSON_UNQUOTE(JSON_EXTRACT(json, '$.OrderTotal.Amount')) AS total_price,  
                JSON_UNQUOTE(JSON_EXTRACT(json, '$.OrderStatus')) AS fulfillments_status,
                JSON_UNQUOTE(JSON_EXTRACT(json, '$.FulfillmentChannel')) AS tracking_company
            FROM
                iwa_marketplace_orders
                CROSS JOIN JSON_TABLE(json, '$.OrderItems[*]' COLUMNS (
                    value JSON PATH '$'
                )) AS line_item
            WHERE
                JSON_UNQUOTE(JSON_EXTRACT(line_item.value, '$.ASIN')) IS NOT NULL
                AND JSON_UNQUOTE(JSON_EXTRACT(line_item.value, '$.ASIN')) != 'null'
                AND JSON_UNQUOTE(JSON_EXTRACT(line_item.value, '$.ASIN')) != ''
                AND marketplace_id = $marketPlaceId
			ON DUPLICATE KEY UPDATE 
                marketplace_type = VALUES(marketplace_type),
                marketplace_id = VALUES(marketplace_id),
                created_at = VALUES(created_at),
                closed_at = VALUES(closed_at),
                product_id = VALUES(product_id),
                variant_id = VALUES(variant_id),
                price = VALUES(price),
                currency = VALUES(currency),
                quantity = VALUES(quantity),
                variant_title = VALUES(variant_title),
                total_discount = VALUES(total_discount),
                shipping_city = VALUES(shipping_city),
                shipping_country_code = VALUES(shipping_country_code),
                total_price = VALUES(total_price),
                fulfillments_status = VALUES(fulfillments_status),
                province_code = VALUES(province_code),
                tracking_company = VALUES(tracking_company);";
        try {
            $db = \Pimcore\Db::get();
            $db->query($amazonSql);
        } catch (\Exception $e) {
            echo "Error: " . $e->getMessage();
        }
    }

    protected function transferOrdersEtsy($marketPlaceId, $marketplaceType): void
    {
        $etsySql = "
            INSERT INTO iwa_marketplace_orders_line_items (
            marketplace_type, marketplace_id, created_at, closed_at, order_id, product_id, variant_id, price, currency, quantity, variant_title, 
            total_discount, shipping_city, shipping_country_code, total_price, subtotal_price, fulfillments_status, tracking_company)
            SELECT
                '$marketplaceType',
                '$marketPlaceId',
                FROM_UNIXTIME(CAST(JSON_UNQUOTE(JSON_EXTRACT(json, '$.created_timestamp')) AS UNSIGNED)) AS created_at,
                FROM_UNIXTIME(CAST(JSON_UNQUOTE(JSON_EXTRACT(json, '$.updated_timestamp')) AS UNSIGNED)) AS closed_at,         
                order_id AS order_id,
                JSON_UNQUOTE(JSON_EXTRACT(line_item.value, '$.listing_id')) AS product_id,
                JSON_UNQUOTE(JSON_EXTRACT(line_item.value, '$.product_id')) AS variant_id,
                JSON_UNQUOTE(JSON_EXTRACT(line_item.value, '$.price.amount')) / JSON_UNQUOTE(JSON_EXTRACT(line_item.value, '$.price.divisor')) AS price,
                JSON_UNQUOTE(JSON_EXTRACT(line_item.value, '$.price.currency_code')) AS currency,        
                JSON_UNQUOTE(JSON_EXTRACT(line_item.value, '$.quantity')) AS quantity,
                JSON_UNQUOTE(JSON_EXTRACT(line_item.value, '$.title')) AS variant_title,
                JSON_UNQUOTE(JSON_EXTRACT(json, '$.discount_amt.amount')) / JSON_UNQUOTE(JSON_EXTRACT(json, '$.discount_amt.divisor'))  AS total_discount,
                JSON_UNQUOTE(JSON_EXTRACT(json, '$.city')) AS shipping_city,
                JSON_UNQUOTE(JSON_EXTRACT(json, '$.country_iso')) AS shipping_country_code,
                JSON_UNQUOTE(JSON_EXTRACT(json, '$.grandtotal.amount')) / JSON_UNQUOTE(JSON_EXTRACT(json, '$.grandtotal.divisor'))  AS total_price,  
                JSON_UNQUOTE(JSON_EXTRACT(json, '$.subtotal.amount')) / JSON_UNQUOTE(JSON_EXTRACT(json, '$.subtotal.divisor'))  AS subtotal_price,  
                JSON_UNQUOTE(JSON_EXTRACT(json, '$.status')) AS fulfillments_status,
                JSON_UNQUOTE(JSON_EXTRACT(shipments.value, '$.carrier_name')) AS tracking_company
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
                marketplace_id = VALUES(marketplace_id),
                created_at = VALUES(created_at),
                closed_at = VALUES(closed_at),
                product_id = VALUES(product_id),
                variant_id = VALUES(variant_id),
                price = VALUES(price),
                currency = VALUES(currency),
                quantity = VALUES(quantity),
                variant_title = VALUES(variant_title),
                total_discount = VALUES(total_discount),
                shipping_country = VALUES(shipping_country),
                shipping_city = VALUES(shipping_city),
                shipping_country_code = VALUES(shipping_country_code),
                total_price = VALUES(total_price),
                subtotal_price = VALUES(subtotal_price),
                fulfillments_status = VALUES(fulfillments_status),
                tracking_company = VALUES(tracking_company);";
        try {
            $db = \Pimcore\Db::get();
            $db->query($etsySql);
        } catch (\Exception $e) {
            echo "Error: " . $e->getMessage();
        }
    }

    protected function transferOrdersTrendyol($marketPlaceId, $marketplaceType): void
    {
        $trendyolSql = "
            INSERT INTO iwa_marketplace_orders_line_items (
            marketplace_type, marketplace_id, created_at, closed_at, order_id, product_id, variant_id, price, currency, quantity, variant_title, total_discount, 
            shipping_city, shipping_company, shipping_country_code,total_price, fulfillments_status, tracking_company)
            SELECT
                '$marketplaceType',
                '$marketPlaceId',
                FROM_UNIXTIME(JSON_UNQUOTE(JSON_EXTRACT(json, '$.orderDate')) / 1000) AS created_at,
                FROM_UNIXTIME(JSON_UNQUOTE(JSON_EXTRACT(json, '$.lastModifiedDate')) / 1000) AS closed_at,         
                JSON_UNQUOTE(JSON_EXTRACT(json, '$.orderNumber')) AS order_id,
                JSON_UNQUOTE(JSON_EXTRACT(line_item.value, '$.id')) AS product_id,
                JSON_UNQUOTE(JSON_EXTRACT(line_item.value, '$.productCode')) AS variant_id,
                JSON_UNQUOTE(JSON_EXTRACT(line_item.value, '$.price')) AS price,
                JSON_UNQUOTE(JSON_EXTRACT(json, '$.currencyCode')) AS currency,        
                JSON_UNQUOTE(JSON_EXTRACT(line_item.value, '$.quantity')) AS quantity,
                JSON_UNQUOTE(JSON_EXTRACT(line_item.value, '$.productName')) AS variant_title,
                JSON_UNQUOTE(JSON_EXTRACT(json, '$.totalDiscount')) AS total_discount,
                JSON_UNQUOTE(JSON_EXTRACT(json, '$.invoiceAddress.city')) AS shipping_city,
                JSON_UNQUOTE(JSON_EXTRACT(json, '$.commercial')) AS shipping_company,
                JSON_UNQUOTE(JSON_EXTRACT(json, '$.invoiceAddress.countryCode')) AS shipping_country_code,
                JSON_UNQUOTE(JSON_EXTRACT(json, '$.totalPrice')) AS total_price,
                JSON_UNQUOTE(JSON_EXTRACT(json, '$.status')) AS fulfillments_status,
                JSON_UNQUOTE(JSON_EXTRACT(json, '$.cargoProviderName')) AS tracking_company
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
                marketplace_id = VALUES(marketplace_id),
                created_at = VALUES(created_at),
                closed_at = VALUES(closed_at),
                product_id = VALUES(product_id),
                variant_id = VALUES(variant_id),
                price = VALUES(price),
                currency = VALUES(currency),
                quantity = VALUES(quantity),
                variant_title = VALUES(variant_title),
                total_discount = VALUES(total_discount),
                shipping_city = VALUES(shipping_city),
                shipping_company = VALUES(shipping_company),
                shipping_country_code = VALUES(shipping_country_code),
                total_price = VALUES(total_price),
                fulfillments_status = VALUES(fulfillments_status),
                tracking_company = VALUES(tracking_company)";
        try {
            $db = \Pimcore\Db::get();
            $db->query($trendyolSql);
        } catch (\Exception $e) {
            echo "Error: " . $e->getMessage();
        }
    }

    protected function transferOrdersFromShopify($marketPlaceId, $marketplaceType): void
    {
        $shopifySql = "
            INSERT INTO iwa_marketplace_orders_line_items (
                marketplace_type, marketplace_id, created_at, closed_at, order_id, product_id, variant_id, price, currency, quantity, variant_title, total_discount,
                shipping_country, shipping_province, shipping_city, shipping_company, shipping_country_code, total_price, subtotal_price, 
                fulfillments_status, tracking_company, fulfillments_status_control, referring_site, landing_site
            )
            SELECT
                '$marketplaceType',
                '$marketPlaceId',
                JSON_UNQUOTE(JSON_EXTRACT(json, '$.created_at')) AS created_at,
                JSON_UNQUOTE(JSON_EXTRACT(json, '$.closed_at')) AS closed_at,               
                JSON_UNQUOTE(JSON_EXTRACT(json, '$.id')) AS order_id,
                JSON_UNQUOTE(JSON_EXTRACT(line_item.value, '$.product_id')) AS product_id,
                JSON_UNQUOTE(JSON_EXTRACT(line_item.value, '$.variant_id')) AS variant_id,
                JSON_UNQUOTE(JSON_EXTRACT(line_item.value, '$.price')) AS price,
                JSON_UNQUOTE(JSON_EXTRACT(json, '$.currency')) AS currency,        
                JSON_UNQUOTE(JSON_EXTRACT(line_item.value, '$.quantity')) AS quantity,
                JSON_UNQUOTE(JSON_EXTRACT(line_item.value, '$.name')) AS variant_title,
                JSON_UNQUOTE(JSON_EXTRACT(json, '$.total_discounts')) AS total_discount,
                JSON_UNQUOTE(JSON_EXTRACT(json, '$.shipping_address.country')) AS shipping_country,
                JSON_UNQUOTE(JSON_EXTRACT(json, '$.shipping_address.province')) AS shipping_province,
                JSON_UNQUOTE(JSON_EXTRACT(json, '$.shipping_address.city')) AS shipping_city,
                JSON_UNQUOTE(JSON_EXTRACT(json, '$.shipping_address.company')) AS shipping_company,
                JSON_UNQUOTE(JSON_EXTRACT(json, '$.shipping_address.country_code')) AS shipping_country_code,
                JSON_UNQUOTE(JSON_EXTRACT(json, '$.total_price')) AS total_price,
                JSON_UNQUOTE(JSON_EXTRACT(json, '$.subtotal_price')) AS subtotal_price,
                COALESCE(JSON_UNQUOTE(JSON_EXTRACT(fulfillments.value, '$.status')), NULL) AS fulfillments_status,
                COALESCE(JSON_UNQUOTE(JSON_EXTRACT(fulfillments.value, '$.tracking_company')), NULL) AS tracking_company,
                JSON_UNQUOTE(JSON_EXTRACT(json, '$.cancelled_at')) AS fulfillments_status_control,
                JSON_UNQUOTE(JSON_EXTRACT(json, '$.referring_site')) AS referring_site,
                COALESCE(LEFT(JSON_UNQUOTE(JSON_EXTRACT(json, '$.landing_site')), 255), NULL) AS landing_site
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
                marketplace_id = VALUES(marketplace_id),
                created_at = VALUES(created_at),
                closed_at = VALUES(closed_at),
                product_id = VALUES(product_id),
                variant_id = VALUES(variant_id),
                price = VALUES(price),
                currency = VALUES(currency),
                quantity = VALUES(quantity),
                variant_title = VALUES(variant_title),
                total_discount = VALUES(total_discount),
                shipping_country = VALUES(shipping_country),
                shipping_province = VALUES(shipping_province),
                shipping_city = VALUES(shipping_city),
                shipping_company = VALUES(shipping_company),
                shipping_country_code = VALUES(shipping_country_code),
                total_price = VALUES(total_price),
                subtotal_price = VALUES(subtotal_price),
                fulfillments_status = VALUES(fulfillments_status),
                tracking_company = VALUES(tracking_company),
                fulfillments_status_control = VALUES(fulfillments_status_control),
                referring_site = VALUES(referring_site),
                landing_site = VALUES(landing_site);";
        try {
            $db = \Pimcore\Db::get();
            $db->query($shopifySql);
        } catch (\Exception $e) {
            echo "Error: " . $e->getMessage();
        }
    }

    protected function transferOrdersFromBolcom($marketPlaceId, $marketplaceType): void
    {
        $bolcomSql = "
        INSERT INTO iwa_marketplace_orders_line_items (
            marketplace_type, marketplace_id, created_at, closed_at, order_id, product_id, variant_id, price, currency, quantity,
            variant_title,  shipping_city, shipping_country_code, fulfillments_status, fulfillments_status_control
        )
        SELECT
            '$marketplaceType',
            '$marketPlaceId',
            JSON_UNQUOTE(JSON_EXTRACT(json, '$.orderPlacedDateTime')) AS created_at,
            JSON_UNQUOTE(JSON_EXTRACT(json, '$.orderPlacedDateTime')) AS closed_at,               
            JSON_UNQUOTE(JSON_EXTRACT(json, '$.orderId')) AS order_id,
            JSON_UNQUOTE(JSON_EXTRACT(order_item_detail.value, '$.product.ean')) AS product_id,
            JSON_UNQUOTE(JSON_EXTRACT(order_item_detail.value, '$.product.bolProductId')) AS variant_id,
            JSON_UNQUOTE(JSON_EXTRACT(order_item_detail.value, '$.unitPrice')) AS price,
            'EUR' AS currency,        
            JSON_UNQUOTE(JSON_EXTRACT(order_item_detail.value, '$.quantity')) AS quantity,
            JSON_UNQUOTE(JSON_EXTRACT(order_item_detail.value, '$.product.title')) AS variant_title,
            JSON_UNQUOTE(JSON_EXTRACT(json, '$.orderDetail.shipmentDetails.city')) AS shipping_city,
            JSON_UNQUOTE(JSON_EXTRACT(json, '$.orderDetail.shipmentDetails.countryCode')) AS shipping_country_code,
            COALESCE(JSON_UNQUOTE(JSON_EXTRACT(order_item.value, '$.fulfilmentStatus')), NULL) AS fulfillments_status,
            JSON_UNQUOTE(JSON_EXTRACT(order_item_detail.value, '$.cancellationRequest')) AS fulfillments_status_control
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
            marketplace_id = VALUES(marketplace_id),    
            created_at = VALUES(created_at),
            closed_at = VALUES(closed_at),
            product_id = VALUES(product_id),
            variant_id = VALUES(variant_id),
            price = VALUES(price),
            quantity = VALUES(quantity),
            variant_title = VALUES(variant_title),
            shipping_city = VALUES(shipping_city),
            shipping_country_code = VALUES(shipping_country_code),
            fulfillments_status = VALUES(fulfillments_status),
            fulfillments_status_control = VALUES(fulfillments_status_control);";
        try {
            $db = \Pimcore\Db::get();
            $db->query($bolcomSql);
        } catch (\Exception $e) {
            echo "Error: " . $e->getMessage();
        }
    }

    protected function transferOrdersTakealot($marketPlaceId, $marketplaceType): void
    {
        $takealotSql = "
            INSERT INTO iwa_marketplace_orders_line_items (
                marketplace_type, marketplace_id, created_at, order_id, product_id, variant_id, price, 
                currency, quantity, variant_title, fulfillments_status, tracking_company
            )
            SELECT
                '$marketplaceType',
                '$marketPlaceId',
                DATE_FORMAT(STR_TO_DATE(JSON_UNQUOTE(JSON_EXTRACT(json, '$.order_date')), '%d %b %Y %H:%i:%s'), '%Y-%m-%d %H:%i:%s') AS created_at,
                JSON_UNQUOTE(JSON_EXTRACT(json, '$.order_id')) AS order_id,
                JSON_UNQUOTE(JSON_EXTRACT(json, '$.order_item_id')) AS product_id,
                JSON_UNQUOTE(JSON_EXTRACT(json, '$.tsin')) AS variant_id,
                JSON_UNQUOTE(JSON_EXTRACT(json, '$.selling_price')) AS price,
                'ZAR' AS currency,
                JSON_UNQUOTE(JSON_EXTRACT(json, '$.quantity')) AS quantity,
                JSON_UNQUOTE(JSON_EXTRACT(json, '$.product_title')) AS variant_title,
                JSON_UNQUOTE(JSON_EXTRACT(json, '$.sale_status')) AS fulfillments_status,
                JSON_UNQUOTE(JSON_EXTRACT(json, '$.dc')) AS tracking_company
            FROM
                iwa_marketplace_orders
            WHERE
                JSON_UNQUOTE(JSON_EXTRACT(json, '$.tsin')) IS NOT NULL
                AND JSON_UNQUOTE(JSON_EXTRACT(json, '$.tsin')) != 'null'
                AND JSON_UNQUOTE(JSON_EXTRACT(json, '$.tsin')) != ''
                AND CAST(JSON_UNQUOTE(JSON_EXTRACT(json, '$.tsin')) AS UNSIGNED) > 0
                AND marketplace_id = $marketPlaceId
            ON DUPLICATE KEY UPDATE
                marketplace_type = VALUES(marketplace_type),
                marketplace_id = VALUES(marketplace_id),
                created_at = VALUES(created_at),
                product_id = VALUES(product_id),
                variant_id = VALUES(variant_id),
                price = VALUES(price),
                currency = VALUES(currency),
                quantity = VALUES(quantity),
                variant_title = VALUES(variant_title),
                fulfillments_status = VALUES(fulfillments_status),
                tracking_company = VALUES(tracking_company);";
        try {
            $db = \Pimcore\Db::get();
            $db->query($takealotSql);
        } catch (\Exception $e) {
            echo "Error: " . $e->getMessage();
        }
    }

    protected function transferOrdersWallmart($marketPlaceId, $marketplaceType): void
    {
        $wallmartSql = "
            INSERT INTO iwa_marketplace_orders_line_items (
                marketplace_type, marketplace_id, created_at, closed_at, order_id, variant_id, price, currency, quantity, variant_title,
                shipping_province, shipping_city, shipping_country_code, fulfillments_status, tracking_company, fulfillments_status_control
            )
            SELECT
                '$marketplaceType',
                '$marketPlaceId',
                FROM_UNIXTIME(JSON_UNQUOTE(JSON_EXTRACT(json, '$.orderDate')) / 1000) AS created_at,
                FROM_UNIXTIME(JSON_UNQUOTE(JSON_EXTRACT(line_item.value, '$.statusDate')) / 1000)AS closed_at,
                JSON_UNQUOTE(JSON_EXTRACT(json, '$.purchaseOrderId')) AS order_id,
                JSON_UNQUOTE(JSON_EXTRACT(line_item.value, '$.item.sku')) AS variant_id,
                JSON_UNQUOTE(JSON_EXTRACT(line_item.value, '$.charges.charge[0].chargeAmount.amount')) AS price,
                JSON_UNQUOTE(JSON_EXTRACT(line_item.value, '$.$.charges.charge[0].chargeAmount.currency')) AS currency,
                JSON_UNQUOTE(JSON_EXTRACT(line_item.value, '$.orderLineQuantity.amount')) AS quantity,
                JSON_UNQUOTE(JSON_EXTRACT(line_item.value, '$.item.productName')) AS variant_title,
                JSON_UNQUOTE(JSON_EXTRACT(json, '$.shippingInfo.postalAddress.state')) AS shipping_province,
                JSON_UNQUOTE(JSON_EXTRACT(json, '$.shippingInfo.postalAddress.city')) AS shipping_city,
                JSON_UNQUOTE(JSON_EXTRACT(json, '$.shippingInfo.postalAddress.country')) AS shipping_country_code,
                JSON_UNQUOTE(JSON_EXTRACT(line_item.value, '$.item.orderLineStatuses.orderLineStatus[0].status')) AS fulfillments_status,
                JSON_UNQUOTE(JSON_EXTRACT(line_item.value, '$.item.orderLineStatuses.orderLineStatus[0].trackingInfo.carrierName.carrier')) AS tracking_company,
                JSON_UNQUOTE(JSON_EXTRACT(line_item.value, '$.item.orderLineStatuses.orderLineStatus[0].cancellationReason')) AS fulfillments_status_control
            FROM
                iwa_marketplace_orders
                CROSS JOIN JSON_TABLE(json, '$.orderLines.orderLine[*]' COLUMNS ( value JSON PATH '$' )) AS line_item
                LEFT JOIN JSON_TABLE(json, '$.fulfillments[*]' COLUMNS ( value JSON PATH '$' )) AS fulfillments ON TRUE
                LEFT JOIN JSON_TABLE(json, '$.discount_applications[*]' COLUMNS ( value JSON PATH '$' )) AS discount_application ON TRUE
            WHERE
                JSON_UNQUOTE(JSON_EXTRACT(line_item.value, '$.item.sku')) IS NOT NULL
                AND JSON_UNQUOTE(JSON_EXTRACT(line_item.value, '$.item.sku')) != 'null'
                AND JSON_UNQUOTE(JSON_EXTRACT(line_item.value, '$.item.sku')) != ''
                AND CAST(JSON_UNQUOTE(JSON_EXTRACT(line_item.value, '$.item.sku')) AS UNSIGNED) > 0
                AND marketplace_id = $marketPlaceId
            ON DUPLICATE KEY UPDATE
                marketplace_type = VALUES(marketplace_type),
                marketplace_id = VALUES(marketplace_id),
                created_at = VALUES(created_at),
                closed_at = VALUES(closed_at),
                variant_id = VALUES(variant_id),
                price = VALUES(price),
                currency = VALUES(currency),
                quantity = VALUES(quantity),
                variant_title = VALUES(variant_title),
                shipping_province = VALUES(shipping_province),
                shipping_city = VALUES(shipping_city),
                shipping_country_code = VALUES(shipping_country_code),
                fulfillments_status = VALUES(fulfillments_status),
                tracking_company = VALUES(tracking_company),
                fulfillments_status_control = VALUES(fulfillments_status_control);";
        try {
            $db = \Pimcore\Db::get();
            $db->query($wallmartSql);
        } catch (\Exception $e) {
            echo "Error: " . $e->getMessage();
        }
    }

    protected function transferOrdersCiceksepeti($marketPlaceId, $marketplaceType): void
    {
        $ciceksepetiSql = "
            INSERT INTO iwa_marketplace_orders_line_items (
                marketplace_type, marketplace_id, created_at, closed_at, order_id, product_id, variant_id, price, currency, quantity, variant_title, total_discount,
                shipping_city, shipping_country_code, shipping_company, total_price, fulfillments_status, tracking_company, fulfillments_status_control
            )
            SELECT
                '$marketplaceType',
                '$marketPlaceId',
                JSON_UNQUOTE(JSON_EXTRACT(json, '$.orderCreateDate')) AS created_at,
                JSON_UNQUOTE(JSON_EXTRACT(json, '$.orderModifyDate')) AS closed_at,               
                JSON_UNQUOTE(JSON_EXTRACT(json, '$.orderId')) AS order_id,
                JSON_UNQUOTE(JSON_EXTRACT(json, '$.productId')) AS product_id,
                JSON_UNQUOTE(JSON_EXTRACT(json, '$.productCode')) AS variant_id,
                JSON_UNQUOTE(JSON_EXTRACT(json, '$.itemPrice')) AS price,
                JSON_UNQUOTE(JSON_EXTRACT(json, '$.currency')) AS currency,        
                JSON_UNQUOTE(JSON_EXTRACT(json, '$.quantity')) AS quantity,
                JSON_UNQUOTE(JSON_EXTRACT(json, '$.name')) AS variant_title,
                JSON_UNQUOTE(JSON_EXTRACT(json, '$.discount')) AS total_discount,
                JSON_UNQUOTE(JSON_EXTRACT(json, '$.receiverCity')) AS shipping_city,
                JSON_UNQUOTE(JSON_EXTRACT(json, '$.receiverCountryCode')) AS shipping_country_code,
                JSON_UNQUOTE(JSON_EXTRACT(json, '$.receiverCompanyName')) AS shipping_company,
                JSON_UNQUOTE(JSON_EXTRACT(json, '$.totalPrice')) AS total_price,
                JSON_UNQUOTE(JSON_EXTRACT(json, '$.orderProductStatus')) AS fulfillments_status,
                JSON_UNQUOTE(JSON_EXTRACT(json, '$.cargoCompany')) AS tracking_company,
                JSON_UNQUOTE(JSON_EXTRACT(json, '$.cancellationResult')) AS fulfillments_status_control
            FROM
                iwa_marketplace_orders
            WHERE
                JSON_UNQUOTE(JSON_EXTRACT(json, '$.productCode')) IS NOT NULL
                AND JSON_UNQUOTE(JSON_EXTRACT(json, '$.productCode')) != 'null'
                AND JSON_UNQUOTE(JSON_EXTRACT(json, '$.productCode')) != ''
                AND CAST(JSON_UNQUOTE(JSON_EXTRACT(json, '$.productCode')) AS UNSIGNED) > 0
                AND marketplace_id = $marketPlaceId
            ON DUPLICATE KEY UPDATE
                marketplace_type = VALUES(marketplace_type),
                marketplace_id = VALUES(marketplace_id),
                created_at = VALUES(created_at),
                closed_at = VALUES(closed_at),
                product_id = VALUES(product_id),
                variant_id = VALUES(variant_id),
                price = VALUES(price),
                currency = VALUES(currency),
                quantity = VALUES(quantity),
                variant_title = VALUES(variant_title),
                total_discount = VALUES(total_discount),
                shipping_city = VALUES(shipping_city),
                shipping_company = VALUES(shipping_company),
                shipping_country_code = VALUES(shipping_country_code),
                total_price = VALUES(total_price),
                subtotal_price = VALUES(subtotal_price),
                fulfillments_status = VALUES(fulfillments_status),
                tracking_company = VALUES(tracking_company),
                fulfillments_status_control = VALUES(fulfillments_status_control)";
        try {
            $db = \Pimcore\Db::get();
            $db->query($ciceksepetiSql);
        } catch (\Exception $e) {
            echo "Error: " . $e->getMessage();
        }
    }

    protected function processVariantOrderData(): void
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
                $this->prepareOrderTable($row['variant_id'],$marketplaceType);
            }
        }
    }

    protected function fetchVariantInfo($marketplaceType): array
    {
        $db = \Pimcore\Db::get();
        $sql = "
            SELECT 
                DISTINCT variant_id
            FROM
                iwa_marketplace_orders_line_items
            WHERE 
                marketplace_type = '$marketplaceType'
            ";
        $values = $db->fetchAllAssociative($sql); 
        return $values;
    }

    protected function getTrendyolVariantProduct($uniqueMarketplaceId)
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

    protected function getShopifyVariantProduct($uniqueMarketplaceId)
    {
        $variantProduct = VariantProduct::findOneByField('uniqueMarketplaceId', $uniqueMarketplaceId,$unpublished = true);
        if ($variantProduct) {
            return $variantProduct;
        }
        echo "VariantProduct with uniqueMarketplaceId $uniqueMarketplaceId not found\n";
        return null;
    }

    protected function getBolcomVariantProduct($uniqueMarketplaceId)
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

    protected function getEtsyVariantProduct($uniqueMarketplaceId)
    {
        $variantProduct = VariantProduct::findOneByField('uniqueMarketplaceId', $uniqueMarketplaceId,$unpublished = true);
        if ($variantProduct) {
            return $variantProduct;
        }
        echo "VariantProduct with uniqueMarketplaceId $uniqueMarketplaceId not found\n";
        return null;
    }

    protected function getAmazonVariantProduct($uniqueMarketplaceId)
    {
        $variantProduct = VariantProduct::findOneByField('uniqueMarketplaceId', $uniqueMarketplaceId,$unpublished = true);
        if ($variantProduct) {
            return $variantProduct;
        }
        echo "VariantProduct with uniqueMarketplaceId $uniqueMarketplaceId not found\n";
        return null;
    }

    protected function getTakealotVariantProduct($uniqueMarketplaceId)
    {
        $variantProduct = VariantProduct::findOneByField('uniqueMarketplaceId', $uniqueMarketplaceId,$unpublished = true);
        if ($variantProduct) {
            return $variantProduct;
        }
        echo "VariantProduct with uniqueMarketplaceId $uniqueMarketplaceId not found\n";
        return null;
    }

    protected function getWallmartVariantProduct($uniqueMarketplaceId)
    {
        $sql = "
            SELECT object_id
            FROM iwa_json_store
            WHERE field_name = 'apiResponseJson'
            AND JSON_UNQUOTE(JSON_EXTRACT(json_data, '$.sku')) = ?
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

    protected function getCiceksepetiVariantProduct($uniqueMarketplaceId)
    {
        $variantProduct = VariantProduct::findOneByField('uniqueMarketplaceId', $uniqueMarketplaceId,$unpublished = true);
        if ($variantProduct) {
            return $variantProduct;
        }
        echo "VariantProduct with uniqueMarketplaceId $uniqueMarketplaceId not found\n";
        return null;
    }

    protected function prepareOrderTable($uniqueMarketplaceId, $marketplaceType)
    {
        $variantObject = match ($marketplaceType) {
            'Shopify' => self::getShopifyVariantProduct($uniqueMarketplaceId),
            'Trendyol' => self::getTrendyolVariantProduct($uniqueMarketplaceId),
            'Bol.com' => self::getBolcomVariantProduct($uniqueMarketplaceId),
            'Etsy' => self::getEtsyVariantProduct($uniqueMarketplaceId),
            'Amazon' => self::getAmazonVariantProduct($uniqueMarketplaceId),
            'Takealot' => self::getTakealotVariantProduct($uniqueMarketplaceId),
            'Wallmart' => self::getWallmartVariantProduct($uniqueMarketplaceId),
            'Ciceksepeti' => self::getCiceksepetiVariantProduct($uniqueMarketplaceId),
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
        
        $mainProductObjectArray = $variantObject->getMainProduct(); 
        if(!$mainProductObjectArray) {
            echo "Main product not found for VariantProduct with uniqueMarketplaceId $uniqueMarketplaceId\n";
            return;
        }

        $mainProductObject = reset($mainProductObjectArray);
        if ($mainProductObject instanceof Product) {
            $productCode = $mainProductObject->getProductCode();
            $iwasku =  $mainProductObject->getInheritedField('Iwasku');
            if (!$iwasku) {
                echo "iwasku code is required for adding/updating VariantProduct with uniqueMarketplaceId $uniqueMarketplaceId\n";
                return;
            }
            if ($mainProductObject->level() == 1) {
                $parent = $mainProductObject->getParent();
                if(!$parent) {
                    echo "Parent is required for adding/updating VariantProduct with uniqueMarketplaceId $uniqueMarketplaceId\n";
                    return;
                }
                $identifier = $parent->getInheritedField('ProductIdentifier');
                if (!$identifier) {
                    echo "Identifier is required for adding/updating VariantProduct with uniqueMarketplaceId $uniqueMarketplaceId\n";
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
            $productType = strtok($productIdentifier,'-');
            $path = $mainProductObject->getFullPath(); 
            $parts = explode('/', trim($path, '/'));
            $variantName = array_pop($parts);
            $parentName = array_pop($parts); 
            self::insertIntoTable($uniqueMarketplaceId, $iwasku, $identifier, $productType, $variantName, $parentName, $marketplaceType);
        }
    }

    protected function insertIntoTable($uniqueMarketplaceId, $iwasku, $identifier, $productType, $variantName, $parentName, $marketplaceType)
    {
        $db = \Pimcore\Db::get();
        $sql = "UPDATE iwa_marketplace_orders_line_items
        SET iwasku = :iwasku, parent_identifier  = :identifier, product_type = :productType, variant_name = :variantName, parent_name = :parentName
        WHERE variant_id = :uniqueMarketplaceId AND marketplace_type= :marketplaceType;";
        
        $stmt = $db->prepare($sql);
        /** @var TYPE_NAME $stmt */
        $stmt->execute([
            ':iwasku' => $iwasku,
            ':identifier' => $identifier,
            ':productType' => $productType,
            ':variantName' => $variantName,
            ':parentName' => $parentName,
            ':uniqueMarketplaceId' => $uniqueMarketplaceId,
            ':marketplaceType' => $marketplaceType,
        ]);
    }
   
    protected function marketplaceList()
    {
        $marketplaceList = Marketplace::getMarketplaceList();
        foreach ($marketplaceList as $marketplace) {
            $this->marketplaceListWithIds[$marketplace->getId()] = $marketplace->getMarketplaceType();
        }
    }

    protected function setMarketplaceKey()
    {
        $db = \Pimcore\Db::get();
        $sql = "
            SELECT 
                DISTINCT marketplace_id
            FROM
                iwa_marketplace_orders_line_items
            WHERE 
                marketplace_id IS NOT NULL
            ";
        $values = $db->fetchAllAssociative($sql); 
        foreach ($values as $row) {
            $id = $row['marketplace_id'];
            $marketplace = Marketplace::getById($id);
            if ($marketplace) {
                $marketplaceKey = $marketplace->getKey();
                $updateSql = "
                    UPDATE iwa_marketplace_orders_line_items
                    SET marketplace_key = :marketplaceKey
                    WHERE marketplace_id = :marketplaceId
                ";
                $db->executeStatement($updateSql, [
                    'marketplaceKey' => $marketplaceKey,
                    'marketplaceId' => $id,             
                ]);
            } else {
                echo "Marketplace not found for ID: $id\n";
            }
        }
    }

    protected function calculatePrice()
    {
        $db = \Pimcore\Db::get();
        $sql = "
            SELECT * FROM iwa_marketplace_orders_line_items
            WHERE product_price_usd IS NULL OR total_price_usd IS NULL;
        ";
        $results = $db->fetchAllAssociative($sql);
        foreach ($results as $row) {
            $price = $row['price'];
            $totalPrice = $row['total_price'];
            $subtotalPrice = $row['subtotal_price'];
            $currency = $row['currency'];
            $currencyRate = $row['currency_rate'];
            $currentUsd = $row['current_USD'];
            $productPriceUsd = null;
            $totalPriceUsd = null;
            if ($currency === 'USD') {
                $productPriceUsd = $price;
                $totalPriceUsd = $totalPrice;
                $subtotalPriceUsd = $subtotalPrice;
            } else {
                if ($currency === 'TRY') {
                    if ($currencyRate != 0) { 
                        $productPriceUsd = round($price / $currencyRate, 2);
                        $totalPriceUsd = round($totalPrice / $currencyRate, 2);
                        $subtotalPriceUsd = round($subtotalPrice / $currencyRate, 2);
                    } else {
                        $productPriceUsd = $totalPriceUsd = $subtotalPriceUsd = 0; 
                    }
                } else {
                    if ($currencyRate != 0 && $currentUsd != 0) { 
                        $productPriceUsd = round($price * $currencyRate / $currentUsd, 2);
                        $totalPriceUsd = round($totalPrice * $currencyRate / $currentUsd, 2);
                        $subtotalPriceUsd = round($subtotalPrice * $currencyRate / $currentUsd, 2);
                    } else {
                        $productPriceUsd = $totalPriceUsd = $subtotalPriceUsd = 0; 
                    }
                }
            }
            if ($subtotalPriceUsd == 0) {
                $subtotalPriceUsd = $totalPriceUsd;
            }
            $updateSql = "
                UPDATE iwa_marketplace_orders_line_items
                SET product_price_usd = $productPriceUsd, total_price_usd = $totalPriceUsd, subtotal_price_usd = $subtotalPriceUsd
                WHERE id = {$row['id']};
            ";
            echo "Updating... $updateSql\n";
            try {
                $affectedRows = $db->executeStatement($updateSql);
                echo "Rows affected: $affectedRows\n";
                echo "Update successful\n";
            } catch (Exception $e) {
                echo "Error occurred: " . $e->getMessage() . "\n";
            }
        }
        echo "All processes completed.\n";
    }

    protected function currencyRate()
    {
        $db = \Pimcore\Db::get();
        $sql = "
            SELECT 
                DISTINCT currency,
                DATE(created_at)
            FROM
                iwa_marketplace_orders_line_items
            WHERE currency_rate IS NULL;
        ";
        $results = $db->fetchAllAssociative($sql);
        echo "Start: Updating currency rates...\n";
        foreach ($results as $row) {
            $currency = $row['currency'];
            $date = $row['DATE(created_at)'];
            echo "Processing... Currency: $currency, Date: $date\n";
            if ($currency === 'TRY') {
                $currency = 'USD';
            }
            $rateSql = "
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
            $currencyRate  = $db->fetchOne($rateSql);
            $usdRateSql = "
                SELECT 
                    value
                FROM 
                    iwa_currency_history
                WHERE 
                    currency = 'USD'
                    AND DATE(date) <= '$date'
                ORDER BY 
                    ABS(TIMESTAMPDIFF(DAY, DATE(date), '$date')) ASC
                LIMIT 1;
            ";
            $usdRate = $db->fetchOne($usdRateSql);
        
            if (!$currencyRate) {
                echo "Currency rate not found for currency: $currency, date: $date\n";
                continue;
            }
        
            if (!$usdRate) {
                echo "USD rate not found for date: $date\n";
                continue;
            }

            if($row['currency'] === 'TRY') {
                $updateSql = "
                    UPDATE iwa_marketplace_orders_line_items
                    SET 
                        currency_rate = $currencyRate,
                        current_USD = $usdRate
                    WHERE DATE(created_at)  = '$date' AND currency = 'TRY';
                ";    
            }
            else {
                $updateSql = "
                    UPDATE iwa_marketplace_orders_line_items
                    SET 
                        currency_rate = $currencyRate,
                        current_USD = $usdRate
                    WHERE DATE(created_at)  = '$date' AND currency = '$currency';
                ";
            }
            echo "Updating... $updateSql\n";
            try {
                $affectedRows = $db->executeStatement($updateSql);
                echo "Rows affected: $affectedRows\n";
                echo "Update successful\n";
            } catch (Exception $e) {
                echo "Error occurred: " . $e->getMessage() . "\n";
            }
        }
        echo "All processes completed.\n";
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
            WHEN total_discount IS NOT NULL AND total_discount <> 0.00 THEN TRUE
            ELSE FALSE
        END;
        ";
        $stmt = $db->prepare($sql);
        $stmt->execute();
    }

    protected function isCancelled() 
    {
        $db = \Pimcore\Db::get();
        $sql = "
            UPDATE iwa_marketplace_orders_line_items
            SET is_canceled = CASE
                WHEN marketplace_type = 'Shopify' AND fulfillments_status_control = 'null' THEN 'not_cancelled'
                WHEN marketplace_type = 'Shopify' AND fulfillments_status_control != 'null' THEN 'cancelled'
                WHEN marketplace_type = 'Trendyol' AND fulfillments_status = 'Cancelled' THEN 'cancelled'
                WHEN marketplace_type = 'Trendyol' AND fulfillments_status != 'Cancelled' THEN 'not_cancelled'
                WHEN marketplace_type = 'Bol.com' AND fulfillments_status_control = 'true' THEN 'cancelled'
                WHEN marketplace_type = 'Bol.com' AND fulfillments_status_control != 'true' THEN 'not_cancelled'
                WHEN marketplace_type = 'Etsy' AND fulfillments_status = 'Canceled' THEN 'cancelled'
                WHEN marketplace_type = 'Etsy' AND fulfillments_status != 'Canceled' THEN 'not_cancelled'
                WHEN marketplace_type = 'Amazon' AND fulfillments_status = 'Canceled' THEN 'cancelled'
                WHEN marketplace_type = 'Amazon' AND fulfillments_status != 'Canceled' THEN 'not_cancelled'  
                WHEN marketplace_type = 'Wallmart' AND fulfillments_status_control = 'null' THEN 'cancelled'
                WHEN marketplace_type = 'Wallmart' AND fulfillments_status_control != 'null' THEN 'not_cancelled'
                WHEN marketplace_type = 'Ciceksepeti' AND fulfillments_status_control = 'null' THEN 'cancelled'
                WHEN marketplace_type = 'Ciceksepeti' AND fulfillments_status_control != 'null' THEN 'not_cancelled'
            END;
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

    protected function bolcomTotalPrice()
    {
        $db = \Pimcore\Db::get();
        $sql = "
            UPDATE iwa_marketplace_orders_line_items as t1
            INNER JOIN (
                SELECT 
                    order_id,
                    SUM(product_price_usd) as total_price_usd
                FROM iwa_marketplace_orders_line_items
                WHERE marketplace_type = 'Bol.com'
                GROUP BY order_id
            ) as t2
            ON t1.order_id = t2.order_id
            SET t1.total_price_usd = t2.total_price_usd
            WHERE t1.marketplace_type = 'Bol.com';
        ";
        $stmt = $db->prepare($sql);
        $stmt->execute();
    }

    protected function bolcomFixOrders()
    {
        $db = \Pimcore\Db::get();
        $sql = "
            DELETE FROM iwa_marketplace_orders_line_items
            WHERE marketplace_type = 'Bol.com' AND order_id = '0';
        ";
        $stmt = $db->prepare($sql);
        $stmt->execute();
    }

    protected function countryCodes()
    {
        // ISO 3166-1 alpha-2
        $countries = array
        (
            'AF' => 'Afghanistan',
            'AX' => 'Aland Islands',
            'AL' => 'Albania',
            'DZ' => 'Algeria',
            'AS' => 'American Samoa',
            'AD' => 'Andorra',
            'AO' => 'Angola',
            'AI' => 'Anguilla',
            'AQ' => 'Antarctica',
            'AG' => 'Antigua And Barbuda',
            'AR' => 'Argentina',
            'AM' => 'Armenia',
            'AW' => 'Aruba',
            'AU' => 'Australia',
            'AT' => 'Austria',
            'AZ' => 'Azerbaijan',
            'BS' => 'Bahamas',
            'BH' => 'Bahrain',
            'BD' => 'Bangladesh',
            'BB' => 'Barbados',
            'BY' => 'Belarus',
            'BE' => 'Belgium',
            'BZ' => 'Belize',
            'BJ' => 'Benin',
            'BM' => 'Bermuda',
            'BT' => 'Bhutan',
            'BO' => 'Bolivia',
            'BA' => 'Bosnia And Herzegovina',
            'BW' => 'Botswana',
            'BV' => 'Bouvet Island',
            'BR' => 'Brazil',
            'IO' => 'British Indian Ocean Territory',
            'BN' => 'Brunei Darussalam',
            'BG' => 'Bulgaria',
            'BF' => 'Burkina Faso',
            'BI' => 'Burundi',
            'KH' => 'Cambodia',
            'CM' => 'Cameroon',
            'CA' => 'Canada',
            'CV' => 'Cape Verde',
            'KY' => 'Cayman Islands',
            'CF' => 'Central African Republic',
            'TD' => 'Chad',
            'CL' => 'Chile',
            'CN' => 'China',
            'CX' => 'Christmas Island',
            'CC' => 'Cocos (Keeling) Islands',
            'CO' => 'Colombia',
            'KM' => 'Comoros',
            'CG' => 'Congo',
            'CD' => 'Congo, Democratic Republic',
            'CK' => 'Cook Islands',
            'CR' => 'Costa Rica',
            'CI' => 'Cote D\'Ivoire',
            'HR' => 'Croatia',
            'CU' => 'Cuba',
            'CY' => 'Cyprus',
            'CZ' => 'Czech Republic',
            'DK' => 'Denmark',
            'DJ' => 'Djibouti',
            'DM' => 'Dominica',
            'DO' => 'Dominican Republic',
            'EC' => 'Ecuador',
            'EG' => 'Egypt',
            'SV' => 'El Salvador',
            'GQ' => 'Equatorial Guinea',
            'ER' => 'Eritrea',
            'EE' => 'Estonia',
            'ET' => 'Ethiopia',
            'FK' => 'Falkland Islands (Malvinas)',
            'FO' => 'Faroe Islands',
            'FJ' => 'Fiji',
            'FI' => 'Finland',
            'FR' => 'France',
            'GF' => 'French Guiana',
            'PF' => 'French Polynesia',
            'TF' => 'French Southern Territories',
            'GA' => 'Gabon',
            'GM' => 'Gambia',
            'GE' => 'Georgia',
            'DE' => 'Germany',
            'GH' => 'Ghana',
            'GI' => 'Gibraltar',
            'GR' => 'Greece',
            'GL' => 'Greenland',
            'GD' => 'Grenada',
            'GP' => 'Guadeloupe',
            'GU' => 'Guam',
            'GT' => 'Guatemala',
            'GG' => 'Guernsey',
            'GN' => 'Guinea',
            'GW' => 'Guinea-Bissau',
            'GY' => 'Guyana',
            'HT' => 'Haiti',
            'HM' => 'Heard Island & Mcdonald Islands',
            'VA' => 'Holy See (Vatican City State)',
            'HN' => 'Honduras',
            'HK' => 'Hong Kong',
            'HU' => 'Hungary',
            'IS' => 'Iceland',
            'IN' => 'India',
            'ID' => 'Indonesia',
            'IR' => 'Iran, Islamic Republic Of',
            'IQ' => 'Iraq',
            'IE' => 'Ireland',
            'IM' => 'Isle Of Man',
            'IL' => 'Israel',
            'IT' => 'Italy',
            'JM' => 'Jamaica',
            'JP' => 'Japan',
            'JE' => 'Jersey',
            'JO' => 'Jordan',
            'KZ' => 'Kazakhstan',
            'KE' => 'Kenya',
            'KI' => 'Kiribati',
            'KR' => 'Korea',
            'KW' => 'Kuwait',
            'KG' => 'Kyrgyzstan',
            'LA' => 'Lao People\'s Democratic Republic',
            'LV' => 'Latvia',
            'LB' => 'Lebanon',
            'LS' => 'Lesotho',
            'LR' => 'Liberia',
            'LY' => 'Libyan Arab Jamahiriya',
            'LI' => 'Liechtenstein',
            'LT' => 'Lithuania',
            'LU' => 'Luxembourg',
            'MO' => 'Macao',
            'MK' => 'Macedonia',
            'MG' => 'Madagascar',
            'MW' => 'Malawi',
            'MY' => 'Malaysia',
            'MV' => 'Maldives',
            'ML' => 'Mali',
            'MT' => 'Malta',
            'MH' => 'Marshall Islands',
            'MQ' => 'Martinique',
            'MR' => 'Mauritania',
            'MU' => 'Mauritius',
            'YT' => 'Mayotte',
            'MX' => 'Mexico',
            'FM' => 'Micronesia, Federated States Of',
            'MD' => 'Moldova',
            'MC' => 'Monaco',
            'MN' => 'Mongolia',
            'ME' => 'Montenegro',
            'MS' => 'Montserrat',
            'MA' => 'Morocco',
            'MZ' => 'Mozambique',
            'MM' => 'Myanmar',
            'NA' => 'Namibia',
            'NR' => 'Nauru',
            'NP' => 'Nepal',
            'NL' => 'Netherlands',
            'AN' => 'Netherlands Antilles',
            'NC' => 'New Caledonia',
            'NZ' => 'New Zealand',
            'NI' => 'Nicaragua',
            'NE' => 'Niger',
            'NG' => 'Nigeria',
            'NU' => 'Niue',
            'NF' => 'Norfolk Island',
            'MP' => 'Northern Mariana Islands',
            'NO' => 'Norway',
            'OM' => 'Oman',
            'PK' => 'Pakistan',
            'PW' => 'Palau',
            'PS' => 'Palestinian Territory, Occupied',
            'PA' => 'Panama',
            'PG' => 'Papua New Guinea',
            'PY' => 'Paraguay',
            'PE' => 'Peru',
            'PH' => 'Philippines',
            'PN' => 'Pitcairn',
            'PL' => 'Poland',
            'PT' => 'Portugal',
            'PR' => 'Puerto Rico',
            'QA' => 'Qatar',
            'RE' => 'Reunion',
            'RO' => 'Romania',
            'RU' => 'Russian Federation',
            'RW' => 'Rwanda',
            'BL' => 'Saint Barthelemy',
            'SH' => 'Saint Helena',
            'KN' => 'Saint Kitts And Nevis',
            'LC' => 'Saint Lucia',
            'MF' => 'Saint Martin',
            'PM' => 'Saint Pierre And Miquelon',
            'VC' => 'Saint Vincent And Grenadines',
            'WS' => 'Samoa',
            'SM' => 'San Marino',
            'ST' => 'Sao Tome And Principe',
            'SA' => 'Saudi Arabia',
            'SN' => 'Senegal',
            'RS' => 'Serbia',
            'SC' => 'Seychelles',
            'SL' => 'Sierra Leone',
            'SG' => 'Singapore',
            'SK' => 'Slovakia',
            'SI' => 'Slovenia',
            'SB' => 'Solomon Islands',
            'SO' => 'Somalia',
            'ZA' => 'South Africa',
            'GS' => 'South Georgia And Sandwich Isl.',
            'ES' => 'Spain',
            'LK' => 'Sri Lanka',
            'SD' => 'Sudan',
            'SR' => 'Suriname',
            'SJ' => 'Svalbard And Jan Mayen',
            'SZ' => 'Swaziland',
            'SE' => 'Sweden',
            'CH' => 'Switzerland',
            'SY' => 'Syrian Arab Republic',
            'TW' => 'Taiwan',
            'TJ' => 'Tajikistan',
            'TZ' => 'Tanzania',
            'TH' => 'Thailand',
            'TL' => 'Timor-Leste',
            'TG' => 'Togo',
            'TK' => 'Tokelau',
            'TO' => 'Tonga',
            'TT' => 'Trinidad And Tobago',
            'TN' => 'Tunisia',
            'TR' => 'Turkey',
            'TM' => 'Turkmenistan',
            'TC' => 'Turks And Caicos Islands',
            'TV' => 'Tuvalu',
            'UG' => 'Uganda',
            'UA' => 'Ukraine',
            'AE' => 'United Arab Emirates',
            'GB' => 'United Kingdom',
            'US' => 'United States',
            'UM' => 'United States Outlying Islands',
            'UY' => 'Uruguay',
            'UZ' => 'Uzbekistan',
            'VU' => 'Vanuatu',
            'VE' => 'Venezuela',
            'VN' => 'Viet Nam',
            'VG' => 'Virgin Islands, British',
            'VI' => 'Virgin Islands, U.S.',
            'WF' => 'Wallis And Futuna',
            'EH' => 'Western Sahara',
            'YE' => 'Yemen',
            'ZM' => 'Zambia',
            'ZW' => 'Zimbabwe',
        );
        $db = \Pimcore\Db::get();
        $sql = "
            SELECT DISTINCT shipping_country_code 
            FROM iwa_marketplace_orders_line_items 
            WHERE shipping_country_code IS NOT NULL 
            AND shipping_country_code != '' 
            AND shipping_country_code != 'null'
            ";
        $results = $db->fetchAllAssociative($sql);
        foreach ($results as $result) {
            $shippingCountryCode = $result['shipping_country_code'];
            if (isset($countries[$shippingCountryCode])) {
                $countryName = $countries[$shippingCountryCode];
                $updateSql = "
                    UPDATE iwa_marketplace_orders_line_items 
                    SET shipping_country = :shipping_country 
                    WHERE shipping_country_code = :shipping_country_code
                ";
                $db->executeStatement($updateSql, [
                    'shipping_country_code' => $shippingCountryCode,
                    'shipping_country' => $countryName
                ]);
            }
        }
    }

}