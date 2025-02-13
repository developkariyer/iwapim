<?php

/**
 * Inheritance: no
 * Variants: no
 *
 * Fields Summary:
 * - title [input]
 * - imageGallery [imageGallery]
 * - imageUrl [externalImage]
 * - urlLink [link]
 * - stock [table]
 * - lastUpdate [datetime]
 * - salePrice [numeric]
 * - saleCurrency [input]
 * - attributes [input]
 * - last7Orders [calculatedValue]
 * - last30Orders [calculatedValue]
 * - totalOrders [calculatedValue]
 * - uniqueMarketplaceId [input]
 * - marketplace [manyToOneRelation]
 * - mainProduct [reverseObjectRelation]
 * - amazonMarketplace [fieldcollections]
 * - sellerSku [input]
 * - quantity [numeric]
 * - calculatedWisersellCode [input]
 * - wisersellVariantCode [input]
 * - wisersellVariantJson [textarea]
 * - countMainProduct [numeric]
 * - fnsku [input]
 * - marketplaceType [input]
 * - ean [input]
 */

namespace Pimcore\Model\DataObject;

use Pimcore\Model\DataObject\Exception\InheritanceParentNotFoundException;
use Pimcore\Model\DataObject\PreGetValueHookInterface;

/**
* @method static \Pimcore\Model\DataObject\VariantProduct\Listing getList(array $config = [])
* @method static \Pimcore\Model\DataObject\VariantProduct\Listing|\Pimcore\Model\DataObject\VariantProduct|null getByTitle(mixed $value, ?int $limit = null, int $offset = 0, ?array $objectTypes = null)
* @method static \Pimcore\Model\DataObject\VariantProduct\Listing|\Pimcore\Model\DataObject\VariantProduct|null getByLastUpdate(mixed $value, ?int $limit = null, int $offset = 0, ?array $objectTypes = null)
* @method static \Pimcore\Model\DataObject\VariantProduct\Listing|\Pimcore\Model\DataObject\VariantProduct|null getBySalePrice(mixed $value, ?int $limit = null, int $offset = 0, ?array $objectTypes = null)
* @method static \Pimcore\Model\DataObject\VariantProduct\Listing|\Pimcore\Model\DataObject\VariantProduct|null getBySaleCurrency(mixed $value, ?int $limit = null, int $offset = 0, ?array $objectTypes = null)
* @method static \Pimcore\Model\DataObject\VariantProduct\Listing|\Pimcore\Model\DataObject\VariantProduct|null getByAttributes(mixed $value, ?int $limit = null, int $offset = 0, ?array $objectTypes = null)
* @method static \Pimcore\Model\DataObject\VariantProduct\Listing|\Pimcore\Model\DataObject\VariantProduct|null getByUniqueMarketplaceId(mixed $value, ?int $limit = null, int $offset = 0, ?array $objectTypes = null)
* @method static \Pimcore\Model\DataObject\VariantProduct\Listing|\Pimcore\Model\DataObject\VariantProduct|null getByMarketplace(mixed $value, ?int $limit = null, int $offset = 0, ?array $objectTypes = null)
* @method static \Pimcore\Model\DataObject\VariantProduct\Listing|\Pimcore\Model\DataObject\VariantProduct|null getByMainProduct(mixed $value, ?int $limit = null, int $offset = 0, ?array $objectTypes = null)
* @method static \Pimcore\Model\DataObject\VariantProduct\Listing|\Pimcore\Model\DataObject\VariantProduct|null getBySellerSku(mixed $value, ?int $limit = null, int $offset = 0, ?array $objectTypes = null)
* @method static \Pimcore\Model\DataObject\VariantProduct\Listing|\Pimcore\Model\DataObject\VariantProduct|null getByQuantity(mixed $value, ?int $limit = null, int $offset = 0, ?array $objectTypes = null)
* @method static \Pimcore\Model\DataObject\VariantProduct\Listing|\Pimcore\Model\DataObject\VariantProduct|null getByCalculatedWisersellCode(mixed $value, ?int $limit = null, int $offset = 0, ?array $objectTypes = null)
* @method static \Pimcore\Model\DataObject\VariantProduct\Listing|\Pimcore\Model\DataObject\VariantProduct|null getByWisersellVariantCode(mixed $value, ?int $limit = null, int $offset = 0, ?array $objectTypes = null)
* @method static \Pimcore\Model\DataObject\VariantProduct\Listing|\Pimcore\Model\DataObject\VariantProduct|null getByWisersellVariantJson(mixed $value, ?int $limit = null, int $offset = 0, ?array $objectTypes = null)
* @method static \Pimcore\Model\DataObject\VariantProduct\Listing|\Pimcore\Model\DataObject\VariantProduct|null getByCountMainProduct(mixed $value, ?int $limit = null, int $offset = 0, ?array $objectTypes = null)
* @method static \Pimcore\Model\DataObject\VariantProduct\Listing|\Pimcore\Model\DataObject\VariantProduct|null getByFnsku(mixed $value, ?int $limit = null, int $offset = 0, ?array $objectTypes = null)
* @method static \Pimcore\Model\DataObject\VariantProduct\Listing|\Pimcore\Model\DataObject\VariantProduct|null getByMarketplaceType(mixed $value, ?int $limit = null, int $offset = 0, ?array $objectTypes = null)
* @method static \Pimcore\Model\DataObject\VariantProduct\Listing|\Pimcore\Model\DataObject\VariantProduct|null getByEan(mixed $value, ?int $limit = null, int $offset = 0, ?array $objectTypes = null)
*/

class VariantProduct extends \App\Model\DataObject\VariantProduct
{
public const FIELD_TITLE = 'title';
public const FIELD_IMAGE_GALLERY = 'imageGallery';
public const FIELD_IMAGE_URL = 'imageUrl';
public const FIELD_URL_LINK = 'urlLink';
public const FIELD_STOCK = 'stock';
public const FIELD_LAST_UPDATE = 'lastUpdate';
public const FIELD_SALE_PRICE = 'salePrice';
public const FIELD_SALE_CURRENCY = 'saleCurrency';
public const FIELD_ATTRIBUTES = 'attributes';
public const FIELD_LAST7_ORDERS = 'last7Orders';
public const FIELD_LAST30_ORDERS = 'last30Orders';
public const FIELD_TOTAL_ORDERS = 'totalOrders';
public const FIELD_UNIQUE_MARKETPLACE_ID = 'uniqueMarketplaceId';
public const FIELD_MARKETPLACE = 'marketplace';
public const FIELD_MAIN_PRODUCT = 'mainProduct';
public const FIELD_AMAZON_MARKETPLACE = 'amazonMarketplace';
public const FIELD_SELLER_SKU = 'sellerSku';
public const FIELD_QUANTITY = 'quantity';
public const FIELD_CALCULATED_WISERSELL_CODE = 'calculatedWisersellCode';
public const FIELD_WISERSELL_VARIANT_CODE = 'wisersellVariantCode';
public const FIELD_WISERSELL_VARIANT_JSON = 'wisersellVariantJson';
public const FIELD_COUNT_MAIN_PRODUCT = 'countMainProduct';
public const FIELD_FNSKU = 'fnsku';
public const FIELD_MARKETPLACE_TYPE = 'marketplaceType';
public const FIELD_EAN = 'ean';

protected $classId = "varyantproduct";
protected $className = "VariantProduct";
protected $title;
protected $imageGallery;
protected $imageUrl;
protected $urlLink;
protected $stock;
protected $lastUpdate;
protected $salePrice;
protected $saleCurrency;
protected $attributes;
protected $uniqueMarketplaceId;
protected $marketplace;
protected $amazonMarketplace;
protected $sellerSku;
protected $quantity;
protected $calculatedWisersellCode;
protected $wisersellVariantCode;
protected $wisersellVariantJson;
protected $countMainProduct;
protected $fnsku;
protected $marketplaceType;
protected $ean;


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
* Get title - Title
* @return string|null
*/
public function getTitle(): ?string
{
	if ($this instanceof PreGetValueHookInterface && !\Pimcore::inAdmin()) {
		$preValue = $this->preGetValue("title");
		if ($preValue !== null) {
			return $preValue;
		}
	}

	$data = $this->title;

	if ($data instanceof \Pimcore\Model\DataObject\Data\EncryptedField) {
		return $data->getPlain();
	}

	return $data;
}

/**
* Set title - Title
* @param string|null $title
* @return $this
*/
public function setTitle(?string $title): static
{
	$this->markFieldDirty("title", true);

	$this->title = $title;

	return $this;
}

/**
* Get imageGallery - Image Gallery
* @return \Pimcore\Model\DataObject\Data\ImageGallery|null
*/
public function getImageGallery(): ?\Pimcore\Model\DataObject\Data\ImageGallery
{
	if ($this instanceof PreGetValueHookInterface && !\Pimcore::inAdmin()) {
		$preValue = $this->preGetValue("imageGallery");
		if ($preValue !== null) {
			return $preValue;
		}
	}

	$data = $this->imageGallery;

	if ($data instanceof \Pimcore\Model\DataObject\Data\EncryptedField) {
		return $data->getPlain();
	}

	return $data;
}

/**
* Set imageGallery - Image Gallery
* @param \Pimcore\Model\DataObject\Data\ImageGallery|null $imageGallery
* @return $this
*/
public function setImageGallery(?\Pimcore\Model\DataObject\Data\ImageGallery $imageGallery): static
{
	$this->markFieldDirty("imageGallery", true);

	$this->imageGallery = $imageGallery;

	return $this;
}

/**
* Get imageUrl - Image Url
* @return \Pimcore\Model\DataObject\Data\ExternalImage|null
*/
public function getImageUrl(): ?\Pimcore\Model\DataObject\Data\ExternalImage
{
	if ($this instanceof PreGetValueHookInterface && !\Pimcore::inAdmin()) {
		$preValue = $this->preGetValue("imageUrl");
		if ($preValue !== null) {
			return $preValue;
		}
	}

	$data = $this->imageUrl;

	if ($data instanceof \Pimcore\Model\DataObject\Data\EncryptedField) {
		return $data->getPlain();
	}

	return $data;
}

/**
* Set imageUrl - Image Url
* @param \Pimcore\Model\DataObject\Data\ExternalImage|null $imageUrl
* @return $this
*/
public function setImageUrl(?\Pimcore\Model\DataObject\Data\ExternalImage $imageUrl): static
{
	$this->markFieldDirty("imageUrl", true);

	$this->imageUrl = $imageUrl;

	return $this;
}

/**
* Get urlLink - Url Link
* @return \Pimcore\Model\DataObject\Data\Link|null
*/
public function getUrlLink(): ?\Pimcore\Model\DataObject\Data\Link
{
	if ($this instanceof PreGetValueHookInterface && !\Pimcore::inAdmin()) {
		$preValue = $this->preGetValue("urlLink");
		if ($preValue !== null) {
			return $preValue;
		}
	}

	$data = $this->urlLink;

	if ($data instanceof \Pimcore\Model\DataObject\Data\EncryptedField) {
		return $data->getPlain();
	}

	return $data;
}

/**
* Set urlLink - Url Link
* @param \Pimcore\Model\DataObject\Data\Link|null $urlLink
* @return $this
*/
public function setUrlLink(?\Pimcore\Model\DataObject\Data\Link $urlLink): static
{
	$this->markFieldDirty("urlLink", true);

	$this->urlLink = $urlLink;

	return $this;
}

/**
* Get stock - Stok
* @return array
*/
public function getStock(): array
{
	if ($this instanceof PreGetValueHookInterface && !\Pimcore::inAdmin()) {
		$preValue = $this->preGetValue("stock");
		if ($preValue !== null) {
			return $preValue;
		}
	}

	$data = $this->stock;

	if ($data instanceof \Pimcore\Model\DataObject\Data\EncryptedField) {
		return $data->getPlain() ?? [];
	}

	return $data ?? [];
}

/**
* Set stock - Stok
* @param array|null $stock
* @return $this
*/
public function setStock(?array $stock): static
{
	$this->markFieldDirty("stock", true);

	$this->stock = $stock;

	return $this;
}

/**
* Get lastUpdate - Last Update
* @return \Carbon\Carbon|null
*/
public function getLastUpdate(): ?\Carbon\Carbon
{
	if ($this instanceof PreGetValueHookInterface && !\Pimcore::inAdmin()) {
		$preValue = $this->preGetValue("lastUpdate");
		if ($preValue !== null) {
			return $preValue;
		}
	}

	$data = $this->lastUpdate;

	if ($data instanceof \Pimcore\Model\DataObject\Data\EncryptedField) {
		return $data->getPlain();
	}

	return $data;
}

/**
* Set lastUpdate - Last Update
* @param \Carbon\Carbon|null $lastUpdate
* @return $this
*/
public function setLastUpdate(?\Carbon\Carbon $lastUpdate): static
{
	$this->markFieldDirty("lastUpdate", true);

	$this->lastUpdate = $lastUpdate;

	return $this;
}

/**
* Get salePrice - Sale Price
* @return string|null
*/
public function getSalePrice(): ?string
{
	if ($this instanceof PreGetValueHookInterface && !\Pimcore::inAdmin()) {
		$preValue = $this->preGetValue("salePrice");
		if ($preValue !== null) {
			return $preValue;
		}
	}

	$data = $this->salePrice;

	if ($data instanceof \Pimcore\Model\DataObject\Data\EncryptedField) {
		return $data->getPlain();
	}

	return $data;
}

/**
* Set salePrice - Sale Price
* @param string|null $salePrice
* @return $this
*/
public function setSalePrice(?string $salePrice): static
{
	/** @var \Pimcore\Model\DataObject\ClassDefinition\Data\Numeric $fd */
	$fd = $this->getClass()->getFieldDefinition("salePrice");
	$this->salePrice = $fd->preSetData($this, $salePrice);
	return $this;
}

/**
* Get saleCurrency - Sale Currency
* @return string|null
*/
public function getSaleCurrency(): ?string
{
	if ($this instanceof PreGetValueHookInterface && !\Pimcore::inAdmin()) {
		$preValue = $this->preGetValue("saleCurrency");
		if ($preValue !== null) {
			return $preValue;
		}
	}

	$data = $this->saleCurrency;

	if ($data instanceof \Pimcore\Model\DataObject\Data\EncryptedField) {
		return $data->getPlain();
	}

	return $data;
}

/**
* Set saleCurrency - Sale Currency
* @param string|null $saleCurrency
* @return $this
*/
public function setSaleCurrency(?string $saleCurrency): static
{
	$this->markFieldDirty("saleCurrency", true);

	$this->saleCurrency = $saleCurrency;

	return $this;
}

/**
* Get attributes - Attributes
* @return string|null
*/
public function getAttributes(): ?string
{
	if ($this instanceof PreGetValueHookInterface && !\Pimcore::inAdmin()) {
		$preValue = $this->preGetValue("attributes");
		if ($preValue !== null) {
			return $preValue;
		}
	}

	$data = $this->attributes;

	if ($data instanceof \Pimcore\Model\DataObject\Data\EncryptedField) {
		return $data->getPlain();
	}

	return $data;
}

/**
* Set attributes - Attributes
* @param string|null $attributes
* @return $this
*/
public function setAttributes(?string $attributes): static
{
	$this->markFieldDirty("attributes", true);

	$this->attributes = $attributes;

	return $this;
}

/**
* Get last7Orders - Son 7 Gün Satış
* @return mixed
*/
public function getLast7Orders(): mixed
{
	$data = new \Pimcore\Model\DataObject\Data\CalculatedValue('last7Orders');
	$data->setContextualData("object", null, null, null);
	$object = $this;
	$data = \Pimcore\Model\DataObject\Service::getCalculatedFieldValue($object, $data);

	return $data;
}

/**
* Get last30Orders - Son 30 Gün Satış
* @return mixed
*/
public function getLast30Orders(): mixed
{
	$data = new \Pimcore\Model\DataObject\Data\CalculatedValue('last30Orders');
	$data->setContextualData("object", null, null, null);
	$object = $this;
	$data = \Pimcore\Model\DataObject\Service::getCalculatedFieldValue($object, $data);

	return $data;
}

/**
* Get totalOrders - Toplam Satış
* @return mixed
*/
public function getTotalOrders(): mixed
{
	$data = new \Pimcore\Model\DataObject\Data\CalculatedValue('totalOrders');
	$data->setContextualData("object", null, null, null);
	$object = $this;
	$data = \Pimcore\Model\DataObject\Service::getCalculatedFieldValue($object, $data);

	return $data;
}

/**
* Get uniqueMarketplaceId - Unique Marketplace Id
* @return string|null
*/
public function getUniqueMarketplaceId(): ?string
{
	if ($this instanceof PreGetValueHookInterface && !\Pimcore::inAdmin()) {
		$preValue = $this->preGetValue("uniqueMarketplaceId");
		if ($preValue !== null) {
			return $preValue;
		}
	}

	$data = $this->uniqueMarketplaceId;

	if ($data instanceof \Pimcore\Model\DataObject\Data\EncryptedField) {
		return $data->getPlain();
	}

	return $data;
}

/**
* Set uniqueMarketplaceId - Unique Marketplace Id
* @param string|null $uniqueMarketplaceId
* @return $this
*/
public function setUniqueMarketplaceId(?string $uniqueMarketplaceId): static
{
	$this->markFieldDirty("uniqueMarketplaceId", true);

	$this->uniqueMarketplaceId = $uniqueMarketplaceId;

	return $this;
}

/**
* Get marketplace - Marketplace
* @return \Pimcore\Model\DataObject\Marketplace|null
*/
public function getMarketplace(): ?\Pimcore\Model\Element\AbstractElement
{
	if ($this instanceof PreGetValueHookInterface && !\Pimcore::inAdmin()) {
		$preValue = $this->preGetValue("marketplace");
		if ($preValue !== null) {
			return $preValue;
		}
	}

	$data = $this->getClass()->getFieldDefinition("marketplace")->preGetData($this);

	if ($data instanceof \Pimcore\Model\DataObject\Data\EncryptedField) {
		return $data->getPlain();
	}

	return $data;
}

/**
* Set marketplace - Marketplace
* @param \Pimcore\Model\DataObject\Marketplace|null $marketplace
* @return $this
*/
public function setMarketplace(?\Pimcore\Model\Element\AbstractElement $marketplace): static
{
	/** @var \Pimcore\Model\DataObject\ClassDefinition\Data\ManyToOneRelation $fd */
	$fd = $this->getClass()->getFieldDefinition("marketplace");
	$hideUnpublished = \Pimcore\Model\DataObject\Concrete::getHideUnpublished();
	\Pimcore\Model\DataObject\Concrete::setHideUnpublished(false);
	$currentData = $this->getMarketplace();
	\Pimcore\Model\DataObject\Concrete::setHideUnpublished($hideUnpublished);
	$isEqual = $fd->isEqual($currentData, $marketplace);
	if (!$isEqual) {
		$this->markFieldDirty("marketplace", true);
	}
	$this->marketplace = $fd->preSetData($this, $marketplace);
	return $this;
}

/**
* Get mainProduct - Main Product
* @return \Pimcore\Model\DataObject\Product[]
*/
public function getMainProduct(): array
{
	if ($this instanceof PreGetValueHookInterface && !\Pimcore::inAdmin()) {
		$preValue = $this->preGetValue("mainProduct");
		if ($preValue !== null) {
			return $preValue;
		}
	}

	$data = $this->getClass()->getFieldDefinition("mainProduct")->preGetData($this);

	if ($data instanceof \Pimcore\Model\DataObject\Data\EncryptedField) {
		return $data->getPlain();
	}

	return $data;
}

/**
* @return \Pimcore\Model\DataObject\Fieldcollection|null
*/
public function getAmazonMarketplace(): ?\Pimcore\Model\DataObject\Fieldcollection
{
	if ($this instanceof PreGetValueHookInterface && !\Pimcore::inAdmin()) {
		$preValue = $this->preGetValue("amazonMarketplace");
		if ($preValue !== null) {
			return $preValue;
		}
	}

	$data = $this->getClass()->getFieldDefinition("amazonMarketplace")->preGetData($this);
	return $data;
}

/**
* Set amazonMarketplace - Amazon Marketplace
* @param \Pimcore\Model\DataObject\Fieldcollection|null $amazonMarketplace
* @return $this
*/
public function setAmazonMarketplace(?\Pimcore\Model\DataObject\Fieldcollection $amazonMarketplace): static
{
	/** @var \Pimcore\Model\DataObject\ClassDefinition\Data\Fieldcollections $fd */
	$fd = $this->getClass()->getFieldDefinition("amazonMarketplace");
	$this->amazonMarketplace = $fd->preSetData($this, $amazonMarketplace);
	return $this;
}

/**
* Get sellerSku - Girilen SKU
* @return string|null
*/
public function getSellerSku(): ?string
{
	if ($this instanceof PreGetValueHookInterface && !\Pimcore::inAdmin()) {
		$preValue = $this->preGetValue("sellerSku");
		if ($preValue !== null) {
			return $preValue;
		}
	}

	$data = $this->sellerSku;

	if ($data instanceof \Pimcore\Model\DataObject\Data\EncryptedField) {
		return $data->getPlain();
	}

	return $data;
}

/**
* Set sellerSku - Girilen SKU
* @param string|null $sellerSku
* @return $this
*/
public function setSellerSku(?string $sellerSku): static
{
	$this->markFieldDirty("sellerSku", true);

	$this->sellerSku = $sellerSku;

	return $this;
}

/**
* Get quantity - Miktar
* @return float|null
*/
public function getQuantity(): ?float
{
	if ($this instanceof PreGetValueHookInterface && !\Pimcore::inAdmin()) {
		$preValue = $this->preGetValue("quantity");
		if ($preValue !== null) {
			return $preValue;
		}
	}

	$data = $this->quantity;

	if ($data instanceof \Pimcore\Model\DataObject\Data\EncryptedField) {
		return $data->getPlain();
	}

	return $data;
}

/**
* Set quantity - Miktar
* @param float|null $quantity
* @return $this
*/
public function setQuantity(?float $quantity): static
{
	/** @var \Pimcore\Model\DataObject\ClassDefinition\Data\Numeric $fd */
	$fd = $this->getClass()->getFieldDefinition("quantity");
	$this->quantity = $fd->preSetData($this, $quantity);
	return $this;
}

/**
* Get calculatedWisersellCode - Calculated Wisersell Code
* @return string|null
*/
public function getCalculatedWisersellCode(): ?string
{
	if ($this instanceof PreGetValueHookInterface && !\Pimcore::inAdmin()) {
		$preValue = $this->preGetValue("calculatedWisersellCode");
		if ($preValue !== null) {
			return $preValue;
		}
	}

	$data = $this->calculatedWisersellCode;

	if ($data instanceof \Pimcore\Model\DataObject\Data\EncryptedField) {
		return $data->getPlain();
	}

	return $data;
}

/**
* Set calculatedWisersellCode - Calculated Wisersell Code
* @param string|null $calculatedWisersellCode
* @return $this
*/
public function setCalculatedWisersellCode(?string $calculatedWisersellCode): static
{
	$this->markFieldDirty("calculatedWisersellCode", true);

	$this->calculatedWisersellCode = $calculatedWisersellCode;

	return $this;
}

/**
* Get wisersellVariantCode - Wisersell Variant Code
* @return string|null
*/
public function getWisersellVariantCode(): ?string
{
	if ($this instanceof PreGetValueHookInterface && !\Pimcore::inAdmin()) {
		$preValue = $this->preGetValue("wisersellVariantCode");
		if ($preValue !== null) {
			return $preValue;
		}
	}

	$data = $this->wisersellVariantCode;

	if ($data instanceof \Pimcore\Model\DataObject\Data\EncryptedField) {
		return $data->getPlain();
	}

	return $data;
}

/**
* Set wisersellVariantCode - Wisersell Variant Code
* @param string|null $wisersellVariantCode
* @return $this
*/
public function setWisersellVariantCode(?string $wisersellVariantCode): static
{
	$this->markFieldDirty("wisersellVariantCode", true);

	$this->wisersellVariantCode = $wisersellVariantCode;

	return $this;
}

/**
* Get wisersellVariantJson - Wisersell Variant Json
* @return string|null
*/
public function getWisersellVariantJson(): ?string
{
	if ($this instanceof PreGetValueHookInterface && !\Pimcore::inAdmin()) {
		$preValue = $this->preGetValue("wisersellVariantJson");
		if ($preValue !== null) {
			return $preValue;
		}
	}

	$data = $this->wisersellVariantJson;

	if ($data instanceof \Pimcore\Model\DataObject\Data\EncryptedField) {
		return $data->getPlain();
	}

	return $data;
}

/**
* Set wisersellVariantJson - Wisersell Variant Json
* @param string|null $wisersellVariantJson
* @return $this
*/
public function setWisersellVariantJson(?string $wisersellVariantJson): static
{
	$this->markFieldDirty("wisersellVariantJson", true);

	$this->wisersellVariantJson = $wisersellVariantJson;

	return $this;
}

/**
* Get countMainProduct - Count Main Product
* @return float|null
*/
public function getCountMainProduct(): ?float
{
	if ($this instanceof PreGetValueHookInterface && !\Pimcore::inAdmin()) {
		$preValue = $this->preGetValue("countMainProduct");
		if ($preValue !== null) {
			return $preValue;
		}
	}

	$data = $this->countMainProduct;

	if ($data instanceof \Pimcore\Model\DataObject\Data\EncryptedField) {
		return $data->getPlain();
	}

	return $data;
}

/**
* Set countMainProduct - Count Main Product
* @param float|null $countMainProduct
* @return $this
*/
public function setCountMainProduct(?float $countMainProduct): static
{
	/** @var \Pimcore\Model\DataObject\ClassDefinition\Data\Numeric $fd */
	$fd = $this->getClass()->getFieldDefinition("countMainProduct");
	$this->countMainProduct = $fd->preSetData($this, $countMainProduct);
	return $this;
}

/**
* Get fnsku - Fnsku
* @return string|null
*/
public function getFnsku(): ?string
{
	if ($this instanceof PreGetValueHookInterface && !\Pimcore::inAdmin()) {
		$preValue = $this->preGetValue("fnsku");
		if ($preValue !== null) {
			return $preValue;
		}
	}

	$data = $this->fnsku;

	if ($data instanceof \Pimcore\Model\DataObject\Data\EncryptedField) {
		return $data->getPlain();
	}

	return $data;
}

/**
* Set fnsku - Fnsku
* @param string|null $fnsku
* @return $this
*/
public function setFnsku(?string $fnsku): static
{
	$this->markFieldDirty("fnsku", true);

	$this->fnsku = $fnsku;

	return $this;
}

/**
* Get marketplaceType - Pazaryeri Tipi
* @return string|null
*/
public function getMarketplaceType(): ?string
{
	if ($this instanceof PreGetValueHookInterface && !\Pimcore::inAdmin()) {
		$preValue = $this->preGetValue("marketplaceType");
		if ($preValue !== null) {
			return $preValue;
		}
	}

	$data = $this->marketplaceType;

	if ($data instanceof \Pimcore\Model\DataObject\Data\EncryptedField) {
		return $data->getPlain();
	}

	return $data;
}

/**
* Set marketplaceType - Pazaryeri Tipi
* @param string|null $marketplaceType
* @return $this
*/
public function setMarketplaceType(?string $marketplaceType): static
{
	$this->markFieldDirty("marketplaceType", true);

	$this->marketplaceType = $marketplaceType;

	return $this;
}

/**
* Get ean - Ean
* @return string|null
*/
public function getEan(): ?string
{
	if ($this instanceof PreGetValueHookInterface && !\Pimcore::inAdmin()) {
		$preValue = $this->preGetValue("ean");
		if ($preValue !== null) {
			return $preValue;
		}
	}

	$data = $this->ean;

	if ($data instanceof \Pimcore\Model\DataObject\Data\EncryptedField) {
		return $data->getPlain();
	}

	return $data;
}

/**
* Set ean - Ean
* @param string|null $ean
* @return $this
*/
public function setEan(?string $ean): static
{
	$this->markFieldDirty("ean", true);

	$this->ean = $ean;

	return $this;
}

}

