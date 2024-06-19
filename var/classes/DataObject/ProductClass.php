<?php

/**
 * Inheritance: no
 * Variants: no
 *
 * Fields Summary:
 * - productClassName [input]
 * - order [numeric]
 */

namespace Pimcore\Model\DataObject;

use Pimcore\Model\DataObject\Exception\InheritanceParentNotFoundException;
use Pimcore\Model\DataObject\PreGetValueHookInterface;

/**
* @method static \Pimcore\Model\DataObject\ProductClass\Listing getList(array $config = [])
* @method static \Pimcore\Model\DataObject\ProductClass\Listing|\Pimcore\Model\DataObject\ProductClass|null getByProductClassName(mixed $value, ?int $limit = null, int $offset = 0, ?array $objectTypes = null)
* @method static \Pimcore\Model\DataObject\ProductClass\Listing|\Pimcore\Model\DataObject\ProductClass|null getByOrder(mixed $value, ?int $limit = null, int $offset = 0, ?array $objectTypes = null)
*/

class ProductClass extends Concrete
{
public const FIELD_PRODUCT_CLASS_NAME = 'productClassName';
public const FIELD_ORDER = 'order';

protected $classId = "pclass";
protected $className = "ProductClass";
protected $productClassName;
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
* Get productClassName - Ürün Sınıfı
* @return string|null
*/
public function getProductClassName(): ?string
{
	if ($this instanceof PreGetValueHookInterface && !\Pimcore::inAdmin()) {
		$preValue = $this->preGetValue("productClassName");
		if ($preValue !== null) {
			return $preValue;
		}
	}

	$data = $this->productClassName;

	if ($data instanceof \Pimcore\Model\DataObject\Data\EncryptedField) {
		return $data->getPlain();
	}

	return $data;
}

/**
* Set productClassName - Ürün Sınıfı
* @param string|null $productClassName
* @return $this
*/
public function setProductClassName(?string $productClassName): static
{
	$this->markFieldDirty("productClassName", true);

	$this->productClassName = $productClassName;

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

}

