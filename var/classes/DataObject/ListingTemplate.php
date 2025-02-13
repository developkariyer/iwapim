<?php

/**
 * Inheritance: no
 * Variants: no
 *
 * Fields Summary:
 * - marketplace [manyToOneRelation]
 */

namespace Pimcore\Model\DataObject;

use Pimcore\Model\DataObject\Exception\InheritanceParentNotFoundException;
use Pimcore\Model\DataObject\PreGetValueHookInterface;

/**
* @method static \Pimcore\Model\DataObject\ListingTemplate\Listing getList(array $config = [])
* @method static \Pimcore\Model\DataObject\ListingTemplate\Listing|\Pimcore\Model\DataObject\ListingTemplate|null getByMarketplace(mixed $value, ?int $limit = null, int $offset = 0, ?array $objectTypes = null)
*/

class ListingTemplate extends Concrete
{
public const FIELD_MARKETPLACE = 'marketplace';

protected $classId = "listingTemplate";
protected $className = "ListingTemplate";
protected $marketplace;


/**
* @param array $values
* @return static
*/
public static function create(array $values = []): static
{
	$object = new static();
	$object->setValues($values);
	return $object;
}

/**
* Get marketplace - Market
* @return \Pimcore\Model\DataObject\Marketplace|null
*/
public function getMarketplace(): ?\Pimcore\Model\Element\AbstractElement
{
	if ($this instanceof PreGetValueHookInterface && !\Pimcore::inAdmin()) {
		$preValue = $this->preGetValue("marketplace");
		if ($preValue !== null) {
			return $preValue;
		}
	}

	$data = $this->getClass()->getFieldDefinition("marketplace")->preGetData($this);

	if ($data instanceof \Pimcore\Model\DataObject\Data\EncryptedField) {
		return $data->getPlain();
	}

	return $data;
}

/**
* Set marketplace - Market
* @param \Pimcore\Model\DataObject\Marketplace|null $marketplace
* @return $this
*/
public function setMarketplace(?\Pimcore\Model\Element\AbstractElement $marketplace): static
{
	/** @var \Pimcore\Model\DataObject\ClassDefinition\Data\ManyToOneRelation $fd */
	$fd = $this->getClass()->getFieldDefinition("marketplace");
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

}

