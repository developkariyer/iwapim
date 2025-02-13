<?php

namespace Pimcore\Model\DataObject\Brand;

use Pimcore\Model;
use Pimcore\Model\DataObject;

/**
 * @method DataObject\Brand|false current()
 * @method DataObject\Brand[] load()
 * @method DataObject\Brand[] getData()
 * @method DataObject\Brand[] getObjects()
 */

class Listing extends DataObject\Listing\Concrete
{
protected $classId = "brand";
protected $className = "Brand";


/**
* Filter by target (Hedef)
* @param string|int|float|array|Model\Element\ElementInterface $data  comparison data, can be scalar or array (if operator is e.g. "IN (?)")
* @param string $operator  SQL comparison operator, e.g. =, <, >= etc. You can use "?" as placeholder, e.g. "IN (?)"
* @return $this
*/
public function filterByTarget ($data, $operator = '='): static
{
	$this->getClass()->getFieldDefinition("target")->addListingFilter($this, $data, $operator);
	return $this;
}

/**
* Filter by order (Sıra)
* @param string|int|float|array|Model\Element\ElementInterface $data  comparison data, can be scalar or array (if operator is e.g. "IN (?)")
* @param string $operator  SQL comparison operator, e.g. =, <, >= etc. You can use "?" as placeholder, e.g. "IN (?)"
* @return $this
*/
public function filterByOrder ($data, $operator = '='): static
{
	$this->getClass()->getFieldDefinition("order")->addListingFilter($this, $data, $operator);
	return $this;
}

/**
* Filter by productList (Ürünler)
* @param mixed $data
* @param string $operator SQL comparison operator, e.g. =, <, >= etc. You can use "?" as placeholder, e.g. "IN (?)"
* @return $this
*/
public function filterByProductList ($data, $operator = '='): static
{
	$this->getClass()->getFieldDefinition("productList")->addListingFilter($this, $data, $operator);
	return $this;
}



}
