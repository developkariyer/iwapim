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
        $cfwTrVariantProductsIds = Utility::fetchFromSql($cfwTrSql, ['marketplace_id' => 84124]);
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
            $result = $this->findBestAttributeMatch(2000361, $mainProduct->getVariationSize(), true);
            echo $mainProduct->getIwasku() . " - " . $mainProduct->getVariationSize() . " -----> ";
            print_r($result);
            echo "\n";

        }

        return Command::SUCCESS;
    }

    private function parseDimensions($value): ?array
    {
        $normalized = strtolower(trim($value));
        $normalized = str_replace(',', '.', $normalized);
        $normalized = preg_replace('/[^0-9.x]/', '', $normalized);
        $parts = explode('x', $normalized);
        $dims = [];
        foreach ($parts as $part) {
            if (is_numeric($part)) {
                $dims[] = (int) round((float) $part);
            }
        }
        return !empty($dims) ? $dims : null;
    }

    /**
     * @param int $attributeId
     * @param string $searchValue
     * @param bool $isSize
     * @return array|null
     */
    private function findBestAttributeMatch($attributeId, $searchValue, $isSize): ?array
    {
        $sql = "SELECT attribute_value_id, name FROM iwa_ciceksepeti_category_attributes_values 
    WHERE attribute_id = :attribute_id";
        $allValues = Utility::fetchFromSql($sql, ['attribute_id' => $attributeId]);
        if (empty($allValues)) {
            return null;
        }
        $bestMatch = null;
        $smallestDiff = PHP_INT_MAX;

        $searchValueNormalized = $this->normalizeAttributeValue($searchValue);
        $searchDims = $isSize ? $this->parseDimensions($searchValueNormalized) : null;

        foreach ($allValues as $value) {
            if ($searchValue === $value['name']) {
                return $value;
            }
            if ($isSize && $searchDims) {
                $dbValueNormalized = $this->normalizeAttributeValue($value['name']);
                $dbDims = $this->parseDimensions($dbValueNormalized);

                if ($dbDims && count($dbDims) === count($searchDims)) {
                    $diffs = [];
                    $allWithinThreshold = true;
                    foreach ($searchDims as $i => $dim) {
                        $diff = abs($dim - $dbDims[$i]);
                        $diffs[] = $diff;
                        if ($diff > 25) { // threshold
                            $allWithinThreshold = false;
                            break;
                        }
                    }
                    if ($allWithinThreshold) {
                        $totalDiff = array_sum($diffs);
                        if ($totalDiff < $smallestDiff) {
                            $smallestDiff = $totalDiff;
                            $bestMatch = $value;
                        }
                    }
                }
            }
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
