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

    protected function marketplaceList(): void
    {
        $marketplaceList = Marketplace::getMarketplaceList();
        foreach ($marketplaceList as $marketplace) {
            $this->marketplaceListWithIds[$marketplace->getId()] = $marketplace->getMarketplaceType();
        }
    }

    protected function transferOrders(): void
    {
        if (empty($this->marketplaceListWithIds)) {
            $this->marketplaceList();
        }
        $db = \Pimcore\Db::get();
        $sql = "SELECT DISTINCT marketplace_id FROM iwa_marketplace_orders";
        $marketplaceIds = $db->fetchAllAssociative($sql);
        $filePath = PIMCORE_PROJECT_ROOT . '/src/SQL/OrderTable/';
        foreach ($marketplaceIds as $marketplaceId) {
            $id = $marketplaceId['marketplace_id']; 
            if (isset($this->marketplaceListWithIds[$id])) {
                $marketplaceType = $this->marketplaceListWithIds[$id];
                echo "Marketplace ID: $id - Type: $marketplaceType\n";
                $result = match ($marketplaceType) {
                    'Shopify' => $this->transferOrdersExecute($filePath . 'iwa_marketplace_orders_transfer_shopify.sql', $id, $marketplaceType),
                    'Trendyol' => $this->transferOrdersExecute($filePath . 'iwa_marketplace_orders_transfer_trendyol.sql', $id,$marketplaceType),
                    'Bol.com' => $this->transferOrdersExecute($filePath . 'iwa_marketplace_orders_transfer_bolcom.sql', $id,$marketplaceType),
                    'Etsy' => $this->transferOrdersExecute($filePath . 'iwa_marketplace_orders_transfer_etsy.sql', $id,$marketplaceType),
                    'Amazon' => $this->transferOrdersExecute($filePath . 'iwa_marketplace_orders_transfer_amazon.sql', $id,$marketplaceType),
                    'Takealot' => $this->transferOrdersExecute($filePath . 'iwa_marketplace_orders_transfer_takealot.sql', $id,$marketplaceType),
                    'Wallmart' => $this->transferOrdersExecute($filePath . 'iwa_marketplace_orders_transfer_wallmart.sql', $id,$marketplaceType),
                    'Ciceksepeti' => $this->transferOrdersExecute($filePath . 'iwa_marketplace_orders_transfer_ciceksepeti.sql', $id,$marketplaceType),
                    default => null,
                };
                echo "Result: $result\n";
                echo "Complated: $marketplaceType\n";
            }
        }
    }

    protected function transferOrdersExecute($sqlPath, $marketPlaceId, $marketPlaceType): void
    {
        $sql = file_get_contents($sqlPath);
        try {
            $db = \Pimcore\Db::get();
            $statement = $db->prepare($sql);
            $statement->executeStatement([
                'marketPlaceId' => $marketPlaceId,
                'marketplaceType' => $marketPlaceType,
            ]);
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

    protected function prepareOrderTable($uniqueMarketplaceId, $marketplaceType): void
    {
        $variantObject = match ($marketplaceType) {
            'Shopify', 'Etsy', 'Amazon', 'Takealot', 'Ciceksepeti' => $this->findVariantProduct($uniqueMarketplaceId),
            'Trendyol' => $this->findVariantProduct($uniqueMarketplaceId,'productCode'),
            'Bol.com' => $this->findVariantProduct($uniqueMarketplaceId,'\"product-ids\".bolProductId'),
            'Wallmart' => $this->findVariantProduct($uniqueMarketplaceId,'sku'),
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
            $this->insertIntoTable($uniqueMarketplaceId, $iwasku, $identifier, $productType, $variantName, $parentName, $marketplaceType);
        }
    }

    protected function insertIntoTable($uniqueMarketplaceId, $iwasku, $identifier, $productType, $variantName, $parentName, $marketplaceType)
    {
        $db = \Pimcore\Db::get();
        $sql = "UPDATE iwa_marketplace_orders_line_items
        SET iwasku = :iwasku, parent_identifier  = :identifier, product_type = :productType, variant_name = :variantName, parent_name = :parentName
        WHERE variant_id = :uniqueMarketplaceId AND marketplace_type= :marketplaceType;";
        $statement = $db->prepare($sql);
        $statement->executeStatement([
            'iwasku' => $iwasku,
            'identifier' => $identifier,
            'productType' => $productType,
            'variantName' => $variantName,
            'parentName' => $parentName,
            'uniqueMarketplaceId' => $uniqueMarketplaceId,
            'marketplaceType' => $marketplaceType,
        ]);
    }

    protected function findVariantProduct($uniqueMarketplaceId, $field = null)
    {
        $variantProduct = null;
        if ($field === null) {
            return VariantProduct::findOneByField('uniqueMarketplaceId', $uniqueMarketplaceId,$unpublished = true);
        }
        $sql = "SELECT object_id FROM iwa_json_store  WHERE field_name = 'apiResponseJson'  AND JSON_UNQUOTE(JSON_EXTRACT(json_data, '$.$field')) = ? LIMIT 1;";
        $db = \Pimcore\Db::get();
        $result = $db->fetchAllAssociative($sql, [$uniqueMarketplaceId]);
        $objectId = $result[0]['object_id'] ?? null;
        return VariantProduct::getById($objectId);
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