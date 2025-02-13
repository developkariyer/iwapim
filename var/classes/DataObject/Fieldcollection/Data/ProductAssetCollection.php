<?php

/**
 * Fields Summary:
 * - collectionName [input]
 * - collectionAssets [manyToManyRelation]
 */

namespace Pimcore\Model\DataObject\Fieldcollection\Data;

use Pimcore\Model\DataObject;
use Pimcore\Model\DataObject\PreGetValueHookInterface;

class ProductAssetCollection extends DataObject\Fieldcollection\Data\AbstractData
{
public const FIELD_COLLECTION_NAME = 'collectionName';
public const FIELD_COLLECTION_ASSETS = 'collectionAssets';

protected string $type = "ProductAssetCollection";
protected $collectionName;
protected $collectionAssets;


/**
* Get collectionName - Dosya Grubu Adı
* @return string|null
*/
public function getCollectionName(): ?string
{
	$data = $this->collectionName;
	if ($data instanceof \Pimcore\Model\DataObject\Data\EncryptedField) {
		return $data->getPlain();
	}

	return $data;
}

/**
* Set collectionName - Dosya Grubu Adı
* @param string|null $collectionName
* @return $this
*/
public function setCollectionName(?string $collectionName): static
{
	$this->collectionName = $collectionName;

	return $this;
}

/**
* Get collectionAssets - Dosyalar
* @return \Pimcore\Model\Asset[]
*/
public function getCollectionAssets(): array
{
	$container = $this;
	/** @var \Pimcore\Model\DataObject\ClassDefinition\Data\ManyToManyRelation $fd */
	$fd = $this->getDefinition()->getFieldDefinition("collectionAssets");
	$data = $fd->preGetData($container);
	if ($data instanceof \Pimcore\Model\DataObject\Data\EncryptedField) {
		return $data->getPlain();
	}

	return $data;
}

/**
* Set collectionAssets - Dosyalar
* @param \Pimcore\Model\Asset[] $collectionAssets
* @return $this
*/
public function setCollectionAssets(?array $collectionAssets): static
{
	/** @var \Pimcore\Model\DataObject\ClassDefinition\Data\ManyToManyRelation $fd */
	$fd = $this->getDefinition()->getFieldDefinition("collectionAssets");
	$hideUnpublished = \Pimcore\Model\DataObject\Concrete::getHideUnpublished();
	\Pimcore\Model\DataObject\Concrete::setHideUnpublished(false);
	$currentData = $this->getCollectionAssets();
	\Pimcore\Model\DataObject\Concrete::setHideUnpublished($hideUnpublished);
	$isEqual = $fd->isEqual($currentData, $collectionAssets);
	if (!$isEqual) {
		$this->markFieldDirty("collectionAssets", true);
	}
	$this->collectionAssets = $fd->preSetData($this, $collectionAssets);
	return $this;
}

}

