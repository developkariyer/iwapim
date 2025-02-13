<?php

/**
 * Inheritance: no
 * Variants: no
 *
 * Fields Summary:
 * - description [textarea]
 * - pricingNodes [advancedManyToManyObjectRelation]
 * - products [advancedManyToManyObjectRelation]
 */

namespace Pimcore\Model\DataObject;

use Pimcore\Model\DataObject\Exception\InheritanceParentNotFoundException;
use Pimcore\Model\DataObject\PreGetValueHookInterface;

/**
* @method static \Pimcore\Model\DataObject\PriceModel\Listing getList(array $config = [])
* @method static \Pimcore\Model\DataObject\PriceModel\Listing|\Pimcore\Model\DataObject\PriceModel|null getByDescription(mixed $value, ?int $limit = null, int $offset = 0, ?array $objectTypes = null)
* @method static \Pimcore\Model\DataObject\PriceModel\Listing|\Pimcore\Model\DataObject\PriceModel|null getByPricingNodes(mixed $value, ?int $limit = null, int $offset = 0, ?array $objectTypes = null)
* @method static \Pimcore\Model\DataObject\PriceModel\Listing|\Pimcore\Model\DataObject\PriceModel|null getByProducts(mixed $value, ?int $limit = null, int $offset = 0, ?array $objectTypes = null)
*/

class PriceModel extends Concrete
{
public const FIELD_DESCRIPTION = 'description';
public const FIELD_PRICING_NODES = 'pricingNodes';
public const FIELD_PRODUCTS = 'products';

protected $classId = "pricing";
protected $className = "PriceModel";
protected $description;
protected $pricingNodes;
protected $products;


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
* Get description - Açıklama
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
* Set description - Açıklama
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
* Get pricingNodes - Dağıtım Maliyetleri
* @return \Pimcore\Model\DataObject\Data\ObjectMetadata[]
*/
public function getPricingNodes(): array
{
	if ($this instanceof PreGetValueHookInterface && !\Pimcore::inAdmin()) {
		$preValue = $this->preGetValue("pricingNodes");
		if ($preValue !== null) {
			return $preValue;
		}
	}

	$data = $this->getClass()->getFieldDefinition("pricingNodes")->preGetData($this);

	if ($data instanceof \Pimcore\Model\DataObject\Data\EncryptedField) {
		return $data->getPlain();
	}

	return $data;
}

/**
* Set pricingNodes - Dağıtım Maliyetleri
* @param \Pimcore\Model\DataObject\Data\ObjectMetadata[] $pricingNodes
* @return $this
*/
public function setPricingNodes(?array $pricingNodes): static
{
	/** @var \Pimcore\Model\DataObject\ClassDefinition\Data\AdvancedManyToManyObjectRelation $fd */
	$fd = $this->getClass()->getFieldDefinition("pricingNodes");
	$hideUnpublished = \Pimcore\Model\DataObject\Concrete::getHideUnpublished();
	\Pimcore\Model\DataObject\Concrete::setHideUnpublished(false);
	$currentData = $this->getPricingNodes();
	\Pimcore\Model\DataObject\Concrete::setHideUnpublished($hideUnpublished);
	$isEqual = $fd->isEqual($currentData, $pricingNodes);
	if (!$isEqual) {
		$this->markFieldDirty("pricingNodes", true);
	}
	$this->pricingNodes = $fd->preSetData($this, $pricingNodes);
	return $this;
}

/**
* Get products - Ürünler
* @return \Pimcore\Model\DataObject\Data\ObjectMetadata[]
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

/**
* Set products - Ürünler
* @param \Pimcore\Model\DataObject\Data\ObjectMetadata[] $products
* @return $this
*/
public function setProducts(?array $products): static
{
	/** @var \Pimcore\Model\DataObject\ClassDefinition\Data\AdvancedManyToManyObjectRelation $fd */
	$fd = $this->getClass()->getFieldDefinition("products");
	$hideUnpublished = \Pimcore\Model\DataObject\Concrete::getHideUnpublished();
	\Pimcore\Model\DataObject\Concrete::setHideUnpublished(false);
	$currentData = $this->getProducts();
	\Pimcore\Model\DataObject\Concrete::setHideUnpublished($hideUnpublished);
	$isEqual = $fd->isEqual($currentData, $products);
	if (!$isEqual) {
		$this->markFieldDirty("products", true);
	}
	$this->products = $fd->preSetData($this, $products);
	return $this;
}

}

