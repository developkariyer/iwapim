<?php

/**
 * Fields Summary:
 * - costItem [manyToOneRelation]
 * - amount [numeric]
 */

namespace Pimcore\Model\DataObject\Fieldcollection\Data;

use Pimcore\Model\DataObject;
use Pimcore\Model\DataObject\PreGetValueHookInterface;

class CostItems extends DataObject\Fieldcollection\Data\AbstractData
{
public const FIELD_COST_ITEM = 'costItem';
public const FIELD_AMOUNT = 'amount';

protected string $type = "costItems";
protected $costItem;
protected $amount;


/**
* Get costItem - Sarf Kalemi
* @return \Pimcore\Model\DataObject\CostNode|null
*/
public function getCostItem(): ?\Pimcore\Model\Element\AbstractElement
{
	$container = $this;
	/** @var \Pimcore\Model\DataObject\ClassDefinition\Data\ManyToOneRelation $fd */
	$fd = $this->getDefinition()->getFieldDefinition("costItem");
	$data = $fd->preGetData($container);
	if ($data instanceof \Pimcore\Model\DataObject\Data\EncryptedField) {
		return $data->getPlain();
	}

	return $data;
}

/**
* Set costItem - Sarf Kalemi
* @param \Pimcore\Model\DataObject\CostNode|null $costItem
* @return $this
*/
public function setCostItem(?\Pimcore\Model\Element\AbstractElement $costItem): static
{
	/** @var \Pimcore\Model\DataObject\ClassDefinition\Data\ManyToOneRelation $fd */
	$fd = $this->getDefinition()->getFieldDefinition("costItem");
	$hideUnpublished = \Pimcore\Model\DataObject\Concrete::getHideUnpublished();
	\Pimcore\Model\DataObject\Concrete::setHideUnpublished(false);
	$currentData = $this->getCostItem();
	\Pimcore\Model\DataObject\Concrete::setHideUnpublished($hideUnpublished);
	$isEqual = $fd->isEqual($currentData, $costItem);
	if (!$isEqual) {
		$this->markFieldDirty("costItem", true);
	}
	$this->costItem = $fd->preSetData($this, $costItem);
	return $this;
}

/**
* Get amount - Sarf Miktarı
* @return float|null
*/
public function getAmount(): ?float
{
	$data = $this->amount;
	if ($data instanceof \Pimcore\Model\DataObject\Data\EncryptedField) {
		return $data->getPlain();
	}

	return $data;
}

/**
* Set amount - Sarf Miktarı
* @param float|null $amount
* @return $this
*/
public function setAmount(?float $amount): static
{
	/** @var \Pimcore\Model\DataObject\ClassDefinition\Data\Numeric $fd */
	$fd = $this->getDefinition()->getFieldDefinition("amount");
	$this->amount = $fd->preSetData($this, $amount);
	return $this;
}

}

