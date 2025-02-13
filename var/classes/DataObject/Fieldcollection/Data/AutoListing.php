<?php

/**
 * Fields Summary:
 * - product [manyToOneRelation]
 * - price [numeric]
 */

namespace Pimcore\Model\DataObject\Fieldcollection\Data;

use Pimcore\Model\DataObject;
use Pimcore\Model\DataObject\PreGetValueHookInterface;

class AutoListing extends DataObject\Fieldcollection\Data\AbstractData
{
public const FIELD_PRODUCT = 'product';
public const FIELD_PRICE = 'price';

protected string $type = "AutoListing";
protected $product;
protected $price;


/**
* Get product - Product
* @return \Pimcore\Model\DataObject\Product|null
*/
public function getProduct(): ?\Pimcore\Model\Element\AbstractElement
{
	$container = $this;
	/** @var \Pimcore\Model\DataObject\ClassDefinition\Data\ManyToOneRelation $fd */
	$fd = $this->getDefinition()->getFieldDefinition("product");
	$data = $fd->preGetData($container);
	if ($data instanceof \Pimcore\Model\DataObject\Data\EncryptedField) {
		return $data->getPlain();
	}

	return $data;
}

/**
* Set product - Product
* @param \Pimcore\Model\DataObject\Product|null $product
* @return $this
*/
public function setProduct(?\Pimcore\Model\Element\AbstractElement $product): static
{
	/** @var \Pimcore\Model\DataObject\ClassDefinition\Data\ManyToOneRelation $fd */
	$fd = $this->getDefinition()->getFieldDefinition("product");
	$hideUnpublished = \Pimcore\Model\DataObject\Concrete::getHideUnpublished();
	\Pimcore\Model\DataObject\Concrete::setHideUnpublished(false);
	$currentData = $this->getProduct();
	\Pimcore\Model\DataObject\Concrete::setHideUnpublished($hideUnpublished);
	$isEqual = $fd->isEqual($currentData, $product);
	if (!$isEqual) {
		$this->markFieldDirty("product", true);
	}
	$this->product = $fd->preSetData($this, $product);
	return $this;
}

/**
* Get price - Price
* @return string|null
*/
public function getPrice(): ?string
{
	$data = $this->price;
	if ($data instanceof \Pimcore\Model\DataObject\Data\EncryptedField) {
		return $data->getPlain();
	}

	return $data;
}

/**
* Set price - Price
* @param string|null $price
* @return $this
*/
public function setPrice(?string $price): static
{
	/** @var \Pimcore\Model\DataObject\ClassDefinition\Data\Numeric $fd */
	$fd = $this->getDefinition()->getFieldDefinition("price");
	$this->price = $fd->preSetData($this, $price);
	return $this;
}

}

