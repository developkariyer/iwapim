<?php

namespace App\Model\DataObject;

use Pimcore\Model\DataObject\Concrete;
use Pimcore\Model\DataObject\Product\Listing;
use App\Utils\Utility;
use Pimcore\Model\DataObject\Service;
use Pimcore\Model\DataObject\Data\Video;

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
        if ($this->level() == 1 && $this->isPublished() && strlen($this->getIwasku()) != 12) {
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
        $asset->setFilename($newFilename);
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
        $append = substr($identifierParts[1], 0, 1);
        $level2Folder = Utility::checkSetAssetPath(
            "{$identifierParts[0]}-{$append}__",
            $level1Folder
        );
        $append = substr($identifierParts[1], 0, 2);
        $level3Folder = Utility::checkSetAssetPath(
            "{$identifierParts[0]}-{$append}_",
            $level2Folder
        );
        return Utility::checkSetAssetPath(
            $productIdentifier,
            $level3Folder
        );
    }

    public function checkAssetFolders()
    {
        if (!$this->isPublished()) {
            return;
        }

        $albumFolder = $designFolder = $technicalFolder = $rawMediaFolder = null;

        $album = $this->getAlbum();
        $design = $this->getDesignFiles();
        $technicals = $this->getTechnicals();
        $rawMedia = $this->getRawMedia();

        foreach ($album as $asset) {
            if (!$asset) {continue;}
            if (empty($albumFolder)) {
                $albumFolder = $this->generateAssetPath('Album');
            }
            $this->updateAsset($asset->getImage(), $albumFolder);
        }
        foreach ($design as $asset) {
            if (empty($designFolder)) {
                $designFolder = $this->generateAssetPath('Tasarim');
            }
            $this->updateAsset($asset, $designFolder);
        }
        foreach ($technicals as $asset) {
            if (empty($technicalFolder)) {
                $technicalFolder = $this->generateAssetPath('Kilavuz');
            }
            $this->updateAsset($asset, $technicalFolder);
        }
        foreach ($rawMedia as $asset) {
            if (empty($rawMediaFolder)) {
                $rawMediaFolder = $this->generateAssetPath('Ham');
            }
            $this->updateAsset($asset, $rawMediaFolder);
        }
        $this->updateAsset($this->getImage(), $albumFolder);
        $this->updateAsset($this->getVideo1(), $albumFolder);
        $this->updateAsset($this->getVideo2(), $albumFolder);
        $this->updateAsset($this->getVideo3(), $albumFolder);
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
