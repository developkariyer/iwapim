<?php

/**
 * Inheritance: no
 * Variants: no
 *
 * Fields Summary:
 * - rate [numeric]
 * - currencyCode [input]
 * - date [date]
 */

namespace Pimcore\Model\DataObject;

use Pimcore\Model\DataObject\Exception\InheritanceParentNotFoundException;
use Pimcore\Model\DataObject\PreGetValueHookInterface;

/**
* @method static \Pimcore\Model\DataObject\Currency\Listing getList(array $config = [])
* @method static \Pimcore\Model\DataObject\Currency\Listing|\Pimcore\Model\DataObject\Currency|null getByRate(mixed $value, ?int $limit = null, int $offset = 0, ?array $objectTypes = null)
* @method static \Pimcore\Model\DataObject\Currency\Listing|\Pimcore\Model\DataObject\Currency|null getByCurrencyCode(mixed $value, ?int $limit = null, int $offset = 0, ?array $objectTypes = null)
* @method static \Pimcore\Model\DataObject\Currency\Listing|\Pimcore\Model\DataObject\Currency|null getByDate(mixed $value, ?int $limit = null, int $offset = 0, ?array $objectTypes = null)
*/

class Currency extends \App\Model\DataObject\Currency
{
public const FIELD_RATE = 'rate';
public const FIELD_CURRENCY_CODE = 'currencyCode';
public const FIELD_DATE = 'date';

protected $classId = "currency";
protected $className = "Currency";
protected $rate;
protected $currencyCode;
protected $date;


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
* Get currencyCode - Para Birimi
* @return string|null
*/
public function getCurrencyCode(): ?string
{
	if ($this instanceof PreGetValueHookInterface && !\Pimcore::inAdmin()) {
		$preValue = $this->preGetValue("currencyCode");
		if ($preValue !== null) {
			return $preValue;
		}
	}

	$data = $this->currencyCode;

	if ($data instanceof \Pimcore\Model\DataObject\Data\EncryptedField) {
		return $data->getPlain();
	}

	return $data;
}

/**
* Set currencyCode - Para Birimi
* @param string|null $currencyCode
* @return $this
*/
public function setCurrencyCode(?string $currencyCode): static
{
	$this->markFieldDirty("currencyCode", true);

	$this->currencyCode = $currencyCode;

	return $this;
}

/**
* Get date - Date
* @return \Carbon\Carbon|null
*/
public function getDate(): ?\Carbon\Carbon
{
	if ($this instanceof PreGetValueHookInterface && !\Pimcore::inAdmin()) {
		$preValue = $this->preGetValue("date");
		if ($preValue !== null) {
			return $preValue;
		}
	}

	$data = $this->date;

	if ($data instanceof \Pimcore\Model\DataObject\Data\EncryptedField) {
		return $data->getPlain();
	}

	return $data;
}

/**
* Set date - Date
* @param \Carbon\Carbon|null $date
* @return $this
*/
public function setDate(?\Carbon\Carbon $date): static
{
	$this->markFieldDirty("date", true);

	$this->date = $date;

	return $this;
}

}

