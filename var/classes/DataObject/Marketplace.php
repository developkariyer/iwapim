<?php

/**
 * Inheritance: no
 * Variants: no
 *
 * Fields Summary:
 * - pricingCosts [manyToManyObjectRelation]
 * - products [advancedManyToManyObjectRelation]
 * - apiKey [input]
 * - apiSecretKey [input]
 * - accessToken [input]
 * - oauthToken [input]
 * - apiUrl [input]
 */

namespace Pimcore\Model\DataObject;

use Pimcore\Model\DataObject\Exception\InheritanceParentNotFoundException;
use Pimcore\Model\DataObject\PreGetValueHookInterface;

/**
* @method static \Pimcore\Model\DataObject\Marketplace\Listing getList(array $config = [])
* @method static \Pimcore\Model\DataObject\Marketplace\Listing|\Pimcore\Model\DataObject\Marketplace|null getByPricingCosts(mixed $value, ?int $limit = null, int $offset = 0, ?array $objectTypes = null)
* @method static \Pimcore\Model\DataObject\Marketplace\Listing|\Pimcore\Model\DataObject\Marketplace|null getByProducts(mixed $value, ?int $limit = null, int $offset = 0, ?array $objectTypes = null)
* @method static \Pimcore\Model\DataObject\Marketplace\Listing|\Pimcore\Model\DataObject\Marketplace|null getByApiKey(mixed $value, ?int $limit = null, int $offset = 0, ?array $objectTypes = null)
* @method static \Pimcore\Model\DataObject\Marketplace\Listing|\Pimcore\Model\DataObject\Marketplace|null getByApiSecretKey(mixed $value, ?int $limit = null, int $offset = 0, ?array $objectTypes = null)
* @method static \Pimcore\Model\DataObject\Marketplace\Listing|\Pimcore\Model\DataObject\Marketplace|null getByAccessToken(mixed $value, ?int $limit = null, int $offset = 0, ?array $objectTypes = null)
* @method static \Pimcore\Model\DataObject\Marketplace\Listing|\Pimcore\Model\DataObject\Marketplace|null getByOauthToken(mixed $value, ?int $limit = null, int $offset = 0, ?array $objectTypes = null)
* @method static \Pimcore\Model\DataObject\Marketplace\Listing|\Pimcore\Model\DataObject\Marketplace|null getByApiUrl(mixed $value, ?int $limit = null, int $offset = 0, ?array $objectTypes = null)
*/

class Marketplace extends Concrete
{
public const FIELD_PRICING_COSTS = 'pricingCosts';
public const FIELD_PRODUCTS = 'products';
public const FIELD_API_KEY = 'apiKey';
public const FIELD_API_SECRET_KEY = 'apiSecretKey';
public const FIELD_ACCESS_TOKEN = 'accessToken';
public const FIELD_OAUTH_TOKEN = 'oauthToken';
public const FIELD_API_URL = 'apiUrl';

protected $classId = "marketplace";
protected $className = "Marketplace";
protected $pricingCosts;
protected $products;
protected $apiKey;
protected $apiSecretKey;
protected $accessToken;
protected $oauthToken;
protected $apiUrl;


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

/**
* Get apiKey - Api Key
* @return string|null
*/
public function getApiKey(): ?string
{
	if ($this instanceof PreGetValueHookInterface && !\Pimcore::inAdmin()) {
		$preValue = $this->preGetValue("apiKey");
		if ($preValue !== null) {
			return $preValue;
		}
	}

	$data = $this->apiKey;

	if ($data instanceof \Pimcore\Model\DataObject\Data\EncryptedField) {
		return $data->getPlain();
	}

	return $data;
}

/**
* Set apiKey - Api Key
* @param string|null $apiKey
* @return $this
*/
public function setApiKey(?string $apiKey): static
{
	$this->markFieldDirty("apiKey", true);

	$this->apiKey = $apiKey;

	return $this;
}

/**
* Get apiSecretKey - API Secret Key
* @return string|null
*/
public function getApiSecretKey(): ?string
{
	if ($this instanceof PreGetValueHookInterface && !\Pimcore::inAdmin()) {
		$preValue = $this->preGetValue("apiSecretKey");
		if ($preValue !== null) {
			return $preValue;
		}
	}

	$data = $this->apiSecretKey;

	if ($data instanceof \Pimcore\Model\DataObject\Data\EncryptedField) {
		return $data->getPlain();
	}

	return $data;
}

/**
* Set apiSecretKey - API Secret Key
* @param string|null $apiSecretKey
* @return $this
*/
public function setApiSecretKey(?string $apiSecretKey): static
{
	$this->markFieldDirty("apiSecretKey", true);

	$this->apiSecretKey = $apiSecretKey;

	return $this;
}

/**
* Get accessToken - Access Token
* @return string|null
*/
public function getAccessToken(): ?string
{
	if ($this instanceof PreGetValueHookInterface && !\Pimcore::inAdmin()) {
		$preValue = $this->preGetValue("accessToken");
		if ($preValue !== null) {
			return $preValue;
		}
	}

	$data = $this->accessToken;

	if ($data instanceof \Pimcore\Model\DataObject\Data\EncryptedField) {
		return $data->getPlain();
	}

	return $data;
}

/**
* Set accessToken - Access Token
* @param string|null $accessToken
* @return $this
*/
public function setAccessToken(?string $accessToken): static
{
	$this->markFieldDirty("accessToken", true);

	$this->accessToken = $accessToken;

	return $this;
}

/**
* Get oauthToken - Oauth Token
* @return string|null
*/
public function getOauthToken(): ?string
{
	if ($this instanceof PreGetValueHookInterface && !\Pimcore::inAdmin()) {
		$preValue = $this->preGetValue("oauthToken");
		if ($preValue !== null) {
			return $preValue;
		}
	}

	$data = $this->oauthToken;

	if ($data instanceof \Pimcore\Model\DataObject\Data\EncryptedField) {
		return $data->getPlain();
	}

	return $data;
}

/**
* Set oauthToken - Oauth Token
* @param string|null $oauthToken
* @return $this
*/
public function setOauthToken(?string $oauthToken): static
{
	$this->markFieldDirty("oauthToken", true);

	$this->oauthToken = $oauthToken;

	return $this;
}

/**
* Get apiUrl - API URL
* @return string|null
*/
public function getApiUrl(): ?string
{
	if ($this instanceof PreGetValueHookInterface && !\Pimcore::inAdmin()) {
		$preValue = $this->preGetValue("apiUrl");
		if ($preValue !== null) {
			return $preValue;
		}
	}

	$data = $this->apiUrl;

	if ($data instanceof \Pimcore\Model\DataObject\Data\EncryptedField) {
		return $data->getPlain();
	}

	return $data;
}

/**
* Set apiUrl - API URL
* @param string|null $apiUrl
* @return $this
*/
public function setApiUrl(?string $apiUrl): static
{
	$this->markFieldDirty("apiUrl", true);

	$this->apiUrl = $apiUrl;

	return $this;
}

}

