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
        if (empty($value)) {
            return null;
        }
        $normalized = strtolower(trim($value));
        $normalized = str_replace(',', '.', $normalized);
        if (preg_match('/([a-z]+)-(\d+)cm/i', $normalized, $matches)) {
            return [
                'width' => (int) round((float) $matches[2]),
                'height' => 0,
                'depth' => 0,
                'size_type' => '1D',
            ];
        }
        $normalized = preg_replace('/[^0-9.x]/', '', $normalized);
        $parts = explode('x', $normalized);
        if (count($parts) >= 3 && is_numeric($parts[0]) && is_numeric($parts[1]) && is_numeric($parts[2])) {
            return [
                'width' => (int) round((float) $parts[0]),
                'height' => (int) round((float) $parts[1]),
                'depth' => (int) round((float) $parts[2]),
                'size_type' => '3D',
            ];
        } elseif (count($parts) >= 2 && is_numeric($parts[0]) && is_numeric($parts[1])) {
            return [
                'width' => (int) round((float) $parts[0]),
                'height' => (int) round((float) $parts[1]),
                'depth' => 0,
                'size_type' => '2D',
            ];
        } elseif (count($parts) === 1 && is_numeric($parts[0])) {
            return [
                'width' => (int) round((float) $parts[0]),
                'height' => 0,
                'depth' => 0,
                'size_type' => '1D',
            ];
        }

        return null;
    }

    /**
     * Find best attribute match based on dimensions comparison
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
        if (empty($allValues) || empty($searchValue)) {
            return null;
        }
        foreach ($allValues as $value) {
            if (strtolower(trim($searchValue)) === strtolower(trim($value['name']))) {
                return $value;
            }
        }
        if ($isSize) {
            $searchValueNormalized = $this->normalizeAttributeValue($searchValue);
            $searchDims = $this->parseDimensions($searchValueNormalized);
            if (!$searchDims) {
                return null;
            }
            $bestMatch = null;
            $bestScore = -1;
            $maxDiffAllowed = 25;
            $primaryDimension = $searchDims['width'];
            foreach ($allValues as $value) {
                $dbValueNormalized = $this->normalizeAttributeValue($value['name']);
                $dbDims = $this->parseDimensions($dbValueNormalized);
                if (!$dbDims) {
                    continue;
                }
                $dimensionRatio = $dbDims['height'] > 0 ? $dbDims['width'] / $dbDims['height'] : 0;
                if ($dbDims['height'] > 0 && ($dimensionRatio > 10 || $dimensionRatio < 0.1)) {
                    continue;
                }
                if ($searchDims['size_type'] === '1D' && $dbDims['size_type'] === '1D') {
                    $diff = abs($searchDims['width'] - $dbDims['width']);
                    $score = $diff <= $maxDiffAllowed ? 100 - $diff : 0;
                } elseif ($searchDims['size_type'] === '1D') {
                    $diff = abs($searchDims['width'] - $dbDims['width']);
                    $score = $diff <= $maxDiffAllowed ? 90 - $diff : 0;
                } elseif ($dbDims['size_type'] === '1D') {
                    $diff = abs($primaryDimension - $dbDims['width']);
                    $score = $diff <= $maxDiffAllowed ? 85 - $diff : 0;
                } else {
                    $widthDiff = abs($searchDims['width'] - $dbDims['width']);
                    $heightDiff = abs($searchDims['height'] - $dbDims['height']);
                    $totalDiff = $widthDiff + $heightDiff;
                    if ($widthDiff <= $maxDiffAllowed && $heightDiff <= $maxDiffAllowed) {
                        $score = 95 - $totalDiff;
                    } else {
                        continue;
                    }
                }
                if ($score > $bestScore) {
                    $bestScore = $score;
                    $bestMatch = $value;
                }
            }
            return $bestMatch;
        }
        return null;
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
