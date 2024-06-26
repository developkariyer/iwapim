<?php

/**
 * Inheritance: no
 * Variants: no
 *
 * Fields Summary:
 * - productId [input]
 * - shopifyId [input]
 * - title [input]
 * - price [numeric]
 * - sku [input]
 * - position [numeric]
 * - inventoryPolicy [input]
 * - compareAtPrice [input]
 * - fulfillmentService [input]
 * - inventoryManagement [input]
 * - option1 [input]
 * - option2 [input]
 * - option3 [input]
 * - taxable [checkbox]
 * - barcode [input]
 * - grams [numeric]
 * - weight [numeric]
 * - weightUnit [input]
 * - inventoryItemId [input]
 * - inventoryQuantity [numeric]
 * - oldInventoryQuantity [numeric]
 * - requiresShipping [checkbox]
 * - imageId [input]
 * - adminGraphqlApiId [input]
 */

namespace Pimcore\Model\DataObject;

use Pimcore\Model\DataObject\Exception\InheritanceParentNotFoundException;
use Pimcore\Model\DataObject\PreGetValueHookInterface;

/**
* @method static \Pimcore\Model\DataObject\ShopifyVariant\Listing getList(array $config = [])
* @method static \Pimcore\Model\DataObject\ShopifyVariant\Listing|\Pimcore\Model\DataObject\ShopifyVariant|null getByProductId(mixed $value, ?int $limit = null, int $offset = 0, ?array $objectTypes = null)
* @method static \Pimcore\Model\DataObject\ShopifyVariant\Listing|\Pimcore\Model\DataObject\ShopifyVariant|null getByShopifyId(mixed $value, ?int $limit = null, int $offset = 0, ?array $objectTypes = null)
* @method static \Pimcore\Model\DataObject\ShopifyVariant\Listing|\Pimcore\Model\DataObject\ShopifyVariant|null getByTitle(mixed $value, ?int $limit = null, int $offset = 0, ?array $objectTypes = null)
* @method static \Pimcore\Model\DataObject\ShopifyVariant\Listing|\Pimcore\Model\DataObject\ShopifyVariant|null getByPrice(mixed $value, ?int $limit = null, int $offset = 0, ?array $objectTypes = null)
* @method static \Pimcore\Model\DataObject\ShopifyVariant\Listing|\Pimcore\Model\DataObject\ShopifyVariant|null getBySku(mixed $value, ?int $limit = null, int $offset = 0, ?array $objectTypes = null)
* @method static \Pimcore\Model\DataObject\ShopifyVariant\Listing|\Pimcore\Model\DataObject\ShopifyVariant|null getByPosition(mixed $value, ?int $limit = null, int $offset = 0, ?array $objectTypes = null)
* @method static \Pimcore\Model\DataObject\ShopifyVariant\Listing|\Pimcore\Model\DataObject\ShopifyVariant|null getByInventoryPolicy(mixed $value, ?int $limit = null, int $offset = 0, ?array $objectTypes = null)
* @method static \Pimcore\Model\DataObject\ShopifyVariant\Listing|\Pimcore\Model\DataObject\ShopifyVariant|null getByCompareAtPrice(mixed $value, ?int $limit = null, int $offset = 0, ?array $objectTypes = null)
* @method static \Pimcore\Model\DataObject\ShopifyVariant\Listing|\Pimcore\Model\DataObject\ShopifyVariant|null getByFulfillmentService(mixed $value, ?int $limit = null, int $offset = 0, ?array $objectTypes = null)
* @method static \Pimcore\Model\DataObject\ShopifyVariant\Listing|\Pimcore\Model\DataObject\ShopifyVariant|null getByInventoryManagement(mixed $value, ?int $limit = null, int $offset = 0, ?array $objectTypes = null)
* @method static \Pimcore\Model\DataObject\ShopifyVariant\Listing|\Pimcore\Model\DataObject\ShopifyVariant|null getByOption1(mixed $value, ?int $limit = null, int $offset = 0, ?array $objectTypes = null)
* @method static \Pimcore\Model\DataObject\ShopifyVariant\Listing|\Pimcore\Model\DataObject\ShopifyVariant|null getByOption2(mixed $value, ?int $limit = null, int $offset = 0, ?array $objectTypes = null)
* @method static \Pimcore\Model\DataObject\ShopifyVariant\Listing|\Pimcore\Model\DataObject\ShopifyVariant|null getByOption3(mixed $value, ?int $limit = null, int $offset = 0, ?array $objectTypes = null)
* @method static \Pimcore\Model\DataObject\ShopifyVariant\Listing|\Pimcore\Model\DataObject\ShopifyVariant|null getByTaxable(mixed $value, ?int $limit = null, int $offset = 0, ?array $objectTypes = null)
* @method static \Pimcore\Model\DataObject\ShopifyVariant\Listing|\Pimcore\Model\DataObject\ShopifyVariant|null getByBarcode(mixed $value, ?int $limit = null, int $offset = 0, ?array $objectTypes = null)
* @method static \Pimcore\Model\DataObject\ShopifyVariant\Listing|\Pimcore\Model\DataObject\ShopifyVariant|null getByGrams(mixed $value, ?int $limit = null, int $offset = 0, ?array $objectTypes = null)
* @method static \Pimcore\Model\DataObject\ShopifyVariant\Listing|\Pimcore\Model\DataObject\ShopifyVariant|null getByWeight(mixed $value, ?int $limit = null, int $offset = 0, ?array $objectTypes = null)
* @method static \Pimcore\Model\DataObject\ShopifyVariant\Listing|\Pimcore\Model\DataObject\ShopifyVariant|null getByWeightUnit(mixed $value, ?int $limit = null, int $offset = 0, ?array $objectTypes = null)
* @method static \Pimcore\Model\DataObject\ShopifyVariant\Listing|\Pimcore\Model\DataObject\ShopifyVariant|null getByInventoryItemId(mixed $value, ?int $limit = null, int $offset = 0, ?array $objectTypes = null)
* @method static \Pimcore\Model\DataObject\ShopifyVariant\Listing|\Pimcore\Model\DataObject\ShopifyVariant|null getByInventoryQuantity(mixed $value, ?int $limit = null, int $offset = 0, ?array $objectTypes = null)
* @method static \Pimcore\Model\DataObject\ShopifyVariant\Listing|\Pimcore\Model\DataObject\ShopifyVariant|null getByOldInventoryQuantity(mixed $value, ?int $limit = null, int $offset = 0, ?array $objectTypes = null)
* @method static \Pimcore\Model\DataObject\ShopifyVariant\Listing|\Pimcore\Model\DataObject\ShopifyVariant|null getByRequiresShipping(mixed $value, ?int $limit = null, int $offset = 0, ?array $objectTypes = null)
* @method static \Pimcore\Model\DataObject\ShopifyVariant\Listing|\Pimcore\Model\DataObject\ShopifyVariant|null getByImageId(mixed $value, ?int $limit = null, int $offset = 0, ?array $objectTypes = null)
* @method static \Pimcore\Model\DataObject\ShopifyVariant\Listing|\Pimcore\Model\DataObject\ShopifyVariant|null getByAdminGraphqlApiId(mixed $value, ?int $limit = null, int $offset = 0, ?array $objectTypes = null)
*/

class ShopifyVariant extends Concrete
{
public const FIELD_PRODUCT_ID = 'productId';
public const FIELD_SHOPIFY_ID = 'shopifyId';
public const FIELD_TITLE = 'title';
public const FIELD_PRICE = 'price';
public const FIELD_SKU = 'sku';
public const FIELD_POSITION = 'position';
public const FIELD_INVENTORY_POLICY = 'inventoryPolicy';
public const FIELD_COMPARE_AT_PRICE = 'compareAtPrice';
public const FIELD_FULFILLMENT_SERVICE = 'fulfillmentService';
public const FIELD_INVENTORY_MANAGEMENT = 'inventoryManagement';
public const FIELD_OPTION1 = 'option1';
public const FIELD_OPTION2 = 'option2';
public const FIELD_OPTION3 = 'option3';
public const FIELD_TAXABLE = 'taxable';
public const FIELD_BARCODE = 'barcode';
public const FIELD_GRAMS = 'grams';
public const FIELD_WEIGHT = 'weight';
public const FIELD_WEIGHT_UNIT = 'weightUnit';
public const FIELD_INVENTORY_ITEM_ID = 'inventoryItemId';
public const FIELD_INVENTORY_QUANTITY = 'inventoryQuantity';
public const FIELD_OLD_INVENTORY_QUANTITY = 'oldInventoryQuantity';
public const FIELD_REQUIRES_SHIPPING = 'requiresShipping';
public const FIELD_IMAGE_ID = 'imageId';
public const FIELD_ADMIN_GRAPHQL_API_ID = 'adminGraphqlApiId';

protected $classId = "shopvariant";
protected $className = "ShopifyVariant";
protected $productId;
protected $shopifyId;
protected $title;
protected $price;
protected $sku;
protected $position;
protected $inventoryPolicy;
protected $compareAtPrice;
protected $fulfillmentService;
protected $inventoryManagement;
protected $option1;
protected $option2;
protected $option3;
protected $taxable;
protected $barcode;
protected $grams;
protected $weight;
protected $weightUnit;
protected $inventoryItemId;
protected $inventoryQuantity;
protected $oldInventoryQuantity;
protected $requiresShipping;
protected $imageId;
protected $adminGraphqlApiId;


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
* Get productId - Product Id
* @return string|null
*/
public function getProductId(): ?string
{
	if ($this instanceof PreGetValueHookInterface && !\Pimcore::inAdmin()) {
		$preValue = $this->preGetValue("productId");
		if ($preValue !== null) {
			return $preValue;
		}
	}

	$data = $this->productId;

	if ($data instanceof \Pimcore\Model\DataObject\Data\EncryptedField) {
		return $data->getPlain();
	}

	return $data;
}

/**
* Set productId - Product Id
* @param string|null $productId
* @return $this
*/
public function setProductId(?string $productId): static
{
	$this->markFieldDirty("productId", true);

	$this->productId = $productId;

	return $this;
}

/**
* Get shopifyId - Shopify Id
* @return string|null
*/
public function getShopifyId(): ?string
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
* @param string|null $shopifyId
* @return $this
*/
public function setShopifyId(?string $shopifyId): static
{
	$this->markFieldDirty("shopifyId", true);

	$this->shopifyId = $shopifyId;

	return $this;
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
* Get price - Price
* @return string|null
*/
public function getPrice(): ?string
{
	if ($this instanceof PreGetValueHookInterface && !\Pimcore::inAdmin()) {
		$preValue = $this->preGetValue("price");
		if ($preValue !== null) {
			return $preValue;
		}
	}

	$data = $this->price;

	if ($data instanceof \Pimcore\Model\DataObject\Data\EncryptedField) {
		return $data->getPlain();
	}

	return $data;
}

/**
* Set price - Price
* @param string|null $price
* @return $this
*/
public function setPrice(?string $price): static
{
	/** @var \Pimcore\Model\DataObject\ClassDefinition\Data\Numeric $fd */
	$fd = $this->getClass()->getFieldDefinition("price");
	$this->price = $fd->preSetData($this, $price);
	return $this;
}

/**
* Get sku - Sku
* @return string|null
*/
public function getSku(): ?string
{
	if ($this instanceof PreGetValueHookInterface && !\Pimcore::inAdmin()) {
		$preValue = $this->preGetValue("sku");
		if ($preValue !== null) {
			return $preValue;
		}
	}

	$data = $this->sku;

	if ($data instanceof \Pimcore\Model\DataObject\Data\EncryptedField) {
		return $data->getPlain();
	}

	return $data;
}

/**
* Set sku - Sku
* @param string|null $sku
* @return $this
*/
public function setSku(?string $sku): static
{
	$this->markFieldDirty("sku", true);

	$this->sku = $sku;

	return $this;
}

/**
* Get position - Position
* @return int|null
*/
public function getPosition(): ?int
{
	if ($this instanceof PreGetValueHookInterface && !\Pimcore::inAdmin()) {
		$preValue = $this->preGetValue("position");
		if ($preValue !== null) {
			return $preValue;
		}
	}

	$data = $this->position;

	if ($data instanceof \Pimcore\Model\DataObject\Data\EncryptedField) {
		return $data->getPlain();
	}

	return $data;
}

/**
* Set position - Position
* @param int|null $position
* @return $this
*/
public function setPosition(?int $position): static
{
	/** @var \Pimcore\Model\DataObject\ClassDefinition\Data\Numeric $fd */
	$fd = $this->getClass()->getFieldDefinition("position");
	$this->position = $fd->preSetData($this, $position);
	return $this;
}

/**
* Get inventoryPolicy - Inventory Policy
* @return string|null
*/
public function getInventoryPolicy(): ?string
{
	if ($this instanceof PreGetValueHookInterface && !\Pimcore::inAdmin()) {
		$preValue = $this->preGetValue("inventoryPolicy");
		if ($preValue !== null) {
			return $preValue;
		}
	}

	$data = $this->inventoryPolicy;

	if ($data instanceof \Pimcore\Model\DataObject\Data\EncryptedField) {
		return $data->getPlain();
	}

	return $data;
}

/**
* Set inventoryPolicy - Inventory Policy
* @param string|null $inventoryPolicy
* @return $this
*/
public function setInventoryPolicy(?string $inventoryPolicy): static
{
	$this->markFieldDirty("inventoryPolicy", true);

	$this->inventoryPolicy = $inventoryPolicy;

	return $this;
}

/**
* Get compareAtPrice - Compare At Price
* @return string|null
*/
public function getCompareAtPrice(): ?string
{
	if ($this instanceof PreGetValueHookInterface && !\Pimcore::inAdmin()) {
		$preValue = $this->preGetValue("compareAtPrice");
		if ($preValue !== null) {
			return $preValue;
		}
	}

	$data = $this->compareAtPrice;

	if ($data instanceof \Pimcore\Model\DataObject\Data\EncryptedField) {
		return $data->getPlain();
	}

	return $data;
}

/**
* Set compareAtPrice - Compare At Price
* @param string|null $compareAtPrice
* @return $this
*/
public function setCompareAtPrice(?string $compareAtPrice): static
{
	$this->markFieldDirty("compareAtPrice", true);

	$this->compareAtPrice = $compareAtPrice;

	return $this;
}

/**
* Get fulfillmentService - Fulfillment Service
* @return string|null
*/
public function getFulfillmentService(): ?string
{
	if ($this instanceof PreGetValueHookInterface && !\Pimcore::inAdmin()) {
		$preValue = $this->preGetValue("fulfillmentService");
		if ($preValue !== null) {
			return $preValue;
		}
	}

	$data = $this->fulfillmentService;

	if ($data instanceof \Pimcore\Model\DataObject\Data\EncryptedField) {
		return $data->getPlain();
	}

	return $data;
}

/**
* Set fulfillmentService - Fulfillment Service
* @param string|null $fulfillmentService
* @return $this
*/
public function setFulfillmentService(?string $fulfillmentService): static
{
	$this->markFieldDirty("fulfillmentService", true);

	$this->fulfillmentService = $fulfillmentService;

	return $this;
}

/**
* Get inventoryManagement - Inventory Management
* @return string|null
*/
public function getInventoryManagement(): ?string
{
	if ($this instanceof PreGetValueHookInterface && !\Pimcore::inAdmin()) {
		$preValue = $this->preGetValue("inventoryManagement");
		if ($preValue !== null) {
			return $preValue;
		}
	}

	$data = $this->inventoryManagement;

	if ($data instanceof \Pimcore\Model\DataObject\Data\EncryptedField) {
		return $data->getPlain();
	}

	return $data;
}

/**
* Set inventoryManagement - Inventory Management
* @param string|null $inventoryManagement
* @return $this
*/
public function setInventoryManagement(?string $inventoryManagement): static
{
	$this->markFieldDirty("inventoryManagement", true);

	$this->inventoryManagement = $inventoryManagement;

	return $this;
}

/**
* Get option1 - Option1
* @return string|null
*/
public function getOption1(): ?string
{
	if ($this instanceof PreGetValueHookInterface && !\Pimcore::inAdmin()) {
		$preValue = $this->preGetValue("option1");
		if ($preValue !== null) {
			return $preValue;
		}
	}

	$data = $this->option1;

	if ($data instanceof \Pimcore\Model\DataObject\Data\EncryptedField) {
		return $data->getPlain();
	}

	return $data;
}

/**
* Set option1 - Option1
* @param string|null $option1
* @return $this
*/
public function setOption1(?string $option1): static
{
	$this->markFieldDirty("option1", true);

	$this->option1 = $option1;

	return $this;
}

/**
* Get option2 - Option2
* @return string|null
*/
public function getOption2(): ?string
{
	if ($this instanceof PreGetValueHookInterface && !\Pimcore::inAdmin()) {
		$preValue = $this->preGetValue("option2");
		if ($preValue !== null) {
			return $preValue;
		}
	}

	$data = $this->option2;

	if ($data instanceof \Pimcore\Model\DataObject\Data\EncryptedField) {
		return $data->getPlain();
	}

	return $data;
}

/**
* Set option2 - Option2
* @param string|null $option2
* @return $this
*/
public function setOption2(?string $option2): static
{
	$this->markFieldDirty("option2", true);

	$this->option2 = $option2;

	return $this;
}

/**
* Get option3 - Option3
* @return string|null
*/
public function getOption3(): ?string
{
	if ($this instanceof PreGetValueHookInterface && !\Pimcore::inAdmin()) {
		$preValue = $this->preGetValue("option3");
		if ($preValue !== null) {
			return $preValue;
		}
	}

	$data = $this->option3;

	if ($data instanceof \Pimcore\Model\DataObject\Data\EncryptedField) {
		return $data->getPlain();
	}

	return $data;
}

/**
* Set option3 - Option3
* @param string|null $option3
* @return $this
*/
public function setOption3(?string $option3): static
{
	$this->markFieldDirty("option3", true);

	$this->option3 = $option3;

	return $this;
}

/**
* Get taxable - Taxable
* @return bool|null
*/
public function getTaxable(): ?bool
{
	if ($this instanceof PreGetValueHookInterface && !\Pimcore::inAdmin()) {
		$preValue = $this->preGetValue("taxable");
		if ($preValue !== null) {
			return $preValue;
		}
	}

	$data = $this->taxable;

	if ($data instanceof \Pimcore\Model\DataObject\Data\EncryptedField) {
		return $data->getPlain();
	}

	return $data;
}

/**
* Set taxable - Taxable
* @param bool|null $taxable
* @return $this
*/
public function setTaxable(?bool $taxable): static
{
	$this->markFieldDirty("taxable", true);

	$this->taxable = $taxable;

	return $this;
}

/**
* Get barcode - Barcode
* @return string|null
*/
public function getBarcode(): ?string
{
	if ($this instanceof PreGetValueHookInterface && !\Pimcore::inAdmin()) {
		$preValue = $this->preGetValue("barcode");
		if ($preValue !== null) {
			return $preValue;
		}
	}

	$data = $this->barcode;

	if ($data instanceof \Pimcore\Model\DataObject\Data\EncryptedField) {
		return $data->getPlain();
	}

	return $data;
}

/**
* Set barcode - Barcode
* @param string|null $barcode
* @return $this
*/
public function setBarcode(?string $barcode): static
{
	$this->markFieldDirty("barcode", true);

	$this->barcode = $barcode;

	return $this;
}

/**
* Get grams - Grams
* @return string|null
*/
public function getGrams(): ?string
{
	if ($this instanceof PreGetValueHookInterface && !\Pimcore::inAdmin()) {
		$preValue = $this->preGetValue("grams");
		if ($preValue !== null) {
			return $preValue;
		}
	}

	$data = $this->grams;

	if ($data instanceof \Pimcore\Model\DataObject\Data\EncryptedField) {
		return $data->getPlain();
	}

	return $data;
}

/**
* Set grams - Grams
* @param string|null $grams
* @return $this
*/
public function setGrams(?string $grams): static
{
	/** @var \Pimcore\Model\DataObject\ClassDefinition\Data\Numeric $fd */
	$fd = $this->getClass()->getFieldDefinition("grams");
	$this->grams = $fd->preSetData($this, $grams);
	return $this;
}

/**
* Get weight - Weight
* @return string|null
*/
public function getWeight(): ?string
{
	if ($this instanceof PreGetValueHookInterface && !\Pimcore::inAdmin()) {
		$preValue = $this->preGetValue("weight");
		if ($preValue !== null) {
			return $preValue;
		}
	}

	$data = $this->weight;

	if ($data instanceof \Pimcore\Model\DataObject\Data\EncryptedField) {
		return $data->getPlain();
	}

	return $data;
}

/**
* Set weight - Weight
* @param string|null $weight
* @return $this
*/
public function setWeight(?string $weight): static
{
	/** @var \Pimcore\Model\DataObject\ClassDefinition\Data\Numeric $fd */
	$fd = $this->getClass()->getFieldDefinition("weight");
	$this->weight = $fd->preSetData($this, $weight);
	return $this;
}

/**
* Get weightUnit - Weight Unit
* @return string|null
*/
public function getWeightUnit(): ?string
{
	if ($this instanceof PreGetValueHookInterface && !\Pimcore::inAdmin()) {
		$preValue = $this->preGetValue("weightUnit");
		if ($preValue !== null) {
			return $preValue;
		}
	}

	$data = $this->weightUnit;

	if ($data instanceof \Pimcore\Model\DataObject\Data\EncryptedField) {
		return $data->getPlain();
	}

	return $data;
}

/**
* Set weightUnit - Weight Unit
* @param string|null $weightUnit
* @return $this
*/
public function setWeightUnit(?string $weightUnit): static
{
	$this->markFieldDirty("weightUnit", true);

	$this->weightUnit = $weightUnit;

	return $this;
}

/**
* Get inventoryItemId - Inventory Item Id
* @return string|null
*/
public function getInventoryItemId(): ?string
{
	if ($this instanceof PreGetValueHookInterface && !\Pimcore::inAdmin()) {
		$preValue = $this->preGetValue("inventoryItemId");
		if ($preValue !== null) {
			return $preValue;
		}
	}

	$data = $this->inventoryItemId;

	if ($data instanceof \Pimcore\Model\DataObject\Data\EncryptedField) {
		return $data->getPlain();
	}

	return $data;
}

/**
* Set inventoryItemId - Inventory Item Id
* @param string|null $inventoryItemId
* @return $this
*/
public function setInventoryItemId(?string $inventoryItemId): static
{
	$this->markFieldDirty("inventoryItemId", true);

	$this->inventoryItemId = $inventoryItemId;

	return $this;
}

/**
* Get inventoryQuantity - Inventory Quantity
* @return int|null
*/
public function getInventoryQuantity(): ?int
{
	if ($this instanceof PreGetValueHookInterface && !\Pimcore::inAdmin()) {
		$preValue = $this->preGetValue("inventoryQuantity");
		if ($preValue !== null) {
			return $preValue;
		}
	}

	$data = $this->inventoryQuantity;

	if ($data instanceof \Pimcore\Model\DataObject\Data\EncryptedField) {
		return $data->getPlain();
	}

	return $data;
}

/**
* Set inventoryQuantity - Inventory Quantity
* @param int|null $inventoryQuantity
* @return $this
*/
public function setInventoryQuantity(?int $inventoryQuantity): static
{
	/** @var \Pimcore\Model\DataObject\ClassDefinition\Data\Numeric $fd */
	$fd = $this->getClass()->getFieldDefinition("inventoryQuantity");
	$this->inventoryQuantity = $fd->preSetData($this, $inventoryQuantity);
	return $this;
}

/**
* Get oldInventoryQuantity - Old Inventory Quantity
* @return int|null
*/
public function getOldInventoryQuantity(): ?int
{
	if ($this instanceof PreGetValueHookInterface && !\Pimcore::inAdmin()) {
		$preValue = $this->preGetValue("oldInventoryQuantity");
		if ($preValue !== null) {
			return $preValue;
		}
	}

	$data = $this->oldInventoryQuantity;

	if ($data instanceof \Pimcore\Model\DataObject\Data\EncryptedField) {
		return $data->getPlain();
	}

	return $data;
}

/**
* Set oldInventoryQuantity - Old Inventory Quantity
* @param int|null $oldInventoryQuantity
* @return $this
*/
public function setOldInventoryQuantity(?int $oldInventoryQuantity): static
{
	/** @var \Pimcore\Model\DataObject\ClassDefinition\Data\Numeric $fd */
	$fd = $this->getClass()->getFieldDefinition("oldInventoryQuantity");
	$this->oldInventoryQuantity = $fd->preSetData($this, $oldInventoryQuantity);
	return $this;
}

/**
* Get requiresShipping - Requires Shipping
* @return bool|null
*/
public function getRequiresShipping(): ?bool
{
	if ($this instanceof PreGetValueHookInterface && !\Pimcore::inAdmin()) {
		$preValue = $this->preGetValue("requiresShipping");
		if ($preValue !== null) {
			return $preValue;
		}
	}

	$data = $this->requiresShipping;

	if ($data instanceof \Pimcore\Model\DataObject\Data\EncryptedField) {
		return $data->getPlain();
	}

	return $data;
}

/**
* Set requiresShipping - Requires Shipping
* @param bool|null $requiresShipping
* @return $this
*/
public function setRequiresShipping(?bool $requiresShipping): static
{
	$this->markFieldDirty("requiresShipping", true);

	$this->requiresShipping = $requiresShipping;

	return $this;
}

/**
* Get imageId - Image Id
* @return string|null
*/
public function getImageId(): ?string
{
	if ($this instanceof PreGetValueHookInterface && !\Pimcore::inAdmin()) {
		$preValue = $this->preGetValue("imageId");
		if ($preValue !== null) {
			return $preValue;
		}
	}

	$data = $this->imageId;

	if ($data instanceof \Pimcore\Model\DataObject\Data\EncryptedField) {
		return $data->getPlain();
	}

	return $data;
}

/**
* Set imageId - Image Id
* @param string|null $imageId
* @return $this
*/
public function setImageId(?string $imageId): static
{
	$this->markFieldDirty("imageId", true);

	$this->imageId = $imageId;

	return $this;
}

/**
* Get adminGraphqlApiId - Admin Graphql Api Id
* @return string|null
*/
public function getAdminGraphqlApiId(): ?string
{
	if ($this instanceof PreGetValueHookInterface && !\Pimcore::inAdmin()) {
		$preValue = $this->preGetValue("adminGraphqlApiId");
		if ($preValue !== null) {
			return $preValue;
		}
	}

	$data = $this->adminGraphqlApiId;

	if ($data instanceof \Pimcore\Model\DataObject\Data\EncryptedField) {
		return $data->getPlain();
	}

	return $data;
}

/**
* Set adminGraphqlApiId - Admin Graphql Api Id
* @param string|null $adminGraphqlApiId
* @return $this
*/
public function setAdminGraphqlApiId(?string $adminGraphqlApiId): static
{
	$this->markFieldDirty("adminGraphqlApiId", true);

	$this->adminGraphqlApiId = $adminGraphqlApiId;

	return $this;
}

}

