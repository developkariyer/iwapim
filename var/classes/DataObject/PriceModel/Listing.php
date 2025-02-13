<?php

namespace Pimcore\Model\DataObject\PriceModel;

use Pimcore\Model;
use Pimcore\Model\DataObject;

/**
 * @method DataObject\PriceModel|false current()
 * @method DataObject\PriceModel[] load()
 * @method DataObject\PriceModel[] getData()
 * @method DataObject\PriceModel[] getObjects()
 */

class Listing extends DataObject\Listing\Concrete
{
protected $classId = "pricing";
protected $className = "PriceModel";


/**
* Filter by description (Açıklama)
* @param string|int|float|array|Model\Element\ElementInterface $data  comparison data, can be scalar or array (if operator is e.g. "IN (?)")
* @param string $operator  SQL comparison operator, e.g. =, <, >= etc. You can use "?" as placeholder, e.g. "IN (?)"
* @return $this
*/
public function filterByDescription ($data, $operator = '='): static
{
	$this->getClass()->getFieldDefinition("description")->addListingFilter($this, $data, $operator);
	return $this;
}

/**
* Filter by pricingNodes (Dağıtım Maliyetleri)
* @param mixed $data
* @param string $operator SQL comparison operator, e.g. =, <, >= etc. You can use "?" as placeholder, e.g. "IN (?)"
* @return $this
*/
public function filterByPricingNodes ($data, $operator = '='): static
{
	$this->getClass()->getFieldDefinition("pricingNodes")->addListingFilter($this, $data, $operator);
	return $this;
}

/**
* Filter by products (Ürünler)
* @param mixed $data
* @param string $operator SQL comparison operator, e.g. =, <, >= etc. You can use "?" as placeholder, e.g. "IN (?)"
* @return $this
*/
public function filterByProducts ($data, $operator = '='): static
{
	$this->getClass()->getFieldDefinition("products")->addListingFilter($this, $data, $operator);
	return $this;
}



}
