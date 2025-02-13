<?php

/**
 * Inheritance: no
 * Variants: no
 *
 * Fields Summary:
 * - target [select]
 * - order [numeric]
 * - productList [reverseObjectRelation]
 */

namespace Pimcore\Model\DataObject;

use Pimcore\Model\DataObject\Exception\InheritanceParentNotFoundException;
use Pimcore\Model\DataObject\PreGetValueHookInterface;

/**
* @method static \Pimcore\Model\DataObject\Brand\Listing getList(array $config = [])
* @method static \Pimcore\Model\DataObject\Brand\Listing|\Pimcore\Model\DataObject\Brand|null getByTarget(mixed $value, ?int $limit = null, int $offset = 0, ?array $objectTypes = null)
* @method static \Pimcore\Model\DataObject\Brand\Listing|\Pimcore\Model\DataObject\Brand|null getByOrder(mixed $value, ?int $limit = null, int $offset = 0, ?array $objectTypes = null)
* @method static \Pimcore\Model\DataObject\Brand\Listing|\Pimcore\Model\DataObject\Brand|null getByProductList(mixed $value, ?int $limit = null, int $offset = 0, ?array $objectTypes = null)
*/

class Brand extends Concrete
{
public const FIELD_TARGET = 'target';
public const FIELD_ORDER = 'order';
public const FIELD_PRODUCT_LIST = 'productList';

protected $classId = "brand";
protected $className = "Brand";
protected $target;
protected $order;


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
* Get target - Hedef
* @return string|null
*/
public function getTarget(): ?string
{
	if ($this instanceof PreGetValueHookInterface && !\Pimcore::inAdmin()) {
		$preValue = $this->preGetValue("target");
		if ($preValue !== null) {
			return $preValue;
		}
	}

	$data = $this->target;

	if ($data instanceof \Pimcore\Model\DataObject\Data\EncryptedField) {
		return $data->getPlain();
	}

	return $data;
}

/**
* Set target - Hedef
* @param string|null $target
* @return $this
*/
public function setTarget(?string $target): static
{
	$this->markFieldDirty("target", true);

	$this->target = $target;

	return $this;
}

/**
* Get order - Sıra
* @return float|null
*/
public function getOrder(): ?float
{
	if ($this instanceof PreGetValueHookInterface && !\Pimcore::inAdmin()) {
		$preValue = $this->preGetValue("order");
		if ($preValue !== null) {
			return $preValue;
		}
	}

	$data = $this->order;

	if ($data instanceof \Pimcore\Model\DataObject\Data\EncryptedField) {
		return $data->getPlain();
	}

	return $data;
}

/**
* Set order - Sıra
* @param float|null $order
* @return $this
*/
public function setOrder(?float $order): static
{
	/** @var \Pimcore\Model\DataObject\ClassDefinition\Data\Numeric $fd */
	$fd = $this->getClass()->getFieldDefinition("order");
	$this->order = $fd->preSetData($this, $order);
	return $this;
}

/**
* Get productList - Ürünler
* @return \Pimcore\Model\DataObject\Product[]
*/
public function getProductList(): array
{
	if ($this instanceof PreGetValueHookInterface && !\Pimcore::inAdmin()) {
		$preValue = $this->preGetValue("productList");
		if ($preValue !== null) {
			return $preValue;
		}
	}

	$data = $this->getClass()->getFieldDefinition("productList")->preGetData($this);

	if ($data instanceof \Pimcore\Model\DataObject\Data\EncryptedField) {
		return $data->getPlain();
	}

	return $data;
}

}

