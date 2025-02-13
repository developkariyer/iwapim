<?php

/**
 * Inheritance: yes
 * Variants: no
 *
 * Fields Summary:
 * - image [image]
 * - iwasku [input]
 * - productCode [input]
 * - wisersellId [input]
 * - eanGtin [input]
 * - requireEan [checkbox]
 * - productIdentifier [input]
 * - productCategory [select]
 * - name [input]
 * - nameEnglish [input]
 * - variationSize [input]
 * - variationColor [input]
 * - variationSizeList [textarea]
 * - variationColorList [textarea]
 * - fixVariations [checkbox]
 * - description [textarea]
 * - reportLink [link]
 * - sticker4x6iwasku [manyToOneRelation]
 * - wisersellJson [textarea]
 * - sticker4x6eu [manyToOneRelation]
 * - productLevel [calculatedValue]
 * - listingsCount [calculatedValue]
 * - requiresIwasku [checkbox]
 * - variantDescription [textarea]
 * - technicals [manyToManyRelation]
 * - album [imageGallery]
 * - productAlbum [fieldcollections]
 * - dimensionsPostponed [checkbox]
 * - productDimension1 [numeric]
 * - productDimension2 [numeric]
 * - productDimension3 [numeric]
 * - productWeight [numeric]
 * - packageDimension1 [numeric]
 * - packageDimension2 [numeric]
 * - packageDimension3 [numeric]
 * - packageWeight [numeric]
 * - desi5000 [calculatedValue]
 * - boxDimension1 [numeric]
 * - boxDimension2 [numeric]
 * - boxDimension3 [numeric]
 * - boxWeight [numeric]
 * - inBoxCount [numeric]
 * - inPaletteCount [numeric]
 * - inContainerCount [numeric]
 * - seoTitle [input]
 * - seoTitleEn [input]
 * - seoDescription [textarea]
 * - seoDescriptionEn [textarea]
 * - seoKeywords [textarea]
 * - seoKeywordsEn [textarea]
 * - listingItems [manyToManyObjectRelation]
 * - brandItems [manyToManyObjectRelation]
 * - identifierControlled [checkbox]
 * - imageUrl [externalImage]
 * - bundleProducts [advancedManyToManyObjectRelation]
 * - listingUniqueIds [input]
 * - productCost [calculatedValue]
 * - costModelProduct [manyToManyObjectRelation]
 * - costModelVariant [manyToManyObjectRelation]
 * - cutComplexity [select]
 * - pricingControlled [checkbox]
 * - pricingCosts [reverseObjectRelation]
 * - designFiles [manyToManyRelation]
 * - rawFiles [fieldcollections]
 */

namespace Pimcore\Model\DataObject;

use Pimcore\Model\DataObject\Exception\InheritanceParentNotFoundException;
use Pimcore\Model\DataObject\PreGetValueHookInterface;

/**
* @method static \Pimcore\Model\DataObject\Product\Listing getList(array $config = [])
* @method static \Pimcore\Model\DataObject\Product\Listing|\Pimcore\Model\DataObject\Product|null getByImage(mixed $value, ?int $limit = null, int $offset = 0, ?array $objectTypes = null)
* @method static \Pimcore\Model\DataObject\Product\Listing|\Pimcore\Model\DataObject\Product|null getByIwasku(mixed $value, ?int $limit = null, int $offset = 0, ?array $objectTypes = null)
* @method static \Pimcore\Model\DataObject\Product\Listing|\Pimcore\Model\DataObject\Product|null getByProductCode(mixed $value, ?int $limit = null, int $offset = 0, ?array $objectTypes = null)
* @method static \Pimcore\Model\DataObject\Product\Listing|\Pimcore\Model\DataObject\Product|null getByWisersellId(mixed $value, ?int $limit = null, int $offset = 0, ?array $objectTypes = null)
* @method static \Pimcore\Model\DataObject\Product\Listing|\Pimcore\Model\DataObject\Product|null getByEanGtin(mixed $value, ?int $limit = null, int $offset = 0, ?array $objectTypes = null)
* @method static \Pimcore\Model\DataObject\Product\Listing|\Pimcore\Model\DataObject\Product|null getByRequireEan(mixed $value, ?int $limit = null, int $offset = 0, ?array $objectTypes = null)
* @method static \Pimcore\Model\DataObject\Product\Listing|\Pimcore\Model\DataObject\Product|null getByProductIdentifier(mixed $value, ?int $limit = null, int $offset = 0, ?array $objectTypes = null)
* @method static \Pimcore\Model\DataObject\Product\Listing|\Pimcore\Model\DataObject\Product|null getByProductCategory(mixed $value, ?int $limit = null, int $offset = 0, ?array $objectTypes = null)
* @method static \Pimcore\Model\DataObject\Product\Listing|\Pimcore\Model\DataObject\Product|null getByName(mixed $value, ?int $limit = null, int $offset = 0, ?array $objectTypes = null)
* @method static \Pimcore\Model\DataObject\Product\Listing|\Pimcore\Model\DataObject\Product|null getByNameEnglish(mixed $value, ?int $limit = null, int $offset = 0, ?array $objectTypes = null)
* @method static \Pimcore\Model\DataObject\Product\Listing|\Pimcore\Model\DataObject\Product|null getByVariationSize(mixed $value, ?int $limit = null, int $offset = 0, ?array $objectTypes = null)
* @method static \Pimcore\Model\DataObject\Product\Listing|\Pimcore\Model\DataObject\Product|null getByVariationColor(mixed $value, ?int $limit = null, int $offset = 0, ?array $objectTypes = null)
* @method static \Pimcore\Model\DataObject\Product\Listing|\Pimcore\Model\DataObject\Product|null getByVariationSizeList(mixed $value, ?int $limit = null, int $offset = 0, ?array $objectTypes = null)
* @method static \Pimcore\Model\DataObject\Product\Listing|\Pimcore\Model\DataObject\Product|null getByVariationColorList(mixed $value, ?int $limit = null, int $offset = 0, ?array $objectTypes = null)
* @method static \Pimcore\Model\DataObject\Product\Listing|\Pimcore\Model\DataObject\Product|null getByFixVariations(mixed $value, ?int $limit = null, int $offset = 0, ?array $objectTypes = null)
* @method static \Pimcore\Model\DataObject\Product\Listing|\Pimcore\Model\DataObject\Product|null getByDescription(mixed $value, ?int $limit = null, int $offset = 0, ?array $objectTypes = null)
* @method static \Pimcore\Model\DataObject\Product\Listing|\Pimcore\Model\DataObject\Product|null getBySticker4x6iwasku(mixed $value, ?int $limit = null, int $offset = 0, ?array $objectTypes = null)
* @method static \Pimcore\Model\DataObject\Product\Listing|\Pimcore\Model\DataObject\Product|null getByWisersellJson(mixed $value, ?int $limit = null, int $offset = 0, ?array $objectTypes = null)
* @method static \Pimcore\Model\DataObject\Product\Listing|\Pimcore\Model\DataObject\Product|null getBySticker4x6eu(mixed $value, ?int $limit = null, int $offset = 0, ?array $objectTypes = null)
* @method static \Pimcore\Model\DataObject\Product\Listing|\Pimcore\Model\DataObject\Product|null getByRequiresIwasku(mixed $value, ?int $limit = null, int $offset = 0, ?array $objectTypes = null)
* @method static \Pimcore\Model\DataObject\Product\Listing|\Pimcore\Model\DataObject\Product|null getByVariantDescription(mixed $value, ?int $limit = null, int $offset = 0, ?array $objectTypes = null)
* @method static \Pimcore\Model\DataObject\Product\Listing|\Pimcore\Model\DataObject\Product|null getByTechnicals(mixed $value, ?int $limit = null, int $offset = 0, ?array $objectTypes = null)
* @method static \Pimcore\Model\DataObject\Product\Listing|\Pimcore\Model\DataObject\Product|null getByDimensionsPostponed(mixed $value, ?int $limit = null, int $offset = 0, ?array $objectTypes = null)
* @method static \Pimcore\Model\DataObject\Product\Listing|\Pimcore\Model\DataObject\Product|null getByProductDimension1(mixed $value, ?int $limit = null, int $offset = 0, ?array $objectTypes = null)
* @method static \Pimcore\Model\DataObject\Product\Listing|\Pimcore\Model\DataObject\Product|null getByProductDimension2(mixed $value, ?int $limit = null, int $offset = 0, ?array $objectTypes = null)
* @method static \Pimcore\Model\DataObject\Product\Listing|\Pimcore\Model\DataObject\Product|null getByProductDimension3(mixed $value, ?int $limit = null, int $offset = 0, ?array $objectTypes = null)
* @method static \Pimcore\Model\DataObject\Product\Listing|\Pimcore\Model\DataObject\Product|null getByProductWeight(mixed $value, ?int $limit = null, int $offset = 0, ?array $objectTypes = null)
* @method static \Pimcore\Model\DataObject\Product\Listing|\Pimcore\Model\DataObject\Product|null getByPackageDimension1(mixed $value, ?int $limit = null, int $offset = 0, ?array $objectTypes = null)
* @method static \Pimcore\Model\DataObject\Product\Listing|\Pimcore\Model\DataObject\Product|null getByPackageDimension2(mixed $value, ?int $limit = null, int $offset = 0, ?array $objectTypes = null)
* @method static \Pimcore\Model\DataObject\Product\Listing|\Pimcore\Model\DataObject\Product|null getByPackageDimension3(mixed $value, ?int $limit = null, int $offset = 0, ?array $objectTypes = null)
* @method static \Pimcore\Model\DataObject\Product\Listing|\Pimcore\Model\DataObject\Product|null getByPackageWeight(mixed $value, ?int $limit = null, int $offset = 0, ?array $objectTypes = null)
* @method static \Pimcore\Model\DataObject\Product\Listing|\Pimcore\Model\DataObject\Product|null getByBoxDimension1(mixed $value, ?int $limit = null, int $offset = 0, ?array $objectTypes = null)
* @method static \Pimcore\Model\DataObject\Product\Listing|\Pimcore\Model\DataObject\Product|null getByBoxDimension2(mixed $value, ?int $limit = null, int $offset = 0, ?array $objectTypes = null)
* @method static \Pimcore\Model\DataObject\Product\Listing|\Pimcore\Model\DataObject\Product|null getByBoxDimension3(mixed $value, ?int $limit = null, int $offset = 0, ?array $objectTypes = null)
* @method static \Pimcore\Model\DataObject\Product\Listing|\Pimcore\Model\DataObject\Product|null getByBoxWeight(mixed $value, ?int $limit = null, int $offset = 0, ?array $objectTypes = null)
* @method static \Pimcore\Model\DataObject\Product\Listing|\Pimcore\Model\DataObject\Product|null getByInBoxCount(mixed $value, ?int $limit = null, int $offset = 0, ?array $objectTypes = null)
* @method static \Pimcore\Model\DataObject\Product\Listing|\Pimcore\Model\DataObject\Product|null getByInPaletteCount(mixed $value, ?int $limit = null, int $offset = 0, ?array $objectTypes = null)
* @method static \Pimcore\Model\DataObject\Product\Listing|\Pimcore\Model\DataObject\Product|null getByInContainerCount(mixed $value, ?int $limit = null, int $offset = 0, ?array $objectTypes = null)
* @method static \Pimcore\Model\DataObject\Product\Listing|\Pimcore\Model\DataObject\Product|null getBySeoTitle(mixed $value, ?int $limit = null, int $offset = 0, ?array $objectTypes = null)
* @method static \Pimcore\Model\DataObject\Product\Listing|\Pimcore\Model\DataObject\Product|null getBySeoTitleEn(mixed $value, ?int $limit = null, int $offset = 0, ?array $objectTypes = null)
* @method static \Pimcore\Model\DataObject\Product\Listing|\Pimcore\Model\DataObject\Product|null getBySeoDescription(mixed $value, ?int $limit = null, int $offset = 0, ?array $objectTypes = null)
* @method static \Pimcore\Model\DataObject\Product\Listing|\Pimcore\Model\DataObject\Product|null getBySeoDescriptionEn(mixed $value, ?int $limit = null, int $offset = 0, ?array $objectTypes = null)
* @method static \Pimcore\Model\DataObject\Product\Listing|\Pimcore\Model\DataObject\Product|null getBySeoKeywords(mixed $value, ?int $limit = null, int $offset = 0, ?array $objectTypes = null)
* @method static \Pimcore\Model\DataObject\Product\Listing|\Pimcore\Model\DataObject\Product|null getBySeoKeywordsEn(mixed $value, ?int $limit = null, int $offset = 0, ?array $objectTypes = null)
* @method static \Pimcore\Model\DataObject\Product\Listing|\Pimcore\Model\DataObject\Product|null getByListingItems(mixed $value, ?int $limit = null, int $offset = 0, ?array $objectTypes = null)
* @method static \Pimcore\Model\DataObject\Product\Listing|\Pimcore\Model\DataObject\Product|null getByBrandItems(mixed $value, ?int $limit = null, int $offset = 0, ?array $objectTypes = null)
* @method static \Pimcore\Model\DataObject\Product\Listing|\Pimcore\Model\DataObject\Product|null getByIdentifierControlled(mixed $value, ?int $limit = null, int $offset = 0, ?array $objectTypes = null)
* @method static \Pimcore\Model\DataObject\Product\Listing|\Pimcore\Model\DataObject\Product|null getByBundleProducts(mixed $value, ?int $limit = null, int $offset = 0, ?array $objectTypes = null)
* @method static \Pimcore\Model\DataObject\Product\Listing|\Pimcore\Model\DataObject\Product|null getByListingUniqueIds(mixed $value, ?int $limit = null, int $offset = 0, ?array $objectTypes = null)
* @method static \Pimcore\Model\DataObject\Product\Listing|\Pimcore\Model\DataObject\Product|null getByCostModelProduct(mixed $value, ?int $limit = null, int $offset = 0, ?array $objectTypes = null)
* @method static \Pimcore\Model\DataObject\Product\Listing|\Pimcore\Model\DataObject\Product|null getByCostModelVariant(mixed $value, ?int $limit = null, int $offset = 0, ?array $objectTypes = null)
* @method static \Pimcore\Model\DataObject\Product\Listing|\Pimcore\Model\DataObject\Product|null getByCutComplexity(mixed $value, ?int $limit = null, int $offset = 0, ?array $objectTypes = null)
* @method static \Pimcore\Model\DataObject\Product\Listing|\Pimcore\Model\DataObject\Product|null getByPricingControlled(mixed $value, ?int $limit = null, int $offset = 0, ?array $objectTypes = null)
* @method static \Pimcore\Model\DataObject\Product\Listing|\Pimcore\Model\DataObject\Product|null getByPricingCosts(mixed $value, ?int $limit = null, int $offset = 0, ?array $objectTypes = null)
* @method static \Pimcore\Model\DataObject\Product\Listing|\Pimcore\Model\DataObject\Product|null getByDesignFiles(mixed $value, ?int $limit = null, int $offset = 0, ?array $objectTypes = null)
*/

class Product extends \App\Model\DataObject\Product
{
public const FIELD_IMAGE = 'image';
public const FIELD_IWASKU = 'iwasku';
public const FIELD_PRODUCT_CODE = 'productCode';
public const FIELD_WISERSELL_ID = 'wisersellId';
public const FIELD_EAN_GTIN = 'eanGtin';
public const FIELD_REQUIRE_EAN = 'requireEan';
public const FIELD_PRODUCT_IDENTIFIER = 'productIdentifier';
public const FIELD_PRODUCT_CATEGORY = 'productCategory';
public const FIELD_NAME = 'name';
public const FIELD_NAME_ENGLISH = 'nameEnglish';
public const FIELD_VARIATION_SIZE = 'variationSize';
public const FIELD_VARIATION_COLOR = 'variationColor';
public const FIELD_VARIATION_SIZE_LIST = 'variationSizeList';
public const FIELD_VARIATION_COLOR_LIST = 'variationColorList';
public const FIELD_FIX_VARIATIONS = 'fixVariations';
public const FIELD_DESCRIPTION = 'description';
public const FIELD_REPORT_LINK = 'reportLink';
public const FIELD_STICKER4X6IWASKU = 'sticker4x6iwasku';
public const FIELD_WISERSELL_JSON = 'wisersellJson';
public const FIELD_STICKER4X6EU = 'sticker4x6eu';
public const FIELD_PRODUCT_LEVEL = 'productLevel';
public const FIELD_LISTINGS_COUNT = 'listingsCount';
public const FIELD_REQUIRES_IWASKU = 'requiresIwasku';
public const FIELD_VARIANT_DESCRIPTION = 'variantDescription';
public const FIELD_TECHNICALS = 'technicals';
public const FIELD_ALBUM = 'album';
public const FIELD_PRODUCT_ALBUM = 'productAlbum';
public const FIELD_DIMENSIONS_POSTPONED = 'dimensionsPostponed';
public const FIELD_PRODUCT_DIMENSION1 = 'productDimension1';
public const FIELD_PRODUCT_DIMENSION2 = 'productDimension2';
public const FIELD_PRODUCT_DIMENSION3 = 'productDimension3';
public const FIELD_PRODUCT_WEIGHT = 'productWeight';
public const FIELD_PACKAGE_DIMENSION1 = 'packageDimension1';
public const FIELD_PACKAGE_DIMENSION2 = 'packageDimension2';
public const FIELD_PACKAGE_DIMENSION3 = 'packageDimension3';
public const FIELD_PACKAGE_WEIGHT = 'packageWeight';
public const FIELD_DESI5000 = 'desi5000';
public const FIELD_BOX_DIMENSION1 = 'boxDimension1';
public const FIELD_BOX_DIMENSION2 = 'boxDimension2';
public const FIELD_BOX_DIMENSION3 = 'boxDimension3';
public const FIELD_BOX_WEIGHT = 'boxWeight';
public const FIELD_IN_BOX_COUNT = 'inBoxCount';
public const FIELD_IN_PALETTE_COUNT = 'inPaletteCount';
public const FIELD_IN_CONTAINER_COUNT = 'inContainerCount';
public const FIELD_SEO_TITLE = 'seoTitle';
public const FIELD_SEO_TITLE_EN = 'seoTitleEn';
public const FIELD_SEO_DESCRIPTION = 'seoDescription';
public const FIELD_SEO_DESCRIPTION_EN = 'seoDescriptionEn';
public const FIELD_SEO_KEYWORDS = 'seoKeywords';
public const FIELD_SEO_KEYWORDS_EN = 'seoKeywordsEn';
public const FIELD_LISTING_ITEMS = 'listingItems';
public const FIELD_BRAND_ITEMS = 'brandItems';
public const FIELD_IDENTIFIER_CONTROLLED = 'identifierControlled';
public const FIELD_IMAGE_URL = 'imageUrl';
public const FIELD_BUNDLE_PRODUCTS = 'bundleProducts';
public const FIELD_LISTING_UNIQUE_IDS = 'listingUniqueIds';
public const FIELD_PRODUCT_COST = 'productCost';
public const FIELD_COST_MODEL_PRODUCT = 'costModelProduct';
public const FIELD_COST_MODEL_VARIANT = 'costModelVariant';
public const FIELD_CUT_COMPLEXITY = 'cutComplexity';
public const FIELD_PRICING_CONTROLLED = 'pricingControlled';
public const FIELD_PRICING_COSTS = 'pricingCosts';
public const FIELD_DESIGN_FILES = 'designFiles';
public const FIELD_RAW_FILES = 'rawFiles';

protected $classId = "product";
protected $className = "Product";
protected $image;
protected $iwasku;
protected $productCode;
protected $wisersellId;
protected $eanGtin;
protected $requireEan;
protected $productIdentifier;
protected $productCategory;
protected $name;
protected $nameEnglish;
protected $variationSize;
protected $variationColor;
protected $variationSizeList;
protected $variationColorList;
protected $fixVariations;
protected $description;
protected $reportLink;
protected $sticker4x6iwasku;
protected $wisersellJson;
protected $sticker4x6eu;
protected $requiresIwasku;
protected $variantDescription;
protected $technicals;
protected $album;
protected $productAlbum;
protected $dimensionsPostponed;
protected $productDimension1;
protected $productDimension2;
protected $productDimension3;
protected $productWeight;
protected $packageDimension1;
protected $packageDimension2;
protected $packageDimension3;
protected $packageWeight;
protected $boxDimension1;
protected $boxDimension2;
protected $boxDimension3;
protected $boxWeight;
protected $inBoxCount;
protected $inPaletteCount;
protected $inContainerCount;
protected $seoTitle;
protected $seoTitleEn;
protected $seoDescription;
protected $seoDescriptionEn;
protected $seoKeywords;
protected $seoKeywordsEn;
protected $listingItems;
protected $brandItems;
protected $identifierControlled;
protected $imageUrl;
protected $bundleProducts;
protected $listingUniqueIds;
protected $costModelProduct;
protected $costModelVariant;
protected $cutComplexity;
protected $pricingControlled;
protected $designFiles;
protected $rawFiles;


/**
* @param array $values
* @return static
*/
public static function create(array $values = []): static
{
	$object = new static();
	$object->setValues($values);
	return $object;
}

/**
* Get image - Image
* @return \Pimcore\Model\Asset\Image|null
*/
public function getImage(): ?\Pimcore\Model\Asset\Image
{
	if ($this instanceof PreGetValueHookInterface && !\Pimcore::inAdmin()) {
		$preValue = $this->preGetValue("image");
		if ($preValue !== null) {
			return $preValue;
		}
	}

	$data = $this->image;

	if (\Pimcore\Model\DataObject::doGetInheritedValues() && $this->getClass()->getFieldDefinition("image")->isEmpty($data)) {
		try {
			return $this->getValueFromParent("image");
		} catch (InheritanceParentNotFoundException $e) {
			// no data from parent available, continue ...
		}
	}

	if ($data instanceof \Pimcore\Model\DataObject\Data\EncryptedField) {
		return $data->getPlain();
	}

	return $data;
}

/**
* Set image - Image
* @param \Pimcore\Model\Asset\Image|null $image
* @return $this
*/
public function setImage(?\Pimcore\Model\Asset\Image $image): static
{
	$this->markFieldDirty("image", true);

	$this->image = $image;

	return $this;
}

/**
* Get iwasku - IWASKU
* @return string|null
*/
public function getIwasku(): ?string
{
	if ($this instanceof PreGetValueHookInterface && !\Pimcore::inAdmin()) {
		$preValue = $this->preGetValue("iwasku");
		if ($preValue !== null) {
			return $preValue;
		}
	}

	$data = $this->iwasku;

	if (\Pimcore\Model\DataObject::doGetInheritedValues() && $this->getClass()->getFieldDefinition("iwasku")->isEmpty($data)) {
		try {
			return $this->getValueFromParent("iwasku");
		} catch (InheritanceParentNotFoundException $e) {
			// no data from parent available, continue ...
		}
	}

	if ($data instanceof \Pimcore\Model\DataObject\Data\EncryptedField) {
		return $data->getPlain();
	}

	return $data;
}

/**
* Set iwasku - IWASKU
* @param string|null $iwasku
* @return $this
*/
public function setIwasku(?string $iwasku): static
{
	$this->markFieldDirty("iwasku", true);

	$this->iwasku = $iwasku;

	return $this;
}

/**
* Get productCode - Ürün Kodu
* @return string|null
*/
public function getProductCode(): ?string
{
	if ($this instanceof PreGetValueHookInterface && !\Pimcore::inAdmin()) {
		$preValue = $this->preGetValue("productCode");
		if ($preValue !== null) {
			return $preValue;
		}
	}

	$data = $this->productCode;

	if (\Pimcore\Model\DataObject::doGetInheritedValues() && $this->getClass()->getFieldDefinition("productCode")->isEmpty($data)) {
		try {
			return $this->getValueFromParent("productCode");
		} catch (InheritanceParentNotFoundException $e) {
			// no data from parent available, continue ...
		}
	}

	if ($data instanceof \Pimcore\Model\DataObject\Data\EncryptedField) {
		return $data->getPlain();
	}

	return $data;
}

/**
* Set productCode - Ürün Kodu
* @param string|null $productCode
* @return $this
*/
public function setProductCode(?string $productCode): static
{
	$this->markFieldDirty("productCode", true);

	$this->productCode = $productCode;

	return $this;
}

/**
* Get wisersellId - Wisersell Id
* @return string|null
*/
public function getWisersellId(): ?string
{
	if ($this instanceof PreGetValueHookInterface && !\Pimcore::inAdmin()) {
		$preValue = $this->preGetValue("wisersellId");
		if ($preValue !== null) {
			return $preValue;
		}
	}

	$data = $this->wisersellId;

	if (\Pimcore\Model\DataObject::doGetInheritedValues() && $this->getClass()->getFieldDefinition("wisersellId")->isEmpty($data)) {
		try {
			return $this->getValueFromParent("wisersellId");
		} catch (InheritanceParentNotFoundException $e) {
			// no data from parent available, continue ...
		}
	}

	if ($data instanceof \Pimcore\Model\DataObject\Data\EncryptedField) {
		return $data->getPlain();
	}

	return $data;
}

/**
* Set wisersellId - Wisersell Id
* @param string|null $wisersellId
* @return $this
*/
public function setWisersellId(?string $wisersellId): static
{
	$this->markFieldDirty("wisersellId", true);

	$this->wisersellId = $wisersellId;

	return $this;
}

/**
* Get eanGtin - EAN/GTIN
* @return string|null
*/
public function getEanGtin(): ?string
{
	if ($this instanceof PreGetValueHookInterface && !\Pimcore::inAdmin()) {
		$preValue = $this->preGetValue("eanGtin");
		if ($preValue !== null) {
			return $preValue;
		}
	}

	$data = $this->eanGtin;

	if (\Pimcore\Model\DataObject::doGetInheritedValues() && $this->getClass()->getFieldDefinition("eanGtin")->isEmpty($data)) {
		try {
			return $this->getValueFromParent("eanGtin");
		} catch (InheritanceParentNotFoundException $e) {
			// no data from parent available, continue ...
		}
	}

	if ($data instanceof \Pimcore\Model\DataObject\Data\EncryptedField) {
		return $data->getPlain();
	}

	return $data;
}

/**
* Set eanGtin - EAN/GTIN
* @param string|null $eanGtin
* @return $this
*/
public function setEanGtin(?string $eanGtin): static
{
	$this->markFieldDirty("eanGtin", true);

	$this->eanGtin = $eanGtin;

	return $this;
}

/**
* Get requireEan - Ean Al
* @return bool|null
*/
public function getRequireEan(): ?bool
{
	if ($this instanceof PreGetValueHookInterface && !\Pimcore::inAdmin()) {
		$preValue = $this->preGetValue("requireEan");
		if ($preValue !== null) {
			return $preValue;
		}
	}

	$data = $this->requireEan;

	if (\Pimcore\Model\DataObject::doGetInheritedValues() && $this->getClass()->getFieldDefinition("requireEan")->isEmpty($data)) {
		try {
			return $this->getValueFromParent("requireEan");
		} catch (InheritanceParentNotFoundException $e) {
			// no data from parent available, continue ...
		}
	}

	if ($data instanceof \Pimcore\Model\DataObject\Data\EncryptedField) {
		return $data->getPlain();
	}

	return $data;
}

/**
* Set requireEan - Ean Al
* @param bool|null $requireEan
* @return $this
*/
public function setRequireEan(?bool $requireEan): static
{
	$this->markFieldDirty("requireEan", true);

	$this->requireEan = $requireEan;

	return $this;
}

/**
* Get productIdentifier - Ürün Tanıtıcı Adı
* @return string|null
*/
public function getProductIdentifier(): ?string
{
	if ($this instanceof PreGetValueHookInterface && !\Pimcore::inAdmin()) {
		$preValue = $this->preGetValue("productIdentifier");
		if ($preValue !== null) {
			return $preValue;
		}
	}

	$data = $this->productIdentifier;

	if (\Pimcore\Model\DataObject::doGetInheritedValues() && $this->getClass()->getFieldDefinition("productIdentifier")->isEmpty($data)) {
		try {
			return $this->getValueFromParent("productIdentifier");
		} catch (InheritanceParentNotFoundException $e) {
			// no data from parent available, continue ...
		}
	}

	if ($data instanceof \Pimcore\Model\DataObject\Data\EncryptedField) {
		return $data->getPlain();
	}

	return $data;
}

/**
* Set productIdentifier - Ürün Tanıtıcı Adı
* @param string|null $productIdentifier
* @return $this
*/
public function setProductIdentifier(?string $productIdentifier): static
{
	$this->markFieldDirty("productIdentifier", true);

	$this->productIdentifier = $productIdentifier;

	return $this;
}

/**
* Get productCategory - Kategori
* @return string|null
*/
public function getProductCategory(): ?string
{
	if ($this instanceof PreGetValueHookInterface && !\Pimcore::inAdmin()) {
		$preValue = $this->preGetValue("productCategory");
		if ($preValue !== null) {
			return $preValue;
		}
	}

	$data = $this->productCategory;

	if (\Pimcore\Model\DataObject::doGetInheritedValues() && $this->getClass()->getFieldDefinition("productCategory")->isEmpty($data)) {
		try {
			return $this->getValueFromParent("productCategory");
		} catch (InheritanceParentNotFoundException $e) {
			// no data from parent available, continue ...
		}
	}

	if ($data instanceof \Pimcore\Model\DataObject\Data\EncryptedField) {
		return $data->getPlain();
	}

	return $data;
}

/**
* Set productCategory - Kategori
* @param string|null $productCategory
* @return $this
*/
public function setProductCategory(?string $productCategory): static
{
	$this->markFieldDirty("productCategory", true);

	$this->productCategory = $productCategory;

	return $this;
}

/**
* Get name - Ürün Adı
* @return string|null
*/
public function getName(): ?string
{
	if ($this instanceof PreGetValueHookInterface && !\Pimcore::inAdmin()) {
		$preValue = $this->preGetValue("name");
		if ($preValue !== null) {
			return $preValue;
		}
	}

	$data = $this->name;

	if (\Pimcore\Model\DataObject::doGetInheritedValues() && $this->getClass()->getFieldDefinition("name")->isEmpty($data)) {
		try {
			return $this->getValueFromParent("name");
		} catch (InheritanceParentNotFoundException $e) {
			// no data from parent available, continue ...
		}
	}

	if ($data instanceof \Pimcore\Model\DataObject\Data\EncryptedField) {
		return $data->getPlain();
	}

	return $data;
}

/**
* Set name - Ürün Adı
* @param string|null $name
* @return $this
*/
public function setName(?string $name): static
{
	$this->markFieldDirty("name", true);

	$this->name = $name;

	return $this;
}

/**
* Get nameEnglish - Ürün Adı (İngilizce)
* @return string|null
*/
public function getNameEnglish(): ?string
{
	if ($this instanceof PreGetValueHookInterface && !\Pimcore::inAdmin()) {
		$preValue = $this->preGetValue("nameEnglish");
		if ($preValue !== null) {
			return $preValue;
		}
	}

	$data = $this->nameEnglish;

	if (\Pimcore\Model\DataObject::doGetInheritedValues() && $this->getClass()->getFieldDefinition("nameEnglish")->isEmpty($data)) {
		try {
			return $this->getValueFromParent("nameEnglish");
		} catch (InheritanceParentNotFoundException $e) {
			// no data from parent available, continue ...
		}
	}

	if ($data instanceof \Pimcore\Model\DataObject\Data\EncryptedField) {
		return $data->getPlain();
	}

	return $data;
}

/**
* Set nameEnglish - Ürün Adı (İngilizce)
* @param string|null $nameEnglish
* @return $this
*/
public function setNameEnglish(?string $nameEnglish): static
{
	$this->markFieldDirty("nameEnglish", true);

	$this->nameEnglish = $nameEnglish;

	return $this;
}

/**
* Get variationSize - Varyant Ebatı
* @return string|null
*/
public function getVariationSize(): ?string
{
	if ($this instanceof PreGetValueHookInterface && !\Pimcore::inAdmin()) {
		$preValue = $this->preGetValue("variationSize");
		if ($preValue !== null) {
			return $preValue;
		}
	}

	$data = $this->variationSize;

	if (\Pimcore\Model\DataObject::doGetInheritedValues() && $this->getClass()->getFieldDefinition("variationSize")->isEmpty($data)) {
		try {
			return $this->getValueFromParent("variationSize");
		} catch (InheritanceParentNotFoundException $e) {
			// no data from parent available, continue ...
		}
	}

	if ($data instanceof \Pimcore\Model\DataObject\Data\EncryptedField) {
		return $data->getPlain();
	}

	return $data;
}

/**
* Set variationSize - Varyant Ebatı
* @param string|null $variationSize
* @return $this
*/
public function setVariationSize(?string $variationSize): static
{
	$this->markFieldDirty("variationSize", true);

	$this->variationSize = $variationSize;

	return $this;
}

/**
* Get variationColor - Variant Rengi
* @return string|null
*/
public function getVariationColor(): ?string
{
	if ($this instanceof PreGetValueHookInterface && !\Pimcore::inAdmin()) {
		$preValue = $this->preGetValue("variationColor");
		if ($preValue !== null) {
			return $preValue;
		}
	}

	$data = $this->variationColor;

	if (\Pimcore\Model\DataObject::doGetInheritedValues() && $this->getClass()->getFieldDefinition("variationColor")->isEmpty($data)) {
		try {
			return $this->getValueFromParent("variationColor");
		} catch (InheritanceParentNotFoundException $e) {
			// no data from parent available, continue ...
		}
	}

	if ($data instanceof \Pimcore\Model\DataObject\Data\EncryptedField) {
		return $data->getPlain();
	}

	return $data;
}

/**
* Set variationColor - Variant Rengi
* @param string|null $variationColor
* @return $this
*/
public function setVariationColor(?string $variationColor): static
{
	$this->markFieldDirty("variationColor", true);

	$this->variationColor = $variationColor;

	return $this;
}

/**
* Get variationSizeList - Variation Size List
* @return string|null
*/
public function getVariationSizeList(): ?string
{
	if ($this instanceof PreGetValueHookInterface && !\Pimcore::inAdmin()) {
		$preValue = $this->preGetValue("variationSizeList");
		if ($preValue !== null) {
			return $preValue;
		}
	}

	$data = $this->variationSizeList;

	if (\Pimcore\Model\DataObject::doGetInheritedValues() && $this->getClass()->getFieldDefinition("variationSizeList")->isEmpty($data)) {
		try {
			return $this->getValueFromParent("variationSizeList");
		} catch (InheritanceParentNotFoundException $e) {
			// no data from parent available, continue ...
		}
	}

	if ($data instanceof \Pimcore\Model\DataObject\Data\EncryptedField) {
		return $data->getPlain();
	}

	return $data;
}

/**
* Set variationSizeList - Variation Size List
* @param string|null $variationSizeList
* @return $this
*/
public function setVariationSizeList(?string $variationSizeList): static
{
	$this->markFieldDirty("variationSizeList", true);

	$this->variationSizeList = $variationSizeList;

	return $this;
}

/**
* Get variationColorList - Variation Color List
* @return string|null
*/
public function getVariationColorList(): ?string
{
	if ($this instanceof PreGetValueHookInterface && !\Pimcore::inAdmin()) {
		$preValue = $this->preGetValue("variationColorList");
		if ($preValue !== null) {
			return $preValue;
		}
	}

	$data = $this->variationColorList;

	if (\Pimcore\Model\DataObject::doGetInheritedValues() && $this->getClass()->getFieldDefinition("variationColorList")->isEmpty($data)) {
		try {
			return $this->getValueFromParent("variationColorList");
		} catch (InheritanceParentNotFoundException $e) {
			// no data from parent available, continue ...
		}
	}

	if ($data instanceof \Pimcore\Model\DataObject\Data\EncryptedField) {
		return $data->getPlain();
	}

	return $data;
}

/**
* Set variationColorList - Variation Color List
* @param string|null $variationColorList
* @return $this
*/
public function setVariationColorList(?string $variationColorList): static
{
	$this->markFieldDirty("variationColorList", true);

	$this->variationColorList = $variationColorList;

	return $this;
}

/**
* Get fixVariations - Varyasyonları Düzenle
* @return bool|null
*/
public function getFixVariations(): ?bool
{
	if ($this instanceof PreGetValueHookInterface && !\Pimcore::inAdmin()) {
		$preValue = $this->preGetValue("fixVariations");
		if ($preValue !== null) {
			return $preValue;
		}
	}

	$data = $this->fixVariations;

	if (\Pimcore\Model\DataObject::doGetInheritedValues() && $this->getClass()->getFieldDefinition("fixVariations")->isEmpty($data)) {
		try {
			return $this->getValueFromParent("fixVariations");
		} catch (InheritanceParentNotFoundException $e) {
			// no data from parent available, continue ...
		}
	}

	if ($data instanceof \Pimcore\Model\DataObject\Data\EncryptedField) {
		return $data->getPlain();
	}

	return $data;
}

/**
* Set fixVariations - Varyasyonları Düzenle
* @param bool|null $fixVariations
* @return $this
*/
public function setFixVariations(?bool $fixVariations): static
{
	$this->markFieldDirty("fixVariations", true);

	$this->fixVariations = $fixVariations;

	return $this;
}

/**
* Get description - Ürün Tanımı
* @return string|null
*/
public function getDescription(): ?string
{
	if ($this instanceof PreGetValueHookInterface && !\Pimcore::inAdmin()) {
		$preValue = $this->preGetValue("description");
		if ($preValue !== null) {
			return $preValue;
		}
	}

	$data = $this->description;

	if (\Pimcore\Model\DataObject::doGetInheritedValues() && $this->getClass()->getFieldDefinition("description")->isEmpty($data)) {
		try {
			return $this->getValueFromParent("description");
		} catch (InheritanceParentNotFoundException $e) {
			// no data from parent available, continue ...
		}
	}

	if ($data instanceof \Pimcore\Model\DataObject\Data\EncryptedField) {
		return $data->getPlain();
	}

	return $data;
}

/**
* Set description - Ürün Tanımı
* @param string|null $description
* @return $this
*/
public function setDescription(?string $description): static
{
	$this->markFieldDirty("description", true);

	$this->description = $description;

	return $this;
}

/**
* Get reportLink - Analiz
* @return \Pimcore\Model\DataObject\Data\Link|null
*/
public function getReportLink(): ?\Pimcore\Model\DataObject\Data\Link
{
	if ($this instanceof PreGetValueHookInterface && !\Pimcore::inAdmin()) {
		$preValue = $this->preGetValue("reportLink");
		if ($preValue !== null) {
			return $preValue;
		}
	}

	$data = $this->reportLink;

	if (\Pimcore\Model\DataObject::doGetInheritedValues() && $this->getClass()->getFieldDefinition("reportLink")->isEmpty($data)) {
		try {
			return $this->getValueFromParent("reportLink");
		} catch (InheritanceParentNotFoundException $e) {
			// no data from parent available, continue ...
		}
	}

	if ($data instanceof \Pimcore\Model\DataObject\Data\EncryptedField) {
		return $data->getPlain();
	}

	return $data;
}

/**
* Set reportLink - Analiz
* @param \Pimcore\Model\DataObject\Data\Link|null $reportLink
* @return $this
*/
public function setReportLink(?\Pimcore\Model\DataObject\Data\Link $reportLink): static
{
	$this->markFieldDirty("reportLink", true);

	$this->reportLink = $reportLink;

	return $this;
}

/**
* Get sticker4x6iwasku - Etiket 4x6 IWASKU
* @return \Pimcore\Model\Asset|null
*/
public function getSticker4x6iwasku(): ?\Pimcore\Model\Element\AbstractElement
{
	if ($this instanceof PreGetValueHookInterface && !\Pimcore::inAdmin()) {
		$preValue = $this->preGetValue("sticker4x6iwasku");
		if ($preValue !== null) {
			return $preValue;
		}
	}

	$data = $this->getClass()->getFieldDefinition("sticker4x6iwasku")->preGetData($this);

	if (\Pimcore\Model\DataObject::doGetInheritedValues() && $this->getClass()->getFieldDefinition("sticker4x6iwasku")->isEmpty($data)) {
		try {
			return $this->getValueFromParent("sticker4x6iwasku");
		} catch (InheritanceParentNotFoundException $e) {
			// no data from parent available, continue ...
		}
	}

	if ($data instanceof \Pimcore\Model\DataObject\Data\EncryptedField) {
		return $data->getPlain();
	}

	return $data;
}

/**
* Set sticker4x6iwasku - Etiket 4x6 IWASKU
* @param \Pimcore\Model\Asset|null $sticker4x6iwasku
* @return $this
*/
public function setSticker4x6iwasku(?\Pimcore\Model\Element\AbstractElement $sticker4x6iwasku): static
{
	/** @var \Pimcore\Model\DataObject\ClassDefinition\Data\ManyToOneRelation $fd */
	$fd = $this->getClass()->getFieldDefinition("sticker4x6iwasku");
	$hideUnpublished = \Pimcore\Model\DataObject\Concrete::getHideUnpublished();
	\Pimcore\Model\DataObject\Concrete::setHideUnpublished(false);
	$currentData = \Pimcore\Model\DataObject\Service::useInheritedValues(false, function() {
		return $this->getSticker4x6iwasku();
	});
	\Pimcore\Model\DataObject\Concrete::setHideUnpublished($hideUnpublished);
	$isEqual = $fd->isEqual($currentData, $sticker4x6iwasku);
	if (!$isEqual) {
		$this->markFieldDirty("sticker4x6iwasku", true);
	}
	$this->sticker4x6iwasku = $fd->preSetData($this, $sticker4x6iwasku);
	return $this;
}

/**
* Get wisersellJson - Wisersell Json
* @return string|null
*/
public function getWisersellJson(): ?string
{
	if ($this instanceof PreGetValueHookInterface && !\Pimcore::inAdmin()) {
		$preValue = $this->preGetValue("wisersellJson");
		if ($preValue !== null) {
			return $preValue;
		}
	}

	$data = $this->wisersellJson;

	if (\Pimcore\Model\DataObject::doGetInheritedValues() && $this->getClass()->getFieldDefinition("wisersellJson")->isEmpty($data)) {
		try {
			return $this->getValueFromParent("wisersellJson");
		} catch (InheritanceParentNotFoundException $e) {
			// no data from parent available, continue ...
		}
	}

	if ($data instanceof \Pimcore\Model\DataObject\Data\EncryptedField) {
		return $data->getPlain();
	}

	return $data;
}

/**
* Set wisersellJson - Wisersell Json
* @param string|null $wisersellJson
* @return $this
*/
public function setWisersellJson(?string $wisersellJson): static
{
	$this->markFieldDirty("wisersellJson", true);

	$this->wisersellJson = $wisersellJson;

	return $this;
}

/**
* Get sticker4x6eu - Sticker4x6eu
* @return \Pimcore\Model\Asset|null
*/
public function getSticker4x6eu(): ?\Pimcore\Model\Element\AbstractElement
{
	if ($this instanceof PreGetValueHookInterface && !\Pimcore::inAdmin()) {
		$preValue = $this->preGetValue("sticker4x6eu");
		if ($preValue !== null) {
			return $preValue;
		}
	}

	$data = $this->getClass()->getFieldDefinition("sticker4x6eu")->preGetData($this);

	if (\Pimcore\Model\DataObject::doGetInheritedValues() && $this->getClass()->getFieldDefinition("sticker4x6eu")->isEmpty($data)) {
		try {
			return $this->getValueFromParent("sticker4x6eu");
		} catch (InheritanceParentNotFoundException $e) {
			// no data from parent available, continue ...
		}
	}

	if ($data instanceof \Pimcore\Model\DataObject\Data\EncryptedField) {
		return $data->getPlain();
	}

	return $data;
}

/**
* Set sticker4x6eu - Sticker4x6eu
* @param \Pimcore\Model\Asset|null $sticker4x6eu
* @return $this
*/
public function setSticker4x6eu(?\Pimcore\Model\Element\AbstractElement $sticker4x6eu): static
{
	/** @var \Pimcore\Model\DataObject\ClassDefinition\Data\ManyToOneRelation $fd */
	$fd = $this->getClass()->getFieldDefinition("sticker4x6eu");
	$hideUnpublished = \Pimcore\Model\DataObject\Concrete::getHideUnpublished();
	\Pimcore\Model\DataObject\Concrete::setHideUnpublished(false);
	$currentData = \Pimcore\Model\DataObject\Service::useInheritedValues(false, function() {
		return $this->getSticker4x6eu();
	});
	\Pimcore\Model\DataObject\Concrete::setHideUnpublished($hideUnpublished);
	$isEqual = $fd->isEqual($currentData, $sticker4x6eu);
	if (!$isEqual) {
		$this->markFieldDirty("sticker4x6eu", true);
	}
	$this->sticker4x6eu = $fd->preSetData($this, $sticker4x6eu);
	return $this;
}

/**
* Get productLevel - Level
* @return mixed
*/
public function getProductLevel(): mixed
{
	$data = new \Pimcore\Model\DataObject\Data\CalculatedValue('productLevel');
	$data->setContextualData("object", null, null, null);
	$object = $this;
	$data = \Pimcore\Model\DataObject\Service::getCalculatedFieldValue($object, $data);

	return $data;
}

/**
* Get listingsCount - Listings Count
* @return mixed
*/
public function getListingsCount(): mixed
{
	$data = new \Pimcore\Model\DataObject\Data\CalculatedValue('listingsCount');
	$data->setContextualData("object", null, null, null);
	$object = $this;
	$data = \Pimcore\Model\DataObject\Service::getCalculatedFieldValue($object, $data);

	return $data;
}

/**
* Get requiresIwasku - Requires Iwasku
* @return bool|null
*/
public function getRequiresIwasku(): ?bool
{
	if ($this instanceof PreGetValueHookInterface && !\Pimcore::inAdmin()) {
		$preValue = $this->preGetValue("requiresIwasku");
		if ($preValue !== null) {
			return $preValue;
		}
	}

	$data = $this->requiresIwasku;

	if (\Pimcore\Model\DataObject::doGetInheritedValues() && $this->getClass()->getFieldDefinition("requiresIwasku")->isEmpty($data)) {
		try {
			return $this->getValueFromParent("requiresIwasku");
		} catch (InheritanceParentNotFoundException $e) {
			// no data from parent available, continue ...
		}
	}

	if ($data instanceof \Pimcore\Model\DataObject\Data\EncryptedField) {
		return $data->getPlain();
	}

	return $data;
}

/**
* Set requiresIwasku - Requires Iwasku
* @param bool|null $requiresIwasku
* @return $this
*/
public function setRequiresIwasku(?bool $requiresIwasku): static
{
	$this->markFieldDirty("requiresIwasku", true);

	$this->requiresIwasku = $requiresIwasku;

	return $this;
}

/**
* Get variantDescription - Variant Açıklama
* @return string|null
*/
public function getVariantDescription(): ?string
{
	if ($this instanceof PreGetValueHookInterface && !\Pimcore::inAdmin()) {
		$preValue = $this->preGetValue("variantDescription");
		if ($preValue !== null) {
			return $preValue;
		}
	}

	$data = $this->variantDescription;

	if (\Pimcore\Model\DataObject::doGetInheritedValues() && $this->getClass()->getFieldDefinition("variantDescription")->isEmpty($data)) {
		try {
			return $this->getValueFromParent("variantDescription");
		} catch (InheritanceParentNotFoundException $e) {
			// no data from parent available, continue ...
		}
	}

	if ($data instanceof \Pimcore\Model\DataObject\Data\EncryptedField) {
		return $data->getPlain();
	}

	return $data;
}

/**
* Set variantDescription - Variant Açıklama
* @param string|null $variantDescription
* @return $this
*/
public function setVariantDescription(?string $variantDescription): static
{
	$this->markFieldDirty("variantDescription", true);

	$this->variantDescription = $variantDescription;

	return $this;
}

/**
* Get technicals - Teknik Doküman ve Kılavuzlar
* @return \Pimcore\Model\Asset[]
*/
public function getTechnicals(): array
{
	if ($this instanceof PreGetValueHookInterface && !\Pimcore::inAdmin()) {
		$preValue = $this->preGetValue("technicals");
		if ($preValue !== null) {
			return $preValue;
		}
	}

	$data = $this->getClass()->getFieldDefinition("technicals")->preGetData($this);

	if (\Pimcore\Model\DataObject::doGetInheritedValues() && $this->getClass()->getFieldDefinition("technicals")->isEmpty($data)) {
		try {
			return $this->getValueFromParent("technicals");
		} catch (InheritanceParentNotFoundException $e) {
			// no data from parent available, continue ...
		}
	}

	if ($data instanceof \Pimcore\Model\DataObject\Data\EncryptedField) {
		return $data->getPlain();
	}

	return $data;
}

/**
* Set technicals - Teknik Doküman ve Kılavuzlar
* @param \Pimcore\Model\Asset[] $technicals
* @return $this
*/
public function setTechnicals(?array $technicals): static
{
	/** @var \Pimcore\Model\DataObject\ClassDefinition\Data\ManyToManyRelation $fd */
	$fd = $this->getClass()->getFieldDefinition("technicals");
	$hideUnpublished = \Pimcore\Model\DataObject\Concrete::getHideUnpublished();
	\Pimcore\Model\DataObject\Concrete::setHideUnpublished(false);
	$currentData = \Pimcore\Model\DataObject\Service::useInheritedValues(false, function() {
		return $this->getTechnicals();
	});
	\Pimcore\Model\DataObject\Concrete::setHideUnpublished($hideUnpublished);
	$isEqual = $fd->isEqual($currentData, $technicals);
	if (!$isEqual) {
		$this->markFieldDirty("technicals", true);
	}
	$this->technicals = $fd->preSetData($this, $technicals);
	return $this;
}

/**
* Get album - Ürün Görselleri
* @return \Pimcore\Model\DataObject\Data\ImageGallery|null
*/
public function getAlbum(): ?\Pimcore\Model\DataObject\Data\ImageGallery
{
	if ($this instanceof PreGetValueHookInterface && !\Pimcore::inAdmin()) {
		$preValue = $this->preGetValue("album");
		if ($preValue !== null) {
			return $preValue;
		}
	}

	$data = $this->album;

	if (\Pimcore\Model\DataObject::doGetInheritedValues() && $this->getClass()->getFieldDefinition("album")->isEmpty($data)) {
		try {
			return $this->getValueFromParent("album");
		} catch (InheritanceParentNotFoundException $e) {
			// no data from parent available, continue ...
		}
	}

	if ($data instanceof \Pimcore\Model\DataObject\Data\EncryptedField) {
		return $data->getPlain();
	}

	return $data;
}

/**
* Set album - Ürün Görselleri
* @param \Pimcore\Model\DataObject\Data\ImageGallery|null $album
* @return $this
*/
public function setAlbum(?\Pimcore\Model\DataObject\Data\ImageGallery $album): static
{
	$this->markFieldDirty("album", true);

	$this->album = $album;

	return $this;
}

/**
* @return \Pimcore\Model\DataObject\Fieldcollection|null
*/
public function getProductAlbum(): ?\Pimcore\Model\DataObject\Fieldcollection
{
	if ($this instanceof PreGetValueHookInterface && !\Pimcore::inAdmin()) {
		$preValue = $this->preGetValue("productAlbum");
		if ($preValue !== null) {
			return $preValue;
		}
	}

	$data = $this->getClass()->getFieldDefinition("productAlbum")->preGetData($this);
	return $data;
}

/**
* Set productAlbum - Ürün Görselleri
* @param \Pimcore\Model\DataObject\Fieldcollection|null $productAlbum
* @return $this
*/
public function setProductAlbum(?\Pimcore\Model\DataObject\Fieldcollection $productAlbum): static
{
	/** @var \Pimcore\Model\DataObject\ClassDefinition\Data\Fieldcollections $fd */
	$fd = $this->getClass()->getFieldDefinition("productAlbum");
	$this->productAlbum = $fd->preSetData($this, $productAlbum);
	return $this;
}

/**
* Get dimensionsPostponed - Ertelendi
* @return bool|null
*/
public function getDimensionsPostponed(): ?bool
{
	if ($this instanceof PreGetValueHookInterface && !\Pimcore::inAdmin()) {
		$preValue = $this->preGetValue("dimensionsPostponed");
		if ($preValue !== null) {
			return $preValue;
		}
	}

	$data = $this->dimensionsPostponed;

	if (\Pimcore\Model\DataObject::doGetInheritedValues() && $this->getClass()->getFieldDefinition("dimensionsPostponed")->isEmpty($data)) {
		try {
			return $this->getValueFromParent("dimensionsPostponed");
		} catch (InheritanceParentNotFoundException $e) {
			// no data from parent available, continue ...
		}
	}

	if ($data instanceof \Pimcore\Model\DataObject\Data\EncryptedField) {
		return $data->getPlain();
	}

	return $data;
}

/**
* Set dimensionsPostponed - Ertelendi
* @param bool|null $dimensionsPostponed
* @return $this
*/
public function setDimensionsPostponed(?bool $dimensionsPostponed): static
{
	$this->markFieldDirty("dimensionsPostponed", true);

	$this->dimensionsPostponed = $dimensionsPostponed;

	return $this;
}

/**
* Get productDimension1 - En
* @return float|null
*/
public function getProductDimension1(): ?float
{
	if ($this instanceof PreGetValueHookInterface && !\Pimcore::inAdmin()) {
		$preValue = $this->preGetValue("productDimension1");
		if ($preValue !== null) {
			return $preValue;
		}
	}

	$data = $this->productDimension1;

	if (\Pimcore\Model\DataObject::doGetInheritedValues() && $this->getClass()->getFieldDefinition("productDimension1")->isEmpty($data)) {
		try {
			return $this->getValueFromParent("productDimension1");
		} catch (InheritanceParentNotFoundException $e) {
			// no data from parent available, continue ...
		}
	}

	if ($data instanceof \Pimcore\Model\DataObject\Data\EncryptedField) {
		return $data->getPlain();
	}

	return $data;
}

/**
* Set productDimension1 - En
* @param float|null $productDimension1
* @return $this
*/
public function setProductDimension1(?float $productDimension1): static
{
	/** @var \Pimcore\Model\DataObject\ClassDefinition\Data\Numeric $fd */
	$fd = $this->getClass()->getFieldDefinition("productDimension1");
	$this->productDimension1 = $fd->preSetData($this, $productDimension1);
	return $this;
}

/**
* Get productDimension2 - Boy
* @return float|null
*/
public function getProductDimension2(): ?float
{
	if ($this instanceof PreGetValueHookInterface && !\Pimcore::inAdmin()) {
		$preValue = $this->preGetValue("productDimension2");
		if ($preValue !== null) {
			return $preValue;
		}
	}

	$data = $this->productDimension2;

	if (\Pimcore\Model\DataObject::doGetInheritedValues() && $this->getClass()->getFieldDefinition("productDimension2")->isEmpty($data)) {
		try {
			return $this->getValueFromParent("productDimension2");
		} catch (InheritanceParentNotFoundException $e) {
			// no data from parent available, continue ...
		}
	}

	if ($data instanceof \Pimcore\Model\DataObject\Data\EncryptedField) {
		return $data->getPlain();
	}

	return $data;
}

/**
* Set productDimension2 - Boy
* @param float|null $productDimension2
* @return $this
*/
public function setProductDimension2(?float $productDimension2): static
{
	/** @var \Pimcore\Model\DataObject\ClassDefinition\Data\Numeric $fd */
	$fd = $this->getClass()->getFieldDefinition("productDimension2");
	$this->productDimension2 = $fd->preSetData($this, $productDimension2);
	return $this;
}

/**
* Get productDimension3 - Yükseklik
* @return float|null
*/
public function getProductDimension3(): ?float
{
	if ($this instanceof PreGetValueHookInterface && !\Pimcore::inAdmin()) {
		$preValue = $this->preGetValue("productDimension3");
		if ($preValue !== null) {
			return $preValue;
		}
	}

	$data = $this->productDimension3;

	if (\Pimcore\Model\DataObject::doGetInheritedValues() && $this->getClass()->getFieldDefinition("productDimension3")->isEmpty($data)) {
		try {
			return $this->getValueFromParent("productDimension3");
		} catch (InheritanceParentNotFoundException $e) {
			// no data from parent available, continue ...
		}
	}

	if ($data instanceof \Pimcore\Model\DataObject\Data\EncryptedField) {
		return $data->getPlain();
	}

	return $data;
}

/**
* Set productDimension3 - Yükseklik
* @param float|null $productDimension3
* @return $this
*/
public function setProductDimension3(?float $productDimension3): static
{
	/** @var \Pimcore\Model\DataObject\ClassDefinition\Data\Numeric $fd */
	$fd = $this->getClass()->getFieldDefinition("productDimension3");
	$this->productDimension3 = $fd->preSetData($this, $productDimension3);
	return $this;
}

/**
* Get productWeight - Ağırlık
* @return float|null
*/
public function getProductWeight(): ?float
{
	if ($this instanceof PreGetValueHookInterface && !\Pimcore::inAdmin()) {
		$preValue = $this->preGetValue("productWeight");
		if ($preValue !== null) {
			return $preValue;
		}
	}

	$data = $this->productWeight;

	if (\Pimcore\Model\DataObject::doGetInheritedValues() && $this->getClass()->getFieldDefinition("productWeight")->isEmpty($data)) {
		try {
			return $this->getValueFromParent("productWeight");
		} catch (InheritanceParentNotFoundException $e) {
			// no data from parent available, continue ...
		}
	}

	if ($data instanceof \Pimcore\Model\DataObject\Data\EncryptedField) {
		return $data->getPlain();
	}

	return $data;
}

/**
* Set productWeight - Ağırlık
* @param float|null $productWeight
* @return $this
*/
public function setProductWeight(?float $productWeight): static
{
	/** @var \Pimcore\Model\DataObject\ClassDefinition\Data\Numeric $fd */
	$fd = $this->getClass()->getFieldDefinition("productWeight");
	$this->productWeight = $fd->preSetData($this, $productWeight);
	return $this;
}

/**
* Get packageDimension1 - En
* @return float|null
*/
public function getPackageDimension1(): ?float
{
	if ($this instanceof PreGetValueHookInterface && !\Pimcore::inAdmin()) {
		$preValue = $this->preGetValue("packageDimension1");
		if ($preValue !== null) {
			return $preValue;
		}
	}

	$data = $this->packageDimension1;

	if (\Pimcore\Model\DataObject::doGetInheritedValues() && $this->getClass()->getFieldDefinition("packageDimension1")->isEmpty($data)) {
		try {
			return $this->getValueFromParent("packageDimension1");
		} catch (InheritanceParentNotFoundException $e) {
			// no data from parent available, continue ...
		}
	}

	if ($data instanceof \Pimcore\Model\DataObject\Data\EncryptedField) {
		return $data->getPlain();
	}

	return $data;
}

/**
* Set packageDimension1 - En
* @param float|null $packageDimension1
* @return $this
*/
public function setPackageDimension1(?float $packageDimension1): static
{
	/** @var \Pimcore\Model\DataObject\ClassDefinition\Data\Numeric $fd */
	$fd = $this->getClass()->getFieldDefinition("packageDimension1");
	$this->packageDimension1 = $fd->preSetData($this, $packageDimension1);
	return $this;
}

/**
* Get packageDimension2 - Boy
* @return float|null
*/
public function getPackageDimension2(): ?float
{
	if ($this instanceof PreGetValueHookInterface && !\Pimcore::inAdmin()) {
		$preValue = $this->preGetValue("packageDimension2");
		if ($preValue !== null) {
			return $preValue;
		}
	}

	$data = $this->packageDimension2;

	if (\Pimcore\Model\DataObject::doGetInheritedValues() && $this->getClass()->getFieldDefinition("packageDimension2")->isEmpty($data)) {
		try {
			return $this->getValueFromParent("packageDimension2");
		} catch (InheritanceParentNotFoundException $e) {
			// no data from parent available, continue ...
		}
	}

	if ($data instanceof \Pimcore\Model\DataObject\Data\EncryptedField) {
		return $data->getPlain();
	}

	return $data;
}

/**
* Set packageDimension2 - Boy
* @param float|null $packageDimension2
* @return $this
*/
public function setPackageDimension2(?float $packageDimension2): static
{
	/** @var \Pimcore\Model\DataObject\ClassDefinition\Data\Numeric $fd */
	$fd = $this->getClass()->getFieldDefinition("packageDimension2");
	$this->packageDimension2 = $fd->preSetData($this, $packageDimension2);
	return $this;
}

/**
* Get packageDimension3 - Yükseklik
* @return float|null
*/
public function getPackageDimension3(): ?float
{
	if ($this instanceof PreGetValueHookInterface && !\Pimcore::inAdmin()) {
		$preValue = $this->preGetValue("packageDimension3");
		if ($preValue !== null) {
			return $preValue;
		}
	}

	$data = $this->packageDimension3;

	if (\Pimcore\Model\DataObject::doGetInheritedValues() && $this->getClass()->getFieldDefinition("packageDimension3")->isEmpty($data)) {
		try {
			return $this->getValueFromParent("packageDimension3");
		} catch (InheritanceParentNotFoundException $e) {
			// no data from parent available, continue ...
		}
	}

	if ($data instanceof \Pimcore\Model\DataObject\Data\EncryptedField) {
		return $data->getPlain();
	}

	return $data;
}

/**
* Set packageDimension3 - Yükseklik
* @param float|null $packageDimension3
* @return $this
*/
public function setPackageDimension3(?float $packageDimension3): static
{
	/** @var \Pimcore\Model\DataObject\ClassDefinition\Data\Numeric $fd */
	$fd = $this->getClass()->getFieldDefinition("packageDimension3");
	$this->packageDimension3 = $fd->preSetData($this, $packageDimension3);
	return $this;
}

/**
* Get packageWeight - Ağırlık
* @return float|null
*/
public function getPackageWeight(): ?float
{
	if ($this instanceof PreGetValueHookInterface && !\Pimcore::inAdmin()) {
		$preValue = $this->preGetValue("packageWeight");
		if ($preValue !== null) {
			return $preValue;
		}
	}

	$data = $this->packageWeight;

	if (\Pimcore\Model\DataObject::doGetInheritedValues() && $this->getClass()->getFieldDefinition("packageWeight")->isEmpty($data)) {
		try {
			return $this->getValueFromParent("packageWeight");
		} catch (InheritanceParentNotFoundException $e) {
			// no data from parent available, continue ...
		}
	}

	if ($data instanceof \Pimcore\Model\DataObject\Data\EncryptedField) {
		return $data->getPlain();
	}

	return $data;
}

/**
* Set packageWeight - Ağırlık
* @param float|null $packageWeight
* @return $this
*/
public function setPackageWeight(?float $packageWeight): static
{
	/** @var \Pimcore\Model\DataObject\ClassDefinition\Data\Numeric $fd */
	$fd = $this->getClass()->getFieldDefinition("packageWeight");
	$this->packageWeight = $fd->preSetData($this, $packageWeight);
	return $this;
}

/**
* Get desi5000 - Desi 5000
* @return mixed
*/
public function getDesi5000(): mixed
{
	$data = new \Pimcore\Model\DataObject\Data\CalculatedValue('desi5000');
	$data->setContextualData("object", null, null, null);
	$object = $this;
	$data = \Pimcore\Model\DataObject\Service::getCalculatedFieldValue($object, $data);

	return $data;
}

/**
* Get boxDimension1 - En
* @return float|null
*/
public function getBoxDimension1(): ?float
{
	if ($this instanceof PreGetValueHookInterface && !\Pimcore::inAdmin()) {
		$preValue = $this->preGetValue("boxDimension1");
		if ($preValue !== null) {
			return $preValue;
		}
	}

	$data = $this->boxDimension1;

	if (\Pimcore\Model\DataObject::doGetInheritedValues() && $this->getClass()->getFieldDefinition("boxDimension1")->isEmpty($data)) {
		try {
			return $this->getValueFromParent("boxDimension1");
		} catch (InheritanceParentNotFoundException $e) {
			// no data from parent available, continue ...
		}
	}

	if ($data instanceof \Pimcore\Model\DataObject\Data\EncryptedField) {
		return $data->getPlain();
	}

	return $data;
}

/**
* Set boxDimension1 - En
* @param float|null $boxDimension1
* @return $this
*/
public function setBoxDimension1(?float $boxDimension1): static
{
	/** @var \Pimcore\Model\DataObject\ClassDefinition\Data\Numeric $fd */
	$fd = $this->getClass()->getFieldDefinition("boxDimension1");
	$this->boxDimension1 = $fd->preSetData($this, $boxDimension1);
	return $this;
}

/**
* Get boxDimension2 - Boy
* @return float|null
*/
public function getBoxDimension2(): ?float
{
	if ($this instanceof PreGetValueHookInterface && !\Pimcore::inAdmin()) {
		$preValue = $this->preGetValue("boxDimension2");
		if ($preValue !== null) {
			return $preValue;
		}
	}

	$data = $this->boxDimension2;

	if (\Pimcore\Model\DataObject::doGetInheritedValues() && $this->getClass()->getFieldDefinition("boxDimension2")->isEmpty($data)) {
		try {
			return $this->getValueFromParent("boxDimension2");
		} catch (InheritanceParentNotFoundException $e) {
			// no data from parent available, continue ...
		}
	}

	if ($data instanceof \Pimcore\Model\DataObject\Data\EncryptedField) {
		return $data->getPlain();
	}

	return $data;
}

/**
* Set boxDimension2 - Boy
* @param float|null $boxDimension2
* @return $this
*/
public function setBoxDimension2(?float $boxDimension2): static
{
	/** @var \Pimcore\Model\DataObject\ClassDefinition\Data\Numeric $fd */
	$fd = $this->getClass()->getFieldDefinition("boxDimension2");
	$this->boxDimension2 = $fd->preSetData($this, $boxDimension2);
	return $this;
}

/**
* Get boxDimension3 - Yükseklik
* @return float|null
*/
public function getBoxDimension3(): ?float
{
	if ($this instanceof PreGetValueHookInterface && !\Pimcore::inAdmin()) {
		$preValue = $this->preGetValue("boxDimension3");
		if ($preValue !== null) {
			return $preValue;
		}
	}

	$data = $this->boxDimension3;

	if (\Pimcore\Model\DataObject::doGetInheritedValues() && $this->getClass()->getFieldDefinition("boxDimension3")->isEmpty($data)) {
		try {
			return $this->getValueFromParent("boxDimension3");
		} catch (InheritanceParentNotFoundException $e) {
			// no data from parent available, continue ...
		}
	}

	if ($data instanceof \Pimcore\Model\DataObject\Data\EncryptedField) {
		return $data->getPlain();
	}

	return $data;
}

/**
* Set boxDimension3 - Yükseklik
* @param float|null $boxDimension3
* @return $this
*/
public function setBoxDimension3(?float $boxDimension3): static
{
	/** @var \Pimcore\Model\DataObject\ClassDefinition\Data\Numeric $fd */
	$fd = $this->getClass()->getFieldDefinition("boxDimension3");
	$this->boxDimension3 = $fd->preSetData($this, $boxDimension3);
	return $this;
}

/**
* Get boxWeight - Ağırlık
* @return float|null
*/
public function getBoxWeight(): ?float
{
	if ($this instanceof PreGetValueHookInterface && !\Pimcore::inAdmin()) {
		$preValue = $this->preGetValue("boxWeight");
		if ($preValue !== null) {
			return $preValue;
		}
	}

	$data = $this->boxWeight;

	if (\Pimcore\Model\DataObject::doGetInheritedValues() && $this->getClass()->getFieldDefinition("boxWeight")->isEmpty($data)) {
		try {
			return $this->getValueFromParent("boxWeight");
		} catch (InheritanceParentNotFoundException $e) {
			// no data from parent available, continue ...
		}
	}

	if ($data instanceof \Pimcore\Model\DataObject\Data\EncryptedField) {
		return $data->getPlain();
	}

	return $data;
}

/**
* Set boxWeight - Ağırlık
* @param float|null $boxWeight
* @return $this
*/
public function setBoxWeight(?float $boxWeight): static
{
	/** @var \Pimcore\Model\DataObject\ClassDefinition\Data\Numeric $fd */
	$fd = $this->getClass()->getFieldDefinition("boxWeight");
	$this->boxWeight = $fd->preSetData($this, $boxWeight);
	return $this;
}

/**
* Get inBoxCount - Koli İçi Mevcut
* @return float|null
*/
public function getInBoxCount(): ?float
{
	if ($this instanceof PreGetValueHookInterface && !\Pimcore::inAdmin()) {
		$preValue = $this->preGetValue("inBoxCount");
		if ($preValue !== null) {
			return $preValue;
		}
	}

	$data = $this->inBoxCount;

	if (\Pimcore\Model\DataObject::doGetInheritedValues() && $this->getClass()->getFieldDefinition("inBoxCount")->isEmpty($data)) {
		try {
			return $this->getValueFromParent("inBoxCount");
		} catch (InheritanceParentNotFoundException $e) {
			// no data from parent available, continue ...
		}
	}

	if ($data instanceof \Pimcore\Model\DataObject\Data\EncryptedField) {
		return $data->getPlain();
	}

	return $data;
}

/**
* Set inBoxCount - Koli İçi Mevcut
* @param float|null $inBoxCount
* @return $this
*/
public function setInBoxCount(?float $inBoxCount): static
{
	/** @var \Pimcore\Model\DataObject\ClassDefinition\Data\Numeric $fd */
	$fd = $this->getClass()->getFieldDefinition("inBoxCount");
	$this->inBoxCount = $fd->preSetData($this, $inBoxCount);
	return $this;
}

/**
* Get inPaletteCount - Palet İçi Mevcut
* @return float|null
*/
public function getInPaletteCount(): ?float
{
	if ($this instanceof PreGetValueHookInterface && !\Pimcore::inAdmin()) {
		$preValue = $this->preGetValue("inPaletteCount");
		if ($preValue !== null) {
			return $preValue;
		}
	}

	$data = $this->inPaletteCount;

	if (\Pimcore\Model\DataObject::doGetInheritedValues() && $this->getClass()->getFieldDefinition("inPaletteCount")->isEmpty($data)) {
		try {
			return $this->getValueFromParent("inPaletteCount");
		} catch (InheritanceParentNotFoundException $e) {
			// no data from parent available, continue ...
		}
	}

	if ($data instanceof \Pimcore\Model\DataObject\Data\EncryptedField) {
		return $data->getPlain();
	}

	return $data;
}

/**
* Set inPaletteCount - Palet İçi Mevcut
* @param float|null $inPaletteCount
* @return $this
*/
public function setInPaletteCount(?float $inPaletteCount): static
{
	/** @var \Pimcore\Model\DataObject\ClassDefinition\Data\Numeric $fd */
	$fd = $this->getClass()->getFieldDefinition("inPaletteCount");
	$this->inPaletteCount = $fd->preSetData($this, $inPaletteCount);
	return $this;
}

/**
* Get inContainerCount - Konteyner İçi Mevcut
* @return float|null
*/
public function getInContainerCount(): ?float
{
	if ($this instanceof PreGetValueHookInterface && !\Pimcore::inAdmin()) {
		$preValue = $this->preGetValue("inContainerCount");
		if ($preValue !== null) {
			return $preValue;
		}
	}

	$data = $this->inContainerCount;

	if (\Pimcore\Model\DataObject::doGetInheritedValues() && $this->getClass()->getFieldDefinition("inContainerCount")->isEmpty($data)) {
		try {
			return $this->getValueFromParent("inContainerCount");
		} catch (InheritanceParentNotFoundException $e) {
			// no data from parent available, continue ...
		}
	}

	if ($data instanceof \Pimcore\Model\DataObject\Data\EncryptedField) {
		return $data->getPlain();
	}

	return $data;
}

/**
* Set inContainerCount - Konteyner İçi Mevcut
* @param float|null $inContainerCount
* @return $this
*/
public function setInContainerCount(?float $inContainerCount): static
{
	/** @var \Pimcore\Model\DataObject\ClassDefinition\Data\Numeric $fd */
	$fd = $this->getClass()->getFieldDefinition("inContainerCount");
	$this->inContainerCount = $fd->preSetData($this, $inContainerCount);
	return $this;
}

/**
* Get seoTitle - SEO Başlığı (&lt;h1&gt;)
* @return string|null
*/
public function getSeoTitle(): ?string
{
	if ($this instanceof PreGetValueHookInterface && !\Pimcore::inAdmin()) {
		$preValue = $this->preGetValue("seoTitle");
		if ($preValue !== null) {
			return $preValue;
		}
	}

	$data = $this->seoTitle;

	if (\Pimcore\Model\DataObject::doGetInheritedValues() && $this->getClass()->getFieldDefinition("seoTitle")->isEmpty($data)) {
		try {
			return $this->getValueFromParent("seoTitle");
		} catch (InheritanceParentNotFoundException $e) {
			// no data from parent available, continue ...
		}
	}

	if ($data instanceof \Pimcore\Model\DataObject\Data\EncryptedField) {
		return $data->getPlain();
	}

	return $data;
}

/**
* Set seoTitle - SEO Başlığı (&lt;h1&gt;)
* @param string|null $seoTitle
* @return $this
*/
public function setSeoTitle(?string $seoTitle): static
{
	$this->markFieldDirty("seoTitle", true);

	$this->seoTitle = $seoTitle;

	return $this;
}

/**
* Get seoTitleEn - İngilizce
* @return string|null
*/
public function getSeoTitleEn(): ?string
{
	if ($this instanceof PreGetValueHookInterface && !\Pimcore::inAdmin()) {
		$preValue = $this->preGetValue("seoTitleEn");
		if ($preValue !== null) {
			return $preValue;
		}
	}

	$data = $this->seoTitleEn;

	if (\Pimcore\Model\DataObject::doGetInheritedValues() && $this->getClass()->getFieldDefinition("seoTitleEn")->isEmpty($data)) {
		try {
			return $this->getValueFromParent("seoTitleEn");
		} catch (InheritanceParentNotFoundException $e) {
			// no data from parent available, continue ...
		}
	}

	if ($data instanceof \Pimcore\Model\DataObject\Data\EncryptedField) {
		return $data->getPlain();
	}

	return $data;
}

/**
* Set seoTitleEn - İngilizce
* @param string|null $seoTitleEn
* @return $this
*/
public function setSeoTitleEn(?string $seoTitleEn): static
{
	$this->markFieldDirty("seoTitleEn", true);

	$this->seoTitleEn = $seoTitleEn;

	return $this;
}

/**
* Get seoDescription - SEO Açıklama
* @return string|null
*/
public function getSeoDescription(): ?string
{
	if ($this instanceof PreGetValueHookInterface && !\Pimcore::inAdmin()) {
		$preValue = $this->preGetValue("seoDescription");
		if ($preValue !== null) {
			return $preValue;
		}
	}

	$data = $this->seoDescription;

	if (\Pimcore\Model\DataObject::doGetInheritedValues() && $this->getClass()->getFieldDefinition("seoDescription")->isEmpty($data)) {
		try {
			return $this->getValueFromParent("seoDescription");
		} catch (InheritanceParentNotFoundException $e) {
			// no data from parent available, continue ...
		}
	}

	if ($data instanceof \Pimcore\Model\DataObject\Data\EncryptedField) {
		return $data->getPlain();
	}

	return $data;
}

/**
* Set seoDescription - SEO Açıklama
* @param string|null $seoDescription
* @return $this
*/
public function setSeoDescription(?string $seoDescription): static
{
	$this->markFieldDirty("seoDescription", true);

	$this->seoDescription = $seoDescription;

	return $this;
}

/**
* Get seoDescriptionEn - İngilizce
* @return string|null
*/
public function getSeoDescriptionEn(): ?string
{
	if ($this instanceof PreGetValueHookInterface && !\Pimcore::inAdmin()) {
		$preValue = $this->preGetValue("seoDescriptionEn");
		if ($preValue !== null) {
			return $preValue;
		}
	}

	$data = $this->seoDescriptionEn;

	if (\Pimcore\Model\DataObject::doGetInheritedValues() && $this->getClass()->getFieldDefinition("seoDescriptionEn")->isEmpty($data)) {
		try {
			return $this->getValueFromParent("seoDescriptionEn");
		} catch (InheritanceParentNotFoundException $e) {
			// no data from parent available, continue ...
		}
	}

	if ($data instanceof \Pimcore\Model\DataObject\Data\EncryptedField) {
		return $data->getPlain();
	}

	return $data;
}

/**
* Set seoDescriptionEn - İngilizce
* @param string|null $seoDescriptionEn
* @return $this
*/
public function setSeoDescriptionEn(?string $seoDescriptionEn): static
{
	$this->markFieldDirty("seoDescriptionEn", true);

	$this->seoDescriptionEn = $seoDescriptionEn;

	return $this;
}

/**
* Get seoKeywords - SEO Anahtar Kelimeler
* @return string|null
*/
public function getSeoKeywords(): ?string
{
	if ($this instanceof PreGetValueHookInterface && !\Pimcore::inAdmin()) {
		$preValue = $this->preGetValue("seoKeywords");
		if ($preValue !== null) {
			return $preValue;
		}
	}

	$data = $this->seoKeywords;

	if (\Pimcore\Model\DataObject::doGetInheritedValues() && $this->getClass()->getFieldDefinition("seoKeywords")->isEmpty($data)) {
		try {
			return $this->getValueFromParent("seoKeywords");
		} catch (InheritanceParentNotFoundException $e) {
			// no data from parent available, continue ...
		}
	}

	if ($data instanceof \Pimcore\Model\DataObject\Data\EncryptedField) {
		return $data->getPlain();
	}

	return $data;
}

/**
* Set seoKeywords - SEO Anahtar Kelimeler
* @param string|null $seoKeywords
* @return $this
*/
public function setSeoKeywords(?string $seoKeywords): static
{
	$this->markFieldDirty("seoKeywords", true);

	$this->seoKeywords = $seoKeywords;

	return $this;
}

/**
* Get seoKeywordsEn - İngilizce
* @return string|null
*/
public function getSeoKeywordsEn(): ?string
{
	if ($this instanceof PreGetValueHookInterface && !\Pimcore::inAdmin()) {
		$preValue = $this->preGetValue("seoKeywordsEn");
		if ($preValue !== null) {
			return $preValue;
		}
	}

	$data = $this->seoKeywordsEn;

	if (\Pimcore\Model\DataObject::doGetInheritedValues() && $this->getClass()->getFieldDefinition("seoKeywordsEn")->isEmpty($data)) {
		try {
			return $this->getValueFromParent("seoKeywordsEn");
		} catch (InheritanceParentNotFoundException $e) {
			// no data from parent available, continue ...
		}
	}

	if ($data instanceof \Pimcore\Model\DataObject\Data\EncryptedField) {
		return $data->getPlain();
	}

	return $data;
}

/**
* Set seoKeywordsEn - İngilizce
* @param string|null $seoKeywordsEn
* @return $this
*/
public function setSeoKeywordsEn(?string $seoKeywordsEn): static
{
	$this->markFieldDirty("seoKeywordsEn", true);

	$this->seoKeywordsEn = $seoKeywordsEn;

	return $this;
}

/**
* Get listingItems - Listing Öğeleri
* @return \Pimcore\Model\DataObject\VariantProduct[]
*/
public function getListingItems(): array
{
	if ($this instanceof PreGetValueHookInterface && !\Pimcore::inAdmin()) {
		$preValue = $this->preGetValue("listingItems");
		if ($preValue !== null) {
			return $preValue;
		}
	}

	$data = $this->getClass()->getFieldDefinition("listingItems")->preGetData($this);

	if (\Pimcore\Model\DataObject::doGetInheritedValues() && $this->getClass()->getFieldDefinition("listingItems")->isEmpty($data)) {
		try {
			return $this->getValueFromParent("listingItems");
		} catch (InheritanceParentNotFoundException $e) {
			// no data from parent available, continue ...
		}
	}

	if ($data instanceof \Pimcore\Model\DataObject\Data\EncryptedField) {
		return $data->getPlain();
	}

	return $data;
}

/**
* Set listingItems - Listing Öğeleri
* @param \Pimcore\Model\DataObject\VariantProduct[] $listingItems
* @return $this
*/
public function setListingItems(?array $listingItems): static
{
	/** @var \Pimcore\Model\DataObject\ClassDefinition\Data\ManyToManyObjectRelation $fd */
	$fd = $this->getClass()->getFieldDefinition("listingItems");
	$hideUnpublished = \Pimcore\Model\DataObject\Concrete::getHideUnpublished();
	\Pimcore\Model\DataObject\Concrete::setHideUnpublished(false);
	$currentData = \Pimcore\Model\DataObject\Service::useInheritedValues(false, function() {
		return $this->getListingItems();
	});
	\Pimcore\Model\DataObject\Concrete::setHideUnpublished($hideUnpublished);
	$isEqual = $fd->isEqual($currentData, $listingItems);
	if (!$isEqual) {
		$this->markFieldDirty("listingItems", true);
	}
	$this->listingItems = $fd->preSetData($this, $listingItems);
	return $this;
}

/**
* Get brandItems - Satıldığı Markalar
* @return \Pimcore\Model\DataObject\Brand[]
*/
public function getBrandItems(): array
{
	if ($this instanceof PreGetValueHookInterface && !\Pimcore::inAdmin()) {
		$preValue = $this->preGetValue("brandItems");
		if ($preValue !== null) {
			return $preValue;
		}
	}

	$data = $this->getClass()->getFieldDefinition("brandItems")->preGetData($this);

	if (\Pimcore\Model\DataObject::doGetInheritedValues() && $this->getClass()->getFieldDefinition("brandItems")->isEmpty($data)) {
		try {
			return $this->getValueFromParent("brandItems");
		} catch (InheritanceParentNotFoundException $e) {
			// no data from parent available, continue ...
		}
	}

	if ($data instanceof \Pimcore\Model\DataObject\Data\EncryptedField) {
		return $data->getPlain();
	}

	return $data;
}

/**
* Set brandItems - Satıldığı Markalar
* @param \Pimcore\Model\DataObject\Brand[] $brandItems
* @return $this
*/
public function setBrandItems(?array $brandItems): static
{
	/** @var \Pimcore\Model\DataObject\ClassDefinition\Data\ManyToManyObjectRelation $fd */
	$fd = $this->getClass()->getFieldDefinition("brandItems");
	$hideUnpublished = \Pimcore\Model\DataObject\Concrete::getHideUnpublished();
	\Pimcore\Model\DataObject\Concrete::setHideUnpublished(false);
	$currentData = \Pimcore\Model\DataObject\Service::useInheritedValues(false, function() {
		return $this->getBrandItems();
	});
	\Pimcore\Model\DataObject\Concrete::setHideUnpublished($hideUnpublished);
	$isEqual = $fd->isEqual($currentData, $brandItems);
	if (!$isEqual) {
		$this->markFieldDirty("brandItems", true);
	}
	$this->brandItems = $fd->preSetData($this, $brandItems);
	return $this;
}

/**
* Get identifierControlled - Kontrol Edildi
* @return bool|null
*/
public function getIdentifierControlled(): ?bool
{
	if ($this instanceof PreGetValueHookInterface && !\Pimcore::inAdmin()) {
		$preValue = $this->preGetValue("identifierControlled");
		if ($preValue !== null) {
			return $preValue;
		}
	}

	$data = $this->identifierControlled;

	if (\Pimcore\Model\DataObject::doGetInheritedValues() && $this->getClass()->getFieldDefinition("identifierControlled")->isEmpty($data)) {
		try {
			return $this->getValueFromParent("identifierControlled");
		} catch (InheritanceParentNotFoundException $e) {
			// no data from parent available, continue ...
		}
	}

	if ($data instanceof \Pimcore\Model\DataObject\Data\EncryptedField) {
		return $data->getPlain();
	}

	return $data;
}

/**
* Set identifierControlled - Kontrol Edildi
* @param bool|null $identifierControlled
* @return $this
*/
public function setIdentifierControlled(?bool $identifierControlled): static
{
	$this->markFieldDirty("identifierControlled", true);

	$this->identifierControlled = $identifierControlled;

	return $this;
}

/**
* Get imageUrl - Örnek Listing
* @return \Pimcore\Model\DataObject\Data\ExternalImage|null
*/
public function getImageUrl(): ?\Pimcore\Model\DataObject\Data\ExternalImage
{
	if ($this instanceof PreGetValueHookInterface && !\Pimcore::inAdmin()) {
		$preValue = $this->preGetValue("imageUrl");
		if ($preValue !== null) {
			return $preValue;
		}
	}

	$data = $this->imageUrl;

	if (\Pimcore\Model\DataObject::doGetInheritedValues() && $this->getClass()->getFieldDefinition("imageUrl")->isEmpty($data)) {
		try {
			return $this->getValueFromParent("imageUrl");
		} catch (InheritanceParentNotFoundException $e) {
			// no data from parent available, continue ...
		}
	}

	if ($data instanceof \Pimcore\Model\DataObject\Data\EncryptedField) {
		return $data->getPlain();
	}

	return $data;
}

/**
* Set imageUrl - Örnek Listing
* @param \Pimcore\Model\DataObject\Data\ExternalImage|null $imageUrl
* @return $this
*/
public function setImageUrl(?\Pimcore\Model\DataObject\Data\ExternalImage $imageUrl): static
{
	$this->markFieldDirty("imageUrl", true);

	$this->imageUrl = $imageUrl;

	return $this;
}

/**
* Get bundleProducts - Set İçeriği
* @return \Pimcore\Model\DataObject\Data\ObjectMetadata[]
*/
public function getBundleProducts(): array
{
	if ($this instanceof PreGetValueHookInterface && !\Pimcore::inAdmin()) {
		$preValue = $this->preGetValue("bundleProducts");
		if ($preValue !== null) {
			return $preValue;
		}
	}

	$data = $this->getClass()->getFieldDefinition("bundleProducts")->preGetData($this);

	if (\Pimcore\Model\DataObject::doGetInheritedValues() && $this->getClass()->getFieldDefinition("bundleProducts")->isEmpty($data)) {
		try {
			return $this->getValueFromParent("bundleProducts");
		} catch (InheritanceParentNotFoundException $e) {
			// no data from parent available, continue ...
		}
	}

	if ($data instanceof \Pimcore\Model\DataObject\Data\EncryptedField) {
		return $data->getPlain();
	}

	return $data;
}

/**
* Set bundleProducts - Set İçeriği
* @param \Pimcore\Model\DataObject\Data\ObjectMetadata[] $bundleProducts
* @return $this
*/
public function setBundleProducts(?array $bundleProducts): static
{
	/** @var \Pimcore\Model\DataObject\ClassDefinition\Data\AdvancedManyToManyObjectRelation $fd */
	$fd = $this->getClass()->getFieldDefinition("bundleProducts");
	$hideUnpublished = \Pimcore\Model\DataObject\Concrete::getHideUnpublished();
	\Pimcore\Model\DataObject\Concrete::setHideUnpublished(false);
	$currentData = \Pimcore\Model\DataObject\Service::useInheritedValues(false, function() {
		return $this->getBundleProducts();
	});
	\Pimcore\Model\DataObject\Concrete::setHideUnpublished($hideUnpublished);
	$isEqual = $fd->isEqual($currentData, $bundleProducts);
	if (!$isEqual) {
		$this->markFieldDirty("bundleProducts", true);
	}
	$this->bundleProducts = $fd->preSetData($this, $bundleProducts);
	return $this;
}

/**
* Get listingUniqueIds - Listing Unique Ids
* @return string|null
*/
public function getListingUniqueIds(): ?string
{
	if ($this instanceof PreGetValueHookInterface && !\Pimcore::inAdmin()) {
		$preValue = $this->preGetValue("listingUniqueIds");
		if ($preValue !== null) {
			return $preValue;
		}
	}

	$data = $this->listingUniqueIds;

	if (\Pimcore\Model\DataObject::doGetInheritedValues() && $this->getClass()->getFieldDefinition("listingUniqueIds")->isEmpty($data)) {
		try {
			return $this->getValueFromParent("listingUniqueIds");
		} catch (InheritanceParentNotFoundException $e) {
			// no data from parent available, continue ...
		}
	}

	if ($data instanceof \Pimcore\Model\DataObject\Data\EncryptedField) {
		return $data->getPlain();
	}

	return $data;
}

/**
* Set listingUniqueIds - Listing Unique Ids
* @param string|null $listingUniqueIds
* @return $this
*/
public function setListingUniqueIds(?string $listingUniqueIds): static
{
	$this->markFieldDirty("listingUniqueIds", true);

	$this->listingUniqueIds = $listingUniqueIds;

	return $this;
}

/**
* Get productCost - Üretim Maliyeti
* @return mixed
*/
public function getProductCost(): mixed
{
	$data = new \Pimcore\Model\DataObject\Data\CalculatedValue('productCost');
	$data->setContextualData("object", null, null, null);
	$object = $this;
	$data = \Pimcore\Model\DataObject\Service::getCalculatedFieldValue($object, $data);

	return $data;
}

/**
* Get costModelProduct - Genel Maliyet Modeli
* @return \Pimcore\Model\DataObject\CostModel[]
*/
public function getCostModelProduct(): array
{
	if ($this instanceof PreGetValueHookInterface && !\Pimcore::inAdmin()) {
		$preValue = $this->preGetValue("costModelProduct");
		if ($preValue !== null) {
			return $preValue;
		}
	}

	$data = $this->getClass()->getFieldDefinition("costModelProduct")->preGetData($this);

	if (\Pimcore\Model\DataObject::doGetInheritedValues() && $this->getClass()->getFieldDefinition("costModelProduct")->isEmpty($data)) {
		try {
			return $this->getValueFromParent("costModelProduct");
		} catch (InheritanceParentNotFoundException $e) {
			// no data from parent available, continue ...
		}
	}

	if ($data instanceof \Pimcore\Model\DataObject\Data\EncryptedField) {
		return $data->getPlain();
	}

	return $data;
}

/**
* Set costModelProduct - Genel Maliyet Modeli
* @param \Pimcore\Model\DataObject\CostModel[] $costModelProduct
* @return $this
*/
public function setCostModelProduct(?array $costModelProduct): static
{
	/** @var \Pimcore\Model\DataObject\ClassDefinition\Data\ManyToManyObjectRelation $fd */
	$fd = $this->getClass()->getFieldDefinition("costModelProduct");
	$hideUnpublished = \Pimcore\Model\DataObject\Concrete::getHideUnpublished();
	\Pimcore\Model\DataObject\Concrete::setHideUnpublished(false);
	$currentData = \Pimcore\Model\DataObject\Service::useInheritedValues(false, function() {
		return $this->getCostModelProduct();
	});
	\Pimcore\Model\DataObject\Concrete::setHideUnpublished($hideUnpublished);
	$isEqual = $fd->isEqual($currentData, $costModelProduct);
	if (!$isEqual) {
		$this->markFieldDirty("costModelProduct", true);
	}
	$this->costModelProduct = $fd->preSetData($this, $costModelProduct);
	return $this;
}

/**
* Get costModelVariant - Varyant Maliyet Modeli
* @return \Pimcore\Model\DataObject\CostModel[]
*/
public function getCostModelVariant(): array
{
	if ($this instanceof PreGetValueHookInterface && !\Pimcore::inAdmin()) {
		$preValue = $this->preGetValue("costModelVariant");
		if ($preValue !== null) {
			return $preValue;
		}
	}

	$data = $this->getClass()->getFieldDefinition("costModelVariant")->preGetData($this);

	if (\Pimcore\Model\DataObject::doGetInheritedValues() && $this->getClass()->getFieldDefinition("costModelVariant")->isEmpty($data)) {
		try {
			return $this->getValueFromParent("costModelVariant");
		} catch (InheritanceParentNotFoundException $e) {
			// no data from parent available, continue ...
		}
	}

	if ($data instanceof \Pimcore\Model\DataObject\Data\EncryptedField) {
		return $data->getPlain();
	}

	return $data;
}

/**
* Set costModelVariant - Varyant Maliyet Modeli
* @param \Pimcore\Model\DataObject\CostModel[] $costModelVariant
* @return $this
*/
public function setCostModelVariant(?array $costModelVariant): static
{
	/** @var \Pimcore\Model\DataObject\ClassDefinition\Data\ManyToManyObjectRelation $fd */
	$fd = $this->getClass()->getFieldDefinition("costModelVariant");
	$hideUnpublished = \Pimcore\Model\DataObject\Concrete::getHideUnpublished();
	\Pimcore\Model\DataObject\Concrete::setHideUnpublished(false);
	$currentData = \Pimcore\Model\DataObject\Service::useInheritedValues(false, function() {
		return $this->getCostModelVariant();
	});
	\Pimcore\Model\DataObject\Concrete::setHideUnpublished($hideUnpublished);
	$isEqual = $fd->isEqual($currentData, $costModelVariant);
	if (!$isEqual) {
		$this->markFieldDirty("costModelVariant", true);
	}
	$this->costModelVariant = $fd->preSetData($this, $costModelVariant);
	return $this;
}

/**
* Get cutComplexity - Kesim Detay
* @return string|null
*/
public function getCutComplexity(): ?string
{
	if ($this instanceof PreGetValueHookInterface && !\Pimcore::inAdmin()) {
		$preValue = $this->preGetValue("cutComplexity");
		if ($preValue !== null) {
			return $preValue;
		}
	}

	$data = $this->cutComplexity;

	if (\Pimcore\Model\DataObject::doGetInheritedValues() && $this->getClass()->getFieldDefinition("cutComplexity")->isEmpty($data)) {
		try {
			return $this->getValueFromParent("cutComplexity");
		} catch (InheritanceParentNotFoundException $e) {
			// no data from parent available, continue ...
		}
	}

	if ($data instanceof \Pimcore\Model\DataObject\Data\EncryptedField) {
		return $data->getPlain();
	}

	return $data;
}

/**
* Set cutComplexity - Kesim Detay
* @param string|null $cutComplexity
* @return $this
*/
public function setCutComplexity(?string $cutComplexity): static
{
	$this->markFieldDirty("cutComplexity", true);

	$this->cutComplexity = $cutComplexity;

	return $this;
}

/**
* Get pricingControlled - Kontrol Edildi
* @return bool|null
*/
public function getPricingControlled(): ?bool
{
	if ($this instanceof PreGetValueHookInterface && !\Pimcore::inAdmin()) {
		$preValue = $this->preGetValue("pricingControlled");
		if ($preValue !== null) {
			return $preValue;
		}
	}

	$data = $this->pricingControlled;

	if (\Pimcore\Model\DataObject::doGetInheritedValues() && $this->getClass()->getFieldDefinition("pricingControlled")->isEmpty($data)) {
		try {
			return $this->getValueFromParent("pricingControlled");
		} catch (InheritanceParentNotFoundException $e) {
			// no data from parent available, continue ...
		}
	}

	if ($data instanceof \Pimcore\Model\DataObject\Data\EncryptedField) {
		return $data->getPlain();
	}

	return $data;
}

/**
* Set pricingControlled - Kontrol Edildi
* @param bool|null $pricingControlled
* @return $this
*/
public function setPricingControlled(?bool $pricingControlled): static
{
	$this->markFieldDirty("pricingControlled", true);

	$this->pricingControlled = $pricingControlled;

	return $this;
}

/**
* Get pricingCosts - Dağıtım Modelleri
* @return \Pimcore\Model\DataObject\GroupProduct[]
*/
public function getPricingCosts(): array
{
	if ($this instanceof PreGetValueHookInterface && !\Pimcore::inAdmin()) {
		$preValue = $this->preGetValue("pricingCosts");
		if ($preValue !== null) {
			return $preValue;
		}
	}

	$data = $this->getClass()->getFieldDefinition("pricingCosts")->preGetData($this);

	if ($data instanceof \Pimcore\Model\DataObject\Data\EncryptedField) {
		return $data->getPlain();
	}

	return $data;
}

/**
* Get designFiles - Tasarım Dosyaları
* @return \Pimcore\Model\Asset[]
*/
public function getDesignFiles(): array
{
	if ($this instanceof PreGetValueHookInterface && !\Pimcore::inAdmin()) {
		$preValue = $this->preGetValue("designFiles");
		if ($preValue !== null) {
			return $preValue;
		}
	}

	$data = $this->getClass()->getFieldDefinition("designFiles")->preGetData($this);

	if (\Pimcore\Model\DataObject::doGetInheritedValues() && $this->getClass()->getFieldDefinition("designFiles")->isEmpty($data)) {
		try {
			return $this->getValueFromParent("designFiles");
		} catch (InheritanceParentNotFoundException $e) {
			// no data from parent available, continue ...
		}
	}

	if ($data instanceof \Pimcore\Model\DataObject\Data\EncryptedField) {
		return $data->getPlain();
	}

	return $data;
}

/**
* Set designFiles - Tasarım Dosyaları
* @param \Pimcore\Model\Asset[] $designFiles
* @return $this
*/
public function setDesignFiles(?array $designFiles): static
{
	/** @var \Pimcore\Model\DataObject\ClassDefinition\Data\ManyToManyRelation $fd */
	$fd = $this->getClass()->getFieldDefinition("designFiles");
	$hideUnpublished = \Pimcore\Model\DataObject\Concrete::getHideUnpublished();
	\Pimcore\Model\DataObject\Concrete::setHideUnpublished(false);
	$currentData = \Pimcore\Model\DataObject\Service::useInheritedValues(false, function() {
		return $this->getDesignFiles();
	});
	\Pimcore\Model\DataObject\Concrete::setHideUnpublished($hideUnpublished);
	$isEqual = $fd->isEqual($currentData, $designFiles);
	if (!$isEqual) {
		$this->markFieldDirty("designFiles", true);
	}
	$this->designFiles = $fd->preSetData($this, $designFiles);
	return $this;
}

/**
* @return \Pimcore\Model\DataObject\Fieldcollection|null
*/
public function getRawFiles(): ?\Pimcore\Model\DataObject\Fieldcollection
{
	if ($this instanceof PreGetValueHookInterface && !\Pimcore::inAdmin()) {
		$preValue = $this->preGetValue("rawFiles");
		if ($preValue !== null) {
			return $preValue;
		}
	}

	$data = $this->getClass()->getFieldDefinition("rawFiles")->preGetData($this);
	return $data;
}

/**
* Set rawFiles - Ham Dosyalar
* @param \Pimcore\Model\DataObject\Fieldcollection|null $rawFiles
* @return $this
*/
public function setRawFiles(?\Pimcore\Model\DataObject\Fieldcollection $rawFiles): static
{
	/** @var \Pimcore\Model\DataObject\ClassDefinition\Data\Fieldcollections $fd */
	$fd = $this->getClass()->getFieldDefinition("rawFiles");
	$this->rawFiles = $fd->preSetData($this, $rawFiles);
	return $this;
}

}

