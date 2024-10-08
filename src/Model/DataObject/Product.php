<?php

namespace App\Model\DataObject;

use App\Utils\PdfGenerator;
use Pimcore\Model\DataObject\Concrete;
use Pimcore\Model\DataObject\Product\Listing;
use App\Utils\Utility;
use Pimcore\Model\DataObject\Service;
use Pimcore\Model\DataObject\Data\Video;

class Product extends Concrete
{
    private static $recursiveCounter = 0;
    protected $assetPaths = [];

    public static $level0NullFields = [
        'variationSize',
        'variationColor',
        'iwasku',
        'listingItems',
        'brandItems',
        'bundleItems',
        'wisersellId',
        'wisersellJson',
    ];

    public static $level1NullFields = [
        'productIdentifier',
        'productCategory',
        'name',
        'nameEnglish',
        'variationSizeList',
        'variationColorList',
        'description',
        'technicals',
        'seoTitle',
        'seoDescription',
        'seoTitleEn',
        'seoDescriptionEn',
        'seoKeywords',
        'seoKeywordsEn',
        'designFiles',
        'rawFiles',
    ];

    protected static $nullables = [
        'productIdentifier',
        'name',
        'nameEnglish',
        'description',
        'variationSize',
        'variationColor',
        'productDimension1',
        'productDimension2',
        'productDimension3',
        'productWeight',
        'packageDimension1',
        'packageDimension2',
        'packageDimension3',
        'packageWeight',
        'boxDimension1',
        'boxDimension2',
        'boxDimension3',
        'boxWeight',
        'inBoxCount',
        'inPaletteCount',
        'inContainerCount',
    ];

    public function nullify()
    {
        foreach (self::$nullables as $field) {
            if (empty($this->$field) && !is_null($this->$field)) {
                $this->$field = null;
            }
        }    
    }

    public function level()
    {
        return ($this->getParent() instanceof Product) ? 1 : 0;
    }

    public function checkProductCode($numberDigits = 5)
    {
        Product::setGetInheritedValues(false);
        if (strlen($this->getProductCode()) == $numberDigits) {
            Product::setGetInheritedValues(true);
            return false;
        }
        $productCode = $this->generateUniqueCode($numberDigits);
        $this->setProductCode($productCode);
        Product::setGetInheritedValues(true);
        return true;
    }

    public function checkIwasku($forced = false)
    {
        if ($forced || ($this->level() == 1 && $this->isPublished() && strlen($this->getIwasku() ?? '') != 12)) {
            $pid = $this->getInheritedField("productIdentifier");
            $iwasku = str_pad(str_replace('-', '', $pid), 7, '0', STR_PAD_RIGHT);
            $productCode = $this->getProductCode();
            if (strlen($productCode) != 5) {
                $productCode = $this->generateUniqueCode(5);
                $this->setProductCode($productCode);
            }
            $iwasku .= $productCode;
            $this->setIwasku($iwasku);
            return true;
        }
        return false;
    }

    public function checkKey()
    {
        $key = $this->getInheritedField("ProductIdentifier");
        $key .= " ";
        $key .= $this->getInheritedField("Name");
        $variationSize = $this->getInheritedField("VariationSize");
        $variationColor = $this->getInheritedField("VariationColor");
        if (!empty($variationSize)) {
            $key .= " $variationSize";
        }
        if (!empty($variationColor)) {
            $key .= " $variationColor";
        }
        if (!empty($key)) {
            $this->setKey($key);
        } else {
            $this->setKey("gecici_{$this->generateUniqueCode(10)}");
        }
    }

    public function checkProductIdentifier()
    {
        if (empty($this->getProductIdentifier())) {
            return;
        }
        $productIdentifier = $this->getProductIdentifier();
        if (preg_match('/^([A-Z]{2,3}-)(\d+)([A-Z]?)$/', $productIdentifier, $matches)) {
            $paddedNumber = str_pad($matches[2], 3, '0', STR_PAD_LEFT);
            $this->setProductIdentifier($matches[1] . $paddedNumber . $matches[3]);
        }
    }

    public function listVariations()
    {
        $sizes = [];
        $colors = [];
        $children = $this->getChildren([Product::OBJECT_TYPE_OBJECT], true);
        foreach ($children as $child) {
            if ($child instanceof Product) {
                $sizes[] = $child->getVariationSize();
                $colors[] = $child->getVariationColor();
            }
        }
        $sizes = array_unique(array_filter($sizes));
        $colors = array_unique(array_filter($colors));
        sort($sizes);
        sort($colors);
        return [$sizes, $colors];
    }

    protected function updateAsset($asset, $folder)
    {
        if (!$asset) {
            return;
        }
        if ($asset instanceof Video) {
            $asset = $asset->getData();
        }
        if ($asset->getParent() && $asset->getParent()->getFullPath() === $folder->getFullPath()) {
            return;
        }
        $originalFilename = pathinfo($asset->getFilename(), PATHINFO_FILENAME);
        $extension = pathinfo($asset->getFilename(), PATHINFO_EXTENSION);
        $productIdentifier = $this->getInheritedField('productIdentifier');
        $timestamp = date("YmdHis");
        $newFilename = "{$productIdentifier}_{$timestamp}_{$originalFilename}.{$extension}";
        if (strpos($originalFilename, "{$productIdentifier}_") !== 0) {
            $asset->setFilename($newFilename);
        }
        $asset->setParent($folder);
        $asset->save();
    }

    protected function generateAssetPath($mainFolderString)
    {
        $productIdentifier = str_replace(' ', '', $this->getInheritedField("productIdentifier")); // e.g. AMS-123A
        $identifierParts = explode('-', $productIdentifier);
        if (count($identifierParts) != 2) {
            error_log("Invalid product identifier: $productIdentifier");
            return;
        }
        $mainFolder = Utility::checkSetAssetPath($mainFolderString);
        $level1Folder = Utility::checkSetAssetPath(
            $identifierParts[0],
            $mainFolder
        );
        return Utility::checkSetAssetPath(
            $productIdentifier,
            $level1Folder
        );
    }

    protected function cachedAssetPath($path)
    {
        if (!isset($this->cachedAssetPaths[$path])) {
            $this->cachedAssetPaths[$path] = $this->generateAssetPath($path);
        }
        return $this->cachedAssetPaths[$path];
    }

    public function checkAssetFolders()
    {
        if (!$this->isPublished() || $this->level()>0) {
            return;
        }

        $fieldsToFix = [
            'Image' => 'Album',
        ];

        $relationsToFix = [
            'Technicals' => 'Dokuman',
            'DesignFiles' => 'Tasarim',
        ];

        $collectionsToFix = [
            'RawFiles' => 'Ham',
            'ProductAlbum' => 'Album'
        ];

        foreach ($fieldsToFix as $field=>$folder) {
            $fieldName = "get$field";
            $asset = $this->$fieldName();
            $this->updateAsset($asset, $this->cachedAssetPath($folder));
        }

        foreach ($relationsToFix as $field=>$folder) {
            $fieldName = "get$field";
            $fieldObject = $this->$fieldName();
            if (!$fieldObject) {
                continue;
            }
            foreach ($fieldObject as $asset) {
                $this->updateAsset($asset, $this->cachedAssetPath($folder));
            }
        }

        foreach ($collectionsToFix as $collection => $folder) {
            $collectionName = "get$collection";
            $collectionObject = $this->$collectionName() ?? [];
            foreach ($collectionObject as $element) {
                $fieldName = $element->getCollectionName();
                $fieldObject = $element->getCollectionAssets() ?? [];
                $assetFolder = $this->cachedAssetPath($folder);
                foreach ($fieldObject as $asset) {
                    if ($asset instanceof \Pimcore\Model\Asset) {
                        $this->updateAsset($asset, Utility::checkSetAssetPath($fieldName, $assetFolder));
                    }
                }
            }
        }
    }

    public function checkVariations()
    {
        if ($this->level() > 0) {
            return;
        }
        if (!$this->getFixVariations()) {
            return;
        }
        if (!$this->isPublished()) {
            return;
        }
        $sizes = explode("\n", str_replace(['"',' '], '', $this->getVariationSizeList()));
        $variationColors = explode("\n", str_replace('"', '', $this->getVariationColorList()));
        $colors = [];
        foreach ($variationColors as $variationColor) {
            $trimColor = trim($variationColor);
            if ($trimColor === 'Mat') {
                $colors = array_merge($colors, ['Black', 'Copper', 'Gold', 'Silver']);
            } elseif ($trimColor === 'Shiny') {
                $colors = array_merge($colors, ['Shiny Copper', 'Shiny Gold', 'Shiny Silver']);
            } elseif ($trimColor === 'Karma') {
                $colors = array_merge($colors, ['ISOB', 'ISOG', 'IBOS', 'IBOG', 'IGOS', 'IGOB', 'Black', 'Gold', 'Silver']);
            } elseif ($trimColor === 'GSKarma') {
                $colors = array_merge($colors, ['IGOS', 'ISOG', 'Gold', 'Silver']);
            } elseif (!empty($trimColor)) {
                $colors[] = $trimColor;
            }
        }

        $sizes = array_values(array_unique(array_filter(array_map('trim', $sizes))));
        $colors = array_values(array_unique(array_filter(array_map('trim', $colors))));

        if (empty($sizes)) {
            $sizes = ['Tek Ebat'];
        }
        if (empty($colors)) {
            $colors = ['Tek Renk'];
        }

        foreach ($sizes as $size) {
            foreach ($colors as $color) {
                $matrix[$size][$color] = false;
            }
        }

        foreach ($this->getChildren() as $child) {
            if (!$child instanceof Product) {continue;}
            if (!in_array($child->getVariationSize(), $sizes) || !in_array($child->getVariationColor(), $colors)) {
                if (!$child->getListingItems()) {
                    $child->delete();
                } else {
                    $child->setPublished(false);
                    $child->save();
                }
                continue;
            }
            $matrix[$child->getVariationSize()][$child->getVariationColor()] = true;
        }

        foreach ($sizes as $size) {
            foreach ($colors as $color) {
                if ($matrix[$size][$color] || empty($size) || empty($color)) {
                    continue;
                }
                $newSize = new \Pimcore\Model\DataObject\Product();
                $newSize->setParent($this);
                $newSize->setVariationSize($size);
                $newSize->setVariationColor($color);
                $newSize->setPublished(true);
                $newSize->checkProductCode();
                $newSize->checkKey();
                $newSize->save();
            }
        }
        $this->setFixVariations(false);
        $this->save();
    }

    public function addVariant($variant)
    {
        $listingItems = $this->getListingItems();
        $listingIds = array_map(function ($item) {
            return $item->getId();
        }, $listingItems);
        if (is_array($variant)) {
            foreach ($variant as $v) {
                if (!in_array($v->getId(), $listingIds)) {
                    $listingItems[] = $v;
                }
            }
            $this->setListingItems($listingItems);
            return $this->save();
        } else {
            if (!in_array($variant->getId(), $listingIds)) {
                $listingItems[] = $variant;
                $this->setListingItems($listingItems);
                return $this->save();
            }
        }
    }

    public function generateUniqueCode($numberDigits=5)
    {
        while (true) {
            $candidateCode = self::generateCustomString($numberDigits);
            if (!$this->findByField('productCode', $candidateCode)) {
                return $candidateCode;
            }
        }
    }

    public static function findByField($field, $value)
    {
        $list = new Listing();
        $list->setCondition("`$field` = ?", [$value]);
        $list->setUnpublished(true);
        $list->setLimit(1);
        return $list->current();
    }

    public function findSimilarKey()
    {
        $current = $this->findByField('key', $this->key);
        return $current ? $current->getProductCode() : null;
    }

    public static function generateCustomString($length = 5) {
        $characters = 'ABCDEFGHJKMNPQRSTVWXYZ1234567890';
        $charactersLength = strlen($characters);
        $randomString = '';

        for ($i = 0; $i < $length; $i++) {
            $randomIndex = mt_rand(0, $charactersLength - 1);
            $randomString .= $characters[$randomIndex];
        }
        return $randomString;
    }

    public function getInheritedField($field)
    {
        $object = $this;
        $value = Service::useInheritedValues(true, function() use ($object, $field) {
            $fieldName = "get" . ucfirst($field);
            return $object->$fieldName();
        });
        return $value;
    }

    public function productLevel()
    {
        $level = 0;
        $parent = $this->getParent();
        while ($parent instanceof Product) {
            $level++;
            $parent = $parent->getParent();
        }
        return $level;
    }

    public function getArea()
    {
        $dimension1 = $this->getProductDimension1();
        $dimension2 = $this->getProductDimension2();
        if ($dimension1 && $dimension2) {
            return $dimension1 * $dimension2 / 10000;
        }
        return 0;
    }

    public function getPackageArea()
    {
        $dimension1 = $this->getPackageDimension1();
        $dimension2 = $this->getPackageDimension2();
        if ($dimension1 && $dimension2) {
            return $dimension1 * $dimension2 / 10000;
        }
        return 0;
    }

    public function checkSticker4x6iwasku()
    {
        $asset = PdfGenerator::generate4x6iwasku('', '',  $this, "{$this->getKey()}_4x6iwasku.pdf");
        if ($asset) {
            $this->setSticker4x6iwasku($asset);
            $this->save();
        }
        return $asset;
    }

    public function checkSticker4x6eu()
    {
        $asset = PdfGenerator::generate4x6eu('', '',  $this, "{$this->getKey()}_4x6eu.pdf");
        if ($asset) {
            $this->setSticker4x6eu($asset);
            $this->save();
        }
        return $asset;
    }

}