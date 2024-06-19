<?php

/**
 * Fields Summary:
 * - airDeciVar [numeric]
 * - range [numericRange]
 */

namespace Pimcore\Model\DataObject\Fieldcollection\Data;

use Pimcore\Model\DataObject;
use Pimcore\Model\DataObject\PreGetValueHookInterface;

class VarAirDeci extends DataObject\Fieldcollection\Data\AbstractData
{
public const FIELD_AIR_DECI_VAR = 'airDeciVar';
public const FIELD_RANGE = 'range';

protected string $type = "varAirDeci";
protected $airDeciVar;
protected $range;


/**
* Get airDeciVar - Air Deci Var
* @return float|null
*/
public function getAirDeciVar(): ?float
{
	$data = $this->airDeciVar;
	if ($data instanceof \Pimcore\Model\DataObject\Data\EncryptedField) {
		return $data->getPlain();
	}

	return $data;
}

/**
* Set airDeciVar - Air Deci Var
* @param float|null $airDeciVar
* @return $this
*/
public function setAirDeciVar(?float $airDeciVar): static
{
	/** @var \Pimcore\Model\DataObject\ClassDefinition\Data\Numeric $fd */
	$fd = $this->getDefinition()->getFieldDefinition("airDeciVar");
	$this->airDeciVar = $fd->preSetData($this, $airDeciVar);
	return $this;
}

/**
* Get range - Range
* @return \Pimcore\Model\DataObject\Data\NumericRange|null
*/
public function getRange(): ?\Pimcore\Model\DataObject\Data\NumericRange
{
	$data = $this->range;
	if ($data instanceof \Pimcore\Model\DataObject\Data\EncryptedField) {
		return $data->getPlain();
	}

	return $data;
}

/**
* Set range - Range
* @param \Pimcore\Model\DataObject\Data\NumericRange|null $range
* @return $this
*/
public function setRange(?\Pimcore\Model\DataObject\Data\NumericRange $range): static
{
	$this->range = $range;

	return $this;
}

}

