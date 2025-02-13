<?php

namespace Pimcore\Model\DataObject\CostNode;

use Pimcore\Model;
use Pimcore\Model\DataObject;

/**
 * @method DataObject\CostNode|false current()
 * @method DataObject\CostNode[] load()
 * @method DataObject\CostNode[] getData()
 * @method DataObject\CostNode[] getObjects()
 */

class Listing extends DataObject\Listing\Concrete
{
protected $classId = "cost";
protected $className = "CostNode";


/**
* Filter by amount (Miktar)
* @param string|int|float|array|Model\Element\ElementInterface $data  comparison data, can be scalar or array (if operator is e.g. "IN (?)")
* @param string $operator  SQL comparison operator, e.g. =, <, >= etc. You can use "?" as placeholder, e.g. "IN (?)"
* @return $this
*/
public function filterByAmount ($data, $operator = '='): static
{
	$this->getClass()->getFieldDefinition("amount")->addListingFilter($this, $data, $operator);
	return $this;
}

/**
* Filter by unit (Birim)
* @param string|int|float|array|Model\Element\ElementInterface $data  comparison data, can be scalar or array (if operator is e.g. "IN (?)")
* @param string $operator  SQL comparison operator, e.g. =, <, >= etc. You can use "?" as placeholder, e.g. "IN (?)"
* @return $this
*/
public function filterByUnit ($data, $operator = '='): static
{
	$this->getClass()->getFieldDefinition("unit")->addListingFilter($this, $data, $operator);
	return $this;
}

/**
* Filter by cost (Tutar)
* @param string|int|float|array|Model\Element\ElementInterface $data  comparison data, can be scalar or array (if operator is e.g. "IN (?)")
* @param string $operator  SQL comparison operator, e.g. =, <, >= etc. You can use "?" as placeholder, e.g. "IN (?)"
* @return $this
*/
public function filterByCost ($data, $operator = '='): static
{
	$this->getClass()->getFieldDefinition("cost")->addListingFilter($this, $data, $operator);
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
* Filter by combinedCost (Birleşik Hammaddeler)
* @param mixed $data
* @param string $operator SQL comparison operator, e.g. =, <, >= etc. You can use "?" as placeholder, e.g. "IN (?)"
* @return $this
*/
public function filterByCombinedCost ($data, $operator = '='): static
{
	$this->getClass()->getFieldDefinition("combinedCost")->addListingFilter($this, $data, $operator);
	return $this;
}



}
