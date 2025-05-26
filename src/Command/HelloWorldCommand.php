<?php

namespace App\Command;

use App\Connector\Gemini\GeminiConnector;
use App\Message\CiceksepetiCategoryUpdateMessage;
use App\Message\TestMessage;
use App\Model\DataObject\Marketplace;
use App\Model\DataObject\VariantProduct;
use App\Utils\Utility;
use Doctrine\DBAL\Exception;
use phpseclib3\File\ASN1\Maps\AttributeValue;
use Pimcore\Console\AbstractCommand;
use Pimcore\Db;
use Pimcore\Model\Asset;
use Pimcore\Model\DataObject\AbstractObject;
use Pimcore\Model\DataObject\Product;
use Pimcore\Model\Element\DuplicateFullPathException;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Messenger\MessageBusInterface;
use App\Message\ProductListingMessage;
use Symfony\Component\Messenger\Stamp\TransportNamesStamp;
use Symfony\Component\HttpClient\HttpClient;

#[AsCommand(
    name: 'app:hello-world',
    description: 'Outputs Hello, World!'
)]
class HelloWorldCommand extends AbstractCommand
{
    public function __construct(private MessageBusInterface $bus)
    {
        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $cfwTrSql = "SELECT oo_id FROM object_query_varyantproduct WHERE marketplace__id = :marketplace_id";
        $cfwTrVariantProductsIds = Utility::fetchFromSql($cfwTrSql, ['marketplace_id' => $this->marketplaceConfig['shopifycfwtr']]);
        if (!is_array($cfwTrVariantProductsIds) || empty($cfwTrVariantProductsIds)) {
            echo "No Shopify products found for Ciceksepeti sync.\n";
        }
        foreach ($cfwTrVariantProductsIds as $cfwTrVariantProductsId) {
            $shopifyProduct = VariantProduct::getById($cfwTrVariantProductsId['oo_id']);
            if (!$shopifyProduct instanceof VariantProduct) {
                echo "Invalid Shopify product ID: " . $cfwTrVariantProductsId['oo_id'] . ", skipping...";
                continue;
            }
            $mainProducts = $shopifyProduct->getMainProduct();
            if (!is_array($mainProducts) || empty($mainProducts) || !$mainProducts[0] instanceof Product) {
                echo "No main product found for Shopify product ID: " . $cfwTrVariantProductsId['oo_id'] . "\n";
                continue;
            }
            $mainProduct = $mainProducts[0];
            echo $mainProduct->getVariationSize() . "\n";

        }

        return Command::SUCCESS;
    }

    private function parseDimensions($value): ?array
    {
        $normalized = strtolower(trim($value));
        $normalized = str_replace(',', '.', $normalized);
        $normalized = preg_replace('/[^0-9.x]/', '', $normalized);
        $parts = explode('x', $normalized);
        if (count($parts) >= 2 && is_numeric($parts[0]) && is_numeric($parts[1])) {
            return [
                'width' => (int) round((float) $parts[0]),
                'height' => (int) round((float) $parts[1]),
            ];
        }
        if (count($parts) === 1 && is_numeric($parts[0])) {
            return [
                'width' => (int) round((float) $parts[0]),
                'height' => 0
            ];
        }
        return null;
    }

    /**
     * @param int $attributeId
     * @param string $searchValue
     * @param int $threshold
     * @return array|null
     */
    private function findBestAttributeMatch($attributeId, $searchValue, $isSize): ?array
    {
        $searchValueNormalized = $this->normalizeAttributeValue($searchValue);
        $searchDims = $isSize ? $this->parseDimensions($searchValueNormalized) : null;
        $searchDimsCount = $searchDims && isset($searchDims['height']) && $searchDims['height'] === 0 ? 1 : count($searchDims);
        $sql = "SELECT attribute_value_id, name FROM iwa_ciceksepeti_category_attributes_values 
            WHERE attribute_id = :attribute_id";
        $allValues = Utility::fetchFromSql($sql, ['attribute_id' => $attributeId]);
        if (empty($allValues)) {
            $this->logger->warning("⚠️ [AttributeMatch] No attribute values found in DB for attributeId: {$attributeId}");
            return null;
        }
        $bestMatch = null;
        $smallestDiff = PHP_INT_MAX;
        foreach ($allValues as $value) {
            $dbValueNormalized = $this->normalizeAttributeValue($value['name']);
            if ($searchValueNormalized === $dbValueNormalized) {
                $this->logger->info("✅ [AttributeMatch] Exact match: '{$searchValue}' ➜ '{$value['name']}' (ID: {$value['attribute_value_id']})");
                return $value;
            }
            if ($isSize && $searchDims) {
                $dbDims = $this->parseDimensions($dbValueNormalized);
                $dbDimsCount = $dbDims && isset($dbDims['height']) && $dbDims['height'] === 0 ? 1 : count($dbDims);;
                if ($dbDims && $dbDimsCount === $searchDimsCount) {
                    $widthDiff = $searchDims['width'] - $dbDims['width'];
                    $heightDiff = $searchDims['height'] - $dbDims['height'];
                    $totalDiff = $widthDiff + $heightDiff;
                    $widthOk = $widthDiff >= 0 && $widthDiff <= 25;
                    $heightOk = $searchDims['height'] === 0 || ($heightDiff >= 0 && $heightDiff <= 25);
                    if ($widthOk && $heightOk && $totalDiff < $smallestDiff) {
                        $smallestDiff = $totalDiff;
                        $bestMatch = $value;
                    }
                }
            }
        }
        if ($bestMatch) {
            $this->logger->info("🔍 [AttributeMatch] Approximate match: '{$searchValue}' ➜ '{$bestMatch['name']}' (ID: {$bestMatch['attribute_value_id']})");
        } else {
            $this->logger->notice("❌ [AttributeMatch] No match found for: '{$searchValueNormalized}' (attributeId: {$attributeId})");
        }
        return $bestMatch;
    }

    /**
     * @param string $value
     * @return string
     */
    private function normalizeAttributeValue($value): string
    {
        if (!empty($value)) {
            $value = trim($value);
            $search = ['ı', 'ğ', 'ü', 'ş', 'ö', 'ç', 'İ', 'Ğ', 'Ü', 'Ş', 'Ö', 'Ç'];
            $replace = ['i', 'g', 'u', 's', 'o', 'c', 'i', 'g', 'u', 's', 'o', 'c'];
            $value = str_replace($search, $replace, $value);
            $value = mb_strtolower($value, 'UTF-8');
            $value = preg_replace('/\s+/', '', $value);
        }
        return $value;
    }

}
