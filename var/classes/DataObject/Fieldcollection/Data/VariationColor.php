<?php

/**
 * Fields Summary:
 * - variationColors [input]
 */

namespace Pimcore\Model\DataObject\Fieldcollection\Data;

use Pimcore\Model\DataObject;
use Pimcore\Model\DataObject\PreGetValueHookInterface;

class VariationColor extends DataObject\Fieldcollection\Data\AbstractData
{
public const FIELD_VARIATION_COLORS = 'variationColors';

protected string $type = "variationColor";
protected $variationColors;


/**
* Get variationColors - Variation Colors
* @return string|null
*/
public function getVariationColors(): ?string
{
	$data = $this->variationColors;
	if ($data instanceof \Pimcore\Model\DataObject\Data\EncryptedField) {
		return $data->getPlain();
	}

	return $data;
}

/**
* Set variationColors - Variation Colors
* @param string|null $variationColors
* @return $this
*/
public function setVariationColors(?string $variationColors): static
{
	$this->variationColors = $variationColors;

	return $this;
}

}

