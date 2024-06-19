<?php

/**
 * Fields Summary:
 * - shopUrl [input]
 */

namespace Pimcore\Model\DataObject\Fieldcollection\Data;

use Pimcore\Model\DataObject;
use Pimcore\Model\DataObject\PreGetValueHookInterface;

class ListingItems extends DataObject\Fieldcollection\Data\AbstractData
{
public const FIELD_SHOP_URL = 'shopUrl';

protected string $type = "listingItems";
protected $shopUrl;


/**
* Get shopUrl - Shop URL
* @return string|null
*/
public function getShopUrl(): ?string
{
	$data = $this->shopUrl;
	if ($data instanceof \Pimcore\Model\DataObject\Data\EncryptedField) {
		return $data->getPlain();
	}

	return $data;
}

/**
* Set shopUrl - Shop URL
* @param string|null $shopUrl
* @return $this
*/
public function setShopUrl(?string $shopUrl): static
{
	$this->shopUrl = $shopUrl;

	return $this;
}

}

