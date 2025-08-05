<?php

namespace App\Command;

use App\Model\DataObject\Product;
use Pimcore\Console\AbstractCommand;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Input\InputOption;
use Pimcore\Model\DataObject\Product\Listing as ProductListing;
use Pimcore\Model\DataObject\GroupProduct\Listing as GroupProductListing;
use App\Utils\Utility;

#[AsCommand(
    name: 'app:export',
    description: 'Export json'
)]

class ExportCommand extends AbstractCommand
{
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $this->exportGroupListing();
        //$this->processExportProduct();
//        $export = $this->checkData();
//        foreach ($export as &$product) {
//            if (!$product['isDirty']) {
//                $product['sizeTable'] = $this->parseSizeListForTableFormat($product['variationSizeList']);
//            }
//        }
//        echo json_encode($export, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
        return Command::SUCCESS;
    }

    public function exportGroupListing()
    {
        $groupProductListingObject = new GroupProductListing();
        $groupProductListingObject->setLimit(100);
        $groupProducts = $groupProductListingObject->load();
        $result = [];
        foreach ($groupProducts as $groupProduct) {
            $products = $groupProduct->getProducts();
            foreach ($products as $product) {
                $result[$groupProduct->getKey()][] = $product->getIwasku();
            }
        }
        $filePath = PIMCORE_PROJECT_ROOT . '/tmp/groupProduct.json';
        file_put_contents($filePath, json_encode($result, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT));
        echo "Exported to: " . $filePath . "\n";
    }

    public function exportAllProductsToJson()
    {
        $limit = 50;
        $offset = 0;
        $allProducts = [];
        while (true) {
            $export = $this->prepareProductData($limit, $offset);
            if (empty($export)) {
                break;
            }
            foreach ($export as &$product) {
                $result = $this->parseSizeListForTableFormat($product['variationSizeList'], $product['id']);
                $product['sizeTable'] = $result['sizes'] ?? [] ;
                $product['customFieldTable'] = $result['custom'] ?? [];
                $allProducts[] = $product;
            }
            echo "offset = $offset\n";
            $offset += $limit;
        }
        $filePath = PIMCORE_PROJECT_ROOT . '/tmp/exportProduct.json';
        file_put_contents($filePath, json_encode($allProducts, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT));
        echo "Exported to: " . $filePath . "\n";

//        $limit = 50;
//        $offset = 0;
//        $allProducts = [];
//        while (true) {
//            $export = $this->checkData($limit, $offset);
//            if (empty($export)) {
//                break;
//            }
//            foreach ($export as &$product) {
//                if (!$product['isDirty']) {
//                    $product['sizeTable'] = $this->parseSizeListForTableFormat($product['variationSizeList']);
//                }
//            }
//            foreach ($export as $product) {
//                $allProducts[] = $product;
//            }
//            echo "offset = $offset\n";
//            $offset += $limit;
//        }
//        $filePath = PIMCORE_PROJECT_ROOT . '/tmp/exportProduct.json';
//        file_put_contents($filePath, json_encode($allProducts, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT));
//        echo "Exported to: " . $filePath . "\n";
    }

    private function processExportProduct()
    {
        $limit = 50;
        $offset = 0;
        $allProducts = [];
        $txtOutput = '';
        while (true) {
            $export = $this->prepareProductData($limit, $offset);
            if (empty($export)) {
                break;
            }
            foreach ($export as &$product) {
                $result = $this->parseSizeListForTableFormat($product['variationSizeList'], $product['id']);
                $product['sizeTable'] = $result['sizes'] ?? [] ;
                $product['customFieldTable'] = $result['custom'] ?? [];
                //print_r($result['mappings']);
                $this->setVariantCustomData($product);

                $this->updateVariantSize($product, $result['mappings']);
                $allProducts[] = $product;

                $txtOutput .= "Ürün ID: {$product['id']}\n";
                $txtOutput .= "Orijinal SizeList:\n{$product['variationSizeList']}\n";
                $txtOutput .= "Eşleşen Boyutlar (Mapping):\n";
                foreach ($result['mappings'] as $original => $label) {
                    $txtOutput .= "$original => $label\n";
                }
                if (!empty($result['sizes'])) {
                    $txtOutput .= "Size Table:\n";
                    foreach ($result['sizes'] as $sizeRow) {
                        $txtOutput .= "- " . json_encode($sizeRow, JSON_UNESCAPED_UNICODE) . "\n";
                    }
                }
                if (!empty($result['custom'])) {
                    $txtOutput .= "Custom Alanına Düşenler:\n";
                    foreach ($result['custom'] as $customValue) {
                        $txtOutput .= "- $customValue\n";
                    }
                }
                $txtOutput .= str_repeat('-', 40) . "\n\n";
            }
            echo "offset = $offset\n";
            $offset += $limit;
        }
        $filePath = PIMCORE_PROJECT_ROOT . '/tmp/exportProduct.json';
        file_put_contents($filePath, json_encode($allProducts, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT));
        echo "Exported to: " . $filePath . "\n";

        $txtFilePath = PIMCORE_PROJECT_ROOT . '/tmp/size_mappings.txt';
        file_put_contents($txtFilePath, $txtOutput);
        echo "Size mapping exported to: " . $txtFilePath . "\n";
    }

    private function updateVariantSize(&$product, $sizeMap)
    {
        foreach ($product['variants'] as &$variant) {
            $variationSize = $variant['variationSize'];
            if (empty($variationSize)) {
                continue;
            }
            $size = $sizeMap[$variationSize] ?? null;
            $variant['variationSize'] = $size;
        }
    }

    private function setVariantCustomData(&$product)
    {
        $customFieldTableData = $product['customFieldTable'] ?? [];
        if (empty($customFieldTableData)) {
            return;
        }
        foreach ($product['variants'] as &$variant) {
            $variationSize = $variant['variationSize'];
            if (in_array($variationSize, $customFieldTableData)) {
                $variant['customField'] = $variationSize;
                $variant['variationSize'] = '';
            }
        }
    }

    private function prepareProductData($limit, $offset)
    {
        $export = [];
        $mainProducts = $this->getMainProducts($limit, $offset);
        if (empty($mainProducts)) {
            return $export;
        }
        foreach ($mainProducts as $product) {
            $productData = [
                'id' => $product->getId(),
                'name' => $product->getName() ?? '',
                'category' => $product->getProductCategory() ?? '',
                'identifier' => $product->getProductIdentifier() ?? '',
                'variationSizeList' => $product->getVariationSizeList() ?? '',
                'variationColorList' => $product->getVariationColorList() ?? '',
                'description' => $product->getDescription() ?? '',
                'productCode' => $product->getProductCode() ?? '',
                'image' => $product->getImage()?->getFullPath() ?? '',
                'published' => $product->isPublished(),
            ];
            $productData['variants'] = [];
            foreach ($this->getVariantsProduct($product->getProductIdentifier()) as $variant) {
                $productData['variants'][] = [
                    'key' => $variant->getKey() ?? '',
                    'name' => $variant->getName() ?? '',
                    'iwasku' => $variant->getIwasku() ?? '',
                    'productCode' => $variant->getProductCode() ?? '',
                    'ean' => $variant->getEanGtin() ?? '',
                    'variationSize' => $variant->getVariationSize() ?? '',
                    'variationColor' => $variant->getVariationColor() ?? '',
                    'asinMap' => $this->getAsin($variant->getIwasku()),
                    'productWidth' => $variant->getInheritedField('productDimension1') ,
                    'productHeight' => $variant->getInheritedField('productDimension2'),
                    'productLength' => $variant->getInheritedField('productDimension3'),
                    'productWeight' => $variant->getInheritedField('productWeight'),
                    'packageWidth' => $variant->getInheritedField('packageDimension1'),
                    'packageHeight' => $variant->getInheritedField('packageDimension2'),
                    'packageLength' => $variant->getInheritedField('packageDimension3'),
                    'packageWeight' => $variant->getInheritedField('packageWeight'),
                    'setProductIwaskus' => $this->getSetProductIwaskus($variant),
                    'sticker4x6eu' => $variant->getSticker4x6eu()?->getFullPath() ?? '',
                    'sticker4x6iwasku' => $variant->getSticker4x6iwasku()?->getFullPath() ?? '',
                    'marketplaceList' => $this->getListingsMarketplace($variant),
                    'brandList' => $this->getBrandList($variant),
                    'published' => $product->isPublished(),
                ];
            }
            $export[] = $productData;
        }
        return $export;
    }

    private function getMainProducts($limit, $offset)
    {
        $mainProductListing = new ProductListing();
        $mainProductListing->setLimit($limit);
        $mainProductListing->setOffset($offset);
        $mainProductListing->setCondition("productLevel = 0");
        $mainProducts = $mainProductListing->load();
        return $mainProducts;
    }

    private function getVariantsProduct($productIdentifier)
    {
        $variantProductListing = new ProductListing();
        $variantProductListing->setCondition("productLevel = 1 AND productIdentifier = '$productIdentifier'");
        return $variantProductListing->load();
    }

    private function getAsin($iwasku)
    {
        $sql = "SELECT asin, fnsku FROM iwa_inventory where iwasku = '$iwasku' and iwasku is not null";
        $result = Utility::fetchFromSql($sql);
        $asinMap = [];
        foreach ($result as $row) {
            $asin = $row['asin'];
            $fnsku = $row['fnsku'];
            if (!isset($asinMap[$asin])) {
                $asinMap[$asin] = [];
            }
            if (!in_array($fnsku, $asinMap[$asin])) {
                $asinMap[$asin][] = $fnsku;
            }
        }
        return $asinMap;
    }

    private function getSetProductIwaskus($variant)
    {
        $iwaskus = [];
        $setProducts = $variant->getBundleProducts();
        foreach ($setProducts as $setProduct) {
            $objectId = $setProduct->getObjectId();
            $setObject = Product::getById($objectId);
            $iwaskus[$setObject->getIwasku()] = $setProduct->getData()['amount'];
        }
        return $iwaskus;
    }

    private function getListingsMarketplace($variant) {
        $listingMarketplace = [];
        $listings = $variant->getListingItems();
        foreach ($listings as $listing) {
            $listingMarketplace[] = $listing->getMarketplace()->getKey();
        }
        return $listingMarketplace;
    }

    private function getBrandList($variant) {
        $brandList = [];
        $brands = $variant->getbrandItems();
        foreach ($brands as $brand) {
            $brandList[] = $brand->getKey();
        }
        return $brandList;
    }

    private function checkData($limit, $offset)
    {
        $data = $this->prepareProductData($limit, $offset);
        if (empty($data)) {
            return [];
        }
        foreach ($data as &$product) {
            $sizeList = $product['variationSizeList'];
            $dirtySizes = [];
            $parts = preg_split('/[\r\n,]+/', trim($sizeList));
            foreach ($parts as $size) {
                $size = trim($size);
                if ($size === '') continue;
                if (
                    preg_match('/^\d+(\.\d+)?x\d+(\.\d+)?(cm|m)?$/i', $size) ||
                    preg_match('/^\d+(\.\d+)?(cm|m)?$/i', $size)
                ) {
                } else {
                    $dirtySizes[] = $size;
                }
            }
            $product['isDirty'] = !empty($dirtySizes);
//            if (!empty($dirtySizes)) {
//                echo "Dirty Sizes: " . implode(', ', $dirtySizes) . "\n";
//            }
        }
        return $data;
    }

//    private function parseSizeListForTableFormat($variationSizeList)
//    {
//        $results = [];
//        $parts = preg_split('/[\r\n,]+/', trim($variationSizeList));
//        $defaultLabels = ['S', 'M', 'L', 'XL', '2XL', '3XL', '4XL', '5XL'];
//        $labelIndex = 0;
//        foreach ($parts as $size) {
//            $size = trim($size);
//            if ($size === '') continue;
//            if (
//                preg_match('/^(\d+(\.\d+)?)x(\d+(\.\d+)?)(cm|m)?$/i', $size, $sz)
//            ) {
//                $width = (float)$sz[1];
//                $height = (float)$sz[3];
//                $label = $defaultLabels[$labelIndex] ?? ('+' . end($defaultLabels));
//                $labelIndex++;
//                $results[] = [$width, $height, $label];
//            } elseif (
//                preg_match('/^(\d+(\.\d+)?)(cm|m)?$/i', $size, $sz2)
//            ) {
//                $width = (float)$sz2[1];
//                $height = (float)$sz2[1];
//                $label = $defaultLabels[$labelIndex] ?? ('+' . end($defaultLabels));
//                $labelIndex++;
//                $results[] = [$width, $height, $label];
//            }
//        }
//        return $results;
//    }


    private function parseSizeListForTableFormat($variationSizeList, $productId)
    {
        if (empty($variationSizeList)) {
            return [];
        }
        $results = [];
        $customItems = [];
        $originalToLabelMap = [];
        $defaultLabels = ['S', 'M', 'L', 'XL', '2XL', '3XL', '4XL', '5XL', '6XL', '7XL', '8XL', '9XL', '10XL', '11XL', '12XL', '13XL', '14XL', '15XL', '16XL',
            '17XL', '18XL', '19XL', '20XL', '21XL', '22XL', '23XL', '24XL', '25XL'];
        $stringLabels = [
            'Small' => 'S',
            'Medium' => 'M',
            'Large' => 'L',
            'XLarge' => 'XL',
            'Xlarge' => 'XL',
            '2xLarge' => '2XL',
            '2XLarge' => '2XL',
            'XXL' => '2XL'
        ];
        $defaultLabelIndex = 0;
        $parts = preg_split('/[\r\n]+/', trim($variationSizeList));
        $parsedNumericValues = [];
        $parsedDimensionValues = [];
        $parsedSimpleLabels = [];
        $parsedStandardLabels = [];
        foreach ($parts as $part) {
            $part = trim($part);
            if ($part === '') continue;
            if (isset($stringLabels[$part])) {
                $label = $stringLabels[$part];
                $results[] = [$label];
                $originalToLabelMap[$part] = $label;
                continue;
            }
            if (preg_match('/^(\d+(?:[.,]\d+)?)[xX](\d+(?:[.,]\d+)?)[xX](\d+(?:[.,]\d+)?)/', $part, $matches)) {
                $a = (float) str_replace(',', '.', $matches[1]);
                $b = (float) str_replace(',', '.', $matches[2]);
                $c = (float) str_replace(',', '.', $matches[3]);
                $key = [$a, $b, $c];
                $parsedDimensionValues[] = [$key, $part];
                continue;
            }
            if (preg_match('/(\d+(?:[.,]\d+)?)[xX](\d+(?:[.,]\d+)?)/', $part, $matches)) {
                $a = (float) str_replace(',', '.', $matches[1]);
                $b = (float) str_replace(',', '.', $matches[2]);
                $key = [$a, $b];
                $parsedDimensionValues[] = [$key, $part];
                continue;
            }
            if (preg_match('/(\d+(?:[.,]\d+)?)\s*(mm|cm|m)/i', $part, $matches)) {
                $value = (float) str_replace(',', '.', $matches[1]);
                $parsedNumericValues[] = [$value, $part];
                continue;
            }
            if (in_array(strtoupper($part), $defaultLabels)) {
                $label = strtoupper($part);
                $parsedSimpleLabels[] = [$label];
                $originalToLabelMap[$part] = $label;
                continue;
            }
            if (preg_match('/tek\s?ebat|standart/i', $part)) {
                $label = $defaultLabels[$defaultLabelIndex++] ?? end($defaultLabels);
                $results[] = [$label];
                $originalToLabelMap[$part] = $label;
                continue;
            }
            $customItems[] = $part;
        }
        usort($parsedDimensionValues, function($a, $b) {
            return $a[0] <=> $b[0];
        });
        foreach ($parsedDimensionValues as $item) {
            $dims = $item[0];
            $originalPart = $item[1];
            $label = $defaultLabels[$defaultLabelIndex++] ?? end($defaultLabels);
            $results[] = array_merge($dims, [$label]);
            $originalToLabelMap[$originalPart] = $label;
        }
        sort($parsedNumericValues);
        foreach ($parsedNumericValues as $item) {
            $val = $item[0];
            $originalPart = $item[1];
            $label = $defaultLabels[$defaultLabelIndex++] ?? end($defaultLabels);
            $results[] = [$val, $label];
            $originalToLabelMap[$originalPart] = $label;
        }

        foreach ($parsedSimpleLabels as $labelArray) {
            $results[] = $labelArray;
        }
        foreach ($parsedStandardLabels as $labelArray) {
            $results[] = $labelArray;
        }

        if (!empty($customItems)) {
            if ($customItems[0] !== 'Custom') {
                array_unshift($customItems, 'Custom');
            }
        }
        return [
            'sizes' => $results,
            'custom' => $customItems,
            'mappings' => $originalToLabelMap,
        ];
    }
}