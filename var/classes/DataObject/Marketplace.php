<?php

/**
 * Inheritance: no
 * Variants: no
 *
 * Fields Summary:
 * - marketplaceType [select]
 * - marketplaceUrl [input]
 * - wisersellStoreId [input]
 * - merchantId [input]
 * - clientId [input]
 * - clientSecret [input]
 * - refreshToken [input]
 * - mainMerchant [select]
 * - merchantIds [multiselect]
 * - fbaRegions [multiselect]
 * - currency [select]
 * - apiKey [input]
 * - apiSecretKey [input]
 * - accessToken [input]
 * - oauthToken [input]
 * - apiUrl [input]
 * - shopId [input]
 * - keystring [input]
 * - sharedSecret [input]
 * - trendyolApiKey [input]
 * - trendyolSellerId [input]
 * - trendyolApiSecret [input]
 * - trendyolToken [input]
 * - bolClientId [input]
 * - bolSecret [input]
 * - bolJwtToken [textarea]
 * - sellerId [input]
 * - serviceKey [input]
 * - takealotKey [input]
 * - wallmartSecretKey [input]
 * - wallmartClientId [input]
 * - wallmartAccessToken [input]
 * - ciceksepetiApiKey [input]
 * - ciceksepetiSellerId [input]
 * - ozonClientId [input]
 * - ozonApiKey [input]
 * - wayfairClientIdProd [input]
 * - wayfairSecretKeyProd [input]
 * - wayfairAccessTokenProd [input]
 * - ebayClientId [input]
 * - ebayClientSecret [input]
 * - ebayRefreshToken [input]
 * - ebayAuthCode [input]
 * - ebayRuName [input]
 * - ebayAccessToken [textarea]
 */

namespace Pimcore\Model\DataObject;

use Pimcore\Model\DataObject\Exception\InheritanceParentNotFoundException;
use Pimcore\Model\DataObject\PreGetValueHookInterface;

/**
* @method static \Pimcore\Model\DataObject\Marketplace\Listing getList(array $config = [])
* @method static \Pimcore\Model\DataObject\Marketplace\Listing|\Pimcore\Model\DataObject\Marketplace|null getByMarketplaceType(mixed $value, ?int $limit = null, int $offset = 0, ?array $objectTypes = null)
* @method static \Pimcore\Model\DataObject\Marketplace\Listing|\Pimcore\Model\DataObject\Marketplace|null getByMarketplaceUrl(mixed $value, ?int $limit = null, int $offset = 0, ?array $objectTypes = null)
* @method static \Pimcore\Model\DataObject\Marketplace\Listing|\Pimcore\Model\DataObject\Marketplace|null getByWisersellStoreId(mixed $value, ?int $limit = null, int $offset = 0, ?array $objectTypes = null)
* @method static \Pimcore\Model\DataObject\Marketplace\Listing|\Pimcore\Model\DataObject\Marketplace|null getByMerchantId(mixed $value, ?int $limit = null, int $offset = 0, ?array $objectTypes = null)
* @method static \Pimcore\Model\DataObject\Marketplace\Listing|\Pimcore\Model\DataObject\Marketplace|null getByClientId(mixed $value, ?int $limit = null, int $offset = 0, ?array $objectTypes = null)
* @method static \Pimcore\Model\DataObject\Marketplace\Listing|\Pimcore\Model\DataObject\Marketplace|null getByClientSecret(mixed $value, ?int $limit = null, int $offset = 0, ?array $objectTypes = null)
* @method static \Pimcore\Model\DataObject\Marketplace\Listing|\Pimcore\Model\DataObject\Marketplace|null getByRefreshToken(mixed $value, ?int $limit = null, int $offset = 0, ?array $objectTypes = null)
* @method static \Pimcore\Model\DataObject\Marketplace\Listing|\Pimcore\Model\DataObject\Marketplace|null getByMainMerchant(mixed $value, ?int $limit = null, int $offset = 0, ?array $objectTypes = null)
* @method static \Pimcore\Model\DataObject\Marketplace\Listing|\Pimcore\Model\DataObject\Marketplace|null getByMerchantIds(mixed $value, ?int $limit = null, int $offset = 0, ?array $objectTypes = null)
* @method static \Pimcore\Model\DataObject\Marketplace\Listing|\Pimcore\Model\DataObject\Marketplace|null getByFbaRegions(mixed $value, ?int $limit = null, int $offset = 0, ?array $objectTypes = null)
* @method static \Pimcore\Model\DataObject\Marketplace\Listing|\Pimcore\Model\DataObject\Marketplace|null getByCurrency(mixed $value, ?int $limit = null, int $offset = 0, ?array $objectTypes = null)
* @method static \Pimcore\Model\DataObject\Marketplace\Listing|\Pimcore\Model\DataObject\Marketplace|null getByApiKey(mixed $value, ?int $limit = null, int $offset = 0, ?array $objectTypes = null)
* @method static \Pimcore\Model\DataObject\Marketplace\Listing|\Pimcore\Model\DataObject\Marketplace|null getByApiSecretKey(mixed $value, ?int $limit = null, int $offset = 0, ?array $objectTypes = null)
* @method static \Pimcore\Model\DataObject\Marketplace\Listing|\Pimcore\Model\DataObject\Marketplace|null getByAccessToken(mixed $value, ?int $limit = null, int $offset = 0, ?array $objectTypes = null)
* @method static \Pimcore\Model\DataObject\Marketplace\Listing|\Pimcore\Model\DataObject\Marketplace|null getByOauthToken(mixed $value, ?int $limit = null, int $offset = 0, ?array $objectTypes = null)
* @method static \Pimcore\Model\DataObject\Marketplace\Listing|\Pimcore\Model\DataObject\Marketplace|null getByApiUrl(mixed $value, ?int $limit = null, int $offset = 0, ?array $objectTypes = null)
* @method static \Pimcore\Model\DataObject\Marketplace\Listing|\Pimcore\Model\DataObject\Marketplace|null getByShopId(mixed $value, ?int $limit = null, int $offset = 0, ?array $objectTypes = null)
* @method static \Pimcore\Model\DataObject\Marketplace\Listing|\Pimcore\Model\DataObject\Marketplace|null getByKeystring(mixed $value, ?int $limit = null, int $offset = 0, ?array $objectTypes = null)
* @method static \Pimcore\Model\DataObject\Marketplace\Listing|\Pimcore\Model\DataObject\Marketplace|null getBySharedSecret(mixed $value, ?int $limit = null, int $offset = 0, ?array $objectTypes = null)
* @method static \Pimcore\Model\DataObject\Marketplace\Listing|\Pimcore\Model\DataObject\Marketplace|null getByTrendyolApiKey(mixed $value, ?int $limit = null, int $offset = 0, ?array $objectTypes = null)
* @method static \Pimcore\Model\DataObject\Marketplace\Listing|\Pimcore\Model\DataObject\Marketplace|null getByTrendyolSellerId(mixed $value, ?int $limit = null, int $offset = 0, ?array $objectTypes = null)
* @method static \Pimcore\Model\DataObject\Marketplace\Listing|\Pimcore\Model\DataObject\Marketplace|null getByTrendyolApiSecret(mixed $value, ?int $limit = null, int $offset = 0, ?array $objectTypes = null)
* @method static \Pimcore\Model\DataObject\Marketplace\Listing|\Pimcore\Model\DataObject\Marketplace|null getByTrendyolToken(mixed $value, ?int $limit = null, int $offset = 0, ?array $objectTypes = null)
* @method static \Pimcore\Model\DataObject\Marketplace\Listing|\Pimcore\Model\DataObject\Marketplace|null getByBolClientId(mixed $value, ?int $limit = null, int $offset = 0, ?array $objectTypes = null)
* @method static \Pimcore\Model\DataObject\Marketplace\Listing|\Pimcore\Model\DataObject\Marketplace|null getByBolSecret(mixed $value, ?int $limit = null, int $offset = 0, ?array $objectTypes = null)
* @method static \Pimcore\Model\DataObject\Marketplace\Listing|\Pimcore\Model\DataObject\Marketplace|null getByBolJwtToken(mixed $value, ?int $limit = null, int $offset = 0, ?array $objectTypes = null)
* @method static \Pimcore\Model\DataObject\Marketplace\Listing|\Pimcore\Model\DataObject\Marketplace|null getBySellerId(mixed $value, ?int $limit = null, int $offset = 0, ?array $objectTypes = null)
* @method static \Pimcore\Model\DataObject\Marketplace\Listing|\Pimcore\Model\DataObject\Marketplace|null getByServiceKey(mixed $value, ?int $limit = null, int $offset = 0, ?array $objectTypes = null)
* @method static \Pimcore\Model\DataObject\Marketplace\Listing|\Pimcore\Model\DataObject\Marketplace|null getByTakealotKey(mixed $value, ?int $limit = null, int $offset = 0, ?array $objectTypes = null)
* @method static \Pimcore\Model\DataObject\Marketplace\Listing|\Pimcore\Model\DataObject\Marketplace|null getByWallmartSecretKey(mixed $value, ?int $limit = null, int $offset = 0, ?array $objectTypes = null)
* @method static \Pimcore\Model\DataObject\Marketplace\Listing|\Pimcore\Model\DataObject\Marketplace|null getByWallmartClientId(mixed $value, ?int $limit = null, int $offset = 0, ?array $objectTypes = null)
* @method static \Pimcore\Model\DataObject\Marketplace\Listing|\Pimcore\Model\DataObject\Marketplace|null getByWallmartAccessToken(mixed $value, ?int $limit = null, int $offset = 0, ?array $objectTypes = null)
* @method static \Pimcore\Model\DataObject\Marketplace\Listing|\Pimcore\Model\DataObject\Marketplace|null getByCiceksepetiApiKey(mixed $value, ?int $limit = null, int $offset = 0, ?array $objectTypes = null)
* @method static \Pimcore\Model\DataObject\Marketplace\Listing|\Pimcore\Model\DataObject\Marketplace|null getByCiceksepetiSellerId(mixed $value, ?int $limit = null, int $offset = 0, ?array $objectTypes = null)
* @method static \Pimcore\Model\DataObject\Marketplace\Listing|\Pimcore\Model\DataObject\Marketplace|null getByOzonClientId(mixed $value, ?int $limit = null, int $offset = 0, ?array $objectTypes = null)
* @method static \Pimcore\Model\DataObject\Marketplace\Listing|\Pimcore\Model\DataObject\Marketplace|null getByOzonApiKey(mixed $value, ?int $limit = null, int $offset = 0, ?array $objectTypes = null)
* @method static \Pimcore\Model\DataObject\Marketplace\Listing|\Pimcore\Model\DataObject\Marketplace|null getByWayfairClientIdProd(mixed $value, ?int $limit = null, int $offset = 0, ?array $objectTypes = null)
* @method static \Pimcore\Model\DataObject\Marketplace\Listing|\Pimcore\Model\DataObject\Marketplace|null getByWayfairSecretKeyProd(mixed $value, ?int $limit = null, int $offset = 0, ?array $objectTypes = null)
* @method static \Pimcore\Model\DataObject\Marketplace\Listing|\Pimcore\Model\DataObject\Marketplace|null getByWayfairAccessTokenProd(mixed $value, ?int $limit = null, int $offset = 0, ?array $objectTypes = null)
* @method static \Pimcore\Model\DataObject\Marketplace\Listing|\Pimcore\Model\DataObject\Marketplace|null getByEbayClientId(mixed $value, ?int $limit = null, int $offset = 0, ?array $objectTypes = null)
* @method static \Pimcore\Model\DataObject\Marketplace\Listing|\Pimcore\Model\DataObject\Marketplace|null getByEbayClientSecret(mixed $value, ?int $limit = null, int $offset = 0, ?array $objectTypes = null)
* @method static \Pimcore\Model\DataObject\Marketplace\Listing|\Pimcore\Model\DataObject\Marketplace|null getByEbayRefreshToken(mixed $value, ?int $limit = null, int $offset = 0, ?array $objectTypes = null)
* @method static \Pimcore\Model\DataObject\Marketplace\Listing|\Pimcore\Model\DataObject\Marketplace|null getByEbayAuthCode(mixed $value, ?int $limit = null, int $offset = 0, ?array $objectTypes = null)
* @method static \Pimcore\Model\DataObject\Marketplace\Listing|\Pimcore\Model\DataObject\Marketplace|null getByEbayRuName(mixed $value, ?int $limit = null, int $offset = 0, ?array $objectTypes = null)
* @method static \Pimcore\Model\DataObject\Marketplace\Listing|\Pimcore\Model\DataObject\Marketplace|null getByEbayAccessToken(mixed $value, ?int $limit = null, int $offset = 0, ?array $objectTypes = null)
*/

class Marketplace extends \App\Model\DataObject\Marketplace
{
public const FIELD_MARKETPLACE_TYPE = 'marketplaceType';
public const FIELD_MARKETPLACE_URL = 'marketplaceUrl';
public const FIELD_WISERSELL_STORE_ID = 'wisersellStoreId';
public const FIELD_MERCHANT_ID = 'merchantId';
public const FIELD_CLIENT_ID = 'clientId';
public const FIELD_CLIENT_SECRET = 'clientSecret';
public const FIELD_REFRESH_TOKEN = 'refreshToken';
public const FIELD_MAIN_MERCHANT = 'mainMerchant';
public const FIELD_MERCHANT_IDS = 'merchantIds';
public const FIELD_FBA_REGIONS = 'fbaRegions';
public const FIELD_CURRENCY = 'currency';
public const FIELD_API_KEY = 'apiKey';
public const FIELD_API_SECRET_KEY = 'apiSecretKey';
public const FIELD_ACCESS_TOKEN = 'accessToken';
public const FIELD_OAUTH_TOKEN = 'oauthToken';
public const FIELD_API_URL = 'apiUrl';
public const FIELD_SHOP_ID = 'shopId';
public const FIELD_KEYSTRING = 'keystring';
public const FIELD_SHARED_SECRET = 'sharedSecret';
public const FIELD_TRENDYOL_API_KEY = 'trendyolApiKey';
public const FIELD_TRENDYOL_SELLER_ID = 'trendyolSellerId';
public const FIELD_TRENDYOL_API_SECRET = 'trendyolApiSecret';
public const FIELD_TRENDYOL_TOKEN = 'trendyolToken';
public const FIELD_BOL_CLIENT_ID = 'bolClientId';
public const FIELD_BOL_SECRET = 'bolSecret';
public const FIELD_BOL_JWT_TOKEN = 'bolJwtToken';
public const FIELD_SELLER_ID = 'sellerId';
public const FIELD_SERVICE_KEY = 'serviceKey';
public const FIELD_TAKEALOT_KEY = 'takealotKey';
public const FIELD_WALLMART_SECRET_KEY = 'wallmartSecretKey';
public const FIELD_WALLMART_CLIENT_ID = 'wallmartClientId';
public const FIELD_WALLMART_ACCESS_TOKEN = 'wallmartAccessToken';
public const FIELD_CICEKSEPETI_API_KEY = 'ciceksepetiApiKey';
public const FIELD_CICEKSEPETI_SELLER_ID = 'ciceksepetiSellerId';
public const FIELD_OZON_CLIENT_ID = 'ozonClientId';
public const FIELD_OZON_API_KEY = 'ozonApiKey';
public const FIELD_WAYFAIR_CLIENT_ID_PROD = 'wayfairClientIdProd';
public const FIELD_WAYFAIR_SECRET_KEY_PROD = 'wayfairSecretKeyProd';
public const FIELD_WAYFAIR_ACCESS_TOKEN_PROD = 'wayfairAccessTokenProd';
public const FIELD_EBAY_CLIENT_ID = 'ebayClientId';
public const FIELD_EBAY_CLIENT_SECRET = 'ebayClientSecret';
public const FIELD_EBAY_REFRESH_TOKEN = 'ebayRefreshToken';
public const FIELD_EBAY_AUTH_CODE = 'ebayAuthCode';
public const FIELD_EBAY_RU_NAME = 'ebayRuName';
public const FIELD_EBAY_ACCESS_TOKEN = 'ebayAccessToken';

protected $classId = "marketplace";
protected $className = "Marketplace";
protected $marketplaceType;
protected $marketplaceUrl;
protected $wisersellStoreId;
protected $merchantId;
protected $clientId;
protected $clientSecret;
protected $refreshToken;
protected $mainMerchant;
protected $merchantIds;
protected $fbaRegions;
protected $currency;
protected $apiKey;
protected $apiSecretKey;
protected $accessToken;
protected $oauthToken;
protected $apiUrl;
protected $shopId;
protected $keystring;
protected $sharedSecret;
protected $trendyolApiKey;
protected $trendyolSellerId;
protected $trendyolApiSecret;
protected $trendyolToken;
protected $bolClientId;
protected $bolSecret;
protected $bolJwtToken;
protected $sellerId;
protected $serviceKey;
protected $takealotKey;
protected $wallmartSecretKey;
protected $wallmartClientId;
protected $wallmartAccessToken;
protected $ciceksepetiApiKey;
protected $ciceksepetiSellerId;
protected $ozonClientId;
protected $ozonApiKey;
protected $wayfairClientIdProd;
protected $wayfairSecretKeyProd;
protected $wayfairAccessTokenProd;
protected $ebayClientId;
protected $ebayClientSecret;
protected $ebayRefreshToken;
protected $ebayAuthCode;
protected $ebayRuName;
protected $ebayAccessToken;


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
* Get marketplaceUrl - Mağaza Ana Sayfa
* @return string|null
*/
public function getMarketplaceUrl(): ?string
{
	if ($this instanceof PreGetValueHookInterface && !\Pimcore::inAdmin()) {
		$preValue = $this->preGetValue("marketplaceUrl");
		if ($preValue !== null) {
			return $preValue;
		}
	}

	$data = $this->marketplaceUrl;

	if ($data instanceof \Pimcore\Model\DataObject\Data\EncryptedField) {
		return $data->getPlain();
	}

	return $data;
}

/**
* Set marketplaceUrl - Mağaza Ana Sayfa
* @param string|null $marketplaceUrl
* @return $this
*/
public function setMarketplaceUrl(?string $marketplaceUrl): static
{
	$this->markFieldDirty("marketplaceUrl", true);

	$this->marketplaceUrl = $marketplaceUrl;

	return $this;
}

/**
* Get wisersellStoreId - Wisersell Store Id
* @return string|null
*/
public function getWisersellStoreId(): ?string
{
	if ($this instanceof PreGetValueHookInterface && !\Pimcore::inAdmin()) {
		$preValue = $this->preGetValue("wisersellStoreId");
		if ($preValue !== null) {
			return $preValue;
		}
	}

	$data = $this->wisersellStoreId;

	if ($data instanceof \Pimcore\Model\DataObject\Data\EncryptedField) {
		return $data->getPlain();
	}

	return $data;
}

/**
* Set wisersellStoreId - Wisersell Store Id
* @param string|null $wisersellStoreId
* @return $this
*/
public function setWisersellStoreId(?string $wisersellStoreId): static
{
	$this->markFieldDirty("wisersellStoreId", true);

	$this->wisersellStoreId = $wisersellStoreId;

	return $this;
}

/**
* Get merchantId - Merchant Id
* @return string|null
*/
public function getMerchantId(): ?string
{
	if ($this instanceof PreGetValueHookInterface && !\Pimcore::inAdmin()) {
		$preValue = $this->preGetValue("merchantId");
		if ($preValue !== null) {
			return $preValue;
		}
	}

	$data = $this->merchantId;

	if ($data instanceof \Pimcore\Model\DataObject\Data\EncryptedField) {
		return $data->getPlain();
	}

	return $data;
}

/**
* Set merchantId - Merchant Id
* @param string|null $merchantId
* @return $this
*/
public function setMerchantId(?string $merchantId): static
{
	$this->markFieldDirty("merchantId", true);

	$this->merchantId = $merchantId;

	return $this;
}

/**
* Get clientId - Client Id
* @return string|null
*/
public function getClientId(): ?string
{
	if ($this instanceof PreGetValueHookInterface && !\Pimcore::inAdmin()) {
		$preValue = $this->preGetValue("clientId");
		if ($preValue !== null) {
			return $preValue;
		}
	}

	$data = $this->clientId;

	if ($data instanceof \Pimcore\Model\DataObject\Data\EncryptedField) {
		return $data->getPlain();
	}

	return $data;
}

/**
* Set clientId - Client Id
* @param string|null $clientId
* @return $this
*/
public function setClientId(?string $clientId): static
{
	$this->markFieldDirty("clientId", true);

	$this->clientId = $clientId;

	return $this;
}

/**
* Get clientSecret - Client Secret
* @return string|null
*/
public function getClientSecret(): ?string
{
	if ($this instanceof PreGetValueHookInterface && !\Pimcore::inAdmin()) {
		$preValue = $this->preGetValue("clientSecret");
		if ($preValue !== null) {
			return $preValue;
		}
	}

	$data = $this->clientSecret;

	if ($data instanceof \Pimcore\Model\DataObject\Data\EncryptedField) {
		return $data->getPlain();
	}

	return $data;
}

/**
* Set clientSecret - Client Secret
* @param string|null $clientSecret
* @return $this
*/
public function setClientSecret(?string $clientSecret): static
{
	$this->markFieldDirty("clientSecret", true);

	$this->clientSecret = $clientSecret;

	return $this;
}

/**
* Get refreshToken - Refresh Token
* @return string|null
*/
public function getRefreshToken(): ?string
{
	if ($this instanceof PreGetValueHookInterface && !\Pimcore::inAdmin()) {
		$preValue = $this->preGetValue("refreshToken");
		if ($preValue !== null) {
			return $preValue;
		}
	}

	$data = $this->refreshToken;

	if ($data instanceof \Pimcore\Model\DataObject\Data\EncryptedField) {
		return $data->getPlain();
	}

	return $data;
}

/**
* Set refreshToken - Refresh Token
* @param string|null $refreshToken
* @return $this
*/
public function setRefreshToken(?string $refreshToken): static
{
	$this->markFieldDirty("refreshToken", true);

	$this->refreshToken = $refreshToken;

	return $this;
}

/**
* Get mainMerchant - Ana Ülke
* @return string|null
*/
public function getMainMerchant(): ?string
{
	if ($this instanceof PreGetValueHookInterface && !\Pimcore::inAdmin()) {
		$preValue = $this->preGetValue("mainMerchant");
		if ($preValue !== null) {
			return $preValue;
		}
	}

	$data = $this->mainMerchant;

	if ($data instanceof \Pimcore\Model\DataObject\Data\EncryptedField) {
		return $data->getPlain();
	}

	return $data;
}

/**
* Set mainMerchant - Ana Ülke
* @param string|null $mainMerchant
* @return $this
*/
public function setMainMerchant(?string $mainMerchant): static
{
	$this->markFieldDirty("mainMerchant", true);

	$this->mainMerchant = $mainMerchant;

	return $this;
}

/**
* Get merchantIds - Aktif Bölgeler
* @return string[]|null
*/
public function getMerchantIds(): ?array
{
	if ($this instanceof PreGetValueHookInterface && !\Pimcore::inAdmin()) {
		$preValue = $this->preGetValue("merchantIds");
		if ($preValue !== null) {
			return $preValue;
		}
	}

	$data = $this->merchantIds;

	if ($data instanceof \Pimcore\Model\DataObject\Data\EncryptedField) {
		return $data->getPlain();
	}

	return $data;
}

/**
* Set merchantIds - Aktif Bölgeler
* @param string[]|null $merchantIds
* @return $this
*/
public function setMerchantIds(?array $merchantIds): static
{
	$this->markFieldDirty("merchantIds", true);

	$this->merchantIds = $merchantIds;

	return $this;
}

/**
* Get fbaRegions - FBA Depo
* @return string[]|null
*/
public function getFbaRegions(): ?array
{
	if ($this instanceof PreGetValueHookInterface && !\Pimcore::inAdmin()) {
		$preValue = $this->preGetValue("fbaRegions");
		if ($preValue !== null) {
			return $preValue;
		}
	}

	$data = $this->fbaRegions;

	if ($data instanceof \Pimcore\Model\DataObject\Data\EncryptedField) {
		return $data->getPlain();
	}

	return $data;
}

/**
* Set fbaRegions - FBA Depo
* @param string[]|null $fbaRegions
* @return $this
*/
public function setFbaRegions(?array $fbaRegions): static
{
	$this->markFieldDirty("fbaRegions", true);

	$this->fbaRegions = $fbaRegions;

	return $this;
}

/**
* Get currency - Satış Para Birimi
* @return string|null
*/
public function getCurrency(): ?string
{
	if ($this instanceof PreGetValueHookInterface && !\Pimcore::inAdmin()) {
		$preValue = $this->preGetValue("currency");
		if ($preValue !== null) {
			return $preValue;
		}
	}

	$data = $this->currency;

	if ($data instanceof \Pimcore\Model\DataObject\Data\EncryptedField) {
		return $data->getPlain();
	}

	return $data;
}

/**
* Set currency - Satış Para Birimi
* @param string|null $currency
* @return $this
*/
public function setCurrency(?string $currency): static
{
	$this->markFieldDirty("currency", true);

	$this->currency = $currency;

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

/**
* Get shopId - Etsy Shop Id
* @return string|null
*/
public function getShopId(): ?string
{
	if ($this instanceof PreGetValueHookInterface && !\Pimcore::inAdmin()) {
		$preValue = $this->preGetValue("shopId");
		if ($preValue !== null) {
			return $preValue;
		}
	}

	$data = $this->shopId;

	if ($data instanceof \Pimcore\Model\DataObject\Data\EncryptedField) {
		return $data->getPlain();
	}

	return $data;
}

/**
* Set shopId - Etsy Shop Id
* @param string|null $shopId
* @return $this
*/
public function setShopId(?string $shopId): static
{
	$this->markFieldDirty("shopId", true);

	$this->shopId = $shopId;

	return $this;
}

/**
* Get keystring - Keystring
* @return string|null
*/
public function getKeystring(): ?string
{
	if ($this instanceof PreGetValueHookInterface && !\Pimcore::inAdmin()) {
		$preValue = $this->preGetValue("keystring");
		if ($preValue !== null) {
			return $preValue;
		}
	}

	$data = $this->keystring;

	if ($data instanceof \Pimcore\Model\DataObject\Data\EncryptedField) {
		return $data->getPlain();
	}

	return $data;
}

/**
* Set keystring - Keystring
* @param string|null $keystring
* @return $this
*/
public function setKeystring(?string $keystring): static
{
	$this->markFieldDirty("keystring", true);

	$this->keystring = $keystring;

	return $this;
}

/**
* Get sharedSecret - Shared Secret
* @return string|null
*/
public function getSharedSecret(): ?string
{
	if ($this instanceof PreGetValueHookInterface && !\Pimcore::inAdmin()) {
		$preValue = $this->preGetValue("sharedSecret");
		if ($preValue !== null) {
			return $preValue;
		}
	}

	$data = $this->sharedSecret;

	if ($data instanceof \Pimcore\Model\DataObject\Data\EncryptedField) {
		return $data->getPlain();
	}

	return $data;
}

/**
* Set sharedSecret - Shared Secret
* @param string|null $sharedSecret
* @return $this
*/
public function setSharedSecret(?string $sharedSecret): static
{
	$this->markFieldDirty("sharedSecret", true);

	$this->sharedSecret = $sharedSecret;

	return $this;
}

/**
* Get trendyolApiKey - Api Key
* @return string|null
*/
public function getTrendyolApiKey(): ?string
{
	if ($this instanceof PreGetValueHookInterface && !\Pimcore::inAdmin()) {
		$preValue = $this->preGetValue("trendyolApiKey");
		if ($preValue !== null) {
			return $preValue;
		}
	}

	$data = $this->trendyolApiKey;

	if ($data instanceof \Pimcore\Model\DataObject\Data\EncryptedField) {
		return $data->getPlain();
	}

	return $data;
}

/**
* Set trendyolApiKey - Api Key
* @param string|null $trendyolApiKey
* @return $this
*/
public function setTrendyolApiKey(?string $trendyolApiKey): static
{
	$this->markFieldDirty("trendyolApiKey", true);

	$this->trendyolApiKey = $trendyolApiKey;

	return $this;
}

/**
* Get trendyolSellerId - Seller Id
* @return string|null
*/
public function getTrendyolSellerId(): ?string
{
	if ($this instanceof PreGetValueHookInterface && !\Pimcore::inAdmin()) {
		$preValue = $this->preGetValue("trendyolSellerId");
		if ($preValue !== null) {
			return $preValue;
		}
	}

	$data = $this->trendyolSellerId;

	if ($data instanceof \Pimcore\Model\DataObject\Data\EncryptedField) {
		return $data->getPlain();
	}

	return $data;
}

/**
* Set trendyolSellerId - Seller Id
* @param string|null $trendyolSellerId
* @return $this
*/
public function setTrendyolSellerId(?string $trendyolSellerId): static
{
	$this->markFieldDirty("trendyolSellerId", true);

	$this->trendyolSellerId = $trendyolSellerId;

	return $this;
}

/**
* Get trendyolApiSecret - Api Secret
* @return string|null
*/
public function getTrendyolApiSecret(): ?string
{
	if ($this instanceof PreGetValueHookInterface && !\Pimcore::inAdmin()) {
		$preValue = $this->preGetValue("trendyolApiSecret");
		if ($preValue !== null) {
			return $preValue;
		}
	}

	$data = $this->trendyolApiSecret;

	if ($data instanceof \Pimcore\Model\DataObject\Data\EncryptedField) {
		return $data->getPlain();
	}

	return $data;
}

/**
* Set trendyolApiSecret - Api Secret
* @param string|null $trendyolApiSecret
* @return $this
*/
public function setTrendyolApiSecret(?string $trendyolApiSecret): static
{
	$this->markFieldDirty("trendyolApiSecret", true);

	$this->trendyolApiSecret = $trendyolApiSecret;

	return $this;
}

/**
* Get trendyolToken - Token
* @return string|null
*/
public function getTrendyolToken(): ?string
{
	if ($this instanceof PreGetValueHookInterface && !\Pimcore::inAdmin()) {
		$preValue = $this->preGetValue("trendyolToken");
		if ($preValue !== null) {
			return $preValue;
		}
	}

	$data = $this->trendyolToken;

	if ($data instanceof \Pimcore\Model\DataObject\Data\EncryptedField) {
		return $data->getPlain();
	}

	return $data;
}

/**
* Set trendyolToken - Token
* @param string|null $trendyolToken
* @return $this
*/
public function setTrendyolToken(?string $trendyolToken): static
{
	$this->markFieldDirty("trendyolToken", true);

	$this->trendyolToken = $trendyolToken;

	return $this;
}

/**
* Get bolClientId - Bol Client Id
* @return string|null
*/
public function getBolClientId(): ?string
{
	if ($this instanceof PreGetValueHookInterface && !\Pimcore::inAdmin()) {
		$preValue = $this->preGetValue("bolClientId");
		if ($preValue !== null) {
			return $preValue;
		}
	}

	$data = $this->bolClientId;

	if ($data instanceof \Pimcore\Model\DataObject\Data\EncryptedField) {
		return $data->getPlain();
	}

	return $data;
}

/**
* Set bolClientId - Bol Client Id
* @param string|null $bolClientId
* @return $this
*/
public function setBolClientId(?string $bolClientId): static
{
	$this->markFieldDirty("bolClientId", true);

	$this->bolClientId = $bolClientId;

	return $this;
}

/**
* Get bolSecret - Bol Secret
* @return string|null
*/
public function getBolSecret(): ?string
{
	if ($this instanceof PreGetValueHookInterface && !\Pimcore::inAdmin()) {
		$preValue = $this->preGetValue("bolSecret");
		if ($preValue !== null) {
			return $preValue;
		}
	}

	$data = $this->bolSecret;

	if ($data instanceof \Pimcore\Model\DataObject\Data\EncryptedField) {
		return $data->getPlain();
	}

	return $data;
}

/**
* Set bolSecret - Bol Secret
* @param string|null $bolSecret
* @return $this
*/
public function setBolSecret(?string $bolSecret): static
{
	$this->markFieldDirty("bolSecret", true);

	$this->bolSecret = $bolSecret;

	return $this;
}

/**
* Get bolJwtToken - Jwt Token
* @return string|null
*/
public function getBolJwtToken(): ?string
{
	if ($this instanceof PreGetValueHookInterface && !\Pimcore::inAdmin()) {
		$preValue = $this->preGetValue("bolJwtToken");
		if ($preValue !== null) {
			return $preValue;
		}
	}

	$data = $this->bolJwtToken;

	if ($data instanceof \Pimcore\Model\DataObject\Data\EncryptedField) {
		return $data->getPlain();
	}

	return $data;
}

/**
* Set bolJwtToken - Jwt Token
* @param string|null $bolJwtToken
* @return $this
*/
public function setBolJwtToken(?string $bolJwtToken): static
{
	$this->markFieldDirty("bolJwtToken", true);

	$this->bolJwtToken = $bolJwtToken;

	return $this;
}

/**
* Get sellerId - Satici Id
* @return string|null
*/
public function getSellerId(): ?string
{
	if ($this instanceof PreGetValueHookInterface && !\Pimcore::inAdmin()) {
		$preValue = $this->preGetValue("sellerId");
		if ($preValue !== null) {
			return $preValue;
		}
	}

	$data = $this->sellerId;

	if ($data instanceof \Pimcore\Model\DataObject\Data\EncryptedField) {
		return $data->getPlain();
	}

	return $data;
}

/**
* Set sellerId - Satici Id
* @param string|null $sellerId
* @return $this
*/
public function setSellerId(?string $sellerId): static
{
	$this->markFieldDirty("sellerId", true);

	$this->sellerId = $sellerId;

	return $this;
}

/**
* Get serviceKey - Servis Anahtarı
* @return string|null
*/
public function getServiceKey(): ?string
{
	if ($this instanceof PreGetValueHookInterface && !\Pimcore::inAdmin()) {
		$preValue = $this->preGetValue("serviceKey");
		if ($preValue !== null) {
			return $preValue;
		}
	}

	$data = $this->serviceKey;

	if ($data instanceof \Pimcore\Model\DataObject\Data\EncryptedField) {
		return $data->getPlain();
	}

	return $data;
}

/**
* Set serviceKey - Servis Anahtarı
* @param string|null $serviceKey
* @return $this
*/
public function setServiceKey(?string $serviceKey): static
{
	$this->markFieldDirty("serviceKey", true);

	$this->serviceKey = $serviceKey;

	return $this;
}

/**
* Get takealotKey - Takealot Key
* @return string|null
*/
public function getTakealotKey(): ?string
{
	if ($this instanceof PreGetValueHookInterface && !\Pimcore::inAdmin()) {
		$preValue = $this->preGetValue("takealotKey");
		if ($preValue !== null) {
			return $preValue;
		}
	}

	$data = $this->takealotKey;

	if ($data instanceof \Pimcore\Model\DataObject\Data\EncryptedField) {
		return $data->getPlain();
	}

	return $data;
}

/**
* Set takealotKey - Takealot Key
* @param string|null $takealotKey
* @return $this
*/
public function setTakealotKey(?string $takealotKey): static
{
	$this->markFieldDirty("takealotKey", true);

	$this->takealotKey = $takealotKey;

	return $this;
}

/**
* Get wallmartSecretKey - Wallmart Secret Key
* @return string|null
*/
public function getWallmartSecretKey(): ?string
{
	if ($this instanceof PreGetValueHookInterface && !\Pimcore::inAdmin()) {
		$preValue = $this->preGetValue("wallmartSecretKey");
		if ($preValue !== null) {
			return $preValue;
		}
	}

	$data = $this->wallmartSecretKey;

	if ($data instanceof \Pimcore\Model\DataObject\Data\EncryptedField) {
		return $data->getPlain();
	}

	return $data;
}

/**
* Set wallmartSecretKey - Wallmart Secret Key
* @param string|null $wallmartSecretKey
* @return $this
*/
public function setWallmartSecretKey(?string $wallmartSecretKey): static
{
	$this->markFieldDirty("wallmartSecretKey", true);

	$this->wallmartSecretKey = $wallmartSecretKey;

	return $this;
}

/**
* Get wallmartClientId - Wallmart Client Id
* @return string|null
*/
public function getWallmartClientId(): ?string
{
	if ($this instanceof PreGetValueHookInterface && !\Pimcore::inAdmin()) {
		$preValue = $this->preGetValue("wallmartClientId");
		if ($preValue !== null) {
			return $preValue;
		}
	}

	$data = $this->wallmartClientId;

	if ($data instanceof \Pimcore\Model\DataObject\Data\EncryptedField) {
		return $data->getPlain();
	}

	return $data;
}

/**
* Set wallmartClientId - Wallmart Client Id
* @param string|null $wallmartClientId
* @return $this
*/
public function setWallmartClientId(?string $wallmartClientId): static
{
	$this->markFieldDirty("wallmartClientId", true);

	$this->wallmartClientId = $wallmartClientId;

	return $this;
}

/**
* Get wallmartAccessToken - Wallmart Access Token
* @return string|null
*/
public function getWallmartAccessToken(): ?string
{
	if ($this instanceof PreGetValueHookInterface && !\Pimcore::inAdmin()) {
		$preValue = $this->preGetValue("wallmartAccessToken");
		if ($preValue !== null) {
			return $preValue;
		}
	}

	$data = $this->wallmartAccessToken;

	if ($data instanceof \Pimcore\Model\DataObject\Data\EncryptedField) {
		return $data->getPlain();
	}

	return $data;
}

/**
* Set wallmartAccessToken - Wallmart Access Token
* @param string|null $wallmartAccessToken
* @return $this
*/
public function setWallmartAccessToken(?string $wallmartAccessToken): static
{
	$this->markFieldDirty("wallmartAccessToken", true);

	$this->wallmartAccessToken = $wallmartAccessToken;

	return $this;
}

/**
* Get ciceksepetiApiKey - Ciceksepeti Api Key
* @return string|null
*/
public function getCiceksepetiApiKey(): ?string
{
	if ($this instanceof PreGetValueHookInterface && !\Pimcore::inAdmin()) {
		$preValue = $this->preGetValue("ciceksepetiApiKey");
		if ($preValue !== null) {
			return $preValue;
		}
	}

	$data = $this->ciceksepetiApiKey;

	if ($data instanceof \Pimcore\Model\DataObject\Data\EncryptedField) {
		return $data->getPlain();
	}

	return $data;
}

/**
* Set ciceksepetiApiKey - Ciceksepeti Api Key
* @param string|null $ciceksepetiApiKey
* @return $this
*/
public function setCiceksepetiApiKey(?string $ciceksepetiApiKey): static
{
	$this->markFieldDirty("ciceksepetiApiKey", true);

	$this->ciceksepetiApiKey = $ciceksepetiApiKey;

	return $this;
}

/**
* Get ciceksepetiSellerId - Ciceksepeti Seller Id
* @return string|null
*/
public function getCiceksepetiSellerId(): ?string
{
	if ($this instanceof PreGetValueHookInterface && !\Pimcore::inAdmin()) {
		$preValue = $this->preGetValue("ciceksepetiSellerId");
		if ($preValue !== null) {
			return $preValue;
		}
	}

	$data = $this->ciceksepetiSellerId;

	if ($data instanceof \Pimcore\Model\DataObject\Data\EncryptedField) {
		return $data->getPlain();
	}

	return $data;
}

/**
* Set ciceksepetiSellerId - Ciceksepeti Seller Id
* @param string|null $ciceksepetiSellerId
* @return $this
*/
public function setCiceksepetiSellerId(?string $ciceksepetiSellerId): static
{
	$this->markFieldDirty("ciceksepetiSellerId", true);

	$this->ciceksepetiSellerId = $ciceksepetiSellerId;

	return $this;
}

/**
* Get ozonClientId - Ozon Client Id
* @return string|null
*/
public function getOzonClientId(): ?string
{
	if ($this instanceof PreGetValueHookInterface && !\Pimcore::inAdmin()) {
		$preValue = $this->preGetValue("ozonClientId");
		if ($preValue !== null) {
			return $preValue;
		}
	}

	$data = $this->ozonClientId;

	if ($data instanceof \Pimcore\Model\DataObject\Data\EncryptedField) {
		return $data->getPlain();
	}

	return $data;
}

/**
* Set ozonClientId - Ozon Client Id
* @param string|null $ozonClientId
* @return $this
*/
public function setOzonClientId(?string $ozonClientId): static
{
	$this->markFieldDirty("ozonClientId", true);

	$this->ozonClientId = $ozonClientId;

	return $this;
}

/**
* Get ozonApiKey - Ozon Api Key
* @return string|null
*/
public function getOzonApiKey(): ?string
{
	if ($this instanceof PreGetValueHookInterface && !\Pimcore::inAdmin()) {
		$preValue = $this->preGetValue("ozonApiKey");
		if ($preValue !== null) {
			return $preValue;
		}
	}

	$data = $this->ozonApiKey;

	if ($data instanceof \Pimcore\Model\DataObject\Data\EncryptedField) {
		return $data->getPlain();
	}

	return $data;
}

/**
* Set ozonApiKey - Ozon Api Key
* @param string|null $ozonApiKey
* @return $this
*/
public function setOzonApiKey(?string $ozonApiKey): static
{
	$this->markFieldDirty("ozonApiKey", true);

	$this->ozonApiKey = $ozonApiKey;

	return $this;
}

/**
* Get wayfairClientIdProd - Wayfair Client Id Prod
* @return string|null
*/
public function getWayfairClientIdProd(): ?string
{
	if ($this instanceof PreGetValueHookInterface && !\Pimcore::inAdmin()) {
		$preValue = $this->preGetValue("wayfairClientIdProd");
		if ($preValue !== null) {
			return $preValue;
		}
	}

	$data = $this->wayfairClientIdProd;

	if ($data instanceof \Pimcore\Model\DataObject\Data\EncryptedField) {
		return $data->getPlain();
	}

	return $data;
}

/**
* Set wayfairClientIdProd - Wayfair Client Id Prod
* @param string|null $wayfairClientIdProd
* @return $this
*/
public function setWayfairClientIdProd(?string $wayfairClientIdProd): static
{
	$this->markFieldDirty("wayfairClientIdProd", true);

	$this->wayfairClientIdProd = $wayfairClientIdProd;

	return $this;
}

/**
* Get wayfairSecretKeyProd - Wayfair Secret Key Prod
* @return string|null
*/
public function getWayfairSecretKeyProd(): ?string
{
	if ($this instanceof PreGetValueHookInterface && !\Pimcore::inAdmin()) {
		$preValue = $this->preGetValue("wayfairSecretKeyProd");
		if ($preValue !== null) {
			return $preValue;
		}
	}

	$data = $this->wayfairSecretKeyProd;

	if ($data instanceof \Pimcore\Model\DataObject\Data\EncryptedField) {
		return $data->getPlain();
	}

	return $data;
}

/**
* Set wayfairSecretKeyProd - Wayfair Secret Key Prod
* @param string|null $wayfairSecretKeyProd
* @return $this
*/
public function setWayfairSecretKeyProd(?string $wayfairSecretKeyProd): static
{
	$this->markFieldDirty("wayfairSecretKeyProd", true);

	$this->wayfairSecretKeyProd = $wayfairSecretKeyProd;

	return $this;
}

/**
* Get wayfairAccessTokenProd - Wayfair Access Token Prod
* @return string|null
*/
public function getWayfairAccessTokenProd(): ?string
{
	if ($this instanceof PreGetValueHookInterface && !\Pimcore::inAdmin()) {
		$preValue = $this->preGetValue("wayfairAccessTokenProd");
		if ($preValue !== null) {
			return $preValue;
		}
	}

	$data = $this->wayfairAccessTokenProd;

	if ($data instanceof \Pimcore\Model\DataObject\Data\EncryptedField) {
		return $data->getPlain();
	}

	return $data;
}

/**
* Set wayfairAccessTokenProd - Wayfair Access Token Prod
* @param string|null $wayfairAccessTokenProd
* @return $this
*/
public function setWayfairAccessTokenProd(?string $wayfairAccessTokenProd): static
{
	$this->markFieldDirty("wayfairAccessTokenProd", true);

	$this->wayfairAccessTokenProd = $wayfairAccessTokenProd;

	return $this;
}

/**
* Get ebayClientId - Ebay Client Id
* @return string|null
*/
public function getEbayClientId(): ?string
{
	if ($this instanceof PreGetValueHookInterface && !\Pimcore::inAdmin()) {
		$preValue = $this->preGetValue("ebayClientId");
		if ($preValue !== null) {
			return $preValue;
		}
	}

	$data = $this->ebayClientId;

	if ($data instanceof \Pimcore\Model\DataObject\Data\EncryptedField) {
		return $data->getPlain();
	}

	return $data;
}

/**
* Set ebayClientId - Ebay Client Id
* @param string|null $ebayClientId
* @return $this
*/
public function setEbayClientId(?string $ebayClientId): static
{
	$this->markFieldDirty("ebayClientId", true);

	$this->ebayClientId = $ebayClientId;

	return $this;
}

/**
* Get ebayClientSecret - Ebay Client Secret
* @return string|null
*/
public function getEbayClientSecret(): ?string
{
	if ($this instanceof PreGetValueHookInterface && !\Pimcore::inAdmin()) {
		$preValue = $this->preGetValue("ebayClientSecret");
		if ($preValue !== null) {
			return $preValue;
		}
	}

	$data = $this->ebayClientSecret;

	if ($data instanceof \Pimcore\Model\DataObject\Data\EncryptedField) {
		return $data->getPlain();
	}

	return $data;
}

/**
* Set ebayClientSecret - Ebay Client Secret
* @param string|null $ebayClientSecret
* @return $this
*/
public function setEbayClientSecret(?string $ebayClientSecret): static
{
	$this->markFieldDirty("ebayClientSecret", true);

	$this->ebayClientSecret = $ebayClientSecret;

	return $this;
}

/**
* Get ebayRefreshToken - Ebay Refresh Token
* @return string|null
*/
public function getEbayRefreshToken(): ?string
{
	if ($this instanceof PreGetValueHookInterface && !\Pimcore::inAdmin()) {
		$preValue = $this->preGetValue("ebayRefreshToken");
		if ($preValue !== null) {
			return $preValue;
		}
	}

	$data = $this->ebayRefreshToken;

	if ($data instanceof \Pimcore\Model\DataObject\Data\EncryptedField) {
		return $data->getPlain();
	}

	return $data;
}

/**
* Set ebayRefreshToken - Ebay Refresh Token
* @param string|null $ebayRefreshToken
* @return $this
*/
public function setEbayRefreshToken(?string $ebayRefreshToken): static
{
	$this->markFieldDirty("ebayRefreshToken", true);

	$this->ebayRefreshToken = $ebayRefreshToken;

	return $this;
}

/**
* Get ebayAuthCode - Ebay Auth Code
* @return string|null
*/
public function getEbayAuthCode(): ?string
{
	if ($this instanceof PreGetValueHookInterface && !\Pimcore::inAdmin()) {
		$preValue = $this->preGetValue("ebayAuthCode");
		if ($preValue !== null) {
			return $preValue;
		}
	}

	$data = $this->ebayAuthCode;

	if ($data instanceof \Pimcore\Model\DataObject\Data\EncryptedField) {
		return $data->getPlain();
	}

	return $data;
}

/**
* Set ebayAuthCode - Ebay Auth Code
* @param string|null $ebayAuthCode
* @return $this
*/
public function setEbayAuthCode(?string $ebayAuthCode): static
{
	$this->markFieldDirty("ebayAuthCode", true);

	$this->ebayAuthCode = $ebayAuthCode;

	return $this;
}

/**
* Get ebayRuName - Ebay Ru Name
* @return string|null
*/
public function getEbayRuName(): ?string
{
	if ($this instanceof PreGetValueHookInterface && !\Pimcore::inAdmin()) {
		$preValue = $this->preGetValue("ebayRuName");
		if ($preValue !== null) {
			return $preValue;
		}
	}

	$data = $this->ebayRuName;

	if ($data instanceof \Pimcore\Model\DataObject\Data\EncryptedField) {
		return $data->getPlain();
	}

	return $data;
}

/**
* Set ebayRuName - Ebay Ru Name
* @param string|null $ebayRuName
* @return $this
*/
public function setEbayRuName(?string $ebayRuName): static
{
	$this->markFieldDirty("ebayRuName", true);

	$this->ebayRuName = $ebayRuName;

	return $this;
}

/**
* Get ebayAccessToken - Ebay Access Token
* @return string|null
*/
public function getEbayAccessToken(): ?string
{
	if ($this instanceof PreGetValueHookInterface && !\Pimcore::inAdmin()) {
		$preValue = $this->preGetValue("ebayAccessToken");
		if ($preValue !== null) {
			return $preValue;
		}
	}

	$data = $this->ebayAccessToken;

	if ($data instanceof \Pimcore\Model\DataObject\Data\EncryptedField) {
		return $data->getPlain();
	}

	return $data;
}

/**
* Set ebayAccessToken - Ebay Access Token
* @param string|null $ebayAccessToken
* @return $this
*/
public function setEbayAccessToken(?string $ebayAccessToken): static
{
	$this->markFieldDirty("ebayAccessToken", true);

	$this->ebayAccessToken = $ebayAccessToken;

	return $this;
}

}

