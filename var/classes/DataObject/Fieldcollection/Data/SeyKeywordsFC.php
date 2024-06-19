<?php

/**
 * Fields Summary:
 * - seoKeyword [input]
 */

namespace Pimcore\Model\DataObject\Fieldcollection\Data;

use Pimcore\Model\DataObject;
use Pimcore\Model\DataObject\PreGetValueHookInterface;

class SeyKeywordsFC extends DataObject\Fieldcollection\Data\AbstractData
{
public const FIELD_SEO_KEYWORD = 'seoKeyword';

protected string $type = "seyKeywordsFC";
protected $seoKeyword;


/**
* Get seoKeyword - SEO Keyword
* @return string|null
*/
public function getSeoKeyword(): ?string
{
	$data = $this->seoKeyword;
	if ($data instanceof \Pimcore\Model\DataObject\Data\EncryptedField) {
		return $data->getPlain();
	}

	return $data;
}

/**
* Set seoKeyword - SEO Keyword
* @param string|null $seoKeyword
* @return $this
*/
public function setSeoKeyword(?string $seoKeyword): static
{
	$this->seoKeyword = $seoKeyword;

	return $this;
}

}

