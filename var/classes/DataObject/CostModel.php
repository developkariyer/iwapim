<?php

/**
 * Inheritance: no
 * Variants: no
 *
 * Fields Summary:
 * - description [textarea]
 * - costNodes [advancedManyToManyObjectRelation]
 * - products [reverseObjectRelation]
 * - variants [reverseObjectRelation]
 */

namespace Pimcore\Model\DataObject;

use Pimcore\Model\DataObject\Exception\InheritanceParentNotFoundException;
use Pimcore\Model\DataObject\PreGetValueHookInterface;

/**
* @method static \Pimcore\Model\DataObject\CostModel\Listing getList(array $config = [])
* @method static \Pimcore\Model\DataObject\CostModel\Listing|\Pimcore\Model\DataObject\CostModel|null getByDescription(mixed $value, ?int $limit = null, int $offset = 0, ?array $objectTypes = null)
* @method static \Pimcore\Model\DataObject\CostModel\Listing|\Pimcore\Model\DataObject\CostModel|null getByCostNodes(mixed $value, ?int $limit = null, int $offset = 0, ?array $objectTypes = null)
* @method static \Pimcore\Model\DataObject\CostModel\Listing|\Pimcore\Model\DataObject\CostModel|null getByProducts(mixed $value, ?int $limit = null, int $offset = 0, ?array $objectTypes = null)
* @method static \Pimcore\Model\DataObject\CostModel\Listing|\Pimcore\Model\DataObject\CostModel|null getByVariants(mixed $value, ?int $limit = null, int $offset = 0, ?array $objectTypes = null)
*/

class CostModel extends \App\Model\DataObject\CostModel
{
public const FIELD_DESCRIPTION = 'description';
public const FIELD_COST_NODES = 'costNodes';
public const FIELD_PRODUCTS = 'products';
public const FIELD_VARIANTS = 'variants';

protected $classId = "modelcost";
protected $className = "CostModel";
protected $description;
protected $costNodes;


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
* Get costNodes - Cost Nodes
* @return \Pimcore\Model\DataObject\Data\ObjectMetadata[]
*/
public function getCostNodes(): array
{
	if ($this instanceof PreGetValueHookInterface && !\Pimcore::inAdmin()) {
		$preValue = $this->preGetValue("costNodes");
		if ($preValue !== null) {
			return $preValue;
		}
	}

	$data = $this->getClass()->getFieldDefinition("costNodes")->preGetData($this);

	if ($data instanceof \Pimcore\Model\DataObject\Data\EncryptedField) {
		return $data->getPlain();
	}

	return $data;
}

/**
* Set costNodes - Cost Nodes
* @param \Pimcore\Model\DataObject\Data\ObjectMetadata[] $costNodes
* @return $this
*/
public function setCostNodes(?array $costNodes): static
{
	/** @var \Pimcore\Model\DataObject\ClassDefinition\Data\AdvancedManyToManyObjectRelation $fd */
	$fd = $this->getClass()->getFieldDefinition("costNodes");
	$hideUnpublished = \Pimcore\Model\DataObject\Concrete::getHideUnpublished();
	\Pimcore\Model\DataObject\Concrete::setHideUnpublished(false);
	$currentData = $this->getCostNodes();
	\Pimcore\Model\DataObject\Concrete::setHideUnpublished($hideUnpublished);
	$isEqual = $fd->isEqual($currentData, $costNodes);
	if (!$isEqual) {
		$this->markFieldDirty("costNodes", true);
	}
	$this->costNodes = $fd->preSetData($this, $costNodes);
	return $this;
}

/**
* Get products - Products
* @return \Pimcore\Model\DataObject\Product[]
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
* Get variants - Variants
* @return \Pimcore\Model\DataObject\Product[]
*/
public function getVariants(): array
{
	if ($this instanceof PreGetValueHookInterface && !\Pimcore::inAdmin()) {
		$preValue = $this->preGetValue("variants");
		if ($preValue !== null) {
			return $preValue;
		}
	}

	$data = $this->getClass()->getFieldDefinition("variants")->preGetData($this);

	if ($data instanceof \Pimcore\Model\DataObject\Data\EncryptedField) {
		return $data->getPlain();
	}

	return $data;
}

}

