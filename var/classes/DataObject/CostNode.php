<?php

/**
 * Inheritance: no
 * Variants: no
 *
 * Fields Summary:
 * - amount [numeric]
 * - unit [select]
 * - cost [numeric]
 * - currency [select]
 * - description [textarea]
 * - unitCost [calculatedValue]
 */

namespace Pimcore\Model\DataObject;

use Pimcore\Model\DataObject\Exception\InheritanceParentNotFoundException;
use Pimcore\Model\DataObject\PreGetValueHookInterface;

/**
* @method static \Pimcore\Model\DataObject\CostNode\Listing getList(array $config = [])
* @method static \Pimcore\Model\DataObject\CostNode\Listing|\Pimcore\Model\DataObject\CostNode|null getByAmount(mixed $value, ?int $limit = null, int $offset = 0, ?array $objectTypes = null)
* @method static \Pimcore\Model\DataObject\CostNode\Listing|\Pimcore\Model\DataObject\CostNode|null getByUnit(mixed $value, ?int $limit = null, int $offset = 0, ?array $objectTypes = null)
* @method static \Pimcore\Model\DataObject\CostNode\Listing|\Pimcore\Model\DataObject\CostNode|null getByCost(mixed $value, ?int $limit = null, int $offset = 0, ?array $objectTypes = null)
* @method static \Pimcore\Model\DataObject\CostNode\Listing|\Pimcore\Model\DataObject\CostNode|null getByCurrency(mixed $value, ?int $limit = null, int $offset = 0, ?array $objectTypes = null)
* @method static \Pimcore\Model\DataObject\CostNode\Listing|\Pimcore\Model\DataObject\CostNode|null getByDescription(mixed $value, ?int $limit = null, int $offset = 0, ?array $objectTypes = null)
*/

class CostNode extends Concrete
{
public const FIELD_AMOUNT = 'amount';
public const FIELD_UNIT = 'unit';
public const FIELD_COST = 'cost';
public const FIELD_CURRENCY = 'currency';
public const FIELD_DESCRIPTION = 'description';
public const FIELD_UNIT_COST = 'unitCost';

protected $classId = "cost";
protected $className = "CostNode";
protected $amount;
protected $unit;
protected $cost;
protected $currency;
protected $description;


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
* Get amount - Miktar
* @return string|null
*/
public function getAmount(): ?string
{
	if ($this instanceof PreGetValueHookInterface && !\Pimcore::inAdmin()) {
		$preValue = $this->preGetValue("amount");
		if ($preValue !== null) {
			return $preValue;
		}
	}

	$data = $this->amount;

	if ($data instanceof \Pimcore\Model\DataObject\Data\EncryptedField) {
		return $data->getPlain();
	}

	return $data;
}

/**
* Set amount - Miktar
* @param string|null $amount
* @return $this
*/
public function setAmount(?string $amount): static
{
	/** @var \Pimcore\Model\DataObject\ClassDefinition\Data\Numeric $fd */
	$fd = $this->getClass()->getFieldDefinition("amount");
	$this->amount = $fd->preSetData($this, $amount);
	return $this;
}

/**
* Get unit - 
* @return string|null
*/
public function getUnit(): ?string
{
	if ($this instanceof PreGetValueHookInterface && !\Pimcore::inAdmin()) {
		$preValue = $this->preGetValue("unit");
		if ($preValue !== null) {
			return $preValue;
		}
	}

	$data = $this->unit;

	if ($data instanceof \Pimcore\Model\DataObject\Data\EncryptedField) {
		return $data->getPlain();
	}

	return $data;
}

/**
* Set unit - 
* @param string|null $unit
* @return $this
*/
public function setUnit(?string $unit): static
{
	$this->markFieldDirty("unit", true);

	$this->unit = $unit;

	return $this;
}

/**
* Get cost - Tutar
* @return string|null
*/
public function getCost(): ?string
{
	if ($this instanceof PreGetValueHookInterface && !\Pimcore::inAdmin()) {
		$preValue = $this->preGetValue("cost");
		if ($preValue !== null) {
			return $preValue;
		}
	}

	$data = $this->cost;

	if ($data instanceof \Pimcore\Model\DataObject\Data\EncryptedField) {
		return $data->getPlain();
	}

	return $data;
}

/**
* Set cost - Tutar
* @param string|null $cost
* @return $this
*/
public function setCost(?string $cost): static
{
	/** @var \Pimcore\Model\DataObject\ClassDefinition\Data\Numeric $fd */
	$fd = $this->getClass()->getFieldDefinition("cost");
	$this->cost = $fd->preSetData($this, $cost);
	return $this;
}

/**
* Get currency - 
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

	if ($data instanceof \Pimcore\Model\DataObject\Data\EncryptedField) {
		return $data->getPlain();
	}

	return $data;
}

/**
* Set currency - 
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
* Get description - Açıklama
* @return string|null
*/
public function getDescription(): ?string
{
	if ($this instanceof PreGetValueHookInterface && !\Pimcore::inAdmin()) {
		$preValue = $this->preGetValue("description");
		if ($preValue !== null) {
			return $preValue;
		}
	}

	$data = $this->description;

	if ($data instanceof \Pimcore\Model\DataObject\Data\EncryptedField) {
		return $data->getPlain();
	}

	return $data;
}

/**
* Set description - Açıklama
* @param string|null $description
* @return $this
*/
public function setDescription(?string $description): static
{
	$this->markFieldDirty("description", true);

	$this->description = $description;

	return $this;
}

/**
* Get unitCost - Birim Maliyet
* @return mixed
*/
public function getUnitCost(): mixed
{
	$data = new \Pimcore\Model\DataObject\Data\CalculatedValue('unitCost');
	$data->setContextualData("object", null, null, null);
	$object = $this;
	$data = \Pimcore\Model\DataObject\Service::getCalculatedFieldValue($object, $data);

	return $data;
}

}

