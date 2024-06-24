<?php

/**
 * Inheritance: no
 * Variants: no
 *
 * Fields Summary:
 * - pricingCosts [manyToManyObjectRelation]
 * - products [advancedManyToManyObjectRelation]
 */

namespace Pimcore\Model\DataObject;

use Pimcore\Model\DataObject\Exception\InheritanceParentNotFoundException;
use Pimcore\Model\DataObject\PreGetValueHookInterface;

/**
* @method static \Pimcore\Model\DataObject\Marketplace\Listing getList(array $config = [])
* @method static \Pimcore\Model\DataObject\Marketplace\Listing|\Pimcore\Model\DataObject\Marketplace|null getByPricingCosts(mixed $value, ?int $limit = null, int $offset = 0, ?array $objectTypes = null)
* @method static \Pimcore\Model\DataObject\Marketplace\Listing|\Pimcore\Model\DataObject\Marketplace|null getByProducts(mixed $value, ?int $limit = null, int $offset = 0, ?array $objectTypes = null)
*/

class Marketplace extends Concrete
{
public const FIELD_PRICING_COSTS = 'pricingCosts';
public const FIELD_PRODUCTS = 'products';

protected $classId = "marketplace";
protected $className = "Marketplace";
protected $pricingCosts;
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
* Get pricingCosts - Pricing Costs
* @return \Pimcore\Model\DataObject\PricingNode[]
*/
public function getPricingCosts(): array
{
	if ($this instanceof PreGetValueHookInterface && !\Pimcore::inAdmin()) {
		$preValue = $this->preGetValue("pricingCosts");
		if ($preValue !== null) {
			return $preValue;
		}
	}

	$data = $this->getClass()->getFieldDefinition("pricingCosts")->preGetData($this);

	if ($data instanceof \Pimcore\Model\DataObject\Data\EncryptedField) {
		return $data->getPlain();
	}

	return $data;
}

/**
* Set pricingCosts - Pricing Costs
* @param \Pimcore\Model\DataObject\PricingNode[] $pricingCosts
* @return $this
*/
public function setPricingCosts(?array $pricingCosts): static
{
	/** @var \Pimcore\Model\DataObject\ClassDefinition\Data\ManyToManyObjectRelation $fd */
	$fd = $this->getClass()->getFieldDefinition("pricingCosts");
	$hideUnpublished = \Pimcore\Model\DataObject\Concrete::getHideUnpublished();
	\Pimcore\Model\DataObject\Concrete::setHideUnpublished(false);
	$currentData = $this->getPricingCosts();
	\Pimcore\Model\DataObject\Concrete::setHideUnpublished($hideUnpublished);
	$isEqual = $fd->isEqual($currentData, $pricingCosts);
	if (!$isEqual) {
		$this->markFieldDirty("pricingCosts", true);
	}
	$this->pricingCosts = $fd->preSetData($this, $pricingCosts);
	return $this;
}

/**
* Get products - Products
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
* Set products - Products
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

