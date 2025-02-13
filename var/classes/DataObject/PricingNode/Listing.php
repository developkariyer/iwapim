<?php

namespace Pimcore\Model\DataObject\PricingNode;

use Pimcore\Model;
use Pimcore\Model\DataObject;

/**
 * @method DataObject\PricingNode|false current()
 * @method DataObject\PricingNode[] load()
 * @method DataObject\PricingNode[] getData()
 * @method DataObject\PricingNode[] getObjects()
 */

class Listing extends DataObject\Listing\Concrete
{
protected $classId = "price";
protected $className = "PricingNode";


/**
* Filter by pricingValue (Tutar)
* @param string|int|float|array|Model\Element\ElementInterface $data  comparison data, can be scalar or array (if operator is e.g. "IN (?)")
* @param string $operator  SQL comparison operator, e.g. =, <, >= etc. You can use "?" as placeholder, e.g. "IN (?)"
* @return $this
*/
public function filterByPricingValue ($data, $operator = '='): static
{
	$this->getClass()->getFieldDefinition("pricingValue")->addListingFilter($this, $data, $operator);
	return $this;
}

/**
* Filter by currency (Para Birimi)
* @param string|int|float|array|Model\Element\ElementInterface $data  comparison data, can be scalar or array (if operator is e.g. "IN (?)")
* @param string $operator  SQL comparison operator, e.g. =, <, >= etc. You can use "?" as placeholder, e.g. "IN (?)"
* @return $this
*/
public function filterByCurrency ($data, $operator = '='): static
{
	$this->getClass()->getFieldDefinition("currency")->addListingFilter($this, $data, $operator);
	return $this;
}

/**
* Filter by pricingType (Çarpan)
* @param string|int|float|array|Model\Element\ElementInterface $data  comparison data, can be scalar or array (if operator is e.g. "IN (?)")
* @param string $operator  SQL comparison operator, e.g. =, <, >= etc. You can use "?" as placeholder, e.g. "IN (?)"
* @return $this
*/
public function filterByPricingType ($data, $operator = '='): static
{
	$this->getClass()->getFieldDefinition("pricingType")->addListingFilter($this, $data, $operator);
	return $this;
}

/**
* Filter by nodeDescription (Düğüm Açıklaması (varsa))
* @param string|int|float|array|Model\Element\ElementInterface $data  comparison data, can be scalar or array (if operator is e.g. "IN (?)")
* @param string $operator  SQL comparison operator, e.g. =, <, >= etc. You can use "?" as placeholder, e.g. "IN (?)"
* @return $this
*/
public function filterByNodeDescription ($data, $operator = '='): static
{
	$this->getClass()->getFieldDefinition("nodeDescription")->addListingFilter($this, $data, $operator);
	return $this;
}



}
