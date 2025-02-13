<?php

namespace Pimcore\Model\DataObject\VariantProduct;

use Pimcore\Model;
use Pimcore\Model\DataObject;

/**
 * @method DataObject\VariantProduct|false current()
 * @method DataObject\VariantProduct[] load()
 * @method DataObject\VariantProduct[] getData()
 * @method DataObject\VariantProduct[] getObjects()
 */

class Listing extends DataObject\Listing\Concrete
{
protected $classId = "varyantproduct";
protected $className = "VariantProduct";


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
* Filter by lastUpdate (Last Update)
* @param string|int|float|array|Model\Element\ElementInterface $data  comparison data, can be scalar or array (if operator is e.g. "IN (?)")
* @param string $operator  SQL comparison operator, e.g. =, <, >= etc. You can use "?" as placeholder, e.g. "IN (?)"
* @return $this
*/
public function filterByLastUpdate ($data, $operator = '='): static
{
	$this->getClass()->getFieldDefinition("lastUpdate")->addListingFilter($this, $data, $operator);
	return $this;
}

/**
* Filter by salePrice (Sale Price)
* @param string|int|float|array|Model\Element\ElementInterface $data  comparison data, can be scalar or array (if operator is e.g. "IN (?)")
* @param string $operator  SQL comparison operator, e.g. =, <, >= etc. You can use "?" as placeholder, e.g. "IN (?)"
* @return $this
*/
public function filterBySalePrice ($data, $operator = '='): static
{
	$this->getClass()->getFieldDefinition("salePrice")->addListingFilter($this, $data, $operator);
	return $this;
}

/**
* Filter by saleCurrency (Sale Currency)
* @param string|int|float|array|Model\Element\ElementInterface $data  comparison data, can be scalar or array (if operator is e.g. "IN (?)")
* @param string $operator  SQL comparison operator, e.g. =, <, >= etc. You can use "?" as placeholder, e.g. "IN (?)"
* @return $this
*/
public function filterBySaleCurrency ($data, $operator = '='): static
{
	$this->getClass()->getFieldDefinition("saleCurrency")->addListingFilter($this, $data, $operator);
	return $this;
}

/**
* Filter by attributes (Attributes)
* @param string|int|float|array|Model\Element\ElementInterface $data  comparison data, can be scalar or array (if operator is e.g. "IN (?)")
* @param string $operator  SQL comparison operator, e.g. =, <, >= etc. You can use "?" as placeholder, e.g. "IN (?)"
* @return $this
*/
public function filterByAttributes ($data, $operator = '='): static
{
	$this->getClass()->getFieldDefinition("attributes")->addListingFilter($this, $data, $operator);
	return $this;
}

/**
* Filter by uniqueMarketplaceId (Unique Marketplace Id)
* @param string|int|float|array|Model\Element\ElementInterface $data  comparison data, can be scalar or array (if operator is e.g. "IN (?)")
* @param string $operator  SQL comparison operator, e.g. =, <, >= etc. You can use "?" as placeholder, e.g. "IN (?)"
* @return $this
*/
public function filterByUniqueMarketplaceId ($data, $operator = '='): static
{
	$this->getClass()->getFieldDefinition("uniqueMarketplaceId")->addListingFilter($this, $data, $operator);
	return $this;
}

/**
* Filter by marketplace (Marketplace)
* @param mixed $data
* @param string $operator SQL comparison operator, e.g. =, <, >= etc. You can use "?" as placeholder, e.g. "IN (?)"
* @return $this
*/
public function filterByMarketplace ($data, $operator = '='): static
{
	$this->getClass()->getFieldDefinition("marketplace")->addListingFilter($this, $data, $operator);
	return $this;
}

/**
* Filter by mainProduct (Main Product)
* @param mixed $data
* @param string $operator SQL comparison operator, e.g. =, <, >= etc. You can use "?" as placeholder, e.g. "IN (?)"
* @return $this
*/
public function filterByMainProduct ($data, $operator = '='): static
{
	$this->getClass()->getFieldDefinition("mainProduct")->addListingFilter($this, $data, $operator);
	return $this;
}

/**
* Filter by sellerSku (Girilen SKU)
* @param string|int|float|array|Model\Element\ElementInterface $data  comparison data, can be scalar or array (if operator is e.g. "IN (?)")
* @param string $operator  SQL comparison operator, e.g. =, <, >= etc. You can use "?" as placeholder, e.g. "IN (?)"
* @return $this
*/
public function filterBySellerSku ($data, $operator = '='): static
{
	$this->getClass()->getFieldDefinition("sellerSku")->addListingFilter($this, $data, $operator);
	return $this;
}

/**
* Filter by quantity (Miktar)
* @param string|int|float|array|Model\Element\ElementInterface $data  comparison data, can be scalar or array (if operator is e.g. "IN (?)")
* @param string $operator  SQL comparison operator, e.g. =, <, >= etc. You can use "?" as placeholder, e.g. "IN (?)"
* @return $this
*/
public function filterByQuantity ($data, $operator = '='): static
{
	$this->getClass()->getFieldDefinition("quantity")->addListingFilter($this, $data, $operator);
	return $this;
}

/**
* Filter by calculatedWisersellCode (Calculated Wisersell Code)
* @param string|int|float|array|Model\Element\ElementInterface $data  comparison data, can be scalar or array (if operator is e.g. "IN (?)")
* @param string $operator  SQL comparison operator, e.g. =, <, >= etc. You can use "?" as placeholder, e.g. "IN (?)"
* @return $this
*/
public function filterByCalculatedWisersellCode ($data, $operator = '='): static
{
	$this->getClass()->getFieldDefinition("calculatedWisersellCode")->addListingFilter($this, $data, $operator);
	return $this;
}

/**
* Filter by wisersellVariantCode (Wisersell Variant Code)
* @param string|int|float|array|Model\Element\ElementInterface $data  comparison data, can be scalar or array (if operator is e.g. "IN (?)")
* @param string $operator  SQL comparison operator, e.g. =, <, >= etc. You can use "?" as placeholder, e.g. "IN (?)"
* @return $this
*/
public function filterByWisersellVariantCode ($data, $operator = '='): static
{
	$this->getClass()->getFieldDefinition("wisersellVariantCode")->addListingFilter($this, $data, $operator);
	return $this;
}

/**
* Filter by wisersellVariantJson (Wisersell Variant Json)
* @param string|int|float|array|Model\Element\ElementInterface $data  comparison data, can be scalar or array (if operator is e.g. "IN (?)")
* @param string $operator  SQL comparison operator, e.g. =, <, >= etc. You can use "?" as placeholder, e.g. "IN (?)"
* @return $this
*/
public function filterByWisersellVariantJson ($data, $operator = '='): static
{
	$this->getClass()->getFieldDefinition("wisersellVariantJson")->addListingFilter($this, $data, $operator);
	return $this;
}

/**
* Filter by countMainProduct (Count Main Product)
* @param string|int|float|array|Model\Element\ElementInterface $data  comparison data, can be scalar or array (if operator is e.g. "IN (?)")
* @param string $operator  SQL comparison operator, e.g. =, <, >= etc. You can use "?" as placeholder, e.g. "IN (?)"
* @return $this
*/
public function filterByCountMainProduct ($data, $operator = '='): static
{
	$this->getClass()->getFieldDefinition("countMainProduct")->addListingFilter($this, $data, $operator);
	return $this;
}

/**
* Filter by fnsku (Fnsku)
* @param string|int|float|array|Model\Element\ElementInterface $data  comparison data, can be scalar or array (if operator is e.g. "IN (?)")
* @param string $operator  SQL comparison operator, e.g. =, <, >= etc. You can use "?" as placeholder, e.g. "IN (?)"
* @return $this
*/
public function filterByFnsku ($data, $operator = '='): static
{
	$this->getClass()->getFieldDefinition("fnsku")->addListingFilter($this, $data, $operator);
	return $this;
}

/**
* Filter by marketplaceType (Pazaryeri Tipi)
* @param string|int|float|array|Model\Element\ElementInterface $data  comparison data, can be scalar or array (if operator is e.g. "IN (?)")
* @param string $operator  SQL comparison operator, e.g. =, <, >= etc. You can use "?" as placeholder, e.g. "IN (?)"
* @return $this
*/
public function filterByMarketplaceType ($data, $operator = '='): static
{
	$this->getClass()->getFieldDefinition("marketplaceType")->addListingFilter($this, $data, $operator);
	return $this;
}

/**
* Filter by ean (Ean)
* @param string|int|float|array|Model\Element\ElementInterface $data  comparison data, can be scalar or array (if operator is e.g. "IN (?)")
* @param string $operator  SQL comparison operator, e.g. =, <, >= etc. You can use "?" as placeholder, e.g. "IN (?)"
* @return $this
*/
public function filterByEan ($data, $operator = '='): static
{
	$this->getClass()->getFieldDefinition("ean")->addListingFilter($this, $data, $operator);
	return $this;
}



}
