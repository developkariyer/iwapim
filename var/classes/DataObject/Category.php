<?php

/**
 * Inheritance: no
 * Variants: no
 *
 * Fields Summary:
 * - category [input]
 * - description [textarea]
 * - wisersellCategoryId [input]
 * - technicals [manyToManyRelation]
 */

namespace Pimcore\Model\DataObject;

use Pimcore\Model\DataObject\Exception\InheritanceParentNotFoundException;
use Pimcore\Model\DataObject\PreGetValueHookInterface;

/**
* @method static \Pimcore\Model\DataObject\Category\Listing getList(array $config = [])
* @method static \Pimcore\Model\DataObject\Category\Listing|\Pimcore\Model\DataObject\Category|null getByCategory(mixed $value, ?int $limit = null, int $offset = 0, ?array $objectTypes = null)
* @method static \Pimcore\Model\DataObject\Category\Listing|\Pimcore\Model\DataObject\Category|null getByDescription(mixed $value, ?int $limit = null, int $offset = 0, ?array $objectTypes = null)
* @method static \Pimcore\Model\DataObject\Category\Listing|\Pimcore\Model\DataObject\Category|null getByWisersellCategoryId(mixed $value, ?int $limit = null, int $offset = 0, ?array $objectTypes = null)
* @method static \Pimcore\Model\DataObject\Category\Listing|\Pimcore\Model\DataObject\Category|null getByTechnicals(mixed $value, ?int $limit = null, int $offset = 0, ?array $objectTypes = null)
*/

class Category extends Concrete
{
public const FIELD_CATEGORY = 'category';
public const FIELD_DESCRIPTION = 'description';
public const FIELD_WISERSELL_CATEGORY_ID = 'wisersellCategoryId';
public const FIELD_TECHNICALS = 'technicals';

protected $classId = "category";
protected $className = "Category";
protected $category;
protected $description;
protected $wisersellCategoryId;
protected $technicals;


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
* Get category - Category
* @return string|null
*/
public function getCategory(): ?string
{
	if ($this instanceof PreGetValueHookInterface && !\Pimcore::inAdmin()) {
		$preValue = $this->preGetValue("category");
		if ($preValue !== null) {
			return $preValue;
		}
	}

	$data = $this->category;

	if ($data instanceof \Pimcore\Model\DataObject\Data\EncryptedField) {
		return $data->getPlain();
	}

	return $data;
}

/**
* Set category - Category
* @param string|null $category
* @return $this
*/
public function setCategory(?string $category): static
{
	$this->markFieldDirty("category", true);

	$this->category = $category;

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
* Get wisersellCategoryId - Wisersell Category Id
* @return string|null
*/
public function getWisersellCategoryId(): ?string
{
	if ($this instanceof PreGetValueHookInterface && !\Pimcore::inAdmin()) {
		$preValue = $this->preGetValue("wisersellCategoryId");
		if ($preValue !== null) {
			return $preValue;
		}
	}

	$data = $this->wisersellCategoryId;

	if ($data instanceof \Pimcore\Model\DataObject\Data\EncryptedField) {
		return $data->getPlain();
	}

	return $data;
}

/**
* Set wisersellCategoryId - Wisersell Category Id
* @param string|null $wisersellCategoryId
* @return $this
*/
public function setWisersellCategoryId(?string $wisersellCategoryId): static
{
	$this->markFieldDirty("wisersellCategoryId", true);

	$this->wisersellCategoryId = $wisersellCategoryId;

	return $this;
}

/**
* Get technicals - Teknik Doküman ve Kılavuzlar
* @return \Pimcore\Model\Asset[]
*/
public function getTechnicals(): array
{
	if ($this instanceof PreGetValueHookInterface && !\Pimcore::inAdmin()) {
		$preValue = $this->preGetValue("technicals");
		if ($preValue !== null) {
			return $preValue;
		}
	}

	$data = $this->getClass()->getFieldDefinition("technicals")->preGetData($this);

	if ($data instanceof \Pimcore\Model\DataObject\Data\EncryptedField) {
		return $data->getPlain();
	}

	return $data;
}

/**
* Set technicals - Teknik Doküman ve Kılavuzlar
* @param \Pimcore\Model\Asset[] $technicals
* @return $this
*/
public function setTechnicals(?array $technicals): static
{
	/** @var \Pimcore\Model\DataObject\ClassDefinition\Data\ManyToManyRelation $fd */
	$fd = $this->getClass()->getFieldDefinition("technicals");
	$hideUnpublished = \Pimcore\Model\DataObject\Concrete::getHideUnpublished();
	\Pimcore\Model\DataObject\Concrete::setHideUnpublished(false);
	$currentData = $this->getTechnicals();
	\Pimcore\Model\DataObject\Concrete::setHideUnpublished($hideUnpublished);
	$isEqual = $fd->isEqual($currentData, $technicals);
	if (!$isEqual) {
		$this->markFieldDirty("technicals", true);
	}
	$this->technicals = $fd->preSetData($this, $technicals);
	return $this;
}

}

