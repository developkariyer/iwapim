<?php

namespace Pimcore\Model\DataObject\GroupProduct;

use Pimcore\Model;
use Pimcore\Model\DataObject;

/**
 * @method DataObject\GroupProduct|false current()
 * @method DataObject\GroupProduct[] load()
 * @method DataObject\GroupProduct[] getData()
 * @method DataObject\GroupProduct[] getObjects()
 */

class Listing extends DataObject\Listing\Concrete
{
protected $classId = "gproduct";
protected $className = "GroupProduct";


/**
* Filter by products (Products)
* @param mixed $data
* @param string $operator SQL comparison operator, e.g. =, <, >= etc. You can use "?" as placeholder, e.g. "IN (?)"
* @return $this
*/
public function filterByProducts ($data, $operator = '='): static
{
	$this->getClass()->getFieldDefinition("products")->addListingFilter($this, $data, $operator);
	return $this;
}

/**
* Filter by pricingModels (Fiyatlama Modelleri)
* @param mixed $data
* @param string $operator SQL comparison operator, e.g. =, <, >= etc. You can use "?" as placeholder, e.g. "IN (?)"
* @return $this
*/
public function filterByPricingModels ($data, $operator = '='): static
{
	$this->getClass()->getFieldDefinition("pricingModels")->addListingFilter($this, $data, $operator);
	return $this;
}

/**
* Filter by targetMarketplace (Hedef Pazar)
* @param mixed $data
* @param string $operator SQL comparison operator, e.g. =, <, >= etc. You can use "?" as placeholder, e.g. "IN (?)"
* @return $this
*/
public function filterByTargetMarketplace ($data, $operator = '='): static
{
	$this->getClass()->getFieldDefinition("targetMarketplace")->addListingFilter($this, $data, $operator);
	return $this;
}



}
