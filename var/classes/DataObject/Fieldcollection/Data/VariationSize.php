<?php

/**
 * Fields Summary:
 * - variationSizes [input]
 */

namespace Pimcore\Model\DataObject\Fieldcollection\Data;

use Pimcore\Model\DataObject;
use Pimcore\Model\DataObject\PreGetValueHookInterface;

class VariationSize extends DataObject\Fieldcollection\Data\AbstractData
{
public const FIELD_VARIATION_SIZES = 'variationSizes';

protected string $type = "variationSize";
protected $variationSizes;


/**
* Get variationSizes - Variation Sizes
* @return string|null
*/
public function getVariationSizes(): ?string
{
	$data = $this->variationSizes;
	if ($data instanceof \Pimcore\Model\DataObject\Data\EncryptedField) {
		return $data->getPlain();
	}

	return $data;
}

/**
* Set variationSizes - Variation Sizes
* @param string|null $variationSizes
* @return $this
*/
public function setVariationSizes(?string $variationSizes): static
{
	$this->variationSizes = $variationSizes;

	return $this;
}

}

