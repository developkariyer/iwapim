<?php

namespace Pimcore\Model\DataObject\CostModel;

use Pimcore\Model;
use Pimcore\Model\DataObject;

/**
 * @method DataObject\CostModel|false current()
 * @method DataObject\CostModel[] load()
 * @method DataObject\CostModel[] getData()
 * @method DataObject\CostModel[] getObjects()
 */

class Listing extends DataObject\Listing\Concrete
{
protected $classId = "modelcost";
protected $className = "CostModel";


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
* Filter by costNodes (Cost Nodes)
* @param mixed $data
* @param string $operator SQL comparison operator, e.g. =, <, >= etc. You can use "?" as placeholder, e.g. "IN (?)"
* @return $this
*/
public function filterByCostNodes ($data, $operator = '='): static
{
	$this->getClass()->getFieldDefinition("costNodes")->addListingFilter($this, $data, $operator);
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



}
