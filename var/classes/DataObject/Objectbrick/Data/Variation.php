<?php

/**
 * Fields Summary:
 * - variationSize [input]
 * - variationColor [input]
 */

namespace Pimcore\Model\DataObject\Objectbrick\Data;

use Pimcore\Model\DataObject;
use Pimcore\Model\DataObject\Exception\InheritanceParentNotFoundException;
use Pimcore\Model\DataObject\PreGetValueHookInterface;


class Variation extends DataObject\Objectbrick\Data\AbstractData
{
public const FIELD_VARIATION_SIZE = 'variationSize';
public const FIELD_VARIATION_COLOR = 'variationColor';

protected string $type = "Variation";
protected $variationSize;
protected $variationColor;


/**
* Variation constructor.
* @param DataObject\Concrete $object
*/
public function __construct(DataObject\Concrete $object)
{
	parent::__construct($object);
	$this->markFieldDirty("_self");
}


/**
* Get variationSize - Ebat
* @return string|null
*/
public function getVariationSize(): ?string
{
	$data = $this->variationSize;
	if(\Pimcore\Model\DataObject::doGetInheritedValues($this->getObject()) && $this->getDefinition()->getFieldDefinition("variationSize")->isEmpty($data)) {
		try {
			return $this->getValueFromParent("variationSize");
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
* Set variationSize - Ebat
* @param string|null $variationSize
* @return $this
*/
public function setVariationSize (?string $variationSize): static
{
	$this->variationSize = $variationSize;

	return $this;
}

/**
* Get variationColor - Renk
* @return string|null
*/
public function getVariationColor(): ?string
{
	$data = $this->variationColor;
	if(\Pimcore\Model\DataObject::doGetInheritedValues($this->getObject()) && $this->getDefinition()->getFieldDefinition("variationColor")->isEmpty($data)) {
		try {
			return $this->getValueFromParent("variationColor");
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
* Set variationColor - Renk
* @param string|null $variationColor
* @return $this
*/
public function setVariationColor (?string $variationColor): static
{
	$this->variationColor = $variationColor;

	return $this;
}

}

