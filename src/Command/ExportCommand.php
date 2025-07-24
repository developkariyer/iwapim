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
        while (true) {
            $export = $this->prepareProductData($limit, $offset);
            if (empty($export)) {
                break;
            }
            foreach ($export as &$product) {
                $this->parseSizeListForTableFormat($product['variationSizeList']);
            }
            echo "offset = $offset\n";
            $offset += $limit;
        }

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

    private function parseSizeListForTableFormat($variationSizeList)
    {
        $results = [];
        $defaultLabels = ['S', 'M', 'L', 'XL', '2XL', '3XL', '4XL', '5XL', 'XS', 'XXS'];
        $labelIndex = 0;

        $labelMap = [
            'SMALL' => 'S', 'MEDIUM' => 'M', 'LARGE' => 'L', 'XLARGE' => 'XL',
            'XSMALL' => 'XS', 'XXSMALL' => 'XXS'
        ];

        // Girdiyi parçalara ayır
        $parts = preg_split('/[\r\n,;]+/', trim($variationSizeList));
        $customItems = [];

        foreach ($parts as $raw) {
            $item = trim($raw);
            if ($item === '') continue;

            // ===================================================================
            // YENİ VE GELİŞMİŞ KURALLAR (EN SPESİFİKTEN BAŞLAYARAK)
            // ===================================================================

            // Kural 1: "ETİKET-ÖLÇÜ" formatı. Örn: "2XL-250cm"
            // Açıklama: Bir etiket, tire ve ardından gelen bir ölçüyü yakalar.
            if (preg_match('/^([a-zA-Z0-9]+)\s*-\s*([\d.xX]+(?:\s*(?:cm|mm|m))?)$/iu', $item, $m)) {
                $label = strtoupper($m[1]);
                $dimension = trim($m[2]);
                $results[] = [$dimension, null, $labelMap[$label] ?? $label];
                continue;
            }

            // Kural 2: "ÖLÇÜ(AÇIKLAMA)" formatı. Örn: "30cm(Boy)"
            // Açıklama: Ölçüyü ve parantez içindeki açıklamayı ayrı ayrı yakalar. Açıklamayı etiket yapar.
            if (preg_match('/^([\d.xX]+(?:\s*(?:cm|mm|m))?)\s*\((.+)\)$/iu', $item, $m)) {
                $dimension = trim($m[1]);
                $label = strtoupper(trim($m[2]));
                $results[] = [$dimension, null, $label];
                continue;
            }

            // Kural 3: "ÖLÇÜ-ÖLÇÜ" aralık formatı. Örn: "35x35cm-45x45cm"
            // Açıklama: İki ölçü arasındaki tireyi yakalar ve her birini ayrı bir satır olarak işler.
            if (preg_match('/^([\d.xX\s*cmm]+)\s*-\s*([\d.xX\s*cmm]+)$/iu', $item, $m)) {
                $rangeParts = [trim($m[1]), trim($m[2])];
                foreach ($rangeParts as $rangePart) {
                    // Her bir aralık parçasını 2D veya 1D olarak tekrar işlemeye çalışalım
                    if (preg_match('/^(\d+(?:\.\d+)?)\s*x\s*(\d+(?:\.\d+)?)\s*(cm|mm|m)?/iu', $rangePart, $sz)) {
                        $results[] = [($sz[1].($sz[3]??'')), ($sz[2].($sz[3]??'')), $defaultLabels[$labelIndex++] ?? 'Beden-'.$labelIndex];
                    } elseif (preg_match('/^x?(\d+(?:\.\d+)?)\s*(cm|mm|m)?/iu', $rangePart, $sz)) {
                        $results[] = [($sz[1].($sz[2]??'')), ($sz[1].($sz[2]??'')), $defaultLabels[$labelIndex++] ?? 'Beden-'.$labelIndex];
                    }
                }
                continue;
            }

            // Kural 4: 3 veya daha fazla boyutlu ölçüler. Örn: "10x12x9cm", "12.5x12x5x2"
            // Açıklama: 2'den fazla 'x' ile ayrılmış sayı grubunu yakalar. Veri kaybı olmaması için tamamını ilk sütuna yazar.
            if (preg_match('/^(\d+(?:\.\d+)?(?:\s*x\s*\d+(?:\.\d+)?){2,})\s*(cm|mm|m)?/iu', $item, $m)) {
                $results[] = [$m[1] . ($m[2] ?? ''), null, $defaultLabels[$labelIndex++] ?? 'Beden-'.$labelIndex];
                continue;
            }

            // ===================================================================
            // STANDART KURALLAR (Parantezli notları tolere edecek şekilde güncellendi)
            // ===================================================================

            // Kural 5: 2D Ölçü. Örn: "50x70", "10x10cm(herbiri)"
            // Açıklama: İsteğe bağlı parantezli notları görmezden gelir.
            if (preg_match('/^(\d+(?:\.\d+)?)\s*x\s*(\d+(?:\.\d+)?)\s*(cm|mm|m)?(?:\s*\(.*\))?$/iu', $item, $m)) {
                $unit = $m[3] ?? '';
                $results[] = [$m[1].$unit, $m[2].$unit, $defaultLabels[$labelIndex++] ?? 'Beden-'.$labelIndex];
                continue;
            }

            // Kural 6: 1D Ölçü. Örn: "50cm", "x30cm", "40(adet)"
            // Açıklama: İsteğe bağlı parantezli notları görmezden gelir.
            if (preg_match('/^x?(\d+(?:\.\d+)?)\s*(cm|mm|m)?(?:\s*\(.*\))?$/iu', $item, $m)) {
                $value = $m[1] . ($m[2] ?? '');
                $results[] = [$value, $value, $defaultLabels[$labelIndex++] ?? 'Beden-'.$labelIndex];
                continue;
            }

            // Kural 7: Doğrudan Etiketler. Örn: "L", "2XL", "S"
            // Açıklama: Standart etiket listesiyle doğrudan karşılaştırır.
            $upperItem = strtoupper($item);
            if (in_array($upperItem, $defaultLabels)) {
                $results[] = [null, null, $upperItem];
                continue;
            }

            // Kural 8: Yazılı Etiketler. Örn: "Medium", "XLarge"
            // Açıklama: Yazılı etiketleri standart forma dönüştürür.
            $normalLabel = strtoupper(preg_replace('/[^A-Za-z]/', '', $item));
            if (isset($labelMap[$normalLabel])) {
                $results[] = [null, null, $labelMap[$normalLabel]];
                continue;
            }

            // Kural 9: "En: 150cm" gibi etiketli ölçüler.
            if (preg_match('/^([a-zA-Z]+)[\:\- ]+([\d.xX]+(?:\s?(?:cm|mm|m))?)$/iu', $item, $m)) {
                $results[] = [trim($m[2]), null, strtoupper($m[1])];
                continue;
            }

            // Hiçbir kurala uymadıysa 'custom' olarak işaretle.
            $customItems[] = $item;
        }

        if (!empty($customItems)) {
            echo "----------------------------------------\n";
            echo "İşlenemeyen 'Custom' Değerler:\n";
            foreach ($customItems as $custom) {
                echo " - " . $custom . "\n";
            }
            echo "----------------------------------------\n";
        }

        return $results;
    }



}