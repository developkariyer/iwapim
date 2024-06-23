<?php

/**
 * Inheritance: no
 * Variants: no
 *
 * Fields Summary:
 * - rate [numeric]
 * - symbol [input]
 */

namespace Pimcore\Model\DataObject;

use Pimcore\Model\DataObject\Exception\InheritanceParentNotFoundException;
use Pimcore\Model\DataObject\PreGetValueHookInterface;

/**
* @method static \Pimcore\Model\DataObject\Currency\Listing getList(array $config = [])
* @method static \Pimcore\Model\DataObject\Currency\Listing|\Pimcore\Model\DataObject\Currency|null getByRate(mixed $value, ?int $limit = null, int $offset = 0, ?array $objectTypes = null)
* @method static \Pimcore\Model\DataObject\Currency\Listing|\Pimcore\Model\DataObject\Currency|null getBySymbol(mixed $value, ?int $limit = null, int $offset = 0, ?array $objectTypes = null)
*/

class Currency extends Concrete
{
public const FIELD_RATE = 'rate';
public const FIELD_SYMBOL = 'symbol';

protected $classId = "currency";
protected $className = "Currency";
protected $rate;
protected $symbol;


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
* Get rate - TL karşılığı
* @return string|null
*/
public function getRate(): ?string
{
	if ($this instanceof PreGetValueHookInterface && !\Pimcore::inAdmin()) {
		$preValue = $this->preGetValue("rate");
		if ($preValue !== null) {
			return $preValue;
		}
	}

	$data = $this->rate;

	if ($data instanceof \Pimcore\Model\DataObject\Data\EncryptedField) {
		return $data->getPlain();
	}

	return $data;
}

/**
* Set rate - TL karşılığı
* @param string|null $rate
* @return $this
*/
public function setRate(?string $rate): static
{
	/** @var \Pimcore\Model\DataObject\ClassDefinition\Data\Numeric $fd */
	$fd = $this->getClass()->getFieldDefinition("rate");
	$this->rate = $fd->preSetData($this, $rate);
	return $this;
}

/**
* Get symbol - Sembol
* @return string|null
*/
public function getSymbol(): ?string
{
	if ($this instanceof PreGetValueHookInterface && !\Pimcore::inAdmin()) {
		$preValue = $this->preGetValue("symbol");
		if ($preValue !== null) {
			return $preValue;
		}
	}

	$data = $this->symbol;

	if ($data instanceof \Pimcore\Model\DataObject\Data\EncryptedField) {
		return $data->getPlain();
	}

	return $data;
}

/**
* Set symbol - Sembol
* @param string|null $symbol
* @return $this
*/
public function setSymbol(?string $symbol): static
{
	$this->markFieldDirty("symbol", true);

	$this->symbol = $symbol;

	return $this;
}

}

