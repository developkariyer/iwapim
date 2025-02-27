<?php

namespace App\Command;

use Doctrine\DBAL\Exception;
use Pimcore\Console\AbstractCommand;
use Pimcore\Model\DataObject\Concrete;
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
    private string $transferSqlfilePath = PIMCORE_PROJECT_ROOT . '/src/SQL/OrderTable/Transfer/';
    private string $extraColumnsSqlfilePath = PIMCORE_PROJECT_ROOT . '/src/SQL/OrderTable/ExtraColumns/';
    private string $variantSqlfilePath = PIMCORE_PROJECT_ROOT . '/src/SQL/OrderTable/Variant/';

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
        if (empty($this->marketplaceListWithIds)) {
            $this->marketplaceList();
        }
        $marketplaceIds = Utility::fetchFromSqlFile($this->transferSqlfilePath . 'selectMarketplaceIds.sql');
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
        if (empty($this->marketplaceListWithIds)) {
            $this->marketplaceList();
        }
        $marketplaceTypes = array_values(array_unique($this->marketplaceListWithIds));
        foreach ($marketplaceTypes as $marketplaceType) {
            $values = Utility::fetchFromSqlFile($this->variantSqlfilePath . 'selectVariant.sql', ['marketplaceType' => $marketplaceType]);
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
        $variantObject = match ($marketplaceType) {
            'Shopify', 'Etsy', 'Amazon', 'Takealot', 'Ciceksepeti' => $this->findVariantProduct($uniqueMarketplaceId),
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
            Utility::executeSqlFile($this->variantSqlfilePath . 'updateVariant.sql', [
                'iwasku' => $iwasku,
                'identifier' => $identifier,
                'productType' => $productType,
                'variantName' => $variantName,
                'parentName' => $parentName,
                'uniqueMarketplaceId' => $uniqueMarketplaceId,
                'marketplaceType' => $marketplaceType,
            ]);
        }
    }

    /**
     * @throws Exception
     */
    protected function findVariantProduct($uniqueMarketplaceId, $field = null): VariantProduct|Concrete|null
    {
        if ($field === null) {
            return VariantProduct::findOneByField('uniqueMarketplaceId', $uniqueMarketplaceId, $unpublished = true);
        }
        $jsonPath = '$.' . $field;
        $result = Utility::fetchFromSqlFile($this->variantSqlfilePath . 'findVariant.sql', ['jsonPath' => $jsonPath, 'uniqueId' => $uniqueMarketplaceId]);
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
        $distinctRows = Utility::fetchFromSqlFile($this->extraColumnsSqlfilePath . 'selectCurrency.sql');
        foreach ($distinctRows as $row) {
            try {
                $currencyRate = Utility::getCurrencyValueByDate($row['currency'], $row['created_date']);
                Utility::executeSqlFile($this->extraColumnsSqlfilePath . 'updateCurrency.sql', [
                    'currency_rate' => (float) $currencyRate,
                    'currency' => $row['currency'],
                    'created_date' => $row['created_date'],
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
        $results = Utility::fetchFromSqlFile($this->extraColumnsSqlfilePath . 'selectCalculatePriceUsd.sql');
        foreach ($results as $row) {
            $price = $row['price'] ?? 0;
            $subtotalPrice = $row['subtotal_price'] ?? 0;
            $totalPrice = $row['total_price'] ?? 0;
            $productPriceUsd = Utility::convertCurrency($price, $row['currency'], "USD", $row['created_date']) ?? 0;
            $totalPriceUsd = Utility::convertCurrency($totalPrice, $row['currency'], "USD", $row['created_date']) ?? 0;
            $subtotalPriceUsd = Utility::convertCurrency($subtotalPrice, $row['currency'], "USD", $row['created_date']) ?? 0;
            Utility::executeSqlFile($this->extraColumnsSqlfilePath . 'updateCalculatePriceUsd.sql', [
                'productPriceUsd' => $productPriceUsd,
                'totalPriceUsd' => $totalPriceUsd,
                'subtotalPriceUsd' => $subtotalPriceUsd,
                'id' => $row['id'],
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
        Utility::executeSqlFile($this->extraColumnsSqlfilePath . 'closedAtDiff.sql');
        echo "Complated Closed At Diff\n";
        echo "Calculating is Discount\n";
        Utility::executeSqlFile($this->extraColumnsSqlfilePath . 'discountValue.sql');
        echo "Complated is Discount\n";
        echo "Calculating is Country Name\n";
        $this->countryCodes();
        echo "Complated is Country Name\n";
        echo "Calculating USA Code\n";
        $this->usaCode();
        echo "Complated USA Code\n";
        echo "Calculating Bolcom Total Price\n";
        Utility::executeSqlFile($this->extraColumnsSqlfilePath . 'calculateTotalPrice.sql', ['marketplaceType' => 'Bol.com']);
        echo "Complated Bolcom Total Price\n";
        echo "Fix Bolcom Orders\n";
        Utility::executeSqlFile($this->extraColumnsSqlfilePath . 'bolcomFixOrders.sql');
        echo "Complated Fix Bolcom Orders\n";
        echo "Calculating is Cancelled\n";
        Utility::executeSqlFile($this->extraColumnsSqlfilePath . 'isCancelled.sql');
        echo "Complated is Cancelled\n";
        echo "Amazon Subtotal Calculate\n";
        Utility::executeSqlFile($this->extraColumnsSqlfilePath . 'amazonSubtotal.sql');
        echo "Complated Amazon Subtotal Calculate\n";
        echo "Wayfair Total Price\n";
        Utility::executeSqlFile($this->extraColumnsSqlfilePath . 'calculateTotalPrice.sql', ['marketplaceType' => 'Wayfair']);
        echo "Complated Wayfair Total Price\n";
        echo "Wallmart Total Price\n";
        Utility::executeSqlFile($this->extraColumnsSqlfilePath . 'calculateTotalPrice.sql', ['marketplaceType' => 'Wallmart']);
        echo "Complated Wallmart Total Price\n";
        echo "Takealot Total Price\n";
        Utility::executeSqlFile($this->extraColumnsSqlfilePath . 'calculateTotalPrice.sql', ['marketplaceType' => 'Takealot']);
        echo "Complated Takealot Total Price\n";
    }

    /**
     * @throws Exception
     */
    protected function setMarketplaceKey(): void
    {
        $values = Utility::fetchFromSqlFile($this->extraColumnsSqlfilePath . 'setMarketPlaceKeyFetch.sql');
        foreach ($values as $row) {
            $id = $row['marketplace_id'];
            $marketplace = Marketplace::getById($id);
            if ($marketplace) {
                $marketplaceKey = $marketplace->getKey();
                Utility::executeSqlFile($this->extraColumnsSqlfilePath . 'updateMarketPlaceKey.sql', [
                    'marketplaceKey' => $marketplaceKey,
                    'marketplaceId' => $id,
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
        $tldList = ['com', 'org', 'net', 'gov', 'm', 'io', 'I', 'co', 'uk', 'de', 'lens', 'search', 'pay', 'tv', 'nl', 'au', 'ca', 'lm', 'sg', 'at', 'nz', 'in', 'tt', 'dk', 'es', 'no', 'se', 'ae', 'hk', 'sa', 'us', 'ie', 'be', 'pk', 'ro', 'co', 'il', 'hu', 'fi', 'pa', 't', 'm', 'io', 'cse', 'az', 'new', 'tr', 'web', 'cz', 'gm', 'ua', 'www', 'fr', 'gr', 'ch', 'pt', 'pl', 'rs', 'bg', 'hr','l','it','m','lm','pay'];
        $results = Utility::fetchFromSqlFile($this->extraColumnsSqlfilePath . 'parseUrlSelect.sql');
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
                Utility::executeSqlFile($this->extraColumnsSqlfilePath . 'parseUrlUpdate.sql', [
                    'referringSiteDomain' => $domain,
                    'referringSite' => $row['referring_site'],
                ]);
            }
        }
    }

    /**
     * @throws Exception
     */
    protected function usaCode(): void
    {
        $filePath = PIMCORE_PROJECT_ROOT . '/src/JSON/usa_iso_codes.json';
        if (!file_exists($filePath)) {
            throw new Exception("USA states JSON file not found.");
        }
        $isoCodes = json_decode(file_get_contents($filePath), true);
        if (json_last_error() !== JSON_ERROR_NONE) {
            throw new Exception("Error parsing JSON file: " . json_last_error_msg());
        }
        $results = Utility::fetchFromSqlFile($this->extraColumnsSqlfilePath . 'usaCodeSelect.sql');
        foreach ($results as $result) {
            $shippingProvince = $result['shipping_province'];
            if (isset($isoCodes[$shippingProvince])) {
                $provinceCode = $isoCodes[$shippingProvince];
                Utility::executeSqlFile($this->extraColumnsSqlfilePath . 'usaCodeUpdate.sql', [
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
        $filePath = PIMCORE_PROJECT_ROOT . '/src/JSON/country_iso_codes.json';
        if (!file_exists($filePath)) {
            throw new Exception("USA states JSON file not found.");
        }
        $countries = json_decode(file_get_contents($filePath), true);
        if (json_last_error() !== JSON_ERROR_NONE) {
            throw new Exception("Error parsing JSON file: " . json_last_error_msg());
        }
        $results = Utility::fetchFromSqlFile($this->extraColumnsSqlfilePath . 'countryCodesSelect.sql');
        foreach ($results as $result) {
            $shippingCountryCode = $result['shipping_country_code'];
            if (isset($countries[$shippingCountryCode])) {
                $countryName = $countries[$shippingCountryCode];
                Utility::executeSqlFile($this->extraColumnsSqlfilePath . 'countryCodesUpdate.sql', [
                    'shipping_country_code' => $shippingCountryCode,
                    'shipping_country' => $countryName
                ]);
            }
        }
    }

}