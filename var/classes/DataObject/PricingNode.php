<?php

/**
 * Inheritance: yes
 * Variants: no
 *
 * Fields Summary:
 * - nodeName [input]
 * - nodeType [select]
 * - nodeDescription [textarea]
 * - perLandDeci [numeric]
 * - perAirDeci [numeric]
 * - perParcel [numeric]
 * - perShipment [numeric]
 * - perPallet [numeric]
 * - perContainer [numeric]
 * - inPriceTax [slider]
 * - outPriceTax [slider]
 * - feeConstant [numeric]
 * - feeVariable [slider]
 * - varAirDeci [fieldcollections]
 */

namespace Pimcore\Model\DataObject;

use Pimcore\Model\DataObject\Exception\InheritanceParentNotFoundException;
use Pimcore\Model\DataObject\PreGetValueHookInterface;

/**
* @method static \Pimcore\Model\DataObject\PricingNode\Listing getList(array $config = [])
* @method static \Pimcore\Model\DataObject\PricingNode\Listing|\Pimcore\Model\DataObject\PricingNode|null getByNodeName(mixed $value, ?int $limit = null, int $offset = 0, ?array $objectTypes = null)
* @method static \Pimcore\Model\DataObject\PricingNode\Listing|\Pimcore\Model\DataObject\PricingNode|null getByNodeType(mixed $value, ?int $limit = null, int $offset = 0, ?array $objectTypes = null)
* @method static \Pimcore\Model\DataObject\PricingNode\Listing|\Pimcore\Model\DataObject\PricingNode|null getByNodeDescription(mixed $value, ?int $limit = null, int $offset = 0, ?array $objectTypes = null)
* @method static \Pimcore\Model\DataObject\PricingNode\Listing|\Pimcore\Model\DataObject\PricingNode|null getByPerLandDeci(mixed $value, ?int $limit = null, int $offset = 0, ?array $objectTypes = null)
* @method static \Pimcore\Model\DataObject\PricingNode\Listing|\Pimcore\Model\DataObject\PricingNode|null getByPerAirDeci(mixed $value, ?int $limit = null, int $offset = 0, ?array $objectTypes = null)
* @method static \Pimcore\Model\DataObject\PricingNode\Listing|\Pimcore\Model\DataObject\PricingNode|null getByPerParcel(mixed $value, ?int $limit = null, int $offset = 0, ?array $objectTypes = null)
* @method static \Pimcore\Model\DataObject\PricingNode\Listing|\Pimcore\Model\DataObject\PricingNode|null getByPerShipment(mixed $value, ?int $limit = null, int $offset = 0, ?array $objectTypes = null)
* @method static \Pimcore\Model\DataObject\PricingNode\Listing|\Pimcore\Model\DataObject\PricingNode|null getByPerPallet(mixed $value, ?int $limit = null, int $offset = 0, ?array $objectTypes = null)
* @method static \Pimcore\Model\DataObject\PricingNode\Listing|\Pimcore\Model\DataObject\PricingNode|null getByPerContainer(mixed $value, ?int $limit = null, int $offset = 0, ?array $objectTypes = null)
* @method static \Pimcore\Model\DataObject\PricingNode\Listing|\Pimcore\Model\DataObject\PricingNode|null getByInPriceTax(mixed $value, ?int $limit = null, int $offset = 0, ?array $objectTypes = null)
* @method static \Pimcore\Model\DataObject\PricingNode\Listing|\Pimcore\Model\DataObject\PricingNode|null getByOutPriceTax(mixed $value, ?int $limit = null, int $offset = 0, ?array $objectTypes = null)
* @method static \Pimcore\Model\DataObject\PricingNode\Listing|\Pimcore\Model\DataObject\PricingNode|null getByFeeConstant(mixed $value, ?int $limit = null, int $offset = 0, ?array $objectTypes = null)
* @method static \Pimcore\Model\DataObject\PricingNode\Listing|\Pimcore\Model\DataObject\PricingNode|null getByFeeVariable(mixed $value, ?int $limit = null, int $offset = 0, ?array $objectTypes = null)
*/

class PricingNode extends Concrete
{
public const FIELD_NODE_NAME = 'nodeName';
public const FIELD_NODE_TYPE = 'nodeType';
public const FIELD_NODE_DESCRIPTION = 'nodeDescription';
public const FIELD_PER_LAND_DECI = 'perLandDeci';
public const FIELD_PER_AIR_DECI = 'perAirDeci';
public const FIELD_PER_PARCEL = 'perParcel';
public const FIELD_PER_SHIPMENT = 'perShipment';
public const FIELD_PER_PALLET = 'perPallet';
public const FIELD_PER_CONTAINER = 'perContainer';
public const FIELD_IN_PRICE_TAX = 'inPriceTax';
public const FIELD_OUT_PRICE_TAX = 'outPriceTax';
public const FIELD_FEE_CONSTANT = 'feeConstant';
public const FIELD_FEE_VARIABLE = 'feeVariable';
public const FIELD_VAR_AIR_DECI = 'varAirDeci';

protected $classId = "price";
protected $className = "PricingNode";
protected $nodeName;
protected $nodeType;
protected $nodeDescription;
protected $perLandDeci;
protected $perAirDeci;
protected $perParcel;
protected $perShipment;
protected $perPallet;
protected $perContainer;
protected $inPriceTax;
protected $outPriceTax;
protected $feeConstant;
protected $feeVariable;
protected $varAirDeci;


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
* Get nodeName - Düğüm İsmi
* @return string|null
*/
public function getNodeName(): ?string
{
	if ($this instanceof PreGetValueHookInterface && !\Pimcore::inAdmin()) {
		$preValue = $this->preGetValue("nodeName");
		if ($preValue !== null) {
			return $preValue;
		}
	}

	$data = $this->nodeName;

	if (\Pimcore\Model\DataObject::doGetInheritedValues() && $this->getClass()->getFieldDefinition("nodeName")->isEmpty($data)) {
		try {
			return $this->getValueFromParent("nodeName");
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
* Set nodeName - Düğüm İsmi
* @param string|null $nodeName
* @return $this
*/
public function setNodeName(?string $nodeName): static
{
	$this->markFieldDirty("nodeName", true);

	$this->nodeName = $nodeName;

	return $this;
}

/**
* Get nodeType - Düğüm Tipi
* @return string|null
*/
public function getNodeType(): ?string
{
	if ($this instanceof PreGetValueHookInterface && !\Pimcore::inAdmin()) {
		$preValue = $this->preGetValue("nodeType");
		if ($preValue !== null) {
			return $preValue;
		}
	}

	$data = $this->nodeType;

	if (\Pimcore\Model\DataObject::doGetInheritedValues() && $this->getClass()->getFieldDefinition("nodeType")->isEmpty($data)) {
		try {
			return $this->getValueFromParent("nodeType");
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
* Set nodeType - Düğüm Tipi
* @param string|null $nodeType
* @return $this
*/
public function setNodeType(?string $nodeType): static
{
	$this->markFieldDirty("nodeType", true);

	$this->nodeType = $nodeType;

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

/**
* Get perLandDeci - Kara
* @return float|null
*/
public function getPerLandDeci(): ?float
{
	if ($this instanceof PreGetValueHookInterface && !\Pimcore::inAdmin()) {
		$preValue = $this->preGetValue("perLandDeci");
		if ($preValue !== null) {
			return $preValue;
		}
	}

	$data = $this->perLandDeci;

	if (\Pimcore\Model\DataObject::doGetInheritedValues() && $this->getClass()->getFieldDefinition("perLandDeci")->isEmpty($data)) {
		try {
			return $this->getValueFromParent("perLandDeci");
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
* Set perLandDeci - Kara
* @param float|null $perLandDeci
* @return $this
*/
public function setPerLandDeci(?float $perLandDeci): static
{
	/** @var \Pimcore\Model\DataObject\ClassDefinition\Data\Numeric $fd */
	$fd = $this->getClass()->getFieldDefinition("perLandDeci");
	$this->perLandDeci = $fd->preSetData($this, $perLandDeci);
	return $this;
}

/**
* Get perAirDeci - Hava/Deniz
* @return float|null
*/
public function getPerAirDeci(): ?float
{
	if ($this instanceof PreGetValueHookInterface && !\Pimcore::inAdmin()) {
		$preValue = $this->preGetValue("perAirDeci");
		if ($preValue !== null) {
			return $preValue;
		}
	}

	$data = $this->perAirDeci;

	if (\Pimcore\Model\DataObject::doGetInheritedValues() && $this->getClass()->getFieldDefinition("perAirDeci")->isEmpty($data)) {
		try {
			return $this->getValueFromParent("perAirDeci");
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
* Set perAirDeci - Hava/Deniz
* @param float|null $perAirDeci
* @return $this
*/
public function setPerAirDeci(?float $perAirDeci): static
{
	/** @var \Pimcore\Model\DataObject\ClassDefinition\Data\Numeric $fd */
	$fd = $this->getClass()->getFieldDefinition("perAirDeci");
	$this->perAirDeci = $fd->preSetData($this, $perAirDeci);
	return $this;
}

/**
* Get perParcel - Paket
* @return float|null
*/
public function getPerParcel(): ?float
{
	if ($this instanceof PreGetValueHookInterface && !\Pimcore::inAdmin()) {
		$preValue = $this->preGetValue("perParcel");
		if ($preValue !== null) {
			return $preValue;
		}
	}

	$data = $this->perParcel;

	if (\Pimcore\Model\DataObject::doGetInheritedValues() && $this->getClass()->getFieldDefinition("perParcel")->isEmpty($data)) {
		try {
			return $this->getValueFromParent("perParcel");
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
* Set perParcel - Paket
* @param float|null $perParcel
* @return $this
*/
public function setPerParcel(?float $perParcel): static
{
	/** @var \Pimcore\Model\DataObject\ClassDefinition\Data\Numeric $fd */
	$fd = $this->getClass()->getFieldDefinition("perParcel");
	$this->perParcel = $fd->preSetData($this, $perParcel);
	return $this;
}

/**
* Get perShipment - Konşimento
* @return float|null
*/
public function getPerShipment(): ?float
{
	if ($this instanceof PreGetValueHookInterface && !\Pimcore::inAdmin()) {
		$preValue = $this->preGetValue("perShipment");
		if ($preValue !== null) {
			return $preValue;
		}
	}

	$data = $this->perShipment;

	if (\Pimcore\Model\DataObject::doGetInheritedValues() && $this->getClass()->getFieldDefinition("perShipment")->isEmpty($data)) {
		try {
			return $this->getValueFromParent("perShipment");
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
* Set perShipment - Konşimento
* @param float|null $perShipment
* @return $this
*/
public function setPerShipment(?float $perShipment): static
{
	/** @var \Pimcore\Model\DataObject\ClassDefinition\Data\Numeric $fd */
	$fd = $this->getClass()->getFieldDefinition("perShipment");
	$this->perShipment = $fd->preSetData($this, $perShipment);
	return $this;
}

/**
* Get perPallet - Palet
* @return float|null
*/
public function getPerPallet(): ?float
{
	if ($this instanceof PreGetValueHookInterface && !\Pimcore::inAdmin()) {
		$preValue = $this->preGetValue("perPallet");
		if ($preValue !== null) {
			return $preValue;
		}
	}

	$data = $this->perPallet;

	if (\Pimcore\Model\DataObject::doGetInheritedValues() && $this->getClass()->getFieldDefinition("perPallet")->isEmpty($data)) {
		try {
			return $this->getValueFromParent("perPallet");
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
* Set perPallet - Palet
* @param float|null $perPallet
* @return $this
*/
public function setPerPallet(?float $perPallet): static
{
	/** @var \Pimcore\Model\DataObject\ClassDefinition\Data\Numeric $fd */
	$fd = $this->getClass()->getFieldDefinition("perPallet");
	$this->perPallet = $fd->preSetData($this, $perPallet);
	return $this;
}

/**
* Get perContainer - Konteyner
* @return float|null
*/
public function getPerContainer(): ?float
{
	if ($this instanceof PreGetValueHookInterface && !\Pimcore::inAdmin()) {
		$preValue = $this->preGetValue("perContainer");
		if ($preValue !== null) {
			return $preValue;
		}
	}

	$data = $this->perContainer;

	if (\Pimcore\Model\DataObject::doGetInheritedValues() && $this->getClass()->getFieldDefinition("perContainer")->isEmpty($data)) {
		try {
			return $this->getValueFromParent("perContainer");
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
* Set perContainer - Konteyner
* @param float|null $perContainer
* @return $this
*/
public function setPerContainer(?float $perContainer): static
{
	/** @var \Pimcore\Model\DataObject\ClassDefinition\Data\Numeric $fd */
	$fd = $this->getClass()->getFieldDefinition("perContainer");
	$this->perContainer = $fd->preSetData($this, $perContainer);
	return $this;
}

/**
* Get inPriceTax - Vergi (fiyata dahil)
* @return float|null
*/
public function getInPriceTax(): ?float
{
	if ($this instanceof PreGetValueHookInterface && !\Pimcore::inAdmin()) {
		$preValue = $this->preGetValue("inPriceTax");
		if ($preValue !== null) {
			return $preValue;
		}
	}

	$data = $this->inPriceTax;

	if (\Pimcore\Model\DataObject::doGetInheritedValues() && $this->getClass()->getFieldDefinition("inPriceTax")->isEmpty($data)) {
		try {
			return $this->getValueFromParent("inPriceTax");
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
* Set inPriceTax - Vergi (fiyata dahil)
* @param float|null $inPriceTax
* @return $this
*/
public function setInPriceTax(?float $inPriceTax): static
{
	$this->markFieldDirty("inPriceTax", true);

	$this->inPriceTax = $inPriceTax;

	return $this;
}

/**
* Get outPriceTax - Vergi (fiyat harici)
* @return float|null
*/
public function getOutPriceTax(): ?float
{
	if ($this instanceof PreGetValueHookInterface && !\Pimcore::inAdmin()) {
		$preValue = $this->preGetValue("outPriceTax");
		if ($preValue !== null) {
			return $preValue;
		}
	}

	$data = $this->outPriceTax;

	if (\Pimcore\Model\DataObject::doGetInheritedValues() && $this->getClass()->getFieldDefinition("outPriceTax")->isEmpty($data)) {
		try {
			return $this->getValueFromParent("outPriceTax");
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
* Set outPriceTax - Vergi (fiyat harici)
* @param float|null $outPriceTax
* @return $this
*/
public function setOutPriceTax(?float $outPriceTax): static
{
	$this->markFieldDirty("outPriceTax", true);

	$this->outPriceTax = $outPriceTax;

	return $this;
}

/**
* Get feeConstant - Sabit
* @return float|null
*/
public function getFeeConstant(): ?float
{
	if ($this instanceof PreGetValueHookInterface && !\Pimcore::inAdmin()) {
		$preValue = $this->preGetValue("feeConstant");
		if ($preValue !== null) {
			return $preValue;
		}
	}

	$data = $this->feeConstant;

	if (\Pimcore\Model\DataObject::doGetInheritedValues() && $this->getClass()->getFieldDefinition("feeConstant")->isEmpty($data)) {
		try {
			return $this->getValueFromParent("feeConstant");
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
* Set feeConstant - Sabit
* @param float|null $feeConstant
* @return $this
*/
public function setFeeConstant(?float $feeConstant): static
{
	/** @var \Pimcore\Model\DataObject\ClassDefinition\Data\Numeric $fd */
	$fd = $this->getClass()->getFieldDefinition("feeConstant");
	$this->feeConstant = $fd->preSetData($this, $feeConstant);
	return $this;
}

/**
* Get feeVariable - Fiyata Göre
* @return float|null
*/
public function getFeeVariable(): ?float
{
	if ($this instanceof PreGetValueHookInterface && !\Pimcore::inAdmin()) {
		$preValue = $this->preGetValue("feeVariable");
		if ($preValue !== null) {
			return $preValue;
		}
	}

	$data = $this->feeVariable;

	if (\Pimcore\Model\DataObject::doGetInheritedValues() && $this->getClass()->getFieldDefinition("feeVariable")->isEmpty($data)) {
		try {
			return $this->getValueFromParent("feeVariable");
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
* Set feeVariable - Fiyata Göre
* @param float|null $feeVariable
* @return $this
*/
public function setFeeVariable(?float $feeVariable): static
{
	$this->markFieldDirty("feeVariable", true);

	$this->feeVariable = $feeVariable;

	return $this;
}

/**
* @return \Pimcore\Model\DataObject\Fieldcollection|null
*/
public function getVarAirDeci(): ?\Pimcore\Model\DataObject\Fieldcollection
{
	if ($this instanceof PreGetValueHookInterface && !\Pimcore::inAdmin()) {
		$preValue = $this->preGetValue("varAirDeci");
		if ($preValue !== null) {
			return $preValue;
		}
	}

	$data = $this->getClass()->getFieldDefinition("varAirDeci")->preGetData($this);
	return $data;
}

/**
* Set varAirDeci - Var Air Deci
* @param \Pimcore\Model\DataObject\Fieldcollection|null $varAirDeci
* @return $this
*/
public function setVarAirDeci(?\Pimcore\Model\DataObject\Fieldcollection $varAirDeci): static
{
	/** @var \Pimcore\Model\DataObject\ClassDefinition\Data\Fieldcollections $fd */
	$fd = $this->getClass()->getFieldDefinition("varAirDeci");
	$this->varAirDeci = $fd->preSetData($this, $varAirDeci);
	return $this;
}

}

