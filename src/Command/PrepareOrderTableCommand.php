<?php

namespace App\Command;

use Doctrine\DBAL\Exception;
use dokuwiki\Ui\Ui;
use Pimcore\Console\AbstractCommand;
use Pimcore\Model\DataObject\Concrete;
use SimplePie\Cache\DB;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Input\InputOption;
use Pimcore\Model\DataObject\Marketplace;
use Pimcore\Model\DataObject\Product;
use App\Model\DataObject\VariantProduct;
use App\Utils\Utility;

#[AsCommand(
    name: 'app:prepare-order-table',
    description: 'Prepare orderItems table from orders table',
)]

class PrepareOrderTableCommand extends AbstractCommand
{
    private array $marketplaceListWithIds = [];
    private string $transferSqlfilePath = PIMCORE_PROJECT_ROOT . '/src/SQL/OrderTable/Transfer/';

    protected function configure(): void
    {
        $this
            ->addOption('transfer',null, InputOption::VALUE_NONE, 'Transfer iwa_marketplace_orders to iwa_marketplace_orders_line_items')
            ->addOption('variant',null, InputOption::VALUE_NONE, 'Process variant order data find main product')
            ->addOption('updateCoin',null, InputOption::VALUE_NONE, 'Update current coin')
            ->addOption('extraColumns',null, InputOption::VALUE_NONE, 'Insert extra columns')
            ;
    }

    /**
     * @throws Exception
     */
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
            $this->calculatePriceUsd();
        }

        if($input->getOption('extraColumns')) {
            $this->extraColumns();
            $this->currencyRate();
            $this->calculatePriceUsd();
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

    /**
     * @throws Exception
     */
    protected function transferOrders(): void
    {
        $selectMarketplaceIdSql = "SELECT DISTINCT marketplace_id FROM iwa_marketplace_orders";
        if (empty($this->marketplaceListWithIds)) {
            $this->marketplaceList();
        }
        $marketplaceIds = Utility::fetchFromSql($selectMarketplaceIdSql);
        $fileNames = [
            'Shopify' => [
                'old' => 'iwa_marketplace_orders_transfer_shopify_old.sql' ,
                'new' => 'iwa_marketplace_orders_transfer_shopify_new.sql'
            ],
            'Trendyol' => 'iwa_marketplace_orders_transfer_trendyol.sql',
            'Bol.com' => 'iwa_marketplace_orders_transfer_bolcom.sql',
            'Etsy' => 'iwa_marketplace_orders_transfer_etsy.sql',
            'Amazon' => 'iwa_marketplace_orders_transfer_amazon.sql',
            'Takealot' => 'iwa_marketplace_orders_transfer_takealot.sql',
            'Wallmart' => 'iwa_marketplace_orders_transfer_wallmart.sql',
            'Ciceksepeti' => 'iwa_marketplace_orders_transfer_ciceksepeti.sql',
            'Wayfair' => 'iwa_marketplace_orders_transfer_wayfair.sql',
            'Hepsiburada' => 'iwa_marketplace_orders_transfer_hepsiburada.sql',
            'Ebay' => 'iwa_marketplace_orders_transfer_ebay.sql'
        ];
        foreach ($marketplaceIds as $marketplaceId) {
            $id = $marketplaceId['marketplace_id'];
            if (isset($this->marketplaceListWithIds[$id])) {
                $marketplaceType = $this->marketplaceListWithIds[$id];
                echo "Marketplace ID: $id - Type: $marketplaceType\n";
                if (isset($fileNames[$marketplaceType])) {
                    if (is_array($fileNames[$marketplaceType])) {
                        foreach ($fileNames[$marketplaceType] as $key => $file) {
                            Utility::executeSqlFile($this->transferSqlfilePath . $file, [
                                'marketPlaceId' => $id,
                                'marketplaceType' => $marketplaceType
                            ]);
                            echo "Executed: $marketplaceType ($key)\n";
                        }
                    } else {
                        Utility::executeSqlFile($this->transferSqlfilePath . $fileNames[$marketplaceType], [
                            'marketPlaceId' => $id,
                            'marketplaceType' => $marketplaceType
                        ]);
                        echo "Executed: $marketplaceType\n";
                    }
                }
                echo "Completed: $marketplaceType\n";
            }
        }
    }

    /**
     * @throws Exception
     */
    protected function processVariantOrderData(): void
    {
        $selectVariantSql = "
            SELECT
                DISTINCT variant_id
            FROM
                iwa_marketplace_orders_line_items
            WHERE
                marketplace_type = :marketplaceType;";

        if (empty($this->marketplaceListWithIds)) {
            $this->marketplaceList();
        }
        $marketplaceTypes = array_values(array_unique($this->marketplaceListWithIds));
        foreach ($marketplaceTypes as $marketplaceType) {
            $values = Utility::fetchFromSql($selectVariantSql, [
                'marketplaceType' => $marketplaceType
            ]);
            $index = 0;
            foreach ($values as $row) {
                $index++;
                if (!($index % 100)) echo "\rProcessing $index of " . count($values) . "\r";
                $this->prepareOrderTable($row['variant_id'],$marketplaceType);
            }
        }
    }

    /**
     * @throws Exception
     */
    protected function prepareOrderTable($uniqueMarketplaceId, $marketplaceType): void
    {
        $updateVariantSql = "
            UPDATE iwa_marketplace_orders_line_items
            SET iwasku = :iwasku, parent_identifier  = :identifier, product_category = :productCategory, product_type = :productType, variant_name = :variantName, parent_name = :parentName
            WHERE variant_id = :uniqueMarketplaceId AND marketplace_type= :marketplaceType;";

        $variantObject = match ($marketplaceType) {
            'Shopify', 'Etsy', 'Amazon', 'Takealot', 'Ciceksepeti', 'Hepsiburada' => $this->findVariantProduct($uniqueMarketplaceId),
            'Trendyol' => $this->findVariantProduct($uniqueMarketplaceId,'"productCode"'),
            'Bol.com' => $this->findVariantProduct($uniqueMarketplaceId,'"product-ids".bolProductId'),
            'Wallmart' => $this->findVariantProduct($uniqueMarketplaceId,'"sku"'),
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
                $productCategory = $parent->getInheritedField('ProductCategory');
                if (!$productCategory) {
                    echo "Product category is required for adding/updating VariantProduct with uniqueMarketplaceId $uniqueMarketplaceId\n";
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
            Utility::executeSql($updateVariantSql, [
                'iwasku' => $iwasku,
                'identifier' => $identifier,
                'productCategory' => $productCategory,
                'productType' => $productType,
                'variantName' => $variantName,
                'parentName' => $parentName,
                'uniqueMarketplaceId' => $uniqueMarketplaceId,
                'marketplaceType' => $marketplaceType
            ]);
        }
    }

    /**
     * @throws Exception
     */
    protected function findVariantProduct($uniqueMarketplaceId, $field = null): VariantProduct|Concrete|null
    {
        $findVariantSql = "
            SELECT object_id
            FROM iwa_json_store
            WHERE
                field_name = 'apiResponseJson'  AND JSON_UNQUOTE(JSON_EXTRACT(json_data, :jsonPath)) = :uniqueId LIMIT 1;";

        if ($field === null) {
            return VariantProduct::findOneByField('uniqueMarketplaceId', $uniqueMarketplaceId, $unpublished = true);
        }
        $jsonPath = '$.' . $field;
        $result = Utility::fetchFromSql($findVariantSql, [
            'jsonPath' => $jsonPath,
            'uniqueId' => $uniqueMarketplaceId
        ]);
        $objectId = $result[0]['object_id'] ?? null;
        if ($objectId) {
           return VariantProduct::getById($objectId);
        }
        return null;
    }

    /**
     * @return void
     * @throws Exception
     */
    protected function currencyRate(): void
    {
        $selectCurrencySql = "
            SELECT DISTINCT currency, DATE(created_at) as created_date
            FROM iwa_marketplace_orders_line_items
            WHERE currency is not null  AND currency_rate=0.00";

        $updateCurrencySql = "
            UPDATE iwa_marketplace_orders_line_items
            SET currency_rate = :currency_rate
            WHERE currency = :currency AND DATE(created_at) = :created_date AND currency_rate IS NULL;";

        $distinctRows = Utility::fetchFromSql($selectCurrencySql);
        foreach ($distinctRows as $row) {
            try {
                $currencyRate = Utility::getCurrencyValueByDate($row['currency'], $row['created_date']);
                Utility::executeSql($updateCurrencySql, [
                    'currency_rate' => (float) $currencyRate,
                    'currency' => $row['currency'],
                    'created_date' => $row['created_date']
                ]);
                echo "Currency rate updated for currency: {$row['currency']}, date: {$row['created_date']}, rate: {$currencyRate}\n";
            } catch (Exception $e) {
                echo "Error: " . $e->getMessage();
            }
        }
    }

    /**
     * @throws Exception
     */
    protected function calculatePriceUsd(): void
    {
        $calculatePriceSql = "
            SELECT id, currency, price, total_price, subtotal_price, DATE(created_at) as created_date
            FROM iwa_marketplace_orders_line_items
            WHERE (product_price_usd IS NULL OR total_price_usd IS NULL OR total_price_usd = 0) AND currency IS NOT NULL;";

        $calculatePriceUpdateSql = "
            UPDATE iwa_marketplace_orders_line_items
            SET product_price_usd = :productPriceUsd, total_price_usd = :totalPriceUsd, subtotal_price_usd = :subtotalPriceUsd
            WHERE id = :id;";

        $results = Utility::fetchFromSql($calculatePriceSql);
        foreach ($results as $row) {
            $price = $row['price'] ?? 0;
            $subtotalPrice = $row['subtotal_price'] ?? 0;
            $totalPrice = $row['total_price'] ?? 0;
            $productPriceUsd = Utility::convertCurrency($price, $row['currency'], "USD", $row['created_date']) ?? 0;
            $totalPriceUsd = Utility::convertCurrency($totalPrice, $row['currency'], "USD", $row['created_date']) ?? 0;
            $subtotalPriceUsd = Utility::convertCurrency($subtotalPrice, $row['currency'], "USD", $row['created_date']) ?? 0;
            Utility::executeSql($calculatePriceUpdateSql, [
                'productPriceUsd' => $productPriceUsd,
                'totalPriceUsd' => $totalPriceUsd,
                'subtotalPriceUsd' => $subtotalPriceUsd,
                'id' => $row['id']
            ]);
            echo "ID: {$row['id']} Price: $price, Subtotal Price: $subtotalPrice, Total Price: $totalPrice\n";
        }
    }

    /**
     * @throws Exception
     */
    protected function extraColumns(): void
    {
        echo "Set Marketplace key\n";
        $this->setMarketplaceKey();
        echo "Complated Marketplace key\n";
        echo "Calculating is Parse URL\n";
        $this->parseUrl();
        echo "Complated Parse URL\n";
        echo "Calculating Closed At Diff\n";

        $closedAtDiffSql = "
        UPDATE iwa_marketplace_orders_line_items
        SET completion_day = DATEDIFF(DATE(closed_at), DATE(created_at))
        WHERE DATE(closed_at) IS NOT NULL;";

        Utility::executeSql($closedAtDiffSql);
        echo "Complated Closed At Diff\n";
        echo "Calculating is Discount\n";

        $discountSql = "
            UPDATE iwa_marketplace_orders_line_items
            SET has_discount =
            CASE
                WHEN total_discount IS NOT NULL AND total_discount <> 0.00 THEN TRUE
                ELSE FALSE
            END;";

        Utility::executeSql($discountSql);
        echo "Complated is Discount\n";
        echo "Calculating is Country Name\n";
        $this->countryCodes();
        echo "Complated is Country Name\n";
        echo "Calculating USA Code\n";
        $this->usaCode();
        echo "Complated USA Code\n";
        echo "Calculating Bolcom Total Price\n";

        $totalPriceSql = "
            UPDATE iwa_marketplace_orders_line_items AS t1
                INNER JOIN (
                SELECT
                order_id,
                SUM(price) AS total_price
                FROM iwa_marketplace_orders_line_items
                WHERE marketplace_type = :marketplaceType
                GROUP BY order_id
                ) AS t2
            ON t1.order_id = t2.order_id
                SET t1.total_price = t2.total_price
            WHERE t1.marketplace_type = :marketplaceType;";

        Utility::executeSql($totalPriceSql, ['marketplaceType' => 'Bol.com']);
        echo "Complated Bolcom Total Price\n";
        echo "Fix Bolcom Orders\n";

        $bolcomFixOrdersSql = "
            DELETE FROM iwa_marketplace_orders_line_items
            WHERE marketplace_type = 'Bol.com' AND order_id = '0';";

        Utility::executeSql($bolcomFixOrdersSql);
        echo "Complated Fix Bolcom Orders\n";
        echo "Calculating is Cancelled\n";

        $cancelledSql = "
            UPDATE iwa_marketplace_orders_line_items
            SET is_canceled =
                CASE
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
                      WHEN marketplace_type = 'Ciceksepeti' AND fulfillments_status_control = 'null' THEN 'not_cancelled'
                      WHEN marketplace_type = 'Ciceksepeti' AND fulfillments_status_control != 'null' THEN 'cancelled'
                      WHEN marketplace_type = 'Takealot' AND fulfillments_status = 'Returned' OR fulfillments_status = 'Cancelled by Customer'  THEN 'cancelled'
                      WHEN marketplace_type = 'Takealot' AND fulfillments_status = 'Returned' OR fulfillments_status = 'Cancelled by Customer' THEN 'not_cancelled'
                      WHEN marketplace_type = 'Wayfair' AND fulfillments_status = 'true' THEN 'cancelled'
                      WHEN marketplace_type = 'Wayfair' AND fulfillments_status = 'false' THEN 'not_cancelled'
                END;";

        Utility::executeSql($cancelledSql);
        echo "Complated is Cancelled\n";
        echo "Amazon Subtotal Calculate\n";

        $amazonSubtotalSql = "
            UPDATE
            iwa_marketplace_orders_line_items
            JOIN (
            SELECT
            order_id,
            SUM(price) - SUM(total_discount) AS pnet
            FROM
            iwa_marketplace_orders_line_items
            WHERE
            marketplace_type = 'Amazon'
            GROUP BY
            order_id
            ) AS calculated_pnet
        ON
            iwa_marketplace_orders_line_items.order_id = calculated_pnet.order_id
            SET
                iwa_marketplace_orders_line_items.subtotal_price = calculated_pnet.pnet;";

        Utility::executeSql($amazonSubtotalSql);
        echo "Complated Amazon Subtotal Calculate\n";
        echo "Wayfair Total Price\n";
        Utility::executeSql($totalPriceSql, ['marketplaceType' => 'Wayfair']);
        echo "Complated Wayfair Total Price\n";
        echo "Wallmart Total Price\n";
        Utility::executeSql($totalPriceSql, ['marketplaceType' => 'Wallmart']);
        echo "Complated Wallmart Total Price\n";
        echo "Takealot Total Price\n";
        Utility::executeSql($totalPriceSql, ['marketplaceType' => 'Takealot']);
        echo "Complated Takealot Total Price\n";
    }

    /**
     * @throws Exception
     */
    protected function setMarketplaceKey(): void
    {
        $marketplaceKeySql = "
            SELECT
                DISTINCT marketplace_id
            FROM
                iwa_marketplace_orders_line_items
            WHERE
                marketplace_id IS NOT NULL";

        $marketplaceKeyUpdateSql = "
            UPDATE iwa_marketplace_orders_line_items
            SET marketplace_key = :marketplaceKey
            WHERE marketplace_id = :marketplaceId;";

        $values = Utility::fetchFromSql($marketplaceKeySql);
        foreach ($values as $row) {
            $id = $row['marketplace_id'];
            $marketplace = Marketplace::getById($id);
            if ($marketplace) {
                $marketplaceKey = $marketplace->getKey();
                Utility::executeSql($marketplaceKeyUpdateSql, [
                    'marketplaceKey' => $marketplaceKey,
                    'marketplaceId' => $id
                ]);
            } else {
                echo "Marketplace not found for ID: $id\n";
            }
        }
    }

    /**
     * @throws Exception
     */
    protected function parseUrl(): void
    {
        $parseUrlSelectSql = "
            SELECT DISTINCT referring_site
            FROM iwa_marketplace_orders_line_items
            WHERE
                referring_site IS NOT NULL
                AND referring_site != ''
                AND referring_site != 'null';";

        $parseUrlUpdateSql = "
            UPDATE iwa_marketplace_orders_line_items
            SET referring_site_domain = :referringSiteDomain
            WHERE referring_site = :referringSite;";

        $tldList = ['com', 'org', 'net', 'gov', 'm', 'io', 'I', 'co', 'uk', 'de', 'lens', 'search', 'pay', 'tv', 'nl', 'au', 'ca', 'lm', 'sg', 'at', 'nz', 'in', 'tt', 'dk', 'es', 'no', 'se', 'ae', 'hk', 'sa', 'us', 'ie', 'be', 'pk', 'ro', 'co', 'il', 'hu', 'fi', 'pa', 't', 'm', 'io', 'cse', 'az', 'new', 'tr', 'web', 'cz', 'gm', 'ua', 'www', 'fr', 'gr', 'ch', 'pt', 'pl', 'rs', 'bg', 'hr','l','it','m','lm','pay'];
        $results = Utility::fetchFromSql($parseUrlSelectSql);
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
                Utility::executeSql($parseUrlUpdateSql, [
                    'referringSiteDomain' => $domain,
                    'referringSite' => $row['referring_site']
                ]);
            }
        }
    }

    /**
     * @throws Exception
     */
    protected function usaCode(): void
    {
        $usaCodeSelectSql = "
            SELECT DISTINCT shipping_province
            FROM iwa_marketplace_orders_line_items
            WHERE
                shipping_province IS NOT NULL
                AND shipping_province != ''
                AND shipping_province != 'null';";

        $usaCodeUpdateSql = "
            UPDATE iwa_marketplace_orders_line_items
            SET province_code = :province_code
            WHERE shipping_province = :shipping_province";

        $filePath = PIMCORE_PROJECT_ROOT . '/src/JSON/usa_iso_codes.json';
        if (!file_exists($filePath)) {
            throw new Exception("USA states JSON file not found.");
        }
        $isoCodes = json_decode(file_get_contents($filePath), true);
        if (json_last_error() !== JSON_ERROR_NONE) {
            throw new Exception("Error parsing JSON file: " . json_last_error_msg());
        }
        $results = Utility::fetchFromSql($usaCodeSelectSql);
        foreach ($results as $result) {
            $shippingProvince = $result['shipping_province'];
            if (isset($isoCodes[$shippingProvince])) {
                $provinceCode = $isoCodes[$shippingProvince];
                Utility::executeSql($usaCodeUpdateSql, [
                    'province_code' => $provinceCode,
                    'shipping_province' => $shippingProvince
                ]);
            }
        }
    }

    /**
     * @throws Exception
     */
    protected function countryCodes(): void
    {
        $countryCodesSelectSql = "
            SELECT DISTINCT shipping_country_code
            FROM iwa_marketplace_orders_line_items
            WHERE
                shipping_country_code IS NOT NULL
                AND shipping_country_code != ''
                AND shipping_country_code != 'null'";

        $countryCodesUpdateSql = "
            UPDATE iwa_marketplace_orders_line_items
            SET shipping_country = :shipping_country
            WHERE shipping_country_code = :shipping_country_code";

        $filePath = PIMCORE_PROJECT_ROOT . '/src/JSON/country_iso_codes.json';
        if (!file_exists($filePath)) {
            throw new Exception("USA states JSON file not found.");
        }
        $countries = json_decode(file_get_contents($filePath), true);
        if (json_last_error() !== JSON_ERROR_NONE) {
            throw new Exception("Error parsing JSON file: " . json_last_error_msg());
        }
        $results = Utility::fetchFromSql($countryCodesSelectSql);
        foreach ($results as $result) {
            $shippingCountryCode = $result['shipping_country_code'];
            if (isset($countries[$shippingCountryCode])) {
                $countryName = $countries[$shippingCountryCode];
                Utility::executeSql($countryCodesUpdateSql, [
                    'shipping_country_code' => $shippingCountryCode,
                    'shipping_country' => $countryName
                ]);
            }
        }
    }

}