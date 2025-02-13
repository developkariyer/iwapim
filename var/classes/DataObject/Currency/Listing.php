<?php

namespace Pimcore\Model\DataObject\Currency;

use Pimcore\Model;
use Pimcore\Model\DataObject;

/**
 * @method DataObject\Currency|false current()
 * @method DataObject\Currency[] load()
 * @method DataObject\Currency[] getData()
 * @method DataObject\Currency[] getObjects()
 */

class Listing extends DataObject\Listing\Concrete
{
protected $classId = "currency";
protected $className = "Currency";


/**
* Filter by rate (TL karşılığı)
* @param string|int|float|array|Model\Element\ElementInterface $data  comparison data, can be scalar or array (if operator is e.g. "IN (?)")
* @param string $operator  SQL comparison operator, e.g. =, <, >= etc. You can use "?" as placeholder, e.g. "IN (?)"
* @return $this
*/
public function filterByRate ($data, $operator = '='): static
{
	$this->getClass()->getFieldDefinition("rate")->addListingFilter($this, $data, $operator);
	return $this;
}

/**
* Filter by currencyCode (Para Birimi)
* @param string|int|float|array|Model\Element\ElementInterface $data  comparison data, can be scalar or array (if operator is e.g. "IN (?)")
* @param string $operator  SQL comparison operator, e.g. =, <, >= etc. You can use "?" as placeholder, e.g. "IN (?)"
* @return $this
*/
public function filterByCurrencyCode ($data, $operator = '='): static
{
	$this->getClass()->getFieldDefinition("currencyCode")->addListingFilter($this, $data, $operator);
	return $this;
}

/**
* Filter by date (Date)
* @param string|int|float|array|Model\Element\ElementInterface $data  comparison data, can be scalar or array (if operator is e.g. "IN (?)")
* @param string $operator  SQL comparison operator, e.g. =, <, >= etc. You can use "?" as placeholder, e.g. "IN (?)"
* @return $this
*/
public function filterByDate ($data, $operator = '='): static
{
	$this->getClass()->getFieldDefinition("date")->addListingFilter($this, $data, $operator);
	return $this;
}



}
