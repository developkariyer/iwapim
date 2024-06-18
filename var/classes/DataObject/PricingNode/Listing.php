<?php

namespace Pimcore\Model\DataObject\PricingNode;

use Pimcore\Model;
use Pimcore\Model\DataObject;

/**
 * @method DataObject\PricingNode|false current()
 * @method DataObject\PricingNode[] load()
 * @method DataObject\PricingNode[] getData()
 * @method DataObject\PricingNode[] getObjects()
 */

class Listing extends DataObject\Listing\Concrete
{
protected $classId = "price";
protected $className = "PricingNode";


/**
* Filter by nodeName (Düğüm İsmi)
* @param string|int|float|array|Model\Element\ElementInterface $data  comparison data, can be scalar or array (if operator is e.g. "IN (?)")
* @param string $operator  SQL comparison operator, e.g. =, <, >= etc. You can use "?" as placeholder, e.g. "IN (?)"
* @return $this
*/
public function filterByNodeName ($data, $operator = '='): static
{
	$this->getClass()->getFieldDefinition("nodeName")->addListingFilter($this, $data, $operator);
	return $this;
}

/**
* Filter by nodeType (Düğüm Tipi)
* @param string|int|float|array|Model\Element\ElementInterface $data  comparison data, can be scalar or array (if operator is e.g. "IN (?)")
* @param string $operator  SQL comparison operator, e.g. =, <, >= etc. You can use "?" as placeholder, e.g. "IN (?)"
* @return $this
*/
public function filterByNodeType ($data, $operator = '='): static
{
	$this->getClass()->getFieldDefinition("nodeType")->addListingFilter($this, $data, $operator);
	return $this;
}

/**
* Filter by nodeDescription (Düğüm Açıklaması (varsa))
* @param string|int|float|array|Model\Element\ElementInterface $data  comparison data, can be scalar or array (if operator is e.g. "IN (?)")
* @param string $operator  SQL comparison operator, e.g. =, <, >= etc. You can use "?" as placeholder, e.g. "IN (?)"
* @return $this
*/
public function filterByNodeDescription ($data, $operator = '='): static
{
	$this->getClass()->getFieldDefinition("nodeDescription")->addListingFilter($this, $data, $operator);
	return $this;
}

/**
* Filter by perLandDeci (Kara)
* @param string|int|float|array|Model\Element\ElementInterface $data  comparison data, can be scalar or array (if operator is e.g. "IN (?)")
* @param string $operator  SQL comparison operator, e.g. =, <, >= etc. You can use "?" as placeholder, e.g. "IN (?)"
* @return $this
*/
public function filterByPerLandDeci ($data, $operator = '='): static
{
	$this->getClass()->getFieldDefinition("perLandDeci")->addListingFilter($this, $data, $operator);
	return $this;
}

/**
* Filter by perAirDeci (Hava/Deniz)
* @param string|int|float|array|Model\Element\ElementInterface $data  comparison data, can be scalar or array (if operator is e.g. "IN (?)")
* @param string $operator  SQL comparison operator, e.g. =, <, >= etc. You can use "?" as placeholder, e.g. "IN (?)"
* @return $this
*/
public function filterByPerAirDeci ($data, $operator = '='): static
{
	$this->getClass()->getFieldDefinition("perAirDeci")->addListingFilter($this, $data, $operator);
	return $this;
}

/**
* Filter by perParcel (Paket)
* @param string|int|float|array|Model\Element\ElementInterface $data  comparison data, can be scalar or array (if operator is e.g. "IN (?)")
* @param string $operator  SQL comparison operator, e.g. =, <, >= etc. You can use "?" as placeholder, e.g. "IN (?)"
* @return $this
*/
public function filterByPerParcel ($data, $operator = '='): static
{
	$this->getClass()->getFieldDefinition("perParcel")->addListingFilter($this, $data, $operator);
	return $this;
}

/**
* Filter by perShipment (Konşimento)
* @param string|int|float|array|Model\Element\ElementInterface $data  comparison data, can be scalar or array (if operator is e.g. "IN (?)")
* @param string $operator  SQL comparison operator, e.g. =, <, >= etc. You can use "?" as placeholder, e.g. "IN (?)"
* @return $this
*/
public function filterByPerShipment ($data, $operator = '='): static
{
	$this->getClass()->getFieldDefinition("perShipment")->addListingFilter($this, $data, $operator);
	return $this;
}

/**
* Filter by perPallet (Palet)
* @param string|int|float|array|Model\Element\ElementInterface $data  comparison data, can be scalar or array (if operator is e.g. "IN (?)")
* @param string $operator  SQL comparison operator, e.g. =, <, >= etc. You can use "?" as placeholder, e.g. "IN (?)"
* @return $this
*/
public function filterByPerPallet ($data, $operator = '='): static
{
	$this->getClass()->getFieldDefinition("perPallet")->addListingFilter($this, $data, $operator);
	return $this;
}

/**
* Filter by perContainer (Konteyner)
* @param string|int|float|array|Model\Element\ElementInterface $data  comparison data, can be scalar or array (if operator is e.g. "IN (?)")
* @param string $operator  SQL comparison operator, e.g. =, <, >= etc. You can use "?" as placeholder, e.g. "IN (?)"
* @return $this
*/
public function filterByPerContainer ($data, $operator = '='): static
{
	$this->getClass()->getFieldDefinition("perContainer")->addListingFilter($this, $data, $operator);
	return $this;
}

/**
* Filter by inPriceTax (Vergi (fiyata dahil))
* @param string|int|float|array|Model\Element\ElementInterface $data  comparison data, can be scalar or array (if operator is e.g. "IN (?)")
* @param string $operator  SQL comparison operator, e.g. =, <, >= etc. You can use "?" as placeholder, e.g. "IN (?)"
* @return $this
*/
public function filterByInPriceTax ($data, $operator = '='): static
{
	$this->getClass()->getFieldDefinition("inPriceTax")->addListingFilter($this, $data, $operator);
	return $this;
}

/**
* Filter by outPriceTax (Vergi (fiyat harici))
* @param string|int|float|array|Model\Element\ElementInterface $data  comparison data, can be scalar or array (if operator is e.g. "IN (?)")
* @param string $operator  SQL comparison operator, e.g. =, <, >= etc. You can use "?" as placeholder, e.g. "IN (?)"
* @return $this
*/
public function filterByOutPriceTax ($data, $operator = '='): static
{
	$this->getClass()->getFieldDefinition("outPriceTax")->addListingFilter($this, $data, $operator);
	return $this;
}

/**
* Filter by feeConstant (Sabit)
* @param string|int|float|array|Model\Element\ElementInterface $data  comparison data, can be scalar or array (if operator is e.g. "IN (?)")
* @param string $operator  SQL comparison operator, e.g. =, <, >= etc. You can use "?" as placeholder, e.g. "IN (?)"
* @return $this
*/
public function filterByFeeConstant ($data, $operator = '='): static
{
	$this->getClass()->getFieldDefinition("feeConstant")->addListingFilter($this, $data, $operator);
	return $this;
}

/**
* Filter by feeVariable (Fiyata Göre)
* @param string|int|float|array|Model\Element\ElementInterface $data  comparison data, can be scalar or array (if operator is e.g. "IN (?)")
* @param string $operator  SQL comparison operator, e.g. =, <, >= etc. You can use "?" as placeholder, e.g. "IN (?)"
* @return $this
*/
public function filterByFeeVariable ($data, $operator = '='): static
{
	$this->getClass()->getFieldDefinition("feeVariable")->addListingFilter($this, $data, $operator);
	return $this;
}



}
