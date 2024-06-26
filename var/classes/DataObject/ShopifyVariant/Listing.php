<?php

namespace Pimcore\Model\DataObject\ShopifyVariant;

use Pimcore\Model;
use Pimcore\Model\DataObject;

/**
 * @method DataObject\ShopifyVariant|false current()
 * @method DataObject\ShopifyVariant[] load()
 * @method DataObject\ShopifyVariant[] getData()
 * @method DataObject\ShopifyVariant[] getObjects()
 */

class Listing extends DataObject\Listing\Concrete
{
protected $classId = "shopvariant";
protected $className = "ShopifyVariant";


/**
* Filter by productId (Product Id)
* @param string|int|float|array|Model\Element\ElementInterface $data  comparison data, can be scalar or array (if operator is e.g. "IN (?)")
* @param string $operator  SQL comparison operator, e.g. =, <, >= etc. You can use "?" as placeholder, e.g. "IN (?)"
* @return $this
*/
public function filterByProductId ($data, $operator = '='): static
{
	$this->getClass()->getFieldDefinition("productId")->addListingFilter($this, $data, $operator);
	return $this;
}

/**
* Filter by shopifyId (Shopify Id)
* @param string|int|float|array|Model\Element\ElementInterface $data  comparison data, can be scalar or array (if operator is e.g. "IN (?)")
* @param string $operator  SQL comparison operator, e.g. =, <, >= etc. You can use "?" as placeholder, e.g. "IN (?)"
* @return $this
*/
public function filterByShopifyId ($data, $operator = '='): static
{
	$this->getClass()->getFieldDefinition("shopifyId")->addListingFilter($this, $data, $operator);
	return $this;
}

/**
* Filter by title (Title)
* @param string|int|float|array|Model\Element\ElementInterface $data  comparison data, can be scalar or array (if operator is e.g. "IN (?)")
* @param string $operator  SQL comparison operator, e.g. =, <, >= etc. You can use "?" as placeholder, e.g. "IN (?)"
* @return $this
*/
public function filterByTitle ($data, $operator = '='): static
{
	$this->getClass()->getFieldDefinition("title")->addListingFilter($this, $data, $operator);
	return $this;
}

/**
* Filter by price (Price)
* @param string|int|float|array|Model\Element\ElementInterface $data  comparison data, can be scalar or array (if operator is e.g. "IN (?)")
* @param string $operator  SQL comparison operator, e.g. =, <, >= etc. You can use "?" as placeholder, e.g. "IN (?)"
* @return $this
*/
public function filterByPrice ($data, $operator = '='): static
{
	$this->getClass()->getFieldDefinition("price")->addListingFilter($this, $data, $operator);
	return $this;
}

/**
* Filter by sku (Sku)
* @param string|int|float|array|Model\Element\ElementInterface $data  comparison data, can be scalar or array (if operator is e.g. "IN (?)")
* @param string $operator  SQL comparison operator, e.g. =, <, >= etc. You can use "?" as placeholder, e.g. "IN (?)"
* @return $this
*/
public function filterBySku ($data, $operator = '='): static
{
	$this->getClass()->getFieldDefinition("sku")->addListingFilter($this, $data, $operator);
	return $this;
}

/**
* Filter by position (Position)
* @param string|int|float|array|Model\Element\ElementInterface $data  comparison data, can be scalar or array (if operator is e.g. "IN (?)")
* @param string $operator  SQL comparison operator, e.g. =, <, >= etc. You can use "?" as placeholder, e.g. "IN (?)"
* @return $this
*/
public function filterByPosition ($data, $operator = '='): static
{
	$this->getClass()->getFieldDefinition("position")->addListingFilter($this, $data, $operator);
	return $this;
}

/**
* Filter by inventoryPolicy (Inventory Policy)
* @param string|int|float|array|Model\Element\ElementInterface $data  comparison data, can be scalar or array (if operator is e.g. "IN (?)")
* @param string $operator  SQL comparison operator, e.g. =, <, >= etc. You can use "?" as placeholder, e.g. "IN (?)"
* @return $this
*/
public function filterByInventoryPolicy ($data, $operator = '='): static
{
	$this->getClass()->getFieldDefinition("inventoryPolicy")->addListingFilter($this, $data, $operator);
	return $this;
}

/**
* Filter by compareAtPrice (Compare At Price)
* @param string|int|float|array|Model\Element\ElementInterface $data  comparison data, can be scalar or array (if operator is e.g. "IN (?)")
* @param string $operator  SQL comparison operator, e.g. =, <, >= etc. You can use "?" as placeholder, e.g. "IN (?)"
* @return $this
*/
public function filterByCompareAtPrice ($data, $operator = '='): static
{
	$this->getClass()->getFieldDefinition("compareAtPrice")->addListingFilter($this, $data, $operator);
	return $this;
}

/**
* Filter by fulfillmentService (Fulfillment Service)
* @param string|int|float|array|Model\Element\ElementInterface $data  comparison data, can be scalar or array (if operator is e.g. "IN (?)")
* @param string $operator  SQL comparison operator, e.g. =, <, >= etc. You can use "?" as placeholder, e.g. "IN (?)"
* @return $this
*/
public function filterByFulfillmentService ($data, $operator = '='): static
{
	$this->getClass()->getFieldDefinition("fulfillmentService")->addListingFilter($this, $data, $operator);
	return $this;
}

/**
* Filter by inventoryManagement (Inventory Management)
* @param string|int|float|array|Model\Element\ElementInterface $data  comparison data, can be scalar or array (if operator is e.g. "IN (?)")
* @param string $operator  SQL comparison operator, e.g. =, <, >= etc. You can use "?" as placeholder, e.g. "IN (?)"
* @return $this
*/
public function filterByInventoryManagement ($data, $operator = '='): static
{
	$this->getClass()->getFieldDefinition("inventoryManagement")->addListingFilter($this, $data, $operator);
	return $this;
}

/**
* Filter by option1 (Option1)
* @param string|int|float|array|Model\Element\ElementInterface $data  comparison data, can be scalar or array (if operator is e.g. "IN (?)")
* @param string $operator  SQL comparison operator, e.g. =, <, >= etc. You can use "?" as placeholder, e.g. "IN (?)"
* @return $this
*/
public function filterByOption1 ($data, $operator = '='): static
{
	$this->getClass()->getFieldDefinition("option1")->addListingFilter($this, $data, $operator);
	return $this;
}

/**
* Filter by option2 (Option2)
* @param string|int|float|array|Model\Element\ElementInterface $data  comparison data, can be scalar or array (if operator is e.g. "IN (?)")
* @param string $operator  SQL comparison operator, e.g. =, <, >= etc. You can use "?" as placeholder, e.g. "IN (?)"
* @return $this
*/
public function filterByOption2 ($data, $operator = '='): static
{
	$this->getClass()->getFieldDefinition("option2")->addListingFilter($this, $data, $operator);
	return $this;
}

/**
* Filter by option3 (Option3)
* @param string|int|float|array|Model\Element\ElementInterface $data  comparison data, can be scalar or array (if operator is e.g. "IN (?)")
* @param string $operator  SQL comparison operator, e.g. =, <, >= etc. You can use "?" as placeholder, e.g. "IN (?)"
* @return $this
*/
public function filterByOption3 ($data, $operator = '='): static
{
	$this->getClass()->getFieldDefinition("option3")->addListingFilter($this, $data, $operator);
	return $this;
}

/**
* Filter by taxable (Taxable)
* @param string|int|float|array|Model\Element\ElementInterface $data  comparison data, can be scalar or array (if operator is e.g. "IN (?)")
* @param string $operator  SQL comparison operator, e.g. =, <, >= etc. You can use "?" as placeholder, e.g. "IN (?)"
* @return $this
*/
public function filterByTaxable ($data, $operator = '='): static
{
	$this->getClass()->getFieldDefinition("taxable")->addListingFilter($this, $data, $operator);
	return $this;
}

/**
* Filter by barcode (Barcode)
* @param string|int|float|array|Model\Element\ElementInterface $data  comparison data, can be scalar or array (if operator is e.g. "IN (?)")
* @param string $operator  SQL comparison operator, e.g. =, <, >= etc. You can use "?" as placeholder, e.g. "IN (?)"
* @return $this
*/
public function filterByBarcode ($data, $operator = '='): static
{
	$this->getClass()->getFieldDefinition("barcode")->addListingFilter($this, $data, $operator);
	return $this;
}

/**
* Filter by grams (Grams)
* @param string|int|float|array|Model\Element\ElementInterface $data  comparison data, can be scalar or array (if operator is e.g. "IN (?)")
* @param string $operator  SQL comparison operator, e.g. =, <, >= etc. You can use "?" as placeholder, e.g. "IN (?)"
* @return $this
*/
public function filterByGrams ($data, $operator = '='): static
{
	$this->getClass()->getFieldDefinition("grams")->addListingFilter($this, $data, $operator);
	return $this;
}

/**
* Filter by weight (Weight)
* @param string|int|float|array|Model\Element\ElementInterface $data  comparison data, can be scalar or array (if operator is e.g. "IN (?)")
* @param string $operator  SQL comparison operator, e.g. =, <, >= etc. You can use "?" as placeholder, e.g. "IN (?)"
* @return $this
*/
public function filterByWeight ($data, $operator = '='): static
{
	$this->getClass()->getFieldDefinition("weight")->addListingFilter($this, $data, $operator);
	return $this;
}

/**
* Filter by weightUnit (Weight Unit)
* @param string|int|float|array|Model\Element\ElementInterface $data  comparison data, can be scalar or array (if operator is e.g. "IN (?)")
* @param string $operator  SQL comparison operator, e.g. =, <, >= etc. You can use "?" as placeholder, e.g. "IN (?)"
* @return $this
*/
public function filterByWeightUnit ($data, $operator = '='): static
{
	$this->getClass()->getFieldDefinition("weightUnit")->addListingFilter($this, $data, $operator);
	return $this;
}

/**
* Filter by inventoryItemId (Inventory Item Id)
* @param string|int|float|array|Model\Element\ElementInterface $data  comparison data, can be scalar or array (if operator is e.g. "IN (?)")
* @param string $operator  SQL comparison operator, e.g. =, <, >= etc. You can use "?" as placeholder, e.g. "IN (?)"
* @return $this
*/
public function filterByInventoryItemId ($data, $operator = '='): static
{
	$this->getClass()->getFieldDefinition("inventoryItemId")->addListingFilter($this, $data, $operator);
	return $this;
}

/**
* Filter by inventoryQuantity (Inventory Quantity)
* @param string|int|float|array|Model\Element\ElementInterface $data  comparison data, can be scalar or array (if operator is e.g. "IN (?)")
* @param string $operator  SQL comparison operator, e.g. =, <, >= etc. You can use "?" as placeholder, e.g. "IN (?)"
* @return $this
*/
public function filterByInventoryQuantity ($data, $operator = '='): static
{
	$this->getClass()->getFieldDefinition("inventoryQuantity")->addListingFilter($this, $data, $operator);
	return $this;
}

/**
* Filter by oldInventoryQuantity (Old Inventory Quantity)
* @param string|int|float|array|Model\Element\ElementInterface $data  comparison data, can be scalar or array (if operator is e.g. "IN (?)")
* @param string $operator  SQL comparison operator, e.g. =, <, >= etc. You can use "?" as placeholder, e.g. "IN (?)"
* @return $this
*/
public function filterByOldInventoryQuantity ($data, $operator = '='): static
{
	$this->getClass()->getFieldDefinition("oldInventoryQuantity")->addListingFilter($this, $data, $operator);
	return $this;
}

/**
* Filter by requiresShipping (Requires Shipping)
* @param string|int|float|array|Model\Element\ElementInterface $data  comparison data, can be scalar or array (if operator is e.g. "IN (?)")
* @param string $operator  SQL comparison operator, e.g. =, <, >= etc. You can use "?" as placeholder, e.g. "IN (?)"
* @return $this
*/
public function filterByRequiresShipping ($data, $operator = '='): static
{
	$this->getClass()->getFieldDefinition("requiresShipping")->addListingFilter($this, $data, $operator);
	return $this;
}

/**
* Filter by imageId (Image Id)
* @param string|int|float|array|Model\Element\ElementInterface $data  comparison data, can be scalar or array (if operator is e.g. "IN (?)")
* @param string $operator  SQL comparison operator, e.g. =, <, >= etc. You can use "?" as placeholder, e.g. "IN (?)"
* @return $this
*/
public function filterByImageId ($data, $operator = '='): static
{
	$this->getClass()->getFieldDefinition("imageId")->addListingFilter($this, $data, $operator);
	return $this;
}

/**
* Filter by adminGraphqlApiId (Admin Graphql Api Id)
* @param string|int|float|array|Model\Element\ElementInterface $data  comparison data, can be scalar or array (if operator is e.g. "IN (?)")
* @param string $operator  SQL comparison operator, e.g. =, <, >= etc. You can use "?" as placeholder, e.g. "IN (?)"
* @return $this
*/
public function filterByAdminGraphqlApiId ($data, $operator = '='): static
{
	$this->getClass()->getFieldDefinition("adminGraphqlApiId")->addListingFilter($this, $data, $operator);
	return $this;
}



}
