<?php

/**
 * Inheritance: no
 * Variants: no
 *
 * Fields Summary:
 * - serialNumber [numeric]
 * - qrcode [calculatedValue]
 * - product [manyToOneRelation]
 * - label [manyToOneRelation]
 */

namespace Pimcore\Model\DataObject;

use Pimcore\Model\DataObject\Exception\InheritanceParentNotFoundException;
use Pimcore\Model\DataObject\PreGetValueHookInterface;

/**
* @method static \Pimcore\Model\DataObject\Serial\Listing getList(array $config = [])
* @method static \Pimcore\Model\DataObject\Serial\Listing|\Pimcore\Model\DataObject\Serial|null getBySerialNumber(mixed $value, ?int $limit = null, int $offset = 0, ?array $objectTypes = null)
* @method static \Pimcore\Model\DataObject\Serial\Listing|\Pimcore\Model\DataObject\Serial|null getByProduct(mixed $value, ?int $limit = null, int $offset = 0, ?array $objectTypes = null)
* @method static \Pimcore\Model\DataObject\Serial\Listing|\Pimcore\Model\DataObject\Serial|null getByLabel(mixed $value, ?int $limit = null, int $offset = 0, ?array $objectTypes = null)
*/

class Serial extends \App\Model\DataObject\Serial
{
public const FIELD_SERIAL_NUMBER = 'serialNumber';
public const FIELD_QRCODE = 'qrcode';
public const FIELD_PRODUCT = 'product';
public const FIELD_LABEL = 'label';

protected $classId = "serial";
protected $className = "Serial";
protected $serialNumber;
protected $product;
protected $label;


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
* Get serialNumber - Seri No
* @return int|null
*/
public function getSerialNumber(): ?int
{
	if ($this instanceof PreGetValueHookInterface && !\Pimcore::inAdmin()) {
		$preValue = $this->preGetValue("serialNumber");
		if ($preValue !== null) {
			return $preValue;
		}
	}

	$data = $this->serialNumber;

	if ($data instanceof \Pimcore\Model\DataObject\Data\EncryptedField) {
		return $data->getPlain();
	}

	return $data;
}

/**
* Set serialNumber - Seri No
* @param int|null $serialNumber
* @return $this
*/
public function setSerialNumber(?int $serialNumber): static
{
	/** @var \Pimcore\Model\DataObject\ClassDefinition\Data\Numeric $fd */
	$fd = $this->getClass()->getFieldDefinition("serialNumber");
	$this->serialNumber = $fd->preSetData($this, $serialNumber);
	return $this;
}

/**
* Get qrcode - QR Link
* @return mixed
*/
public function getQrcode(): mixed
{
	$data = new \Pimcore\Model\DataObject\Data\CalculatedValue('qrcode');
	$data->setContextualData("object", null, null, null);
	$object = $this;
	$data = \Pimcore\Model\DataObject\Service::getCalculatedFieldValue($object, $data);

	return $data;
}

/**
* Get product - Product
* @return \Pimcore\Model\DataObject\Product|null
*/
public function getProduct(): ?\Pimcore\Model\Element\AbstractElement
{
	if ($this instanceof PreGetValueHookInterface && !\Pimcore::inAdmin()) {
		$preValue = $this->preGetValue("product");
		if ($preValue !== null) {
			return $preValue;
		}
	}

	$data = $this->getClass()->getFieldDefinition("product")->preGetData($this);

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
	$fd = $this->getClass()->getFieldDefinition("product");
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
* Get label - Label
* @return \Pimcore\Model\Asset\Document|null
*/
public function getLabel(): ?\Pimcore\Model\Element\AbstractElement
{
	if ($this instanceof PreGetValueHookInterface && !\Pimcore::inAdmin()) {
		$preValue = $this->preGetValue("label");
		if ($preValue !== null) {
			return $preValue;
		}
	}

	$data = $this->getClass()->getFieldDefinition("label")->preGetData($this);

	if ($data instanceof \Pimcore\Model\DataObject\Data\EncryptedField) {
		return $data->getPlain();
	}

	return $data;
}

/**
* Set label - Label
* @param \Pimcore\Model\Asset\Document|null $label
* @return $this
*/
public function setLabel(?\Pimcore\Model\Element\AbstractElement $label): static
{
	/** @var \Pimcore\Model\DataObject\ClassDefinition\Data\ManyToOneRelation $fd */
	$fd = $this->getClass()->getFieldDefinition("label");
	$hideUnpublished = \Pimcore\Model\DataObject\Concrete::getHideUnpublished();
	\Pimcore\Model\DataObject\Concrete::setHideUnpublished(false);
	$currentData = $this->getLabel();
	\Pimcore\Model\DataObject\Concrete::setHideUnpublished($hideUnpublished);
	$isEqual = $fd->isEqual($currentData, $label);
	if (!$isEqual) {
		$this->markFieldDirty("label", true);
	}
	$this->label = $fd->preSetData($this, $label);
	return $this;
}

}

