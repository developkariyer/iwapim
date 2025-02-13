<?php

/**
 * Inheritance: yes
 * Variants: no
 *
 * Fields Summary:
 * - pricingValue [numeric]
 * - currency [select]
 * - pricingType [select]
 * - nodeDescription [textarea]
 */

namespace Pimcore\Model\DataObject;

use Pimcore\Model\DataObject\Exception\InheritanceParentNotFoundException;
use Pimcore\Model\DataObject\PreGetValueHookInterface;

/**
* @method static \Pimcore\Model\DataObject\PricingNode\Listing getList(array $config = [])
* @method static \Pimcore\Model\DataObject\PricingNode\Listing|\Pimcore\Model\DataObject\PricingNode|null getByPricingValue(mixed $value, ?int $limit = null, int $offset = 0, ?array $objectTypes = null)
* @method static \Pimcore\Model\DataObject\PricingNode\Listing|\Pimcore\Model\DataObject\PricingNode|null getByCurrency(mixed $value, ?int $limit = null, int $offset = 0, ?array $objectTypes = null)
* @method static \Pimcore\Model\DataObject\PricingNode\Listing|\Pimcore\Model\DataObject\PricingNode|null getByPricingType(mixed $value, ?int $limit = null, int $offset = 0, ?array $objectTypes = null)
* @method static \Pimcore\Model\DataObject\PricingNode\Listing|\Pimcore\Model\DataObject\PricingNode|null getByNodeDescription(mixed $value, ?int $limit = null, int $offset = 0, ?array $objectTypes = null)
*/

class PricingNode extends Concrete
{
public const FIELD_PRICING_VALUE = 'pricingValue';
public const FIELD_CURRENCY = 'currency';
public const FIELD_PRICING_TYPE = 'pricingType';
public const FIELD_NODE_DESCRIPTION = 'nodeDescription';

protected $classId = "price";
protected $className = "PricingNode";
protected $pricingValue;
protected $currency;
protected $pricingType;
protected $nodeDescription;


/**
* @param array $values
* @return static
*/
public static function create(array $values = []): static
{
	$object = new static();
	$object->setValues($values);
	return $object;
}

/**
* Get pricingValue - Tutar
* @return float|null
*/
public function getPricingValue(): ?float
{
	if ($this instanceof PreGetValueHookInterface && !\Pimcore::inAdmin()) {
		$preValue = $this->preGetValue("pricingValue");
		if ($preValue !== null) {
			return $preValue;
		}
	}

	$data = $this->pricingValue;

	if (\Pimcore\Model\DataObject::doGetInheritedValues() && $this->getClass()->getFieldDefinition("pricingValue")->isEmpty($data)) {
		try {
			return $this->getValueFromParent("pricingValue");
		} catch (InheritanceParentNotFoundException $e) {
			// no data from parent available, continue ...
		}
	}

	if ($data instanceof \Pimcore\Model\DataObject\Data\EncryptedField) {
		return $data->getPlain();
	}

	return $data;
}

/**
* Set pricingValue - Tutar
* @param float|null $pricingValue
* @return $this
*/
public function setPricingValue(?float $pricingValue): static
{
	/** @var \Pimcore\Model\DataObject\ClassDefinition\Data\Numeric $fd */
	$fd = $this->getClass()->getFieldDefinition("pricingValue");
	$this->pricingValue = $fd->preSetData($this, $pricingValue);
	return $this;
}

/**
* Get currency - Para Birimi
* @return string|null
*/
public function getCurrency(): ?string
{
	if ($this instanceof PreGetValueHookInterface && !\Pimcore::inAdmin()) {
		$preValue = $this->preGetValue("currency");
		if ($preValue !== null) {
			return $preValue;
		}
	}

	$data = $this->currency;

	if (\Pimcore\Model\DataObject::doGetInheritedValues() && $this->getClass()->getFieldDefinition("currency")->isEmpty($data)) {
		try {
			return $this->getValueFromParent("currency");
		} catch (InheritanceParentNotFoundException $e) {
			// no data from parent available, continue ...
		}
	}

	if ($data instanceof \Pimcore\Model\DataObject\Data\EncryptedField) {
		return $data->getPlain();
	}

	return $data;
}

/**
* Set currency - Para Birimi
* @param string|null $currency
* @return $this
*/
public function setCurrency(?string $currency): static
{
	$this->markFieldDirty("currency", true);

	$this->currency = $currency;

	return $this;
}

/**
* Get pricingType - Çarpan
* @return string|null
*/
public function getPricingType(): ?string
{
	if ($this instanceof PreGetValueHookInterface && !\Pimcore::inAdmin()) {
		$preValue = $this->preGetValue("pricingType");
		if ($preValue !== null) {
			return $preValue;
		}
	}

	$data = $this->pricingType;

	if (\Pimcore\Model\DataObject::doGetInheritedValues() && $this->getClass()->getFieldDefinition("pricingType")->isEmpty($data)) {
		try {
			return $this->getValueFromParent("pricingType");
		} catch (InheritanceParentNotFoundException $e) {
			// no data from parent available, continue ...
		}
	}

	if ($data instanceof \Pimcore\Model\DataObject\Data\EncryptedField) {
		return $data->getPlain();
	}

	return $data;
}

/**
* Set pricingType - Çarpan
* @param string|null $pricingType
* @return $this
*/
public function setPricingType(?string $pricingType): static
{
	$this->markFieldDirty("pricingType", true);

	$this->pricingType = $pricingType;

	return $this;
}

/**
* Get nodeDescription - Düğüm Açıklaması (varsa)
* @return string|null
*/
public function getNodeDescription(): ?string
{
	if ($this instanceof PreGetValueHookInterface && !\Pimcore::inAdmin()) {
		$preValue = $this->preGetValue("nodeDescription");
		if ($preValue !== null) {
			return $preValue;
		}
	}

	$data = $this->nodeDescription;

	if (\Pimcore\Model\DataObject::doGetInheritedValues() && $this->getClass()->getFieldDefinition("nodeDescription")->isEmpty($data)) {
		try {
			return $this->getValueFromParent("nodeDescription");
		} catch (InheritanceParentNotFoundException $e) {
			// no data from parent available, continue ...
		}
	}

	if ($data instanceof \Pimcore\Model\DataObject\Data\EncryptedField) {
		return $data->getPlain();
	}

	return $data;
}

/**
* Set nodeDescription - Düğüm Açıklaması (varsa)
* @param string|null $nodeDescription
* @return $this
*/
public function setNodeDescription(?string $nodeDescription): static
{
	$this->markFieldDirty("nodeDescription", true);

	$this->nodeDescription = $nodeDescription;

	return $this;
}

}

