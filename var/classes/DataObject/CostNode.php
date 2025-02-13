<?php

/**
 * Inheritance: yes
 * Variants: no
 *
 * Fields Summary:
 * - amount [numeric]
 * - unit [select]
 * - cost [numeric]
 * - currency [select]
 * - description [textarea]
 * - combinedCost [advancedManyToManyObjectRelation]
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
* @method static \Pimcore\Model\DataObject\CostNode\Listing|\Pimcore\Model\DataObject\CostNode|null getByCombinedCost(mixed $value, ?int $limit = null, int $offset = 0, ?array $objectTypes = null)
*/

class CostNode extends Concrete
{
public const FIELD_AMOUNT = 'amount';
public const FIELD_UNIT = 'unit';
public const FIELD_COST = 'cost';
public const FIELD_CURRENCY = 'currency';
public const FIELD_DESCRIPTION = 'description';
public const FIELD_COMBINED_COST = 'combinedCost';
public const FIELD_UNIT_COST = 'unitCost';

protected $classId = "cost";
protected $className = "CostNode";
protected $amount;
protected $unit;
protected $cost;
protected $currency;
protected $description;
protected $combinedCost;


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

	if (\Pimcore\Model\DataObject::doGetInheritedValues() && $this->getClass()->getFieldDefinition("amount")->isEmpty($data)) {
		try {
			return $this->getValueFromParent("amount");
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
* Get unit - Birim
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

	if (\Pimcore\Model\DataObject::doGetInheritedValues() && $this->getClass()->getFieldDefinition("unit")->isEmpty($data)) {
		try {
			return $this->getValueFromParent("unit");
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
* Set unit - Birim
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

	if (\Pimcore\Model\DataObject::doGetInheritedValues() && $this->getClass()->getFieldDefinition("cost")->isEmpty($data)) {
		try {
			return $this->getValueFromParent("cost");
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

	if (\Pimcore\Model\DataObject::doGetInheritedValues() && $this->getClass()->getFieldDefinition("description")->isEmpty($data)) {
		try {
			return $this->getValueFromParent("description");
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
* Get combinedCost - Birleşik Hammaddeler
* @return \Pimcore\Model\DataObject\Data\ObjectMetadata[]
*/
public function getCombinedCost(): array
{
	if ($this instanceof PreGetValueHookInterface && !\Pimcore::inAdmin()) {
		$preValue = $this->preGetValue("combinedCost");
		if ($preValue !== null) {
			return $preValue;
		}
	}

	$data = $this->getClass()->getFieldDefinition("combinedCost")->preGetData($this);

	if (\Pimcore\Model\DataObject::doGetInheritedValues() && $this->getClass()->getFieldDefinition("combinedCost")->isEmpty($data)) {
		try {
			return $this->getValueFromParent("combinedCost");
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
* Set combinedCost - Birleşik Hammaddeler
* @param \Pimcore\Model\DataObject\Data\ObjectMetadata[] $combinedCost
* @return $this
*/
public function setCombinedCost(?array $combinedCost): static
{
	/** @var \Pimcore\Model\DataObject\ClassDefinition\Data\AdvancedManyToManyObjectRelation $fd */
	$fd = $this->getClass()->getFieldDefinition("combinedCost");
	$hideUnpublished = \Pimcore\Model\DataObject\Concrete::getHideUnpublished();
	\Pimcore\Model\DataObject\Concrete::setHideUnpublished(false);
	$currentData = \Pimcore\Model\DataObject\Service::useInheritedValues(false, function() {
		return $this->getCombinedCost();
	});
	\Pimcore\Model\DataObject\Concrete::setHideUnpublished($hideUnpublished);
	$isEqual = $fd->isEqual($currentData, $combinedCost);
	if (!$isEqual) {
		$this->markFieldDirty("combinedCost", true);
	}
	$this->combinedCost = $fd->preSetData($this, $combinedCost);
	return $this;
}

/**
* Get unitCost - Birim Maliyet (TL)
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

