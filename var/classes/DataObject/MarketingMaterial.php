<?php

/**
 * Inheritance: no
 * Variants: no
 *
 * Fields Summary:
 * - title [input]
 * - description [textarea]
 * - campaignName [input]
 * - status [select]
 * - asset [manyToOneRelation]
 * - products [reverseObjectRelation]
 */

namespace Pimcore\Model\DataObject;

use Pimcore\Model\DataObject\Exception\InheritanceParentNotFoundException;
use Pimcore\Model\DataObject\PreGetValueHookInterface;

/**
* @method static \Pimcore\Model\DataObject\MarketingMaterial\Listing getList(array $config = [])
* @method static \Pimcore\Model\DataObject\MarketingMaterial\Listing|\Pimcore\Model\DataObject\MarketingMaterial|null getByTitle(mixed $value, ?int $limit = null, int $offset = 0, ?array $objectTypes = null)
* @method static \Pimcore\Model\DataObject\MarketingMaterial\Listing|\Pimcore\Model\DataObject\MarketingMaterial|null getByDescription(mixed $value, ?int $limit = null, int $offset = 0, ?array $objectTypes = null)
* @method static \Pimcore\Model\DataObject\MarketingMaterial\Listing|\Pimcore\Model\DataObject\MarketingMaterial|null getByCampaignName(mixed $value, ?int $limit = null, int $offset = 0, ?array $objectTypes = null)
* @method static \Pimcore\Model\DataObject\MarketingMaterial\Listing|\Pimcore\Model\DataObject\MarketingMaterial|null getByStatus(mixed $value, ?int $limit = null, int $offset = 0, ?array $objectTypes = null)
* @method static \Pimcore\Model\DataObject\MarketingMaterial\Listing|\Pimcore\Model\DataObject\MarketingMaterial|null getByAsset(mixed $value, ?int $limit = null, int $offset = 0, ?array $objectTypes = null)
* @method static \Pimcore\Model\DataObject\MarketingMaterial\Listing|\Pimcore\Model\DataObject\MarketingMaterial|null getByProducts(mixed $value, ?int $limit = null, int $offset = 0, ?array $objectTypes = null)
*/

class MarketingMaterial extends Concrete
{
public const FIELD_TITLE = 'title';
public const FIELD_DESCRIPTION = 'description';
public const FIELD_CAMPAIGN_NAME = 'campaignName';
public const FIELD_STATUS = 'status';
public const FIELD_ASSET = 'asset';
public const FIELD_PRODUCTS = 'products';

protected $classId = "MarketingMaterial";
protected $className = "MarketingMaterial";
protected $title;
protected $description;
protected $campaignName;
protected $status;
protected $asset;


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
* Get title - Title
* @return string|null
*/
public function getTitle(): ?string
{
	if ($this instanceof PreGetValueHookInterface && !\Pimcore::inAdmin()) {
		$preValue = $this->preGetValue("title");
		if ($preValue !== null) {
			return $preValue;
		}
	}

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
	$this->markFieldDirty("title", true);

	$this->title = $title;

	return $this;
}

/**
* Get description - Description
* @return string|null
*/
public function getDescription(): ?string
{
	if ($this instanceof PreGetValueHookInterface && !\Pimcore::inAdmin()) {
		$preValue = $this->preGetValue("description");
		if ($preValue !== null) {
			return $preValue;
		}
	}

	$data = $this->description;

	if ($data instanceof \Pimcore\Model\DataObject\Data\EncryptedField) {
		return $data->getPlain();
	}

	return $data;
}

/**
* Set description - Description
* @param string|null $description
* @return $this
*/
public function setDescription(?string $description): static
{
	$this->markFieldDirty("description", true);

	$this->description = $description;

	return $this;
}

/**
* Get campaignName - Campaign Name
* @return string|null
*/
public function getCampaignName(): ?string
{
	if ($this instanceof PreGetValueHookInterface && !\Pimcore::inAdmin()) {
		$preValue = $this->preGetValue("campaignName");
		if ($preValue !== null) {
			return $preValue;
		}
	}

	$data = $this->campaignName;

	if ($data instanceof \Pimcore\Model\DataObject\Data\EncryptedField) {
		return $data->getPlain();
	}

	return $data;
}

/**
* Set campaignName - Campaign Name
* @param string|null $campaignName
* @return $this
*/
public function setCampaignName(?string $campaignName): static
{
	$this->markFieldDirty("campaignName", true);

	$this->campaignName = $campaignName;

	return $this;
}

/**
* Get status - Status
* @return string|null
*/
public function getStatus(): ?string
{
	if ($this instanceof PreGetValueHookInterface && !\Pimcore::inAdmin()) {
		$preValue = $this->preGetValue("status");
		if ($preValue !== null) {
			return $preValue;
		}
	}

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
	$this->markFieldDirty("status", true);

	$this->status = $status;

	return $this;
}

/**
* Get asset - Asset
* @return \Pimcore\Model\Asset\Image|\Pimcore\Model\Asset\Audio|\Pimcore\Model\Asset\Video|null
*/
public function getAsset(): ?\Pimcore\Model\Element\AbstractElement
{
	if ($this instanceof PreGetValueHookInterface && !\Pimcore::inAdmin()) {
		$preValue = $this->preGetValue("asset");
		if ($preValue !== null) {
			return $preValue;
		}
	}

	$data = $this->getClass()->getFieldDefinition("asset")->preGetData($this);

	if ($data instanceof \Pimcore\Model\DataObject\Data\EncryptedField) {
		return $data->getPlain();
	}

	return $data;
}

/**
* Set asset - Asset
* @param \Pimcore\Model\Asset\Image|\Pimcore\Model\Asset\Audio|\Pimcore\Model\Asset\Video|null $asset
* @return $this
*/
public function setAsset(?\Pimcore\Model\Element\AbstractElement $asset): static
{
	/** @var \Pimcore\Model\DataObject\ClassDefinition\Data\ManyToOneRelation $fd */
	$fd = $this->getClass()->getFieldDefinition("asset");
	$hideUnpublished = \Pimcore\Model\DataObject\Concrete::getHideUnpublished();
	\Pimcore\Model\DataObject\Concrete::setHideUnpublished(false);
	$currentData = $this->getAsset();
	\Pimcore\Model\DataObject\Concrete::setHideUnpublished($hideUnpublished);
	$isEqual = $fd->isEqual($currentData, $asset);
	if (!$isEqual) {
		$this->markFieldDirty("asset", true);
	}
	$this->asset = $fd->preSetData($this, $asset);
	return $this;
}

/**
* Get products - Products
* @return \Pimcore\Model\DataObject\AbstractObject[]
*/
public function getProducts(): array
{
	if ($this instanceof PreGetValueHookInterface && !\Pimcore::inAdmin()) {
		$preValue = $this->preGetValue("products");
		if ($preValue !== null) {
			return $preValue;
		}
	}

	$data = $this->getClass()->getFieldDefinition("products")->preGetData($this);

	if ($data instanceof \Pimcore\Model\DataObject\Data\EncryptedField) {
		return $data->getPlain();
	}

	return $data;
}

}

