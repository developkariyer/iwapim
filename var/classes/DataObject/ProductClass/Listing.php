<?php

namespace Pimcore\Model\DataObject\ProductClass;

use Pimcore\Model;
use Pimcore\Model\DataObject;

/**
 * @method DataObject\ProductClass|false current()
 * @method DataObject\ProductClass[] load()
 * @method DataObject\ProductClass[] getData()
 * @method DataObject\ProductClass[] getObjects()
 */

class Listing extends DataObject\Listing\Concrete
{
protected $classId = "pclass";
protected $className = "ProductClass";


/**
* Filter by productClassName (Ürün Sınıfı)
* @param string|int|float|array|Model\Element\ElementInterface $data  comparison data, can be scalar or array (if operator is e.g. "IN (?)")
* @param string $operator  SQL comparison operator, e.g. =, <, >= etc. You can use "?" as placeholder, e.g. "IN (?)"
* @return $this
*/
public function filterByProductClassName ($data, $operator = '='): static
{
	$this->getClass()->getFieldDefinition("productClassName")->addListingFilter($this, $data, $operator);
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



}
