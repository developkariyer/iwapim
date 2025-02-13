<?php

namespace Pimcore\Model\DataObject\Category;

use Pimcore\Model;
use Pimcore\Model\DataObject;

/**
 * @method DataObject\Category|false current()
 * @method DataObject\Category[] load()
 * @method DataObject\Category[] getData()
 * @method DataObject\Category[] getObjects()
 */

class Listing extends DataObject\Listing\Concrete
{
protected $classId = "category";
protected $className = "Category";


/**
* Filter by category (Category)
* @param string|int|float|array|Model\Element\ElementInterface $data  comparison data, can be scalar or array (if operator is e.g. "IN (?)")
* @param string $operator  SQL comparison operator, e.g. =, <, >= etc. You can use "?" as placeholder, e.g. "IN (?)"
* @return $this
*/
public function filterByCategory ($data, $operator = '='): static
{
	$this->getClass()->getFieldDefinition("category")->addListingFilter($this, $data, $operator);
	return $this;
}

/**
* Filter by description (Description)
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
* Filter by wisersellCategoryId (Wisersell Category Id)
* @param string|int|float|array|Model\Element\ElementInterface $data  comparison data, can be scalar or array (if operator is e.g. "IN (?)")
* @param string $operator  SQL comparison operator, e.g. =, <, >= etc. You can use "?" as placeholder, e.g. "IN (?)"
* @return $this
*/
public function filterByWisersellCategoryId ($data, $operator = '='): static
{
	$this->getClass()->getFieldDefinition("wisersellCategoryId")->addListingFilter($this, $data, $operator);
	return $this;
}

/**
* Filter by technicals (Teknik Doküman ve Kılavuzlar)
* @param mixed $data
* @param string $operator SQL comparison operator, e.g. =, <, >= etc. You can use "?" as placeholder, e.g. "IN (?)"
* @return $this
*/
public function filterByTechnicals ($data, $operator = '='): static
{
	$this->getClass()->getFieldDefinition("technicals")->addListingFilter($this, $data, $operator);
	return $this;
}



}
