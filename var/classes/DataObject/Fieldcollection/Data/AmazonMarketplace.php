<?php

/**
 * Fields Summary:
 * - marketplaceId [input]
 * - title [input]
 * - urlLink [link]
 * - salePrice [numeric]
 * - saleCurrency [input]
 * - last7Orders [calculatedValue]
 * - last30Orders [calculatedValue]
 * - totalOrders [calculatedValue]
 * - sku [input]
 * - listingId [input]
 * - quantity [numeric]
 * - fulfillmentChannel [input]
 * - status [input]
 * - marketplace [manyToOneRelation]
 * - lastUpdate [datetime]
 * - ean [input]
 * - countryOfOrigin [input]
 * - madeInTurkiye [checkbox]
 * - brand [input]
 */

namespace Pimcore\Model\DataObject\Fieldcollection\Data;

use Pimcore\Model\DataObject;
use Pimcore\Model\DataObject\PreGetValueHookInterface;

class AmazonMarketplace extends DataObject\Fieldcollection\Data\AbstractData
{
public const FIELD_MARKETPLACE_ID = 'marketplaceId';
public const FIELD_TITLE = 'title';
public const FIELD_URL_LINK = 'urlLink';
public const FIELD_SALE_PRICE = 'salePrice';
public const FIELD_SALE_CURRENCY = 'saleCurrency';
public const FIELD_LAST7_ORDERS = 'last7Orders';
public const FIELD_LAST30_ORDERS = 'last30Orders';
public const FIELD_TOTAL_ORDERS = 'totalOrders';
public const FIELD_SKU = 'sku';
public const FIELD_LISTING_ID = 'listingId';
public const FIELD_QUANTITY = 'quantity';
public const FIELD_FULFILLMENT_CHANNEL = 'fulfillmentChannel';
public const FIELD_STATUS = 'status';
public const FIELD_MARKETPLACE = 'marketplace';
public const FIELD_LAST_UPDATE = 'lastUpdate';
public const FIELD_EAN = 'ean';
public const FIELD_COUNTRY_OF_ORIGIN = 'countryOfOrigin';
public const FIELD_MADE_IN_TURKIYE = 'madeInTurkiye';
public const FIELD_BRAND = 'brand';

protected string $type = "AmazonMarketplace";
protected $marketplaceId;
protected $title;
protected $urlLink;
protected $salePrice;
protected $saleCurrency;
protected $last7Orders;
protected $last30Orders;
protected $totalOrders;
protected $sku;
protected $listingId;
protected $quantity;
protected $fulfillmentChannel;
protected $status;
protected $marketplace;
protected $lastUpdate;
protected $ean;
protected $countryOfOrigin;
protected $madeInTurkiye;
protected $brand;


/**
* Get marketplaceId - Marketplace Id
* @return string|null
*/
public function getMarketplaceId(): ?string
{
	$data = $this->marketplaceId;
	if ($data instanceof \Pimcore\Model\DataObject\Data\EncryptedField) {
		return $data->getPlain();
	}

	return $data;
}

/**
* Set marketplaceId - Marketplace Id
* @param string|null $marketplaceId
* @return $this
*/
public function setMarketplaceId(?string $marketplaceId): static
{
	$this->marketplaceId = $marketplaceId;

	return $this;
}

/**
* Get title - Title
* @return string|null
*/
public function getTitle(): ?string
{
	$data = $this->title;
	if ($data instanceof \Pimcore\Model\DataObject\Data\EncryptedField) {
		return $data->getPlain();
	}

	return $data;
}

/**
* Set title - Title
* @param string|null $title
* @return $this
*/
public function setTitle(?string $title): static
{
	$this->title = $title;

	return $this;
}

/**
* Get urlLink - Url Link
* @return \Pimcore\Model\DataObject\Data\Link|null
*/
public function getUrlLink(): ?\Pimcore\Model\DataObject\Data\Link
{
	$data = $this->urlLink;
	if ($data instanceof \Pimcore\Model\DataObject\Data\EncryptedField) {
		return $data->getPlain();
	}

	return $data;
}

/**
* Set urlLink - Url Link
* @param \Pimcore\Model\DataObject\Data\Link|null $urlLink
* @return $this
*/
public function setUrlLink(?\Pimcore\Model\DataObject\Data\Link $urlLink): static
{
	$this->urlLink = $urlLink;

	return $this;
}

/**
* Get salePrice - Sale Price
* @return string|null
*/
public function getSalePrice(): ?string
{
	$data = $this->salePrice;
	if ($data instanceof \Pimcore\Model\DataObject\Data\EncryptedField) {
		return $data->getPlain();
	}

	return $data;
}

/**
* Set salePrice - Sale Price
* @param string|null $salePrice
* @return $this
*/
public function setSalePrice(?string $salePrice): static
{
	/** @var \Pimcore\Model\DataObject\ClassDefinition\Data\Numeric $fd */
	$fd = $this->getDefinition()->getFieldDefinition("salePrice");
	$this->salePrice = $fd->preSetData($this, $salePrice);
	return $this;
}

/**
* Get saleCurrency - Sale Currency
* @return string|null
*/
public function getSaleCurrency(): ?string
{
	$data = $this->saleCurrency;
	if ($data instanceof \Pimcore\Model\DataObject\Data\EncryptedField) {
		return $data->getPlain();
	}

	return $data;
}

/**
* Set saleCurrency - Sale Currency
* @param string|null $saleCurrency
* @return $this
*/
public function setSaleCurrency(?string $saleCurrency): static
{
	$this->saleCurrency = $saleCurrency;

	return $this;
}

/**
* Get last7Orders - Son 7 Gün Satış
* @return mixed
*/
public function getLast7Orders(): mixed
{
	$data = new \Pimcore\Model\DataObject\Data\CalculatedValue('last7Orders');
	$fcDef = DataObject\Fieldcollection\Definition::getByKey($this->getType());
	$definition = $fcDef->getFieldDefinition('last7Orders');
	$data->setContextualData("fieldcollection", $this->getFieldname(), $this->getIndex(), null, null, null, $definition);
	$data = DataObject\Service::getCalculatedFieldValue($this, $data);

	return $data;
}

/**
* Get last30Orders - Son 30 Gün Satış
* @return mixed
*/
public function getLast30Orders(): mixed
{
	$data = new \Pimcore\Model\DataObject\Data\CalculatedValue('last30Orders');
	$fcDef = DataObject\Fieldcollection\Definition::getByKey($this->getType());
	$definition = $fcDef->getFieldDefinition('last30Orders');
	$data->setContextualData("fieldcollection", $this->getFieldname(), $this->getIndex(), null, null, null, $definition);
	$data = DataObject\Service::getCalculatedFieldValue($this, $data);

	return $data;
}

/**
* Get totalOrders - Toplam Satış
* @return mixed
*/
public function getTotalOrders(): mixed
{
	$data = new \Pimcore\Model\DataObject\Data\CalculatedValue('totalOrders');
	$fcDef = DataObject\Fieldcollection\Definition::getByKey($this->getType());
	$definition = $fcDef->getFieldDefinition('totalOrders');
	$data->setContextualData("fieldcollection", $this->getFieldname(), $this->getIndex(), null, null, null, $definition);
	$data = DataObject\Service::getCalculatedFieldValue($this, $data);

	return $data;
}

/**
* Get sku - SKU
* @return string|null
*/
public function getSku(): ?string
{
	$data = $this->sku;
	if ($data instanceof \Pimcore\Model\DataObject\Data\EncryptedField) {
		return $data->getPlain();
	}

	return $data;
}

/**
* Set sku - SKU
* @param string|null $sku
* @return $this
*/
public function setSku(?string $sku): static
{
	$this->sku = $sku;

	return $this;
}

/**
* Get listingId - Amazon Listing Id
* @return string|null
*/
public function getListingId(): ?string
{
	$data = $this->listingId;
	if ($data instanceof \Pimcore\Model\DataObject\Data\EncryptedField) {
		return $data->getPlain();
	}

	return $data;
}

/**
* Set listingId - Amazon Listing Id
* @param string|null $listingId
* @return $this
*/
public function setListingId(?string $listingId): static
{
	$this->listingId = $listingId;

	return $this;
}

/**
* Get quantity - Miktar
* @return float|null
*/
public function getQuantity(): ?float
{
	$data = $this->quantity;
	if ($data instanceof \Pimcore\Model\DataObject\Data\EncryptedField) {
		return $data->getPlain();
	}

	return $data;
}

/**
* Set quantity - Miktar
* @param float|null $quantity
* @return $this
*/
public function setQuantity(?float $quantity): static
{
	/** @var \Pimcore\Model\DataObject\ClassDefinition\Data\Numeric $fd */
	$fd = $this->getDefinition()->getFieldDefinition("quantity");
	$this->quantity = $fd->preSetData($this, $quantity);
	return $this;
}

/**
* Get fulfillmentChannel - Fulfillment
* @return string|null
*/
public function getFulfillmentChannel(): ?string
{
	$data = $this->fulfillmentChannel;
	if ($data instanceof \Pimcore\Model\DataObject\Data\EncryptedField) {
		return $data->getPlain();
	}

	return $data;
}

/**
* Set fulfillmentChannel - Fulfillment
* @param string|null $fulfillmentChannel
* @return $this
*/
public function setFulfillmentChannel(?string $fulfillmentChannel): static
{
	$this->fulfillmentChannel = $fulfillmentChannel;

	return $this;
}

/**
* Get status - Status
* @return string|null
*/
public function getStatus(): ?string
{
	$data = $this->status;
	if ($data instanceof \Pimcore\Model\DataObject\Data\EncryptedField) {
		return $data->getPlain();
	}

	return $data;
}

/**
* Set status - Status
* @param string|null $status
* @return $this
*/
public function setStatus(?string $status): static
{
	$this->status = $status;

	return $this;
}

/**
* Get marketplace - Marketplace
* @return \Pimcore\Model\DataObject\Marketplace|null
*/
public function getMarketplace(): ?\Pimcore\Model\Element\AbstractElement
{
	$container = $this;
	/** @var \Pimcore\Model\DataObject\ClassDefinition\Data\ManyToOneRelation $fd */
	$fd = $this->getDefinition()->getFieldDefinition("marketplace");
	$data = $fd->preGetData($container);
	if ($data instanceof \Pimcore\Model\DataObject\Data\EncryptedField) {
		return $data->getPlain();
	}

	return $data;
}

/**
* Set marketplace - Marketplace
* @param \Pimcore\Model\DataObject\Marketplace|null $marketplace
* @return $this
*/
public function setMarketplace(?\Pimcore\Model\Element\AbstractElement $marketplace): static
{
	/** @var \Pimcore\Model\DataObject\ClassDefinition\Data\ManyToOneRelation $fd */
	$fd = $this->getDefinition()->getFieldDefinition("marketplace");
	$hideUnpublished = \Pimcore\Model\DataObject\Concrete::getHideUnpublished();
	\Pimcore\Model\DataObject\Concrete::setHideUnpublished(false);
	$currentData = $this->getMarketplace();
	\Pimcore\Model\DataObject\Concrete::setHideUnpublished($hideUnpublished);
	$isEqual = $fd->isEqual($currentData, $marketplace);
	if (!$isEqual) {
		$this->markFieldDirty("marketplace", true);
	}
	$this->marketplace = $fd->preSetData($this, $marketplace);
	return $this;
}

/**
* Get lastUpdate - Last Update
* @return \Carbon\Carbon|null
*/
public function getLastUpdate(): ?\Carbon\Carbon
{
	$data = $this->lastUpdate;
	if ($data instanceof \Pimcore\Model\DataObject\Data\EncryptedField) {
		return $data->getPlain();
	}

	return $data;
}

/**
* Set lastUpdate - Last Update
* @param \Carbon\Carbon|null $lastUpdate
* @return $this
*/
public function setLastUpdate(?\Carbon\Carbon $lastUpdate): static
{
	$this->lastUpdate = $lastUpdate;

	return $this;
}

/**
* Get ean - Ean
* @return string|null
*/
public function getEan(): ?string
{
	$data = $this->ean;
	if ($data instanceof \Pimcore\Model\DataObject\Data\EncryptedField) {
		return $data->getPlain();
	}

	return $data;
}

/**
* Set ean - Ean
* @param string|null $ean
* @return $this
*/
public function setEan(?string $ean): static
{
	$this->ean = $ean;

	return $this;
}

/**
* Get countryOfOrigin - Country Of Origin
* @return string|null
*/
public function getCountryOfOrigin(): ?string
{
	$data = $this->countryOfOrigin;
	if ($data instanceof \Pimcore\Model\DataObject\Data\EncryptedField) {
		return $data->getPlain();
	}

	return $data;
}

/**
* Set countryOfOrigin - Country Of Origin
* @param string|null $countryOfOrigin
* @return $this
*/
public function setCountryOfOrigin(?string $countryOfOrigin): static
{
	$this->countryOfOrigin = $countryOfOrigin;

	return $this;
}

/**
* Get madeInTurkiye - Made in Turkiye
* @return bool|null
*/
public function getMadeInTurkiye(): ?bool
{
	$data = $this->madeInTurkiye;
	if ($data instanceof \Pimcore\Model\DataObject\Data\EncryptedField) {
		return $data->getPlain();
	}

	return $data;
}

/**
* Set madeInTurkiye - Made in Turkiye
* @param bool|null $madeInTurkiye
* @return $this
*/
public function setMadeInTurkiye(?bool $madeInTurkiye): static
{
	$this->madeInTurkiye = $madeInTurkiye;

	return $this;
}

/**
* Get brand - Brand
* @return string|null
*/
public function getBrand(): ?string
{
	$data = $this->brand;
	if ($data instanceof \Pimcore\Model\DataObject\Data\EncryptedField) {
		return $data->getPlain();
	}

	return $data;
}

/**
* Set brand - Brand
* @param string|null $brand
* @return $this
*/
public function setBrand(?string $brand): static
{
	$this->brand = $brand;

	return $this;
}

}

