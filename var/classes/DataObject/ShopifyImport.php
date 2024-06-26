<?php

/**
 * Inheritance: no
 * Variants: no
 *
 * Fields Summary:
 * - title [input]
 * - bodyHtml [textarea]
 * - handle [input]
 * - shopifyId [numeric]
 * - imagesJson [textarea]
 * - optionsJson [textarea]
 * - productType [input]
 * - variants [manyToManyObjectRelation]
 * - vendor [input]
 */

namespace Pimcore\Model\DataObject;

use Pimcore\Model\DataObject\Exception\InheritanceParentNotFoundException;
use Pimcore\Model\DataObject\PreGetValueHookInterface;

/**
* @method static \Pimcore\Model\DataObject\ShopifyImport\Listing getList(array $config = [])
* @method static \Pimcore\Model\DataObject\ShopifyImport\Listing|\Pimcore\Model\DataObject\ShopifyImport|null getByTitle(mixed $value, ?int $limit = null, int $offset = 0, ?array $objectTypes = null)
* @method static \Pimcore\Model\DataObject\ShopifyImport\Listing|\Pimcore\Model\DataObject\ShopifyImport|null getByBodyHtml(mixed $value, ?int $limit = null, int $offset = 0, ?array $objectTypes = null)
* @method static \Pimcore\Model\DataObject\ShopifyImport\Listing|\Pimcore\Model\DataObject\ShopifyImport|null getByHandle(mixed $value, ?int $limit = null, int $offset = 0, ?array $objectTypes = null)
* @method static \Pimcore\Model\DataObject\ShopifyImport\Listing|\Pimcore\Model\DataObject\ShopifyImport|null getByShopifyId(mixed $value, ?int $limit = null, int $offset = 0, ?array $objectTypes = null)
* @method static \Pimcore\Model\DataObject\ShopifyImport\Listing|\Pimcore\Model\DataObject\ShopifyImport|null getByImagesJson(mixed $value, ?int $limit = null, int $offset = 0, ?array $objectTypes = null)
* @method static \Pimcore\Model\DataObject\ShopifyImport\Listing|\Pimcore\Model\DataObject\ShopifyImport|null getByOptionsJson(mixed $value, ?int $limit = null, int $offset = 0, ?array $objectTypes = null)
* @method static \Pimcore\Model\DataObject\ShopifyImport\Listing|\Pimcore\Model\DataObject\ShopifyImport|null getByProductType(mixed $value, ?int $limit = null, int $offset = 0, ?array $objectTypes = null)
* @method static \Pimcore\Model\DataObject\ShopifyImport\Listing|\Pimcore\Model\DataObject\ShopifyImport|null getByVariants(mixed $value, ?int $limit = null, int $offset = 0, ?array $objectTypes = null)
* @method static \Pimcore\Model\DataObject\ShopifyImport\Listing|\Pimcore\Model\DataObject\ShopifyImport|null getByVendor(mixed $value, ?int $limit = null, int $offset = 0, ?array $objectTypes = null)
*/

class ShopifyImport extends Concrete
{
public const FIELD_TITLE = 'title';
public const FIELD_BODY_HTML = 'bodyHtml';
public const FIELD_HANDLE = 'handle';
public const FIELD_SHOPIFY_ID = 'shopifyId';
public const FIELD_IMAGES_JSON = 'imagesJson';
public const FIELD_OPTIONS_JSON = 'optionsJson';
public const FIELD_PRODUCT_TYPE = 'productType';
public const FIELD_VARIANTS = 'variants';
public const FIELD_VENDOR = 'vendor';

protected $classId = "shopimport";
protected $className = "ShopifyImport";
protected $title;
protected $bodyHtml;
protected $handle;
protected $shopifyId;
protected $imagesJson;
protected $optionsJson;
protected $productType;
protected $variants;
protected $vendor;


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
* Get title - Title
* @return string|null
*/
public function getTitle(): ?string
{
	if ($this instanceof PreGetValueHookInterface && !\Pimcore::inAdmin()) {
		$preValue = $this->preGetValue("title");
		if ($preValue !== null) {
			return $preValue;
		}
	}

	$data = $this->title;

	if ($data instanceof \Pimcore\Model\DataObject\Data\EncryptedField) {
		return $data->getPlain();
	}

	return $data;
}

/**
* Set title - Title
* @param string|null $title
* @return $this
*/
public function setTitle(?string $title): static
{
	$this->markFieldDirty("title", true);

	$this->title = $title;

	return $this;
}

/**
* Get bodyHtml - Body Html
* @return string|null
*/
public function getBodyHtml(): ?string
{
	if ($this instanceof PreGetValueHookInterface && !\Pimcore::inAdmin()) {
		$preValue = $this->preGetValue("bodyHtml");
		if ($preValue !== null) {
			return $preValue;
		}
	}

	$data = $this->bodyHtml;

	if ($data instanceof \Pimcore\Model\DataObject\Data\EncryptedField) {
		return $data->getPlain();
	}

	return $data;
}

/**
* Set bodyHtml - Body Html
* @param string|null $bodyHtml
* @return $this
*/
public function setBodyHtml(?string $bodyHtml): static
{
	$this->markFieldDirty("bodyHtml", true);

	$this->bodyHtml = $bodyHtml;

	return $this;
}

/**
* Get handle - Handle
* @return string|null
*/
public function getHandle(): ?string
{
	if ($this instanceof PreGetValueHookInterface && !\Pimcore::inAdmin()) {
		$preValue = $this->preGetValue("handle");
		if ($preValue !== null) {
			return $preValue;
		}
	}

	$data = $this->handle;

	if ($data instanceof \Pimcore\Model\DataObject\Data\EncryptedField) {
		return $data->getPlain();
	}

	return $data;
}

/**
* Set handle - Handle
* @param string|null $handle
* @return $this
*/
public function setHandle(?string $handle): static
{
	$this->markFieldDirty("handle", true);

	$this->handle = $handle;

	return $this;
}

/**
* Get shopifyId - Shopify Id
* @return int|null
*/
public function getShopifyId(): ?int
{
	if ($this instanceof PreGetValueHookInterface && !\Pimcore::inAdmin()) {
		$preValue = $this->preGetValue("shopifyId");
		if ($preValue !== null) {
			return $preValue;
		}
	}

	$data = $this->shopifyId;

	if ($data instanceof \Pimcore\Model\DataObject\Data\EncryptedField) {
		return $data->getPlain();
	}

	return $data;
}

/**
* Set shopifyId - Shopify Id
* @param int|null $shopifyId
* @return $this
*/
public function setShopifyId(?int $shopifyId): static
{
	/** @var \Pimcore\Model\DataObject\ClassDefinition\Data\Numeric $fd */
	$fd = $this->getClass()->getFieldDefinition("shopifyId");
	$this->shopifyId = $fd->preSetData($this, $shopifyId);
	return $this;
}

/**
* Get imagesJson - Images Json
* @return string|null
*/
public function getImagesJson(): ?string
{
	if ($this instanceof PreGetValueHookInterface && !\Pimcore::inAdmin()) {
		$preValue = $this->preGetValue("imagesJson");
		if ($preValue !== null) {
			return $preValue;
		}
	}

	$data = $this->imagesJson;

	if ($data instanceof \Pimcore\Model\DataObject\Data\EncryptedField) {
		return $data->getPlain();
	}

	return $data;
}

/**
* Set imagesJson - Images Json
* @param string|null $imagesJson
* @return $this
*/
public function setImagesJson(?string $imagesJson): static
{
	$this->markFieldDirty("imagesJson", true);

	$this->imagesJson = $imagesJson;

	return $this;
}

/**
* Get optionsJson - Options Json
* @return string|null
*/
public function getOptionsJson(): ?string
{
	if ($this instanceof PreGetValueHookInterface && !\Pimcore::inAdmin()) {
		$preValue = $this->preGetValue("optionsJson");
		if ($preValue !== null) {
			return $preValue;
		}
	}

	$data = $this->optionsJson;

	if ($data instanceof \Pimcore\Model\DataObject\Data\EncryptedField) {
		return $data->getPlain();
	}

	return $data;
}

/**
* Set optionsJson - Options Json
* @param string|null $optionsJson
* @return $this
*/
public function setOptionsJson(?string $optionsJson): static
{
	$this->markFieldDirty("optionsJson", true);

	$this->optionsJson = $optionsJson;

	return $this;
}

/**
* Get productType - Product Type
* @return string|null
*/
public function getProductType(): ?string
{
	if ($this instanceof PreGetValueHookInterface && !\Pimcore::inAdmin()) {
		$preValue = $this->preGetValue("productType");
		if ($preValue !== null) {
			return $preValue;
		}
	}

	$data = $this->productType;

	if ($data instanceof \Pimcore\Model\DataObject\Data\EncryptedField) {
		return $data->getPlain();
	}

	return $data;
}

/**
* Set productType - Product Type
* @param string|null $productType
* @return $this
*/
public function setProductType(?string $productType): static
{
	$this->markFieldDirty("productType", true);

	$this->productType = $productType;

	return $this;
}

/**
* Get variants - Variants
* @return \Pimcore\Model\DataObject\ShopifyVariant[]
*/
public function getVariants(): array
{
	if ($this instanceof PreGetValueHookInterface && !\Pimcore::inAdmin()) {
		$preValue = $this->preGetValue("variants");
		if ($preValue !== null) {
			return $preValue;
		}
	}

	$data = $this->getClass()->getFieldDefinition("variants")->preGetData($this);

	if ($data instanceof \Pimcore\Model\DataObject\Data\EncryptedField) {
		return $data->getPlain();
	}

	return $data;
}

/**
* Set variants - Variants
* @param \Pimcore\Model\DataObject\ShopifyVariant[] $variants
* @return $this
*/
public function setVariants(?array $variants): static
{
	/** @var \Pimcore\Model\DataObject\ClassDefinition\Data\ManyToManyObjectRelation $fd */
	$fd = $this->getClass()->getFieldDefinition("variants");
	$hideUnpublished = \Pimcore\Model\DataObject\Concrete::getHideUnpublished();
	\Pimcore\Model\DataObject\Concrete::setHideUnpublished(false);
	$currentData = $this->getVariants();
	\Pimcore\Model\DataObject\Concrete::setHideUnpublished($hideUnpublished);
	$isEqual = $fd->isEqual($currentData, $variants);
	if (!$isEqual) {
		$this->markFieldDirty("variants", true);
	}
	$this->variants = $fd->preSetData($this, $variants);
	return $this;
}

/**
* Get vendor - Vendor
* @return string|null
*/
public function getVendor(): ?string
{
	if ($this instanceof PreGetValueHookInterface && !\Pimcore::inAdmin()) {
		$preValue = $this->preGetValue("vendor");
		if ($preValue !== null) {
			return $preValue;
		}
	}

	$data = $this->vendor;

	if ($data instanceof \Pimcore\Model\DataObject\Data\EncryptedField) {
		return $data->getPlain();
	}

	return $data;
}

/**
* Set vendor - Vendor
* @param string|null $vendor
* @return $this
*/
public function setVendor(?string $vendor): static
{
	$this->markFieldDirty("vendor", true);

	$this->vendor = $vendor;

	return $this;
}

}

