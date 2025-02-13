<?php

namespace Pimcore\Model\DataObject\Marketplace;

use Pimcore\Model;
use Pimcore\Model\DataObject;

/**
 * @method DataObject\Marketplace|false current()
 * @method DataObject\Marketplace[] load()
 * @method DataObject\Marketplace[] getData()
 * @method DataObject\Marketplace[] getObjects()
 */

class Listing extends DataObject\Listing\Concrete
{
protected $classId = "marketplace";
protected $className = "Marketplace";


/**
* Filter by marketplaceType (Pazaryeri Tipi)
* @param string|int|float|array|Model\Element\ElementInterface $data  comparison data, can be scalar or array (if operator is e.g. "IN (?)")
* @param string $operator  SQL comparison operator, e.g. =, <, >= etc. You can use "?" as placeholder, e.g. "IN (?)"
* @return $this
*/
public function filterByMarketplaceType ($data, $operator = '='): static
{
	$this->getClass()->getFieldDefinition("marketplaceType")->addListingFilter($this, $data, $operator);
	return $this;
}

/**
* Filter by marketplaceUrl (Mağaza Ana Sayfa)
* @param string|int|float|array|Model\Element\ElementInterface $data  comparison data, can be scalar or array (if operator is e.g. "IN (?)")
* @param string $operator  SQL comparison operator, e.g. =, <, >= etc. You can use "?" as placeholder, e.g. "IN (?)"
* @return $this
*/
public function filterByMarketplaceUrl ($data, $operator = '='): static
{
	$this->getClass()->getFieldDefinition("marketplaceUrl")->addListingFilter($this, $data, $operator);
	return $this;
}

/**
* Filter by wisersellStoreId (Wisersell Store Id)
* @param string|int|float|array|Model\Element\ElementInterface $data  comparison data, can be scalar or array (if operator is e.g. "IN (?)")
* @param string $operator  SQL comparison operator, e.g. =, <, >= etc. You can use "?" as placeholder, e.g. "IN (?)"
* @return $this
*/
public function filterByWisersellStoreId ($data, $operator = '='): static
{
	$this->getClass()->getFieldDefinition("wisersellStoreId")->addListingFilter($this, $data, $operator);
	return $this;
}

/**
* Filter by merchantId (Merchant Id)
* @param string|int|float|array|Model\Element\ElementInterface $data  comparison data, can be scalar or array (if operator is e.g. "IN (?)")
* @param string $operator  SQL comparison operator, e.g. =, <, >= etc. You can use "?" as placeholder, e.g. "IN (?)"
* @return $this
*/
public function filterByMerchantId ($data, $operator = '='): static
{
	$this->getClass()->getFieldDefinition("merchantId")->addListingFilter($this, $data, $operator);
	return $this;
}

/**
* Filter by clientId (Client Id)
* @param string|int|float|array|Model\Element\ElementInterface $data  comparison data, can be scalar or array (if operator is e.g. "IN (?)")
* @param string $operator  SQL comparison operator, e.g. =, <, >= etc. You can use "?" as placeholder, e.g. "IN (?)"
* @return $this
*/
public function filterByClientId ($data, $operator = '='): static
{
	$this->getClass()->getFieldDefinition("clientId")->addListingFilter($this, $data, $operator);
	return $this;
}

/**
* Filter by clientSecret (Client Secret)
* @param string|int|float|array|Model\Element\ElementInterface $data  comparison data, can be scalar or array (if operator is e.g. "IN (?)")
* @param string $operator  SQL comparison operator, e.g. =, <, >= etc. You can use "?" as placeholder, e.g. "IN (?)"
* @return $this
*/
public function filterByClientSecret ($data, $operator = '='): static
{
	$this->getClass()->getFieldDefinition("clientSecret")->addListingFilter($this, $data, $operator);
	return $this;
}

/**
* Filter by refreshToken (Refresh Token)
* @param string|int|float|array|Model\Element\ElementInterface $data  comparison data, can be scalar or array (if operator is e.g. "IN (?)")
* @param string $operator  SQL comparison operator, e.g. =, <, >= etc. You can use "?" as placeholder, e.g. "IN (?)"
* @return $this
*/
public function filterByRefreshToken ($data, $operator = '='): static
{
	$this->getClass()->getFieldDefinition("refreshToken")->addListingFilter($this, $data, $operator);
	return $this;
}

/**
* Filter by mainMerchant (Ana Ülke)
* @param string|int|float|array|Model\Element\ElementInterface $data  comparison data, can be scalar or array (if operator is e.g. "IN (?)")
* @param string $operator  SQL comparison operator, e.g. =, <, >= etc. You can use "?" as placeholder, e.g. "IN (?)"
* @return $this
*/
public function filterByMainMerchant ($data, $operator = '='): static
{
	$this->getClass()->getFieldDefinition("mainMerchant")->addListingFilter($this, $data, $operator);
	return $this;
}

/**
* Filter by merchantIds (Aktif Bölgeler)
* @param string|int|float|array|Model\Element\ElementInterface $data  comparison data, can be scalar or array (if operator is e.g. "IN (?)")
* @param string $operator  SQL comparison operator, e.g. =, <, >= etc. You can use "?" as placeholder, e.g. "IN (?)"
* @return $this
*/
public function filterByMerchantIds ($data, $operator = '='): static
{
	$this->getClass()->getFieldDefinition("merchantIds")->addListingFilter($this, $data, $operator);
	return $this;
}

/**
* Filter by fbaRegions (FBA Depo)
* @param string|int|float|array|Model\Element\ElementInterface $data  comparison data, can be scalar or array (if operator is e.g. "IN (?)")
* @param string $operator  SQL comparison operator, e.g. =, <, >= etc. You can use "?" as placeholder, e.g. "IN (?)"
* @return $this
*/
public function filterByFbaRegions ($data, $operator = '='): static
{
	$this->getClass()->getFieldDefinition("fbaRegions")->addListingFilter($this, $data, $operator);
	return $this;
}

/**
* Filter by currency (Satış Para Birimi)
* @param string|int|float|array|Model\Element\ElementInterface $data  comparison data, can be scalar or array (if operator is e.g. "IN (?)")
* @param string $operator  SQL comparison operator, e.g. =, <, >= etc. You can use "?" as placeholder, e.g. "IN (?)"
* @return $this
*/
public function filterByCurrency ($data, $operator = '='): static
{
	$this->getClass()->getFieldDefinition("currency")->addListingFilter($this, $data, $operator);
	return $this;
}

/**
* Filter by apiKey (Api Key)
* @param string|int|float|array|Model\Element\ElementInterface $data  comparison data, can be scalar or array (if operator is e.g. "IN (?)")
* @param string $operator  SQL comparison operator, e.g. =, <, >= etc. You can use "?" as placeholder, e.g. "IN (?)"
* @return $this
*/
public function filterByApiKey ($data, $operator = '='): static
{
	$this->getClass()->getFieldDefinition("apiKey")->addListingFilter($this, $data, $operator);
	return $this;
}

/**
* Filter by apiSecretKey (API Secret Key)
* @param string|int|float|array|Model\Element\ElementInterface $data  comparison data, can be scalar or array (if operator is e.g. "IN (?)")
* @param string $operator  SQL comparison operator, e.g. =, <, >= etc. You can use "?" as placeholder, e.g. "IN (?)"
* @return $this
*/
public function filterByApiSecretKey ($data, $operator = '='): static
{
	$this->getClass()->getFieldDefinition("apiSecretKey")->addListingFilter($this, $data, $operator);
	return $this;
}

/**
* Filter by accessToken (Access Token)
* @param string|int|float|array|Model\Element\ElementInterface $data  comparison data, can be scalar or array (if operator is e.g. "IN (?)")
* @param string $operator  SQL comparison operator, e.g. =, <, >= etc. You can use "?" as placeholder, e.g. "IN (?)"
* @return $this
*/
public function filterByAccessToken ($data, $operator = '='): static
{
	$this->getClass()->getFieldDefinition("accessToken")->addListingFilter($this, $data, $operator);
	return $this;
}

/**
* Filter by oauthToken (Oauth Token)
* @param string|int|float|array|Model\Element\ElementInterface $data  comparison data, can be scalar or array (if operator is e.g. "IN (?)")
* @param string $operator  SQL comparison operator, e.g. =, <, >= etc. You can use "?" as placeholder, e.g. "IN (?)"
* @return $this
*/
public function filterByOauthToken ($data, $operator = '='): static
{
	$this->getClass()->getFieldDefinition("oauthToken")->addListingFilter($this, $data, $operator);
	return $this;
}

/**
* Filter by apiUrl (API URL)
* @param string|int|float|array|Model\Element\ElementInterface $data  comparison data, can be scalar or array (if operator is e.g. "IN (?)")
* @param string $operator  SQL comparison operator, e.g. =, <, >= etc. You can use "?" as placeholder, e.g. "IN (?)"
* @return $this
*/
public function filterByApiUrl ($data, $operator = '='): static
{
	$this->getClass()->getFieldDefinition("apiUrl")->addListingFilter($this, $data, $operator);
	return $this;
}

/**
* Filter by shopId (Etsy Shop Id)
* @param string|int|float|array|Model\Element\ElementInterface $data  comparison data, can be scalar or array (if operator is e.g. "IN (?)")
* @param string $operator  SQL comparison operator, e.g. =, <, >= etc. You can use "?" as placeholder, e.g. "IN (?)"
* @return $this
*/
public function filterByShopId ($data, $operator = '='): static
{
	$this->getClass()->getFieldDefinition("shopId")->addListingFilter($this, $data, $operator);
	return $this;
}

/**
* Filter by keystring (Keystring)
* @param string|int|float|array|Model\Element\ElementInterface $data  comparison data, can be scalar or array (if operator is e.g. "IN (?)")
* @param string $operator  SQL comparison operator, e.g. =, <, >= etc. You can use "?" as placeholder, e.g. "IN (?)"
* @return $this
*/
public function filterByKeystring ($data, $operator = '='): static
{
	$this->getClass()->getFieldDefinition("keystring")->addListingFilter($this, $data, $operator);
	return $this;
}

/**
* Filter by sharedSecret (Shared Secret)
* @param string|int|float|array|Model\Element\ElementInterface $data  comparison data, can be scalar or array (if operator is e.g. "IN (?)")
* @param string $operator  SQL comparison operator, e.g. =, <, >= etc. You can use "?" as placeholder, e.g. "IN (?)"
* @return $this
*/
public function filterBySharedSecret ($data, $operator = '='): static
{
	$this->getClass()->getFieldDefinition("sharedSecret")->addListingFilter($this, $data, $operator);
	return $this;
}

/**
* Filter by trendyolApiKey (Api Key)
* @param string|int|float|array|Model\Element\ElementInterface $data  comparison data, can be scalar or array (if operator is e.g. "IN (?)")
* @param string $operator  SQL comparison operator, e.g. =, <, >= etc. You can use "?" as placeholder, e.g. "IN (?)"
* @return $this
*/
public function filterByTrendyolApiKey ($data, $operator = '='): static
{
	$this->getClass()->getFieldDefinition("trendyolApiKey")->addListingFilter($this, $data, $operator);
	return $this;
}

/**
* Filter by trendyolSellerId (Seller Id)
* @param string|int|float|array|Model\Element\ElementInterface $data  comparison data, can be scalar or array (if operator is e.g. "IN (?)")
* @param string $operator  SQL comparison operator, e.g. =, <, >= etc. You can use "?" as placeholder, e.g. "IN (?)"
* @return $this
*/
public function filterByTrendyolSellerId ($data, $operator = '='): static
{
	$this->getClass()->getFieldDefinition("trendyolSellerId")->addListingFilter($this, $data, $operator);
	return $this;
}

/**
* Filter by trendyolApiSecret (Api Secret)
* @param string|int|float|array|Model\Element\ElementInterface $data  comparison data, can be scalar or array (if operator is e.g. "IN (?)")
* @param string $operator  SQL comparison operator, e.g. =, <, >= etc. You can use "?" as placeholder, e.g. "IN (?)"
* @return $this
*/
public function filterByTrendyolApiSecret ($data, $operator = '='): static
{
	$this->getClass()->getFieldDefinition("trendyolApiSecret")->addListingFilter($this, $data, $operator);
	return $this;
}

/**
* Filter by trendyolToken (Token)
* @param string|int|float|array|Model\Element\ElementInterface $data  comparison data, can be scalar or array (if operator is e.g. "IN (?)")
* @param string $operator  SQL comparison operator, e.g. =, <, >= etc. You can use "?" as placeholder, e.g. "IN (?)"
* @return $this
*/
public function filterByTrendyolToken ($data, $operator = '='): static
{
	$this->getClass()->getFieldDefinition("trendyolToken")->addListingFilter($this, $data, $operator);
	return $this;
}

/**
* Filter by bolClientId (Bol Client Id)
* @param string|int|float|array|Model\Element\ElementInterface $data  comparison data, can be scalar or array (if operator is e.g. "IN (?)")
* @param string $operator  SQL comparison operator, e.g. =, <, >= etc. You can use "?" as placeholder, e.g. "IN (?)"
* @return $this
*/
public function filterByBolClientId ($data, $operator = '='): static
{
	$this->getClass()->getFieldDefinition("bolClientId")->addListingFilter($this, $data, $operator);
	return $this;
}

/**
* Filter by bolSecret (Bol Secret)
* @param string|int|float|array|Model\Element\ElementInterface $data  comparison data, can be scalar or array (if operator is e.g. "IN (?)")
* @param string $operator  SQL comparison operator, e.g. =, <, >= etc. You can use "?" as placeholder, e.g. "IN (?)"
* @return $this
*/
public function filterByBolSecret ($data, $operator = '='): static
{
	$this->getClass()->getFieldDefinition("bolSecret")->addListingFilter($this, $data, $operator);
	return $this;
}

/**
* Filter by bolJwtToken (Jwt Token)
* @param string|int|float|array|Model\Element\ElementInterface $data  comparison data, can be scalar or array (if operator is e.g. "IN (?)")
* @param string $operator  SQL comparison operator, e.g. =, <, >= etc. You can use "?" as placeholder, e.g. "IN (?)"
* @return $this
*/
public function filterByBolJwtToken ($data, $operator = '='): static
{
	$this->getClass()->getFieldDefinition("bolJwtToken")->addListingFilter($this, $data, $operator);
	return $this;
}

/**
* Filter by sellerId (Satici Id)
* @param string|int|float|array|Model\Element\ElementInterface $data  comparison data, can be scalar or array (if operator is e.g. "IN (?)")
* @param string $operator  SQL comparison operator, e.g. =, <, >= etc. You can use "?" as placeholder, e.g. "IN (?)"
* @return $this
*/
public function filterBySellerId ($data, $operator = '='): static
{
	$this->getClass()->getFieldDefinition("sellerId")->addListingFilter($this, $data, $operator);
	return $this;
}

/**
* Filter by serviceKey (Servis Anahtarı)
* @param string|int|float|array|Model\Element\ElementInterface $data  comparison data, can be scalar or array (if operator is e.g. "IN (?)")
* @param string $operator  SQL comparison operator, e.g. =, <, >= etc. You can use "?" as placeholder, e.g. "IN (?)"
* @return $this
*/
public function filterByServiceKey ($data, $operator = '='): static
{
	$this->getClass()->getFieldDefinition("serviceKey")->addListingFilter($this, $data, $operator);
	return $this;
}

/**
* Filter by takealotKey (Takealot Key)
* @param string|int|float|array|Model\Element\ElementInterface $data  comparison data, can be scalar or array (if operator is e.g. "IN (?)")
* @param string $operator  SQL comparison operator, e.g. =, <, >= etc. You can use "?" as placeholder, e.g. "IN (?)"
* @return $this
*/
public function filterByTakealotKey ($data, $operator = '='): static
{
	$this->getClass()->getFieldDefinition("takealotKey")->addListingFilter($this, $data, $operator);
	return $this;
}

/**
* Filter by wallmartSecretKey (Wallmart Secret Key)
* @param string|int|float|array|Model\Element\ElementInterface $data  comparison data, can be scalar or array (if operator is e.g. "IN (?)")
* @param string $operator  SQL comparison operator, e.g. =, <, >= etc. You can use "?" as placeholder, e.g. "IN (?)"
* @return $this
*/
public function filterByWallmartSecretKey ($data, $operator = '='): static
{
	$this->getClass()->getFieldDefinition("wallmartSecretKey")->addListingFilter($this, $data, $operator);
	return $this;
}

/**
* Filter by wallmartClientId (Wallmart Client Id)
* @param string|int|float|array|Model\Element\ElementInterface $data  comparison data, can be scalar or array (if operator is e.g. "IN (?)")
* @param string $operator  SQL comparison operator, e.g. =, <, >= etc. You can use "?" as placeholder, e.g. "IN (?)"
* @return $this
*/
public function filterByWallmartClientId ($data, $operator = '='): static
{
	$this->getClass()->getFieldDefinition("wallmartClientId")->addListingFilter($this, $data, $operator);
	return $this;
}

/**
* Filter by wallmartAccessToken (Wallmart Access Token)
* @param string|int|float|array|Model\Element\ElementInterface $data  comparison data, can be scalar or array (if operator is e.g. "IN (?)")
* @param string $operator  SQL comparison operator, e.g. =, <, >= etc. You can use "?" as placeholder, e.g. "IN (?)"
* @return $this
*/
public function filterByWallmartAccessToken ($data, $operator = '='): static
{
	$this->getClass()->getFieldDefinition("wallmartAccessToken")->addListingFilter($this, $data, $operator);
	return $this;
}

/**
* Filter by ciceksepetiApiKey (Ciceksepeti Api Key)
* @param string|int|float|array|Model\Element\ElementInterface $data  comparison data, can be scalar or array (if operator is e.g. "IN (?)")
* @param string $operator  SQL comparison operator, e.g. =, <, >= etc. You can use "?" as placeholder, e.g. "IN (?)"
* @return $this
*/
public function filterByCiceksepetiApiKey ($data, $operator = '='): static
{
	$this->getClass()->getFieldDefinition("ciceksepetiApiKey")->addListingFilter($this, $data, $operator);
	return $this;
}

/**
* Filter by ciceksepetiSellerId (Ciceksepeti Seller Id)
* @param string|int|float|array|Model\Element\ElementInterface $data  comparison data, can be scalar or array (if operator is e.g. "IN (?)")
* @param string $operator  SQL comparison operator, e.g. =, <, >= etc. You can use "?" as placeholder, e.g. "IN (?)"
* @return $this
*/
public function filterByCiceksepetiSellerId ($data, $operator = '='): static
{
	$this->getClass()->getFieldDefinition("ciceksepetiSellerId")->addListingFilter($this, $data, $operator);
	return $this;
}

/**
* Filter by ozonClientId (Ozon Client Id)
* @param string|int|float|array|Model\Element\ElementInterface $data  comparison data, can be scalar or array (if operator is e.g. "IN (?)")
* @param string $operator  SQL comparison operator, e.g. =, <, >= etc. You can use "?" as placeholder, e.g. "IN (?)"
* @return $this
*/
public function filterByOzonClientId ($data, $operator = '='): static
{
	$this->getClass()->getFieldDefinition("ozonClientId")->addListingFilter($this, $data, $operator);
	return $this;
}

/**
* Filter by ozonApiKey (Ozon Api Key)
* @param string|int|float|array|Model\Element\ElementInterface $data  comparison data, can be scalar or array (if operator is e.g. "IN (?)")
* @param string $operator  SQL comparison operator, e.g. =, <, >= etc. You can use "?" as placeholder, e.g. "IN (?)"
* @return $this
*/
public function filterByOzonApiKey ($data, $operator = '='): static
{
	$this->getClass()->getFieldDefinition("ozonApiKey")->addListingFilter($this, $data, $operator);
	return $this;
}

/**
* Filter by wayfairClientIdProd (Wayfair Client Id Prod)
* @param string|int|float|array|Model\Element\ElementInterface $data  comparison data, can be scalar or array (if operator is e.g. "IN (?)")
* @param string $operator  SQL comparison operator, e.g. =, <, >= etc. You can use "?" as placeholder, e.g. "IN (?)"
* @return $this
*/
public function filterByWayfairClientIdProd ($data, $operator = '='): static
{
	$this->getClass()->getFieldDefinition("wayfairClientIdProd")->addListingFilter($this, $data, $operator);
	return $this;
}

/**
* Filter by wayfairSecretKeyProd (Wayfair Secret Key Prod)
* @param string|int|float|array|Model\Element\ElementInterface $data  comparison data, can be scalar or array (if operator is e.g. "IN (?)")
* @param string $operator  SQL comparison operator, e.g. =, <, >= etc. You can use "?" as placeholder, e.g. "IN (?)"
* @return $this
*/
public function filterByWayfairSecretKeyProd ($data, $operator = '='): static
{
	$this->getClass()->getFieldDefinition("wayfairSecretKeyProd")->addListingFilter($this, $data, $operator);
	return $this;
}

/**
* Filter by wayfairAccessTokenProd (Wayfair Access Token Prod)
* @param string|int|float|array|Model\Element\ElementInterface $data  comparison data, can be scalar or array (if operator is e.g. "IN (?)")
* @param string $operator  SQL comparison operator, e.g. =, <, >= etc. You can use "?" as placeholder, e.g. "IN (?)"
* @return $this
*/
public function filterByWayfairAccessTokenProd ($data, $operator = '='): static
{
	$this->getClass()->getFieldDefinition("wayfairAccessTokenProd")->addListingFilter($this, $data, $operator);
	return $this;
}

/**
* Filter by ebayClientId (Ebay Client Id)
* @param string|int|float|array|Model\Element\ElementInterface $data  comparison data, can be scalar or array (if operator is e.g. "IN (?)")
* @param string $operator  SQL comparison operator, e.g. =, <, >= etc. You can use "?" as placeholder, e.g. "IN (?)"
* @return $this
*/
public function filterByEbayClientId ($data, $operator = '='): static
{
	$this->getClass()->getFieldDefinition("ebayClientId")->addListingFilter($this, $data, $operator);
	return $this;
}

/**
* Filter by ebayClientSecret (Ebay Client Secret)
* @param string|int|float|array|Model\Element\ElementInterface $data  comparison data, can be scalar or array (if operator is e.g. "IN (?)")
* @param string $operator  SQL comparison operator, e.g. =, <, >= etc. You can use "?" as placeholder, e.g. "IN (?)"
* @return $this
*/
public function filterByEbayClientSecret ($data, $operator = '='): static
{
	$this->getClass()->getFieldDefinition("ebayClientSecret")->addListingFilter($this, $data, $operator);
	return $this;
}

/**
* Filter by ebayRefreshToken (Ebay Refresh Token)
* @param string|int|float|array|Model\Element\ElementInterface $data  comparison data, can be scalar or array (if operator is e.g. "IN (?)")
* @param string $operator  SQL comparison operator, e.g. =, <, >= etc. You can use "?" as placeholder, e.g. "IN (?)"
* @return $this
*/
public function filterByEbayRefreshToken ($data, $operator = '='): static
{
	$this->getClass()->getFieldDefinition("ebayRefreshToken")->addListingFilter($this, $data, $operator);
	return $this;
}

/**
* Filter by ebayAuthCode (Ebay Auth Code)
* @param string|int|float|array|Model\Element\ElementInterface $data  comparison data, can be scalar or array (if operator is e.g. "IN (?)")
* @param string $operator  SQL comparison operator, e.g. =, <, >= etc. You can use "?" as placeholder, e.g. "IN (?)"
* @return $this
*/
public function filterByEbayAuthCode ($data, $operator = '='): static
{
	$this->getClass()->getFieldDefinition("ebayAuthCode")->addListingFilter($this, $data, $operator);
	return $this;
}

/**
* Filter by ebayRuName (Ebay Ru Name)
* @param string|int|float|array|Model\Element\ElementInterface $data  comparison data, can be scalar or array (if operator is e.g. "IN (?)")
* @param string $operator  SQL comparison operator, e.g. =, <, >= etc. You can use "?" as placeholder, e.g. "IN (?)"
* @return $this
*/
public function filterByEbayRuName ($data, $operator = '='): static
{
	$this->getClass()->getFieldDefinition("ebayRuName")->addListingFilter($this, $data, $operator);
	return $this;
}

/**
* Filter by ebayAccessToken (Ebay Access Token)
* @param string|int|float|array|Model\Element\ElementInterface $data  comparison data, can be scalar or array (if operator is e.g. "IN (?)")
* @param string $operator  SQL comparison operator, e.g. =, <, >= etc. You can use "?" as placeholder, e.g. "IN (?)"
* @return $this
*/
public function filterByEbayAccessToken ($data, $operator = '='): static
{
	$this->getClass()->getFieldDefinition("ebayAccessToken")->addListingFilter($this, $data, $operator);
	return $this;
}



}
