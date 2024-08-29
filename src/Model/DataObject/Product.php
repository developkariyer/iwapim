<?php

namespace App\Model\DataObject;

use Pimcore\Model\DataObject\Concrete;
use Pimcore\Model\DataObject\Product\Listing;
use App\Utils\Utility;
use Pimcore\Model\DataObject\Service;

class Product extends Concrete
{
    private static $recursiveCounter = 0;

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
        $level = 0;
        $parent = $this->getParent();
        while ($parent instanceof Product) {
            $level++;
            $parent = $parent->getParent();
        }
        return $level;
    }

    public function checkProductCode($numberDigits = 5)
    {
        if (strlen($this->getProductCode()) == $numberDigits) {
            return;
        }
        $productCode = $this->generateUniqueCode($numberDigits);
        $this->setProductCode($productCode);
    }

    public function checkIwasku()
    {
        if ($this->level() < 2) {
            return;
        }
        if ($this->isPublished() && strlen($this->getIwasku()) != 12) {
            $pid = $this->getInheritedField("ProductIdentifier");
            $iwasku = str_pad(str_replace('-', '', $pid), 7, '0', STR_PAD_RIGHT);
            $iwasku .= $this->getProductCode();
            $this->setIwasku($iwasku);             
        }
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
                foreach ($child->getChildren([Product::OBJECT_TYPE_OBJECT], true) as $variant) {
                    if ($variant instanceof Product) {
                        $sizes[] = $variant->getVariationSize();
                        $colors[] = $variant->getVariationColor();                            
                    }
                }
            }
        }
        // remove null values and make unique and sort
        $sizes = array_unique(array_filter($sizes));
        $colors = array_unique(array_filter($colors));
        sort($sizes);
        sort($colors);
        return [$sizes, $colors];
    }

    protected function updateAsset($asset, $folder)
    {
        if (!$asset || $asset->getParent()->getFullPath() === $folder->getFullPath()) {
            return;
        }
        $originalFilename = pathinfo($asset->getFilename(), PATHINFO_FILENAME);
        $extension = pathinfo($asset->getFilename(), PATHINFO_EXTENSION);
        $productIdentifier = $this->getInheritedField('productIdentifier');
        $timestamp = date("YmdHis");
        $newFilename = "{$productIdentifier}_{$timestamp}_{$originalFilename}.{$extension}";
        $asset->setFilename($newFilename);
        $asset->setParent($folder);
        $asset->save();
    }

    public function checkAssetFolders()
    {
        if (!$this->isPublished()) {
            return;
        }
        $mainAlbumFolder = Utility::checkSetAssetPath('Ürün Albümleri');
        $mainDesignFolder = Utility::checkSetAssetPath('Ürün Tasarımları');
        $mainTechnicalsFolders = Utility::checkSetAssetPath('Ürün Dokümanları');
        $albumFolder = Utility::checkSetAssetPath($this->getInheritedField('productIdentifier'), $mainAlbumFolder);
        $designFolder = Utility::checkSetAssetPath($this->getInheritedField('productIdentifier'), $mainDesignFolder);
        $technicalFolder = Utility::checkSetAssetPath($this->getInheritedField('productIdentifier'), $mainTechnicalsFolders);
        $album = $this->getAlbum();
        $design = $this->getDesignFiles();
        $technicals = $this->getTechnicals();
        foreach ($album as $index=>$asset) {
            if (!$asset) {continue;}
            $this->updateAsset($asset->getImage(), $albumFolder);
        }
        foreach ($design as $index=>$asset) {
            $this->updateAsset($asset, $designFolder);
        }
        foreach ($technicals as $index=>$asset) {
            $this->updateAsset($asset, $technicalFolder);
        }
        $this->updateAsset($this->getImage(), $albumFolder);
    }

    public function checkVariations()
    {
        if ($this->level() > 0) {
            return;
        }
        if (self::$recursiveCounter > 5) {
            self::$recursiveCounter = 0;
            throw new \Exception('Recursive counter exceeded!');
        }
        if (!$this->isPublished()) {
            return;
        }
        self::$recursiveCounter++;
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
                $colors = array_merge($colors, ['ISOB', 'ISOG', 'IBOS', 'IBOG', 'IGOS', 'IGOB']);
            } elseif (!empty($trimColor)) {
                $colors[] = $trimColor;
            }
        }
        //(trim($this->getVariationColorList(), "\n ") === 'Mat') ? ['Black', 'Copper', 'Gold', 'Silver'] : 

        $sizes = array_values(array_unique(array_filter(array_map('trim', $sizes))));
        $colors = array_values(array_unique(array_filter(array_map('trim', $colors))));
        $sizeArray = $colorArray = [];
        foreach ($this->getChildren() as $child) {
            if (!$child instanceof Product) {continue;}
            if (!in_array($child->getVariationSize(), $sizes)) {
                //$child->delete();
            } else {
                $sizeArray[$child->getVariationSize()] = $child;
                $colorArray[$child->getVariationSize()] = [];
                foreach ($child->getChildren() as $variant) {
                    if (!$variant instanceof Product) {continue;}
                    if (!in_array($variant->getVariationColor(), $colors)) {
                        //$variant->delete();
                    } else {
                        $colorArray[$child->getVariationSize()][$variant->getVariationColor()] = true;
                    }
                }
            }
        }
        try {
            foreach ($sizes as $size) {
                if (empty($size)) {continue;}
                if (!isset($sizeArray[$size])) {
                    $newSize = new \Pimcore\Model\DataObject\Product();
                    $newSize->setParent($this);
                    $newSize->setVariationSize($size);
                    $newSize->checkProductCode();
                    $newSize->checkKey();
                    error_log("Details of newSize: {$newSize->getVariationSize()} - {$newSize->getProductCode()} - {$newSize->getKey()}");
                    $newSize->save();
                    usleep(300000);
                    $sizeArray[$size] = $newSize;
                }
                foreach ($colors as $color) {
                    if (empty($color)) {continue;}
                    if (!isset($colorArray[$size][$color])) {
                        $newColor = new \Pimcore\Model\DataObject\Product();
                        $newColor->setParent($sizeArray[$size]);
                        $newColor->setVariationColor($color);
                        $newColor->checkProductCode();
                        $newColor->checkKey();
                        $newColor->save();                        
                    }
                }
            }
        } catch (\Exception $e) {
            error_log("Error in checkVariations: {$e->getMessage()}");
            usleep(300000);
            $this->checkVariations();
        }
    }

    public function addVariant($variant)
    {
        $listingItems = $this->getListingItems();
        $listingIds = array_map(function ($item) {
            return $item->getId();
        }, $listingItems);
        if (!in_array($variant->getId(), $listingIds)) {
            $listingItems[] = $variant;
            $this->setListingItems($listingItems);
            return $this->save();
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

}
