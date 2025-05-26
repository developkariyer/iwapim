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
            $this->dimTest($mainProduct->getVariationSize());
//            $result = $this->findBestAttributeMatch(2000361, $mainProduct->getVariationSize(), true);
//            echo $mainProduct->getIwasku() . " - " . $mainProduct->getVariationSize() . " -----> ";
//            print_r($result);
//            echo "\n";

        }

        return Command::SUCCESS;
    }

    private function dimTest($value)
    {
        $value = trim($value);
        echo $value . "\n";
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
        $sql = "SELECT attribute_value_id, name FROM iwa_ciceksepeti_category_attributes_values 
            WHERE attribute_id = :attribute_id";
        $allValues = Utility::fetchFromSql($sql, ['attribute_id' => $attributeId]);
        if (empty($allValues)) {
            return null;
        }

        $searchValueNormalized = strtolower(trim($searchValue));
        $searchValueNormalized = str_replace(',', '.', $searchValueNormalized);

        // 1. Doğrudan eşleşme
        foreach ($allValues as $value) {
            if (strtolower(trim($value['name'])) === $searchValueNormalized) {
                return $value;
            }
        }

        // 2. Beden etiketi varsa
        $sizeLabels = ['xs', 's', 'm', 'l', 'xl', 'xxl', 'xxxl', '2xl', '3xl'];
        foreach ($sizeLabels as $label) {
            if (stripos($searchValueNormalized, $label) !== false) {
                foreach ($allValues as $value) {
                    if (stripos($value['name'], $label) !== false) {
                        return $value;
                    }
                }
            }
        }

        // 3. Boyut varsa - 80x90, 100cm gibi
        $dims = $this->parseDimensions($searchValueNormalized);
        if ($isSize && $dims) {
            $width = $dims['width'];
            $height = $dims['height'];

            // Yalnızca width varsa
            if ($height === 0) {
                for ($w = $width; $w >= max(0, $width - 25); $w--) {
                    foreach ($allValues as $value) {
                        if (strpos($value['name'], (string)$w) !== false) {
                            return $value;
                        }
                    }
                }
            } else {
                for ($w = $width; $w >= max(0, $width - 25); $w--) {
                    for ($h = $height; $h >= max(0, $height - 25); $h--) {
                        $searchStr1 = "{$w}x{$h}";
                        $searchStr2 = "{$w} x {$h}";
                        foreach ($allValues as $value) {
                            $name = strtolower($value['name']);
                            if (strpos($name, $searchStr1) !== false || strpos($name, $searchStr2) !== false) {
                                return $value;
                            }
                        }
                    }
                }
            }
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
