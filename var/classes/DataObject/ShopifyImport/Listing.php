<?php

namespace Pimcore\Model\DataObject\ShopifyImport;

use Pimcore\Model;
use Pimcore\Model\DataObject;

/**
 * @method DataObject\ShopifyImport|false current()
 * @method DataObject\ShopifyImport[] load()
 * @method DataObject\ShopifyImport[] getData()
 * @method DataObject\ShopifyImport[] getObjects()
 */

class Listing extends DataObject\Listing\Concrete
{
protected $classId = "shopimport";
protected $className = "ShopifyImport";


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
* Filter by bodyHtml (Body Html)
* @param string|int|float|array|Model\Element\ElementInterface $data  comparison data, can be scalar or array (if operator is e.g. "IN (?)")
* @param string $operator  SQL comparison operator, e.g. =, <, >= etc. You can use "?" as placeholder, e.g. "IN (?)"
* @return $this
*/
public function filterByBodyHtml ($data, $operator = '='): static
{
	$this->getClass()->getFieldDefinition("bodyHtml")->addListingFilter($this, $data, $operator);
	return $this;
}

/**
* Filter by handle (Handle)
* @param string|int|float|array|Model\Element\ElementInterface $data  comparison data, can be scalar or array (if operator is e.g. "IN (?)")
* @param string $operator  SQL comparison operator, e.g. =, <, >= etc. You can use "?" as placeholder, e.g. "IN (?)"
* @return $this
*/
public function filterByHandle ($data, $operator = '='): static
{
	$this->getClass()->getFieldDefinition("handle")->addListingFilter($this, $data, $operator);
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
* Filter by imagesJson (Images Json)
* @param string|int|float|array|Model\Element\ElementInterface $data  comparison data, can be scalar or array (if operator is e.g. "IN (?)")
* @param string $operator  SQL comparison operator, e.g. =, <, >= etc. You can use "?" as placeholder, e.g. "IN (?)"
* @return $this
*/
public function filterByImagesJson ($data, $operator = '='): static
{
	$this->getClass()->getFieldDefinition("imagesJson")->addListingFilter($this, $data, $operator);
	return $this;
}

/**
* Filter by optionsJson (Options Json)
* @param string|int|float|array|Model\Element\ElementInterface $data  comparison data, can be scalar or array (if operator is e.g. "IN (?)")
* @param string $operator  SQL comparison operator, e.g. =, <, >= etc. You can use "?" as placeholder, e.g. "IN (?)"
* @return $this
*/
public function filterByOptionsJson ($data, $operator = '='): static
{
	$this->getClass()->getFieldDefinition("optionsJson")->addListingFilter($this, $data, $operator);
	return $this;
}

/**
* Filter by productType (Product Type)
* @param string|int|float|array|Model\Element\ElementInterface $data  comparison data, can be scalar or array (if operator is e.g. "IN (?)")
* @param string $operator  SQL comparison operator, e.g. =, <, >= etc. You can use "?" as placeholder, e.g. "IN (?)"
* @return $this
*/
public function filterByProductType ($data, $operator = '='): static
{
	$this->getClass()->getFieldDefinition("productType")->addListingFilter($this, $data, $operator);
	return $this;
}

/**
* Filter by variants (Variants)
* @param mixed $data
* @param string $operator SQL comparison operator, e.g. =, <, >= etc. You can use "?" as placeholder, e.g. "IN (?)"
* @return $this
*/
public function filterByVariants ($data, $operator = '='): static
{
	$this->getClass()->getFieldDefinition("variants")->addListingFilter($this, $data, $operator);
	return $this;
}

/**
* Filter by vendor (Vendor)
* @param string|int|float|array|Model\Element\ElementInterface $data  comparison data, can be scalar or array (if operator is e.g. "IN (?)")
* @param string $operator  SQL comparison operator, e.g. =, <, >= etc. You can use "?" as placeholder, e.g. "IN (?)"
* @return $this
*/
public function filterByVendor ($data, $operator = '='): static
{
	$this->getClass()->getFieldDefinition("vendor")->addListingFilter($this, $data, $operator);
	return $this;
}



}
