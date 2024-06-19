<?php

namespace Pimcore\Model\DataObject\MarketingMaterial;

use Pimcore\Model;
use Pimcore\Model\DataObject;

/**
 * @method DataObject\MarketingMaterial|false current()
 * @method DataObject\MarketingMaterial[] load()
 * @method DataObject\MarketingMaterial[] getData()
 * @method DataObject\MarketingMaterial[] getObjects()
 */

class Listing extends DataObject\Listing\Concrete
{
protected $classId = "MarketingMaterial";
protected $className = "MarketingMaterial";


/**
* Filter by title (Title)
* @param string|int|float|array|Model\Element\ElementInterface $data  comparison data, can be scalar or array (if operator is e.g. "IN (?)")
* @param string $operator  SQL comparison operator, e.g. =, <, >= etc. You can use "?" as placeholder, e.g. "IN (?)"
* @return $this
*/
public function filterByTitle ($data, $operator = '='): static
{
	$this->getClass()->getFieldDefinition("title")->addListingFilter($this, $data, $operator);
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
* Filter by campaignName (Campaign Name)
* @param string|int|float|array|Model\Element\ElementInterface $data  comparison data, can be scalar or array (if operator is e.g. "IN (?)")
* @param string $operator  SQL comparison operator, e.g. =, <, >= etc. You can use "?" as placeholder, e.g. "IN (?)"
* @return $this
*/
public function filterByCampaignName ($data, $operator = '='): static
{
	$this->getClass()->getFieldDefinition("campaignName")->addListingFilter($this, $data, $operator);
	return $this;
}

/**
* Filter by status (Status)
* @param string|int|float|array|Model\Element\ElementInterface $data  comparison data, can be scalar or array (if operator is e.g. "IN (?)")
* @param string $operator  SQL comparison operator, e.g. =, <, >= etc. You can use "?" as placeholder, e.g. "IN (?)"
* @return $this
*/
public function filterByStatus ($data, $operator = '='): static
{
	$this->getClass()->getFieldDefinition("status")->addListingFilter($this, $data, $operator);
	return $this;
}

/**
* Filter by asset (Asset)
* @param mixed $data
* @param string $operator SQL comparison operator, e.g. =, <, >= etc. You can use "?" as placeholder, e.g. "IN (?)"
* @return $this
*/
public function filterByAsset ($data, $operator = '='): static
{
	$this->getClass()->getFieldDefinition("asset")->addListingFilter($this, $data, $operator);
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
