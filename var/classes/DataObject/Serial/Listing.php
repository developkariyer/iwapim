<?php

namespace Pimcore\Model\DataObject\Serial;

use Pimcore\Model;
use Pimcore\Model\DataObject;

/**
 * @method DataObject\Serial|false current()
 * @method DataObject\Serial[] load()
 * @method DataObject\Serial[] getData()
 * @method DataObject\Serial[] getObjects()
 */

class Listing extends DataObject\Listing\Concrete
{
protected $classId = "serial";
protected $className = "Serial";


/**
* Filter by serialNumber (Seri No)
* @param string|int|float|array|Model\Element\ElementInterface $data  comparison data, can be scalar or array (if operator is e.g. "IN (?)")
* @param string $operator  SQL comparison operator, e.g. =, <, >= etc. You can use "?" as placeholder, e.g. "IN (?)"
* @return $this
*/
public function filterBySerialNumber ($data, $operator = '='): static
{
	$this->getClass()->getFieldDefinition("serialNumber")->addListingFilter($this, $data, $operator);
	return $this;
}

/**
* Filter by product (Product)
* @param mixed $data
* @param string $operator SQL comparison operator, e.g. =, <, >= etc. You can use "?" as placeholder, e.g. "IN (?)"
* @return $this
*/
public function filterByProduct ($data, $operator = '='): static
{
	$this->getClass()->getFieldDefinition("product")->addListingFilter($this, $data, $operator);
	return $this;
}

/**
* Filter by label (Label)
* @param mixed $data
* @param string $operator SQL comparison operator, e.g. =, <, >= etc. You can use "?" as placeholder, e.g. "IN (?)"
* @return $this
*/
public function filterByLabel ($data, $operator = '='): static
{
	$this->getClass()->getFieldDefinition("label")->addListingFilter($this, $data, $operator);
	return $this;
}



}
