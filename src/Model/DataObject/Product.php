<?php

namespace App\Model\DataObject;

use App\Utils\PdfGenerator;
use Exception;
use Pimcore\Model\Asset;
use Pimcore\Model\Asset\Folder;
use Pimcore\Model\DataObject\AbstractObject;
use Pimcore\Model\DataObject\Concrete;
use Pimcore\Model\DataObject\Product\Listing;
use App\Utils\Utility;
use Pimcore\Model\DataObject\Service;
use Pimcore\Model\DataObject\Data\Video;
use Pimcore\Model\DataObject\VariantProduct;
use Pimcore\Model\Element\DuplicateFullPathException;

/**
 * Class Product
 *
 * This class serves as a data object for managing 
 * product data and creating product variations in Pimcore.
 * 
 * @package App\Model\DataObject
 */
class Product extends Concrete
{

    /**
     * @var array $level0NullFields
     * Fields to nullify for level 0 products
     */
    public static array $level0NullFields = [
        'variationSize',
        'variationColor',
        'iwasku',
        'listingItems',
        'brandItems',
        'wisersellId',
        'wisersellJson',
    ];
    /**
     * @var array $level1NullFields
     * Fields to nullify for level 1 products
     */
    public static array $level1NullFields = [
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
    /**
     * @var array $nullables
     * Fields that can be nullified
     */
    protected static array $nullables = [
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

    /**
    * Nullifies nullable fields in the product, setting them to null if they are empty.
    *
    * @return void
    */
    public function nullify(): void
    {
        foreach (self::$nullables as $field) {
            if (empty($this->$field) && !is_null($this->$field)) {
                $this->$field = null;
            }
        }
        foreach (($this->level() ? self::$level1NullFields : self::$level0NullFields) as $field) {
            $this->$field = null;
        }
    }

    /**
    * Returns the level of the product based on its parent status.
    *
    * @return int Returns 1 if the parent is an instance of Product, otherwise 0.
    */
    public function level(): int
    {
        return ($this->getParent() instanceof Product) ? 1 : 0;
    }

    /**
    * Checks if the product code has the specified number of digits; generates a new code if not.
    *
    * @param int $numberDigits The required number of digits for the product code (default is 5).
    * @return bool Returns false if the product code has the correct number of digits, otherwise returns true after generating a new code.
    */
    public function checkProductCode($numberDigits = 5): bool
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

    /**
    * Checks and updates the Iwasku value based on the product identifier and conditions.
    *
    * @param bool $forced If true, forces the check regardless of the current conditions.
    * @return bool Returns true if the Iwasku value was updated, otherwise returns false.
    */
    public function checkIwasku(bool $forced = false): bool
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

    /**
    * Constructs and sets the product key based on inherited fields; if empty, generates a temporary key.
    *
    * @return void
    */
    public function checkKey(): void
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

    /**
    * Validates and formats the product identifier by padding the numeric part with leading zeros.
    *
    * @return void
    */
    public function checkProductIdentifier(): void
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

    /**
    * Retrieves unique sizes and colors of the product variations.
    *
    * @return array An array containing two elements: an array of unique sizes and an array of unique colors.
    */
    public function listVariations(): array
    {
        $sizes = [];
        $colors = [];
        $children = $this->getChildren([AbstractObject::OBJECT_TYPE_OBJECT], true);
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

    /**
     * Updates the asset's filename and parent folder if necessary.
     *
     * @param mixed $asset The asset to be updated, which can be an instance of Video or other asset types.
     * @param Folder $folder The target folder where the asset should be moved.
     * @return void
     * @throws DuplicateFullPathException
     */
    protected function updateAsset(mixed $asset, Folder $folder): void
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
        if (!str_starts_with($originalFilename, "{$productIdentifier}_")) {
            $asset->setFilename($newFilename);
        }
        $asset->setParent($folder);
        $asset->save();
    }

    /**
     * Generates the asset path based on the product identifier and the main folder string.
     *
     * @param string $mainFolderString The main folder path as a string.
     * @return Folder|void|null The generated asset path or null if the product identifier is invalid.
     * @throws DuplicateFullPathException
     */
    protected function generateAssetPath(string $mainFolderString)
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

    /**
     * Retrieves a cached asset path or generates it if not already cached.
     *
     * @param string $path The path to check in the cache.
     * @throws DuplicateFullPathException
     */
    protected function cachedAssetPath(string $path)
    {
        if (!isset($this->cachedAssetPaths[$path])) {
            $this->cachedAssetPaths[$path] = $this->generateAssetPath($path);
        }
        return $this->cachedAssetPaths[$path];
    }

    /**
     * Checks and updates asset folders for the product based on its state and relationships.
     *
     * This method ensures that all assets are stored in the correct folders based on predefined
     * mappings. It handles fields, relations, and collections for assets and updates their
     * paths accordingly.
     *
     * @return void
     * @throws DuplicateFullPathException
     */
    public function checkAssetFolders(): void
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
                    if ($asset instanceof Asset) {
                        $this->updateAsset($asset, Utility::checkSetAssetPath($fieldName, $assetFolder));
                    }
                }
            }
        }
    }

    /**
     * Checks and manages variations of the product based on size and color.
     *
     * This method evaluates the current product's variations (sizes and colors) and ensures
     * that all combinations are represented as child products. If there are missing variations,
     * it creates new child products for them. Additionally, it handles the deletion of child
     * products that no longer match the valid variations.
     *
     * @return void
     * @throws Exception
     */
    public function checkVariations(): void
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
                $child->setPublished(false);
                $child->save();
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
                if ($newSize->checkIwasku()) {
                    $newSize->save();
                }
            }
        }
        $this->setFixVariations(false);
        $this->save();
    }

    /**
     * Adds a variant or variants to the product's listing items.
     *
     * Checks if the given variant(s) already exist. If not, adds them to the listing and saves the product.
     *
     * @param VariantProduct $variant A variant  to add.
     * @return true|Product True if added successfully, false otherwise.
     * @throws Exception
     */
    public function addVariant(VariantProduct $variant): true|Product
    {
        $listingItems = $this->getListingItems();
        $listingIds = array_map(function ($item) {
            return $item->getId();
        }, $listingItems);
        $variantArray = (is_array($variant)) ? $variant : [$variant];
        $dirty = false;
        foreach ($variantArray as $variant) {
            if (!$variant instanceof VariantProduct) {
                continue;
            }
            if (!in_array($variant->getId(), $listingIds)) {
                $dirty = true;
                $listingItems[] = $variant;
            }
        }
        if ($dirty) {
            $this->setListingItems($listingItems);
            return $this->save();
        }
        return true;
    }

    /**
    * Generates a unique product code of a specified number of digits.
    *
    * Continuously generates candidate codes until a unique one is found that does not 
    * already exist in the database.
    *
    * @param int $numberDigits The number of digits for the unique code. Default is 5.
    * @return string The unique product code.
    */
    public function generateUniqueCode(int $numberDigits=5): string
    {
        while (true) {
            $candidateCode = self::generateCustomString($numberDigits);
            if (!$this->findByField('productCode', $candidateCode)) {
                return $candidateCode;
            }
        }
    }

    /**
    * Finds an object by a field and its value.
    *
    * @param string $field The field name.
    * @param mixed $value The value to match.
    * @return \Pimcore\Model\DataObject\Product|false The found object or null.
    */
    public static function findByField(string $field, mixed $value): \Pimcore\Model\DataObject\Product|false
    {
        $list = new Listing();
        $list->setCondition("`$field` = ?", [$value]);
        $list->setUnpublished(true);
        $list->setLimit(1);
        return $list->current();
    }

    /**
    * Generates a random custom string of specified length using alphanumeric characters.
    *
    * @param int $length The desired length of the generated string.
    * @return string The generated random string.
    */
    public static function generateCustomString(int $length = 5): string
    {
        $characters = 'ABCDEFGHJKMNPQRSTVWXYZ1234567890';
        $charactersLength = strlen($characters);
        $randomString = '';

        for ($i = 0; $i < $length; $i++) {
            $randomIndex = mt_rand(0, $charactersLength - 1);
            $randomString .= $characters[$randomIndex];
        }
        return $randomString;
    }

    /**
    * Retrieves the value of an inherited field from the current object.
    *
    * @param string $field The name of the field to retrieve.
    * @return mixed The value of the inherited field.
    */
    public function getInheritedField(string $field): mixed
    {
        return Service::useInheritedValues(true, function() use ($field) {
            $object = $this;
            $fieldName = "get" . ucfirst($field);
            return $object->$fieldName();
        });
    }

    /**
     * Calculates the area of the product based on its dimensions.
     *
     * @return float|int float The area in square meters. Returns 0 if dimensions are not set.
     */
    public function getArea(): float|int
    {
        $dimension1 = $this->getProductDimension1();
        $dimension2 = $this->getProductDimension2();
        if ($dimension1 && $dimension2) {
            return $dimension1 * $dimension2 / 10000;
        }
        return 0;
    }

    /**
     * Calculates the area of the package based on its dimensions.
     *
     * @return float|int The package area in square meters. Returns 0 if dimensions are not set.
     */
    public function getPackageArea(): float|int
    {
        $dimension1 = $this->getPackageDimension1();
        $dimension2 = $this->getPackageDimension2();
        if ($dimension1 && $dimension2) {
            return $dimension1 * $dimension2 / 10000;
        }
        return 0;
    }

    /**
     * Generates a 4x6 iwasku sticker PDF and sets it to the current object.
     *
     * @return mixed The generated PDF asset or null if generation failed.
     * @throws Exception
     */
    public function checkSticker4x6iwasku(): mixed
    {
        $asset = PdfGenerator::generate4x6iwasku($this, "{$this->getKey()}_4x6iwasku.pdf");
        if ($asset) {
            $this->setSticker4x6iwasku($asset);
            $this->save();
        }
        return $asset;
    }

    public function checkStickerFnsku(): mixed
    {
        $assets = [];
        $iwasku = $this->getIwasku();
        $sql = "SELECT  warehouse, asin, fnsku FROM `iwa_inventory` WHERE iwasku = :iwasku";
        $result = Utility::fetchFromSql($sql, ['iwasku' => $iwasku]);
        foreach ($result as $item) {
            $asset  = PdfGenerator::generate4x6Fnsku($this, $item['fnsku'], $item['asin'], "{$item['warehouse']}_{$item['asin']}_{$item['fnsku']}_{$iwasku}.pdf");
            if ($asset) {
                $assets[] = $asset;
            }
        }
        $this->setStickerFnsku($assets);
        $this->save();

        /*$variantObjects  = $this->getListingItems();
        $assets = [];
        foreach ($variantObjects as $variant) {
            if ($variant->getFnsku() !== null) {
                $fnsku = $variant->getFnsku();
                $asin = $variant->getUniqueMarketplaceId();
                $asset = PdfGenerator::generate4x6Fnsku($this, $fnsku, $asin, "{$fnsku}_{$this->getKey()}_fnsku.pdf");
                if ($asset) {
                    $assets[] = $asset;
                }
            }
        }
        $this->setStickerFnsku($assets);
        $this->save();*/
        return $assets;
    }

    /**
     * Generates a 4x6 EU sticker PDF and sets it to the current object.
     *
     * @return mixed The generated PDF asset or null if generation failed.
     * @throws Exception
     */
    public function checkSticker4x6eu(): mixed
    {
        $asset = PdfGenerator::generate4x6eu($this, "{$this->getKey()}_4x6eu.pdf");
        if ($asset) {
            $this->setSticker4x6eu($asset);
            $this->save();
        }
        return $asset;
    }

}