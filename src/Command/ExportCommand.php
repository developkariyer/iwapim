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
use App\Utils\Utility;

#[AsCommand(
    name: 'app:export',
    description: 'Export json'
)]

class ExportCommand extends AbstractCommand
{
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $this->exportAllProductsToJson();
//        $export = $this->checkData();
//        foreach ($export as &$product) {
//            if (!$product['isDirty']) {
//                $product['sizeTable'] = $this->parseSizeListForTableFormat($product['variationSizeList']);
//            }
//        }
//        echo json_encode($export, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
        return Command::SUCCESS;
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
                $product['sizeTable'] = $this->parseSizeListForTableFormat($product);
                $allProducts[] = $product;
            }
            echo "offset = $offset\n";
            $offset += $limit;
        }
//        $filePath = PIMCORE_PROJECT_ROOT . '/tmp/exportProduct.json';
//        file_put_contents($filePath, json_encode($allProducts, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT));
//        echo "Exported to: " . $filePath . "\n";

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


    private function parseSizeListForTableFormat(&$product)
    {
        $variationSizeList = $product['variationSizeList'] ?? '';
        if (empty($variationSizeList)) {
            return [];
        }
        $results = [];
        $customItems = [];
        $selectableOptions = [];
        $defaultLabels = ['S', 'M', 'L', 'XL', '2XL', '3XL', '4XL', '5XL', 'XS', 'XXS'];
        $labelMap = [
            'SMALL'   => 'S', 'MEDIUM' => 'M', 'LARGE' => 'L',
            'XLARGE'  => 'XL', 'XSMALL' => 'XS', 'XXSMALL' => 'XXS'
        ];
        $labelIndex = 0;
        $parts = preg_split('/[\r\n,;]+/', trim($variationSizeList));
        foreach ($parts as $raw) {
            $item = trim($raw);
            if ($item === '') continue;
            $rangeParts = preg_split('/\s*-\s*/', $item, 2);
            if (count($rangeParts) === 2 && preg_match('/\d/', $rangeParts[0]) && preg_match('/\d/', $rangeParts[1])) {
                foreach ($rangeParts as $rangePart) {
                    preg_match_all('/(\d+(?:\.\d+)?)/', $rangePart, $matches);
                    if (!empty($matches[1])) {
                        $dms = array_map('floatval', $matches[1]);
                        $dms[] = $defaultLabels[$labelIndex++] ?? 'Beden-'.$labelIndex;
                        $results[] = $dms;
                    }
                }
                continue;
            }
            if (preg_match('/^([a-zA-Z0-9]+)[\s:-]+(.+)$/iu', $item, $m)) {
                $label = strtoupper(trim($m[1]));
                $dimensionPart = trim($m[2]);
                $normalizedLabel = $labelMap[$label] ?? $label;
                $selectableOptions[] = $normalizedLabel;
                preg_match_all('/(\d+(?:\.\d+)?)/', $dimensionPart, $matches);
                if (!empty($matches[1])) {
                    $dms = array_map('floatval', $matches[1]);
                    $dms[] = $normalizedLabel;
                    $results[] = $dms;
                } else {
                    $results[] = [$normalizedLabel];
                }
                continue;
            }
            if (preg_match('/^(.+?)\s*\((.+)\)$/iu', $item, $m)) {
                $dimensionPart = trim($m[1]);
                $label = strtoupper(trim($m[2]));
                $selectableOptions[] = $label;
                preg_match_all('/(\d+(?:\.\d+)?)/', $dimensionPart, $matches);
                if (!empty($matches[1])) {
                    $dms = array_map('floatval', $matches[1]);
                    $dms[] = $label;
                    $results[] = $dms;
                } else {
                    $results[] = [$label];
                }
                continue;
            }
            if (preg_match('/^\d+\s*(adet|\'li|\'lü)$/i', $item)) {
                $results[] = [$item];
                $selectableOptions[] = $item;
                continue;
            }
            preg_match_all('/(\d+(?:\.\d+)?)/', $item, $matches);
            if (!empty($matches[1])) {
                if (preg_match('/^[x\d\s\.,cm]*$/i', preg_replace('/\s+/', '', $item))) {
                    $dms = array_map('floatval', $matches[1]);
                    $dms[] = $defaultLabels[$labelIndex++] ?? 'Beden-'.$labelIndex;
                    $results[] = $dms;
                    continue;
                }
            }
            $upperItem = strtoupper($item);
            $normalLabel = strtoupper(preg_replace('/[^A-Za-z]/', '', $item));
            if (in_array($upperItem, $defaultLabels)) {
                $results[] = [$upperItem];
                $selectableOptions[] = $upperItem;
                continue;
            }
            if (isset($labelMap[$normalLabel])) {
                $label = $labelMap[$normalLabel];
                $results[] = [$label];
                $selectableOptions[] = $label;
                continue;
            }
            $customItems[] = $item;
        }
        if (!empty($customItems)) {
            $customValuesForTable = [];
            foreach (array_unique($customItems) as $custom) {
                $customValuesForTable[] = ['value' => $custom];
            }
            $product['customTable'] = array_merge([['value' => 'Custom']], $customValuesForTable);
        }
        if (!empty($product['variants']) && !empty($selectableOptions)) {
            $normalizedSelectableOptions = [];
            foreach(array_unique($selectableOptions) as $opt) {
                $cleaned = strtoupper(preg_replace('/[^A-Za-z0-9]/', '', $opt));
                $normalized = $labelMap[$cleaned] ?? $cleaned;
                if(!in_array($normalized, $normalizedSelectableOptions)) {
                    $normalizedSelectableOptions[] = $normalized;
                }
            }
            foreach ($product['variants'] as &$variant) {
                if (!empty($variant['variationSize'])) {
                    $variantLabel = $variant['variationSize'];
                    $cleanedVariantLabel = strtoupper(preg_replace('/[^A-Za-z0-9]/', '', $variantLabel));
                    $normalizedVariantLabel = $labelMap[$cleanedVariantLabel] ?? $cleanedVariantLabel;
                    if (in_array($normalizedVariantLabel, $normalizedSelectableOptions)) {
                        $variant['customField'] = $variant['variationSize'];
                        $variant['variationSize'] = '';
                    }
                }
            }
            unset($variant);
        }
        return $results;
    }



}