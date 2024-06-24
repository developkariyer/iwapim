<?php

namespace Pimcore\Model\DataObject\Marketplace;

use Pimcore\Model;
use Pimcore\Model\DataObject;

/**
 * @method DataObject\Marketplace|false current()
 * @method DataObject\Marketplace[] load()
 * @method DataObject\Marketplace[] getData()
 * @method DataObject\Marketplace[] getObjects()
 */

class Listing extends DataObject\Listing\Concrete
{
protected $classId = "marketplace";
protected $className = "Marketplace";


/**
* Filter by pricingCosts (Pricing Costs)
* @param mixed $data
* @param string $operator SQL comparison operator, e.g. =, <, >= etc. You can use "?" as placeholder, e.g. "IN (?)"
* @return $this
*/
public function filterByPricingCosts ($data, $operator = '='): static
{
	$this->getClass()->getFieldDefinition("pricingCosts")->addListingFilter($this, $data, $operator);
	return $this;
}

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



}
