<?php

/**
 * Inheritance: yes
 * Variants: no
 *
 * Fields Summary:
 * - iwasku [input]
 * - iwaskuActive [checkbox]
 * - productCode [input]
 * - productClass [select]
 * - name [input]
 * - nameEnglish [input]
 * - description [textarea]
 * - album [imageGallery]
 * - variationSize [input]
 * - variationColor [input]
 * - productWidth [numeric]
 * - productHeight [numeric]
 * - productDepth [numeric]
 * - productWeight [numeric]
 * - packageWidth [numeric]
 * - packegeHeight [numeric]
 * - packageDepth [numeric]
 * - packageWeight [numeric]
 * - seoTitle [input]
 * - seoDescription [textarea]
 * - seoKeywords [fieldcollections]
 * - bundleItems [manyToManyObjectRelation]
 * - marketingMaterials [manyToManyObjectRelation]
 * - urls [fieldcollections]
 * - unitCost [numeric]
 * - productCosts [advancedManyToManyObjectRelation]
 * - mainProductCost [calculatedValue]
 * - colorCosts [advancedManyToManyObjectRelation]
 * - colorCost [calculatedValue]
 * - sizeCosts [advancedManyToManyObjectRelation]
 * - sizeCost [calculatedValue]
 * - productCost [calculatedValue]
 */

namespace Pimcore\Model\DataObject;

use Pimcore\Model\DataObject\Exception\InheritanceParentNotFoundException;
use Pimcore\Model\DataObject\PreGetValueHookInterface;

/**
* @method static \Pimcore\Model\DataObject\Product\Listing getList(array $config = [])
* @method static \Pimcore\Model\DataObject\Product\Listing|\Pimcore\Model\DataObject\Product|null getByIwasku(mixed $value, ?int $limit = null, int $offset = 0, ?array $objectTypes = null)
* @method static \Pimcore\Model\DataObject\Product\Listing|\Pimcore\Model\DataObject\Product|null getByIwaskuActive(mixed $value, ?int $limit = null, int $offset = 0, ?array $objectTypes = null)
* @method static \Pimcore\Model\DataObject\Product\Listing|\Pimcore\Model\DataObject\Product|null getByProductCode(mixed $value, ?int $limit = null, int $offset = 0, ?array $objectTypes = null)
* @method static \Pimcore\Model\DataObject\Product\Listing|\Pimcore\Model\DataObject\Product|null getByProductClass(mixed $value, ?int $limit = null, int $offset = 0, ?array $objectTypes = null)
* @method static \Pimcore\Model\DataObject\Product\Listing|\Pimcore\Model\DataObject\Product|null getByName(mixed $value, ?int $limit = null, int $offset = 0, ?array $objectTypes = null)
* @method static \Pimcore\Model\DataObject\Product\Listing|\Pimcore\Model\DataObject\Product|null getByNameEnglish(mixed $value, ?int $limit = null, int $offset = 0, ?array $objectTypes = null)
* @method static \Pimcore\Model\DataObject\Product\Listing|\Pimcore\Model\DataObject\Product|null getByDescription(mixed $value, ?int $limit = null, int $offset = 0, ?array $objectTypes = null)
* @method static \Pimcore\Model\DataObject\Product\Listing|\Pimcore\Model\DataObject\Product|null getByVariationSize(mixed $value, ?int $limit = null, int $offset = 0, ?array $objectTypes = null)
* @method static \Pimcore\Model\DataObject\Product\Listing|\Pimcore\Model\DataObject\Product|null getByVariationColor(mixed $value, ?int $limit = null, int $offset = 0, ?array $objectTypes = null)
* @method static \Pimcore\Model\DataObject\Product\Listing|\Pimcore\Model\DataObject\Product|null getByProductWidth(mixed $value, ?int $limit = null, int $offset = 0, ?array $objectTypes = null)
* @method static \Pimcore\Model\DataObject\Product\Listing|\Pimcore\Model\DataObject\Product|null getByProductHeight(mixed $value, ?int $limit = null, int $offset = 0, ?array $objectTypes = null)
* @method static \Pimcore\Model\DataObject\Product\Listing|\Pimcore\Model\DataObject\Product|null getByProductDepth(mixed $value, ?int $limit = null, int $offset = 0, ?array $objectTypes = null)
* @method static \Pimcore\Model\DataObject\Product\Listing|\Pimcore\Model\DataObject\Product|null getByProductWeight(mixed $value, ?int $limit = null, int $offset = 0, ?array $objectTypes = null)
* @method static \Pimcore\Model\DataObject\Product\Listing|\Pimcore\Model\DataObject\Product|null getByPackageWidth(mixed $value, ?int $limit = null, int $offset = 0, ?array $objectTypes = null)
* @method static \Pimcore\Model\DataObject\Product\Listing|\Pimcore\Model\DataObject\Product|null getByPackegeHeight(mixed $value, ?int $limit = null, int $offset = 0, ?array $objectTypes = null)
* @method static \Pimcore\Model\DataObject\Product\Listing|\Pimcore\Model\DataObject\Product|null getByPackageDepth(mixed $value, ?int $limit = null, int $offset = 0, ?array $objectTypes = null)
* @method static \Pimcore\Model\DataObject\Product\Listing|\Pimcore\Model\DataObject\Product|null getByPackageWeight(mixed $value, ?int $limit = null, int $offset = 0, ?array $objectTypes = null)
* @method static \Pimcore\Model\DataObject\Product\Listing|\Pimcore\Model\DataObject\Product|null getBySeoTitle(mixed $value, ?int $limit = null, int $offset = 0, ?array $objectTypes = null)
* @method static \Pimcore\Model\DataObject\Product\Listing|\Pimcore\Model\DataObject\Product|null getBySeoDescription(mixed $value, ?int $limit = null, int $offset = 0, ?array $objectTypes = null)
* @method static \Pimcore\Model\DataObject\Product\Listing|\Pimcore\Model\DataObject\Product|null getByBundleItems(mixed $value, ?int $limit = null, int $offset = 0, ?array $objectTypes = null)
* @method static \Pimcore\Model\DataObject\Product\Listing|\Pimcore\Model\DataObject\Product|null getByMarketingMaterials(mixed $value, ?int $limit = null, int $offset = 0, ?array $objectTypes = null)
* @method static \Pimcore\Model\DataObject\Product\Listing|\Pimcore\Model\DataObject\Product|null getByUnitCost(mixed $value, ?int $limit = null, int $offset = 0, ?array $objectTypes = null)
* @method static \Pimcore\Model\DataObject\Product\Listing|\Pimcore\Model\DataObject\Product|null getByProductCosts(mixed $value, ?int $limit = null, int $offset = 0, ?array $objectTypes = null)
* @method static \Pimcore\Model\DataObject\Product\Listing|\Pimcore\Model\DataObject\Product|null getByColorCosts(mixed $value, ?int $limit = null, int $offset = 0, ?array $objectTypes = null)
* @method static \Pimcore\Model\DataObject\Product\Listing|\Pimcore\Model\DataObject\Product|null getBySizeCosts(mixed $value, ?int $limit = null, int $offset = 0, ?array $objectTypes = null)
*/

class Product extends \App\Model\DataObject\Product
{
public const FIELD_IWASKU = 'iwasku';
public const FIELD_IWASKU_ACTIVE = 'iwaskuActive';
public const FIELD_PRODUCT_CODE = 'productCode';
public const FIELD_PRODUCT_CLASS = 'productClass';
public const FIELD_NAME = 'name';
public const FIELD_NAME_ENGLISH = 'nameEnglish';
public const FIELD_DESCRIPTION = 'description';
public const FIELD_ALBUM = 'album';
public const FIELD_VARIATION_SIZE = 'variationSize';
public const FIELD_VARIATION_COLOR = 'variationColor';
public const FIELD_PRODUCT_WIDTH = 'productWidth';
public const FIELD_PRODUCT_HEIGHT = 'productHeight';
public const FIELD_PRODUCT_DEPTH = 'productDepth';
public const FIELD_PRODUCT_WEIGHT = 'productWeight';
public const FIELD_PACKAGE_WIDTH = 'packageWidth';
public const FIELD_PACKEGE_HEIGHT = 'packegeHeight';
public const FIELD_PACKAGE_DEPTH = 'packageDepth';
public const FIELD_PACKAGE_WEIGHT = 'packageWeight';
public const FIELD_SEO_TITLE = 'seoTitle';
public const FIELD_SEO_DESCRIPTION = 'seoDescription';
public const FIELD_SEO_KEYWORDS = 'seoKeywords';
public const FIELD_BUNDLE_ITEMS = 'bundleItems';
public const FIELD_MARKETING_MATERIALS = 'marketingMaterials';
public const FIELD_URLS = 'urls';
public const FIELD_UNIT_COST = 'unitCost';
public const FIELD_PRODUCT_COSTS = 'productCosts';
public const FIELD_MAIN_PRODUCT_COST = 'mainProductCost';
public const FIELD_COLOR_COSTS = 'colorCosts';
public const FIELD_COLOR_COST = 'colorCost';
public const FIELD_SIZE_COSTS = 'sizeCosts';
public const FIELD_SIZE_COST = 'sizeCost';
public const FIELD_PRODUCT_COST = 'productCost';

protected $classId = "product";
protected $className = "Product";
protected $iwasku;
protected $iwaskuActive;
protected $productCode;
protected $productClass;
protected $name;
protected $nameEnglish;
protected $description;
protected $album;
protected $variationSize;
protected $variationColor;
protected $productWidth;
protected $productHeight;
protected $productDepth;
protected $productWeight;
protected $packageWidth;
protected $packegeHeight;
protected $packageDepth;
protected $packageWeight;
protected $seoTitle;
protected $seoDescription;
protected $seoKeywords;
protected $bundleItems;
protected $marketingMaterials;
protected $urls;
protected $unitCost;
protected $productCosts;
protected $colorCosts;
protected $sizeCosts;


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
* Get iwaskuActive - Aktif
* @return bool|null
*/
public function getIwaskuActive(): ?bool
{
	if ($this instanceof PreGetValueHookInterface && !\Pimcore::inAdmin()) {
		$preValue = $this->preGetValue("iwaskuActive");
		if ($preValue !== null) {
			return $preValue;
		}
	}

	$data = $this->iwaskuActive;

	if (\Pimcore\Model\DataObject::doGetInheritedValues() && $this->getClass()->getFieldDefinition("iwaskuActive")->isEmpty($data)) {
		try {
			return $this->getValueFromParent("iwaskuActive");
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
* Set iwaskuActive - Aktif
* @param bool|null $iwaskuActive
* @return $this
*/
public function setIwaskuActive(?bool $iwaskuActive): static
{
	$this->markFieldDirty("iwaskuActive", true);

	$this->iwaskuActive = $iwaskuActive;

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
* Get productClass - Ürün Sınıfı
* @return string|null
*/
public function getProductClass(): ?string
{
	if ($this instanceof PreGetValueHookInterface && !\Pimcore::inAdmin()) {
		$preValue = $this->preGetValue("productClass");
		if ($preValue !== null) {
			return $preValue;
		}
	}

	$data = $this->productClass;

	if (\Pimcore\Model\DataObject::doGetInheritedValues() && $this->getClass()->getFieldDefinition("productClass")->isEmpty($data)) {
		try {
			return $this->getValueFromParent("productClass");
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
* Set productClass - Ürün Sınıfı
* @param string|null $productClass
* @return $this
*/
public function setProductClass(?string $productClass): static
{
	$this->markFieldDirty("productClass", true);

	$this->productClass = $productClass;

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
* Get productWidth - En
* @return float|null
*/
public function getProductWidth(): ?float
{
	if ($this instanceof PreGetValueHookInterface && !\Pimcore::inAdmin()) {
		$preValue = $this->preGetValue("productWidth");
		if ($preValue !== null) {
			return $preValue;
		}
	}

	$data = $this->productWidth;

	if (\Pimcore\Model\DataObject::doGetInheritedValues() && $this->getClass()->getFieldDefinition("productWidth")->isEmpty($data)) {
		try {
			return $this->getValueFromParent("productWidth");
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
* Set productWidth - En
* @param float|null $productWidth
* @return $this
*/
public function setProductWidth(?float $productWidth): static
{
	/** @var \Pimcore\Model\DataObject\ClassDefinition\Data\Numeric $fd */
	$fd = $this->getClass()->getFieldDefinition("productWidth");
	$this->productWidth = $fd->preSetData($this, $productWidth);
	return $this;
}

/**
* Get productHeight - Boy
* @return float|null
*/
public function getProductHeight(): ?float
{
	if ($this instanceof PreGetValueHookInterface && !\Pimcore::inAdmin()) {
		$preValue = $this->preGetValue("productHeight");
		if ($preValue !== null) {
			return $preValue;
		}
	}

	$data = $this->productHeight;

	if (\Pimcore\Model\DataObject::doGetInheritedValues() && $this->getClass()->getFieldDefinition("productHeight")->isEmpty($data)) {
		try {
			return $this->getValueFromParent("productHeight");
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
* Set productHeight - Boy
* @param float|null $productHeight
* @return $this
*/
public function setProductHeight(?float $productHeight): static
{
	/** @var \Pimcore\Model\DataObject\ClassDefinition\Data\Numeric $fd */
	$fd = $this->getClass()->getFieldDefinition("productHeight");
	$this->productHeight = $fd->preSetData($this, $productHeight);
	return $this;
}

/**
* Get productDepth - Yükseklik
* @return float|null
*/
public function getProductDepth(): ?float
{
	if ($this instanceof PreGetValueHookInterface && !\Pimcore::inAdmin()) {
		$preValue = $this->preGetValue("productDepth");
		if ($preValue !== null) {
			return $preValue;
		}
	}

	$data = $this->productDepth;

	if (\Pimcore\Model\DataObject::doGetInheritedValues() && $this->getClass()->getFieldDefinition("productDepth")->isEmpty($data)) {
		try {
			return $this->getValueFromParent("productDepth");
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
* Set productDepth - Yükseklik
* @param float|null $productDepth
* @return $this
*/
public function setProductDepth(?float $productDepth): static
{
	/** @var \Pimcore\Model\DataObject\ClassDefinition\Data\Numeric $fd */
	$fd = $this->getClass()->getFieldDefinition("productDepth");
	$this->productDepth = $fd->preSetData($this, $productDepth);
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
* Get packageWidth - En
* @return float|null
*/
public function getPackageWidth(): ?float
{
	if ($this instanceof PreGetValueHookInterface && !\Pimcore::inAdmin()) {
		$preValue = $this->preGetValue("packageWidth");
		if ($preValue !== null) {
			return $preValue;
		}
	}

	$data = $this->packageWidth;

	if (\Pimcore\Model\DataObject::doGetInheritedValues() && $this->getClass()->getFieldDefinition("packageWidth")->isEmpty($data)) {
		try {
			return $this->getValueFromParent("packageWidth");
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
* Set packageWidth - En
* @param float|null $packageWidth
* @return $this
*/
public function setPackageWidth(?float $packageWidth): static
{
	/** @var \Pimcore\Model\DataObject\ClassDefinition\Data\Numeric $fd */
	$fd = $this->getClass()->getFieldDefinition("packageWidth");
	$this->packageWidth = $fd->preSetData($this, $packageWidth);
	return $this;
}

/**
* Get packegeHeight - Boy
* @return float|null
*/
public function getPackegeHeight(): ?float
{
	if ($this instanceof PreGetValueHookInterface && !\Pimcore::inAdmin()) {
		$preValue = $this->preGetValue("packegeHeight");
		if ($preValue !== null) {
			return $preValue;
		}
	}

	$data = $this->packegeHeight;

	if (\Pimcore\Model\DataObject::doGetInheritedValues() && $this->getClass()->getFieldDefinition("packegeHeight")->isEmpty($data)) {
		try {
			return $this->getValueFromParent("packegeHeight");
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
* Set packegeHeight - Boy
* @param float|null $packegeHeight
* @return $this
*/
public function setPackegeHeight(?float $packegeHeight): static
{
	/** @var \Pimcore\Model\DataObject\ClassDefinition\Data\Numeric $fd */
	$fd = $this->getClass()->getFieldDefinition("packegeHeight");
	$this->packegeHeight = $fd->preSetData($this, $packegeHeight);
	return $this;
}

/**
* Get packageDepth - Yükseklik
* @return float|null
*/
public function getPackageDepth(): ?float
{
	if ($this instanceof PreGetValueHookInterface && !\Pimcore::inAdmin()) {
		$preValue = $this->preGetValue("packageDepth");
		if ($preValue !== null) {
			return $preValue;
		}
	}

	$data = $this->packageDepth;

	if (\Pimcore\Model\DataObject::doGetInheritedValues() && $this->getClass()->getFieldDefinition("packageDepth")->isEmpty($data)) {
		try {
			return $this->getValueFromParent("packageDepth");
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
* Set packageDepth - Yükseklik
* @param float|null $packageDepth
* @return $this
*/
public function setPackageDepth(?float $packageDepth): static
{
	/** @var \Pimcore\Model\DataObject\ClassDefinition\Data\Numeric $fd */
	$fd = $this->getClass()->getFieldDefinition("packageDepth");
	$this->packageDepth = $fd->preSetData($this, $packageDepth);
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
* @return \Pimcore\Model\DataObject\Fieldcollection|null
*/
public function getSeoKeywords(): ?\Pimcore\Model\DataObject\Fieldcollection
{
	if ($this instanceof PreGetValueHookInterface && !\Pimcore::inAdmin()) {
		$preValue = $this->preGetValue("seoKeywords");
		if ($preValue !== null) {
			return $preValue;
		}
	}

	$data = $this->getClass()->getFieldDefinition("seoKeywords")->preGetData($this);
	return $data;
}

/**
* Set seoKeywords - SEO Anahtar Kelimeler
* @param \Pimcore\Model\DataObject\Fieldcollection|null $seoKeywords
* @return $this
*/
public function setSeoKeywords(?\Pimcore\Model\DataObject\Fieldcollection $seoKeywords): static
{
	/** @var \Pimcore\Model\DataObject\ClassDefinition\Data\Fieldcollections $fd */
	$fd = $this->getClass()->getFieldDefinition("seoKeywords");
	$this->seoKeywords = $fd->preSetData($this, $seoKeywords);
	return $this;
}

/**
* Get bundleItems - Set İçeriği
* @return \Pimcore\Model\DataObject\Product[]
*/
public function getBundleItems(): array
{
	if ($this instanceof PreGetValueHookInterface && !\Pimcore::inAdmin()) {
		$preValue = $this->preGetValue("bundleItems");
		if ($preValue !== null) {
			return $preValue;
		}
	}

	$data = $this->getClass()->getFieldDefinition("bundleItems")->preGetData($this);

	if (\Pimcore\Model\DataObject::doGetInheritedValues() && $this->getClass()->getFieldDefinition("bundleItems")->isEmpty($data)) {
		try {
			return $this->getValueFromParent("bundleItems");
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
* Set bundleItems - Set İçeriği
* @param \Pimcore\Model\DataObject\Product[] $bundleItems
* @return $this
*/
public function setBundleItems(?array $bundleItems): static
{
	/** @var \Pimcore\Model\DataObject\ClassDefinition\Data\ManyToManyObjectRelation $fd */
	$fd = $this->getClass()->getFieldDefinition("bundleItems");
	$hideUnpublished = \Pimcore\Model\DataObject\Concrete::getHideUnpublished();
	\Pimcore\Model\DataObject\Concrete::setHideUnpublished(false);
	$currentData = \Pimcore\Model\DataObject\Service::useInheritedValues(false, function() {
		return $this->getBundleItems();
	});
	\Pimcore\Model\DataObject\Concrete::setHideUnpublished($hideUnpublished);
	$isEqual = $fd->isEqual($currentData, $bundleItems);
	if (!$isEqual) {
		$this->markFieldDirty("bundleItems", true);
	}
	$this->bundleItems = $fd->preSetData($this, $bundleItems);
	return $this;
}

/**
* Get marketingMaterials - Marketing Materials
* @return \Pimcore\Model\DataObject\MarketingMaterial[]
*/
public function getMarketingMaterials(): array
{
	if ($this instanceof PreGetValueHookInterface && !\Pimcore::inAdmin()) {
		$preValue = $this->preGetValue("marketingMaterials");
		if ($preValue !== null) {
			return $preValue;
		}
	}

	$data = $this->getClass()->getFieldDefinition("marketingMaterials")->preGetData($this);

	if (\Pimcore\Model\DataObject::doGetInheritedValues() && $this->getClass()->getFieldDefinition("marketingMaterials")->isEmpty($data)) {
		try {
			return $this->getValueFromParent("marketingMaterials");
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
* Set marketingMaterials - Marketing Materials
* @param \Pimcore\Model\DataObject\MarketingMaterial[] $marketingMaterials
* @return $this
*/
public function setMarketingMaterials(?array $marketingMaterials): static
{
	/** @var \Pimcore\Model\DataObject\ClassDefinition\Data\ManyToManyObjectRelation $fd */
	$fd = $this->getClass()->getFieldDefinition("marketingMaterials");
	$hideUnpublished = \Pimcore\Model\DataObject\Concrete::getHideUnpublished();
	\Pimcore\Model\DataObject\Concrete::setHideUnpublished(false);
	$currentData = \Pimcore\Model\DataObject\Service::useInheritedValues(false, function() {
		return $this->getMarketingMaterials();
	});
	\Pimcore\Model\DataObject\Concrete::setHideUnpublished($hideUnpublished);
	$isEqual = $fd->isEqual($currentData, $marketingMaterials);
	if (!$isEqual) {
		$this->markFieldDirty("marketingMaterials", true);
	}
	$this->marketingMaterials = $fd->preSetData($this, $marketingMaterials);
	return $this;
}

/**
* @return \Pimcore\Model\DataObject\Fieldcollection|null
*/
public function getUrls(): ?\Pimcore\Model\DataObject\Fieldcollection
{
	if ($this instanceof PreGetValueHookInterface && !\Pimcore::inAdmin()) {
		$preValue = $this->preGetValue("urls");
		if ($preValue !== null) {
			return $preValue;
		}
	}

	$data = $this->getClass()->getFieldDefinition("urls")->preGetData($this);
	return $data;
}

/**
* Set urls - Urls
* @param \Pimcore\Model\DataObject\Fieldcollection|null $urls
* @return $this
*/
public function setUrls(?\Pimcore\Model\DataObject\Fieldcollection $urls): static
{
	/** @var \Pimcore\Model\DataObject\ClassDefinition\Data\Fieldcollections $fd */
	$fd = $this->getClass()->getFieldDefinition("urls");
	$this->urls = $fd->preSetData($this, $urls);
	return $this;
}

/**
* Get unitCost - Birim Maliyet
* @return float|null
*/
public function getUnitCost(): ?float
{
	if ($this instanceof PreGetValueHookInterface && !\Pimcore::inAdmin()) {
		$preValue = $this->preGetValue("unitCost");
		if ($preValue !== null) {
			return $preValue;
		}
	}

	$data = $this->unitCost;

	if (\Pimcore\Model\DataObject::doGetInheritedValues() && $this->getClass()->getFieldDefinition("unitCost")->isEmpty($data)) {
		try {
			return $this->getValueFromParent("unitCost");
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
* Set unitCost - Birim Maliyet
* @param float|null $unitCost
* @return $this
*/
public function setUnitCost(?float $unitCost): static
{
	/** @var \Pimcore\Model\DataObject\ClassDefinition\Data\Numeric $fd */
	$fd = $this->getClass()->getFieldDefinition("unitCost");
	$this->unitCost = $fd->preSetData($this, $unitCost);
	return $this;
}

/**
* Get productCosts - Ortak Maliyetler
* @return \Pimcore\Model\DataObject\Data\ObjectMetadata[]
*/
public function getProductCosts(): array
{
	if ($this instanceof PreGetValueHookInterface && !\Pimcore::inAdmin()) {
		$preValue = $this->preGetValue("productCosts");
		if ($preValue !== null) {
			return $preValue;
		}
	}

	$data = $this->getClass()->getFieldDefinition("productCosts")->preGetData($this);

	if (\Pimcore\Model\DataObject::doGetInheritedValues() && $this->getClass()->getFieldDefinition("productCosts")->isEmpty($data)) {
		try {
			return $this->getValueFromParent("productCosts");
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
* Set productCosts - Ortak Maliyetler
* @param \Pimcore\Model\DataObject\Data\ObjectMetadata[] $productCosts
* @return $this
*/
public function setProductCosts(?array $productCosts): static
{
	/** @var \Pimcore\Model\DataObject\ClassDefinition\Data\AdvancedManyToManyObjectRelation $fd */
	$fd = $this->getClass()->getFieldDefinition("productCosts");
	$hideUnpublished = \Pimcore\Model\DataObject\Concrete::getHideUnpublished();
	\Pimcore\Model\DataObject\Concrete::setHideUnpublished(false);
	$currentData = \Pimcore\Model\DataObject\Service::useInheritedValues(false, function() {
		return $this->getProductCosts();
	});
	\Pimcore\Model\DataObject\Concrete::setHideUnpublished($hideUnpublished);
	$isEqual = $fd->isEqual($currentData, $productCosts);
	if (!$isEqual) {
		$this->markFieldDirty("productCosts", true);
	}
	$this->productCosts = $fd->preSetData($this, $productCosts);
	return $this;
}

/**
* Get mainProductCost - Temel Maliyet
* @return mixed
*/
public function getMainProductCost(): mixed
{
	$data = new \Pimcore\Model\DataObject\Data\CalculatedValue('mainProductCost');
	$data->setContextualData("object", null, null, null);
	$object = $this;
	$data = \Pimcore\Model\DataObject\Service::getCalculatedFieldValue($object, $data);

	return $data;
}

/**
* Get colorCosts - Renk Maliyetleri
* @return \Pimcore\Model\DataObject\Data\ObjectMetadata[]
*/
public function getColorCosts(): array
{
	if ($this instanceof PreGetValueHookInterface && !\Pimcore::inAdmin()) {
		$preValue = $this->preGetValue("colorCosts");
		if ($preValue !== null) {
			return $preValue;
		}
	}

	$data = $this->getClass()->getFieldDefinition("colorCosts")->preGetData($this);

	if (\Pimcore\Model\DataObject::doGetInheritedValues() && $this->getClass()->getFieldDefinition("colorCosts")->isEmpty($data)) {
		try {
			return $this->getValueFromParent("colorCosts");
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
* Set colorCosts - Renk Maliyetleri
* @param \Pimcore\Model\DataObject\Data\ObjectMetadata[] $colorCosts
* @return $this
*/
public function setColorCosts(?array $colorCosts): static
{
	/** @var \Pimcore\Model\DataObject\ClassDefinition\Data\AdvancedManyToManyObjectRelation $fd */
	$fd = $this->getClass()->getFieldDefinition("colorCosts");
	$hideUnpublished = \Pimcore\Model\DataObject\Concrete::getHideUnpublished();
	\Pimcore\Model\DataObject\Concrete::setHideUnpublished(false);
	$currentData = \Pimcore\Model\DataObject\Service::useInheritedValues(false, function() {
		return $this->getColorCosts();
	});
	\Pimcore\Model\DataObject\Concrete::setHideUnpublished($hideUnpublished);
	$isEqual = $fd->isEqual($currentData, $colorCosts);
	if (!$isEqual) {
		$this->markFieldDirty("colorCosts", true);
	}
	$this->colorCosts = $fd->preSetData($this, $colorCosts);
	return $this;
}

/**
* Get colorCost - Renk Maliyeti
* @return mixed
*/
public function getColorCost(): mixed
{
	$data = new \Pimcore\Model\DataObject\Data\CalculatedValue('colorCost');
	$data->setContextualData("object", null, null, null);
	$object = $this;
	$data = \Pimcore\Model\DataObject\Service::getCalculatedFieldValue($object, $data);

	return $data;
}

/**
* Get sizeCosts - Ebat Maliyetleri
* @return \Pimcore\Model\DataObject\Data\ObjectMetadata[]
*/
public function getSizeCosts(): array
{
	if ($this instanceof PreGetValueHookInterface && !\Pimcore::inAdmin()) {
		$preValue = $this->preGetValue("sizeCosts");
		if ($preValue !== null) {
			return $preValue;
		}
	}

	$data = $this->getClass()->getFieldDefinition("sizeCosts")->preGetData($this);

	if (\Pimcore\Model\DataObject::doGetInheritedValues() && $this->getClass()->getFieldDefinition("sizeCosts")->isEmpty($data)) {
		try {
			return $this->getValueFromParent("sizeCosts");
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
* Set sizeCosts - Ebat Maliyetleri
* @param \Pimcore\Model\DataObject\Data\ObjectMetadata[] $sizeCosts
* @return $this
*/
public function setSizeCosts(?array $sizeCosts): static
{
	/** @var \Pimcore\Model\DataObject\ClassDefinition\Data\AdvancedManyToManyObjectRelation $fd */
	$fd = $this->getClass()->getFieldDefinition("sizeCosts");
	$hideUnpublished = \Pimcore\Model\DataObject\Concrete::getHideUnpublished();
	\Pimcore\Model\DataObject\Concrete::setHideUnpublished(false);
	$currentData = \Pimcore\Model\DataObject\Service::useInheritedValues(false, function() {
		return $this->getSizeCosts();
	});
	\Pimcore\Model\DataObject\Concrete::setHideUnpublished($hideUnpublished);
	$isEqual = $fd->isEqual($currentData, $sizeCosts);
	if (!$isEqual) {
		$this->markFieldDirty("sizeCosts", true);
	}
	$this->sizeCosts = $fd->preSetData($this, $sizeCosts);
	return $this;
}

/**
* Get sizeCost - Ebat Maliyeti
* @return mixed
*/
public function getSizeCost(): mixed
{
	$data = new \Pimcore\Model\DataObject\Data\CalculatedValue('sizeCost');
	$data->setContextualData("object", null, null, null);
	$object = $this;
	$data = \Pimcore\Model\DataObject\Service::getCalculatedFieldValue($object, $data);

	return $data;
}

/**
* Get productCost - Ürün Maliyeti
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

}

