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

/**
* Filter by apiKey (Api Key)
* @param string|int|float|array|Model\Element\ElementInterface $data  comparison data, can be scalar or array (if operator is e.g. "IN (?)")
* @param string $operator  SQL comparison operator, e.g. =, <, >= etc. You can use "?" as placeholder, e.g. "IN (?)"
* @return $this
*/
public function filterByApiKey ($data, $operator = '='): static
{
	$this->getClass()->getFieldDefinition("apiKey")->addListingFilter($this, $data, $operator);
	return $this;
}

/**
* Filter by apiSecretKey (API Secret Key)
* @param string|int|float|array|Model\Element\ElementInterface $data  comparison data, can be scalar or array (if operator is e.g. "IN (?)")
* @param string $operator  SQL comparison operator, e.g. =, <, >= etc. You can use "?" as placeholder, e.g. "IN (?)"
* @return $this
*/
public function filterByApiSecretKey ($data, $operator = '='): static
{
	$this->getClass()->getFieldDefinition("apiSecretKey")->addListingFilter($this, $data, $operator);
	return $this;
}

/**
* Filter by accessToken (Access Token)
* @param string|int|float|array|Model\Element\ElementInterface $data  comparison data, can be scalar or array (if operator is e.g. "IN (?)")
* @param string $operator  SQL comparison operator, e.g. =, <, >= etc. You can use "?" as placeholder, e.g. "IN (?)"
* @return $this
*/
public function filterByAccessToken ($data, $operator = '='): static
{
	$this->getClass()->getFieldDefinition("accessToken")->addListingFilter($this, $data, $operator);
	return $this;
}

/**
* Filter by oauthToken (Oauth Token)
* @param string|int|float|array|Model\Element\ElementInterface $data  comparison data, can be scalar or array (if operator is e.g. "IN (?)")
* @param string $operator  SQL comparison operator, e.g. =, <, >= etc. You can use "?" as placeholder, e.g. "IN (?)"
* @return $this
*/
public function filterByOauthToken ($data, $operator = '='): static
{
	$this->getClass()->getFieldDefinition("oauthToken")->addListingFilter($this, $data, $operator);
	return $this;
}

/**
* Filter by apiUrl (API URL)
* @param string|int|float|array|Model\Element\ElementInterface $data  comparison data, can be scalar or array (if operator is e.g. "IN (?)")
* @param string $operator  SQL comparison operator, e.g. =, <, >= etc. You can use "?" as placeholder, e.g. "IN (?)"
* @return $this
*/
public function filterByApiUrl ($data, $operator = '='): static
{
	$this->getClass()->getFieldDefinition("apiUrl")->addListingFilter($this, $data, $operator);
	return $this;
}



}
