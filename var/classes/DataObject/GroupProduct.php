<?php

/**
 * Inheritance: no
 * Variants: no
 *
 * Fields Summary:
 * - products [manyToManyObjectRelation]
 * - pricingModels [manyToManyObjectRelation]
 * - frontendUrl [link]
 * - targetMarketplace [advancedManyToManyObjectRelation]
 * - iwaskuStickers [link]
 * - euStickers [link]
 * - usStickers [link]
 */

namespace Pimcore\Model\DataObject;

use Pimcore\Model\DataObject\Exception\InheritanceParentNotFoundException;
use Pimcore\Model\DataObject\PreGetValueHookInterface;

/**
* @method static \Pimcore\Model\DataObject\GroupProduct\Listing getList(array $config = [])
* @method static \Pimcore\Model\DataObject\GroupProduct\Listing|\Pimcore\Model\DataObject\GroupProduct|null getByProducts(mixed $value, ?int $limit = null, int $offset = 0, ?array $objectTypes = null)
* @method static \Pimcore\Model\DataObject\GroupProduct\Listing|\Pimcore\Model\DataObject\GroupProduct|null getByPricingModels(mixed $value, ?int $limit = null, int $offset = 0, ?array $objectTypes = null)
* @method static \Pimcore\Model\DataObject\GroupProduct\Listing|\Pimcore\Model\DataObject\GroupProduct|null getByTargetMarketplace(mixed $value, ?int $limit = null, int $offset = 0, ?array $objectTypes = null)
*/

class GroupProduct extends Concrete
{
public const FIELD_PRODUCTS = 'products';
public const FIELD_PRICING_MODELS = 'pricingModels';
public const FIELD_FRONTEND_URL = 'frontendUrl';
public const FIELD_TARGET_MARKETPLACE = 'targetMarketplace';
public const FIELD_IWASKU_STICKERS = 'iwaskuStickers';
public const FIELD_EU_STICKERS = 'euStickers';
public const FIELD_US_STICKERS = 'usStickers';

protected $classId = "gproduct";
protected $className = "GroupProduct";
protected $products;
protected $pricingModels;
protected $frontendUrl;
protected $targetMarketplace;
protected $iwaskuStickers;
protected $euStickers;
protected $usStickers;


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
* Get products - Products
* @return \Pimcore\Model\DataObject\Product[]
*/
public function getProducts(): array
{
	if ($this instanceof PreGetValueHookInterface && !\Pimcore::inAdmin()) {
		$preValue = $this->preGetValue("products");
		if ($preValue !== null) {
			return $preValue;
		}
	}

	$data = $this->getClass()->getFieldDefinition("products")->preGetData($this);

	if ($data instanceof \Pimcore\Model\DataObject\Data\EncryptedField) {
		return $data->getPlain();
	}

	return $data;
}

/**
* Set products - Products
* @param \Pimcore\Model\DataObject\Product[] $products
* @return $this
*/
public function setProducts(?array $products): static
{
	/** @var \Pimcore\Model\DataObject\ClassDefinition\Data\ManyToManyObjectRelation $fd */
	$fd = $this->getClass()->getFieldDefinition("products");
	$hideUnpublished = \Pimcore\Model\DataObject\Concrete::getHideUnpublished();
	\Pimcore\Model\DataObject\Concrete::setHideUnpublished(false);
	$currentData = $this->getProducts();
	\Pimcore\Model\DataObject\Concrete::setHideUnpublished($hideUnpublished);
	$isEqual = $fd->isEqual($currentData, $products);
	if (!$isEqual) {
		$this->markFieldDirty("products", true);
	}
	$this->products = $fd->preSetData($this, $products);
	return $this;
}

/**
* Get pricingModels - Fiyatlama Modelleri
* @return \Pimcore\Model\DataObject\PriceModel[]
*/
public function getPricingModels(): array
{
	if ($this instanceof PreGetValueHookInterface && !\Pimcore::inAdmin()) {
		$preValue = $this->preGetValue("pricingModels");
		if ($preValue !== null) {
			return $preValue;
		}
	}

	$data = $this->getClass()->getFieldDefinition("pricingModels")->preGetData($this);

	if ($data instanceof \Pimcore\Model\DataObject\Data\EncryptedField) {
		return $data->getPlain();
	}

	return $data;
}

/**
* Set pricingModels - Fiyatlama Modelleri
* @param \Pimcore\Model\DataObject\PriceModel[] $pricingModels
* @return $this
*/
public function setPricingModels(?array $pricingModels): static
{
	/** @var \Pimcore\Model\DataObject\ClassDefinition\Data\ManyToManyObjectRelation $fd */
	$fd = $this->getClass()->getFieldDefinition("pricingModels");
	$hideUnpublished = \Pimcore\Model\DataObject\Concrete::getHideUnpublished();
	\Pimcore\Model\DataObject\Concrete::setHideUnpublished(false);
	$currentData = $this->getPricingModels();
	\Pimcore\Model\DataObject\Concrete::setHideUnpublished($hideUnpublished);
	$isEqual = $fd->isEqual($currentData, $pricingModels);
	if (!$isEqual) {
		$this->markFieldDirty("pricingModels", true);
	}
	$this->pricingModels = $fd->preSetData($this, $pricingModels);
	return $this;
}

/**
* Get frontendUrl - Tablo Linki
* @return \Pimcore\Model\DataObject\Data\Link|null
*/
public function getFrontendUrl(): ?\Pimcore\Model\DataObject\Data\Link
{
	if ($this instanceof PreGetValueHookInterface && !\Pimcore::inAdmin()) {
		$preValue = $this->preGetValue("frontendUrl");
		if ($preValue !== null) {
			return $preValue;
		}
	}

	$data = $this->frontendUrl;

	if ($data instanceof \Pimcore\Model\DataObject\Data\EncryptedField) {
		return $data->getPlain();
	}

	return $data;
}

/**
* Set frontendUrl - Tablo Linki
* @param \Pimcore\Model\DataObject\Data\Link|null $frontendUrl
* @return $this
*/
public function setFrontendUrl(?\Pimcore\Model\DataObject\Data\Link $frontendUrl): static
{
	$this->markFieldDirty("frontendUrl", true);

	$this->frontendUrl = $frontendUrl;

	return $this;
}

/**
* Get targetMarketplace - Hedef Pazar
* @return \Pimcore\Model\DataObject\Data\ObjectMetadata[]
*/
public function getTargetMarketplace(): array
{
	if ($this instanceof PreGetValueHookInterface && !\Pimcore::inAdmin()) {
		$preValue = $this->preGetValue("targetMarketplace");
		if ($preValue !== null) {
			return $preValue;
		}
	}

	$data = $this->getClass()->getFieldDefinition("targetMarketplace")->preGetData($this);

	if ($data instanceof \Pimcore\Model\DataObject\Data\EncryptedField) {
		return $data->getPlain();
	}

	return $data;
}

/**
* Set targetMarketplace - Hedef Pazar
* @param \Pimcore\Model\DataObject\Data\ObjectMetadata[] $targetMarketplace
* @return $this
*/
public function setTargetMarketplace(?array $targetMarketplace): static
{
	/** @var \Pimcore\Model\DataObject\ClassDefinition\Data\AdvancedManyToManyObjectRelation $fd */
	$fd = $this->getClass()->getFieldDefinition("targetMarketplace");
	$hideUnpublished = \Pimcore\Model\DataObject\Concrete::getHideUnpublished();
	\Pimcore\Model\DataObject\Concrete::setHideUnpublished(false);
	$currentData = $this->getTargetMarketplace();
	\Pimcore\Model\DataObject\Concrete::setHideUnpublished($hideUnpublished);
	$isEqual = $fd->isEqual($currentData, $targetMarketplace);
	if (!$isEqual) {
		$this->markFieldDirty("targetMarketplace", true);
	}
	$this->targetMarketplace = $fd->preSetData($this, $targetMarketplace);
	return $this;
}

/**
* Get iwaskuStickers - Iwasku Etiketleri
* @return \Pimcore\Model\DataObject\Data\Link|null
*/
public function getIwaskuStickers(): ?\Pimcore\Model\DataObject\Data\Link
{
	if ($this instanceof PreGetValueHookInterface && !\Pimcore::inAdmin()) {
		$preValue = $this->preGetValue("iwaskuStickers");
		if ($preValue !== null) {
			return $preValue;
		}
	}

	$data = $this->iwaskuStickers;

	if ($data instanceof \Pimcore\Model\DataObject\Data\EncryptedField) {
		return $data->getPlain();
	}

	return $data;
}

/**
* Set iwaskuStickers - Iwasku Etiketleri
* @param \Pimcore\Model\DataObject\Data\Link|null $iwaskuStickers
* @return $this
*/
public function setIwaskuStickers(?\Pimcore\Model\DataObject\Data\Link $iwaskuStickers): static
{
	$this->markFieldDirty("iwaskuStickers", true);

	$this->iwaskuStickers = $iwaskuStickers;

	return $this;
}

/**
* Get euStickers - EU Etiketleri
* @return \Pimcore\Model\DataObject\Data\Link|null
*/
public function getEuStickers(): ?\Pimcore\Model\DataObject\Data\Link
{
	if ($this instanceof PreGetValueHookInterface && !\Pimcore::inAdmin()) {
		$preValue = $this->preGetValue("euStickers");
		if ($preValue !== null) {
			return $preValue;
		}
	}

	$data = $this->euStickers;

	if ($data instanceof \Pimcore\Model\DataObject\Data\EncryptedField) {
		return $data->getPlain();
	}

	return $data;
}

/**
* Set euStickers - EU Etiketleri
* @param \Pimcore\Model\DataObject\Data\Link|null $euStickers
* @return $this
*/
public function setEuStickers(?\Pimcore\Model\DataObject\Data\Link $euStickers): static
{
	$this->markFieldDirty("euStickers", true);

	$this->euStickers = $euStickers;

	return $this;
}

/**
* Get usStickers - ABD FNSKU Etiketleri
* @return \Pimcore\Model\DataObject\Data\Link|null
*/
public function getUsStickers(): ?\Pimcore\Model\DataObject\Data\Link
{
	if ($this instanceof PreGetValueHookInterface && !\Pimcore::inAdmin()) {
		$preValue = $this->preGetValue("usStickers");
		if ($preValue !== null) {
			return $preValue;
		}
	}

	$data = $this->usStickers;

	if ($data instanceof \Pimcore\Model\DataObject\Data\EncryptedField) {
		return $data->getPlain();
	}

	return $data;
}

/**
* Set usStickers - ABD FNSKU Etiketleri
* @param \Pimcore\Model\DataObject\Data\Link|null $usStickers
* @return $this
*/
public function setUsStickers(?\Pimcore\Model\DataObject\Data\Link $usStickers): static
{
	$this->markFieldDirty("usStickers", true);

	$this->usStickers = $usStickers;

	return $this;
}

}

