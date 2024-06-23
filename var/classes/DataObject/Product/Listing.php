<?php

namespace Pimcore\Model\DataObject\Product;

use Pimcore\Model;
use Pimcore\Model\DataObject;

/**
 * @method DataObject\Product|false current()
 * @method DataObject\Product[] load()
 * @method DataObject\Product[] getData()
 * @method DataObject\Product[] getObjects()
 */

class Listing extends DataObject\Listing\Concrete
{
protected $classId = "product";
protected $className = "Product";


/**
* Filter by iwasku (IWASKU)
* @param string|int|float|array|Model\Element\ElementInterface $data  comparison data, can be scalar or array (if operator is e.g. "IN (?)")
* @param string $operator  SQL comparison operator, e.g. =, <, >= etc. You can use "?" as placeholder, e.g. "IN (?)"
* @return $this
*/
public function filterByIwasku ($data, $operator = '='): static
{
	$this->getClass()->getFieldDefinition("iwasku")->addListingFilter($this, $data, $operator);
	return $this;
}

/**
* Filter by iwaskuActive (Aktif)
* @param string|int|float|array|Model\Element\ElementInterface $data  comparison data, can be scalar or array (if operator is e.g. "IN (?)")
* @param string $operator  SQL comparison operator, e.g. =, <, >= etc. You can use "?" as placeholder, e.g. "IN (?)"
* @return $this
*/
public function filterByIwaskuActive ($data, $operator = '='): static
{
	$this->getClass()->getFieldDefinition("iwaskuActive")->addListingFilter($this, $data, $operator);
	return $this;
}

/**
* Filter by productCode (Ürün Kodu)
* @param string|int|float|array|Model\Element\ElementInterface $data  comparison data, can be scalar or array (if operator is e.g. "IN (?)")
* @param string $operator  SQL comparison operator, e.g. =, <, >= etc. You can use "?" as placeholder, e.g. "IN (?)"
* @return $this
*/
public function filterByProductCode ($data, $operator = '='): static
{
	$this->getClass()->getFieldDefinition("productCode")->addListingFilter($this, $data, $operator);
	return $this;
}

/**
* Filter by productClass (Ürün Sınıfı)
* @param string|int|float|array|Model\Element\ElementInterface $data  comparison data, can be scalar or array (if operator is e.g. "IN (?)")
* @param string $operator  SQL comparison operator, e.g. =, <, >= etc. You can use "?" as placeholder, e.g. "IN (?)"
* @return $this
*/
public function filterByProductClass ($data, $operator = '='): static
{
	$this->getClass()->getFieldDefinition("productClass")->addListingFilter($this, $data, $operator);
	return $this;
}

/**
* Filter by name (Ürün Adı)
* @param string|int|float|array|Model\Element\ElementInterface $data  comparison data, can be scalar or array (if operator is e.g. "IN (?)")
* @param string $operator  SQL comparison operator, e.g. =, <, >= etc. You can use "?" as placeholder, e.g. "IN (?)"
* @return $this
*/
public function filterByName ($data, $operator = '='): static
{
	$this->getClass()->getFieldDefinition("name")->addListingFilter($this, $data, $operator);
	return $this;
}

/**
* Filter by nameEnglish (Ürün Adı (İngilizce))
* @param string|int|float|array|Model\Element\ElementInterface $data  comparison data, can be scalar or array (if operator is e.g. "IN (?)")
* @param string $operator  SQL comparison operator, e.g. =, <, >= etc. You can use "?" as placeholder, e.g. "IN (?)"
* @return $this
*/
public function filterByNameEnglish ($data, $operator = '='): static
{
	$this->getClass()->getFieldDefinition("nameEnglish")->addListingFilter($this, $data, $operator);
	return $this;
}

/**
* Filter by description (Ürün Tanımı)
* @param string|int|float|array|Model\Element\ElementInterface $data  comparison data, can be scalar or array (if operator is e.g. "IN (?)")
* @param string $operator  SQL comparison operator, e.g. =, <, >= etc. You can use "?" as placeholder, e.g. "IN (?)"
* @return $this
*/
public function filterByDescription ($data, $operator = '='): static
{
	$this->getClass()->getFieldDefinition("description")->addListingFilter($this, $data, $operator);
	return $this;
}

/**
* Filter by variationSize (Varyant Ebatı)
* @param string|int|float|array|Model\Element\ElementInterface $data  comparison data, can be scalar or array (if operator is e.g. "IN (?)")
* @param string $operator  SQL comparison operator, e.g. =, <, >= etc. You can use "?" as placeholder, e.g. "IN (?)"
* @return $this
*/
public function filterByVariationSize ($data, $operator = '='): static
{
	$this->getClass()->getFieldDefinition("variationSize")->addListingFilter($this, $data, $operator);
	return $this;
}

/**
* Filter by variationColor (Variant Rengi)
* @param string|int|float|array|Model\Element\ElementInterface $data  comparison data, can be scalar or array (if operator is e.g. "IN (?)")
* @param string $operator  SQL comparison operator, e.g. =, <, >= etc. You can use "?" as placeholder, e.g. "IN (?)"
* @return $this
*/
public function filterByVariationColor ($data, $operator = '='): static
{
	$this->getClass()->getFieldDefinition("variationColor")->addListingFilter($this, $data, $operator);
	return $this;
}

/**
* Filter by productWidth (En)
* @param string|int|float|array|Model\Element\ElementInterface $data  comparison data, can be scalar or array (if operator is e.g. "IN (?)")
* @param string $operator  SQL comparison operator, e.g. =, <, >= etc. You can use "?" as placeholder, e.g. "IN (?)"
* @return $this
*/
public function filterByProductWidth ($data, $operator = '='): static
{
	$this->getClass()->getFieldDefinition("productWidth")->addListingFilter($this, $data, $operator);
	return $this;
}

/**
* Filter by productHeight (Boy)
* @param string|int|float|array|Model\Element\ElementInterface $data  comparison data, can be scalar or array (if operator is e.g. "IN (?)")
* @param string $operator  SQL comparison operator, e.g. =, <, >= etc. You can use "?" as placeholder, e.g. "IN (?)"
* @return $this
*/
public function filterByProductHeight ($data, $operator = '='): static
{
	$this->getClass()->getFieldDefinition("productHeight")->addListingFilter($this, $data, $operator);
	return $this;
}

/**
* Filter by productDepth (Yükseklik)
* @param string|int|float|array|Model\Element\ElementInterface $data  comparison data, can be scalar or array (if operator is e.g. "IN (?)")
* @param string $operator  SQL comparison operator, e.g. =, <, >= etc. You can use "?" as placeholder, e.g. "IN (?)"
* @return $this
*/
public function filterByProductDepth ($data, $operator = '='): static
{
	$this->getClass()->getFieldDefinition("productDepth")->addListingFilter($this, $data, $operator);
	return $this;
}

/**
* Filter by productWeight (Ağırlık)
* @param string|int|float|array|Model\Element\ElementInterface $data  comparison data, can be scalar or array (if operator is e.g. "IN (?)")
* @param string $operator  SQL comparison operator, e.g. =, <, >= etc. You can use "?" as placeholder, e.g. "IN (?)"
* @return $this
*/
public function filterByProductWeight ($data, $operator = '='): static
{
	$this->getClass()->getFieldDefinition("productWeight")->addListingFilter($this, $data, $operator);
	return $this;
}

/**
* Filter by packageWidth (En)
* @param string|int|float|array|Model\Element\ElementInterface $data  comparison data, can be scalar or array (if operator is e.g. "IN (?)")
* @param string $operator  SQL comparison operator, e.g. =, <, >= etc. You can use "?" as placeholder, e.g. "IN (?)"
* @return $this
*/
public function filterByPackageWidth ($data, $operator = '='): static
{
	$this->getClass()->getFieldDefinition("packageWidth")->addListingFilter($this, $data, $operator);
	return $this;
}

/**
* Filter by packegeHeight (Boy)
* @param string|int|float|array|Model\Element\ElementInterface $data  comparison data, can be scalar or array (if operator is e.g. "IN (?)")
* @param string $operator  SQL comparison operator, e.g. =, <, >= etc. You can use "?" as placeholder, e.g. "IN (?)"
* @return $this
*/
public function filterByPackegeHeight ($data, $operator = '='): static
{
	$this->getClass()->getFieldDefinition("packegeHeight")->addListingFilter($this, $data, $operator);
	return $this;
}

/**
* Filter by packageDepth (Yükseklik)
* @param string|int|float|array|Model\Element\ElementInterface $data  comparison data, can be scalar or array (if operator is e.g. "IN (?)")
* @param string $operator  SQL comparison operator, e.g. =, <, >= etc. You can use "?" as placeholder, e.g. "IN (?)"
* @return $this
*/
public function filterByPackageDepth ($data, $operator = '='): static
{
	$this->getClass()->getFieldDefinition("packageDepth")->addListingFilter($this, $data, $operator);
	return $this;
}

/**
* Filter by packageWeight (Ağırlık)
* @param string|int|float|array|Model\Element\ElementInterface $data  comparison data, can be scalar or array (if operator is e.g. "IN (?)")
* @param string $operator  SQL comparison operator, e.g. =, <, >= etc. You can use "?" as placeholder, e.g. "IN (?)"
* @return $this
*/
public function filterByPackageWeight ($data, $operator = '='): static
{
	$this->getClass()->getFieldDefinition("packageWeight")->addListingFilter($this, $data, $operator);
	return $this;
}

/**
* Filter by seoTitle (SEO Başlığı (&lt;h1&gt;))
* @param string|int|float|array|Model\Element\ElementInterface $data  comparison data, can be scalar or array (if operator is e.g. "IN (?)")
* @param string $operator  SQL comparison operator, e.g. =, <, >= etc. You can use "?" as placeholder, e.g. "IN (?)"
* @return $this
*/
public function filterBySeoTitle ($data, $operator = '='): static
{
	$this->getClass()->getFieldDefinition("seoTitle")->addListingFilter($this, $data, $operator);
	return $this;
}

/**
* Filter by seoDescription (SEO Açıklama)
* @param string|int|float|array|Model\Element\ElementInterface $data  comparison data, can be scalar or array (if operator is e.g. "IN (?)")
* @param string $operator  SQL comparison operator, e.g. =, <, >= etc. You can use "?" as placeholder, e.g. "IN (?)"
* @return $this
*/
public function filterBySeoDescription ($data, $operator = '='): static
{
	$this->getClass()->getFieldDefinition("seoDescription")->addListingFilter($this, $data, $operator);
	return $this;
}

/**
* Filter by bundleItems (Set İçeriği)
* @param mixed $data
* @param string $operator SQL comparison operator, e.g. =, <, >= etc. You can use "?" as placeholder, e.g. "IN (?)"
* @return $this
*/
public function filterByBundleItems ($data, $operator = '='): static
{
	$this->getClass()->getFieldDefinition("bundleItems")->addListingFilter($this, $data, $operator);
	return $this;
}

/**
* Filter by marketingMaterials (Marketing Materials)
* @param mixed $data
* @param string $operator SQL comparison operator, e.g. =, <, >= etc. You can use "?" as placeholder, e.g. "IN (?)"
* @return $this
*/
public function filterByMarketingMaterials ($data, $operator = '='): static
{
	$this->getClass()->getFieldDefinition("marketingMaterials")->addListingFilter($this, $data, $operator);
	return $this;
}

/**
* Filter by unitCost (Birim Maliyet)
* @param string|int|float|array|Model\Element\ElementInterface $data  comparison data, can be scalar or array (if operator is e.g. "IN (?)")
* @param string $operator  SQL comparison operator, e.g. =, <, >= etc. You can use "?" as placeholder, e.g. "IN (?)"
* @return $this
*/
public function filterByUnitCost ($data, $operator = '='): static
{
	$this->getClass()->getFieldDefinition("unitCost")->addListingFilter($this, $data, $operator);
	return $this;
}

/**
* Filter by productCosts (Ortak Maliyetler)
* @param mixed $data
* @param string $operator SQL comparison operator, e.g. =, <, >= etc. You can use "?" as placeholder, e.g. "IN (?)"
* @return $this
*/
public function filterByProductCosts ($data, $operator = '='): static
{
	$this->getClass()->getFieldDefinition("productCosts")->addListingFilter($this, $data, $operator);
	return $this;
}

/**
* Filter by colorCosts (Renk Maliyetleri)
* @param mixed $data
* @param string $operator SQL comparison operator, e.g. =, <, >= etc. You can use "?" as placeholder, e.g. "IN (?)"
* @return $this
*/
public function filterByColorCosts ($data, $operator = '='): static
{
	$this->getClass()->getFieldDefinition("colorCosts")->addListingFilter($this, $data, $operator);
	return $this;
}

/**
* Filter by sizeCosts (Ebat Maliyetleri)
* @param mixed $data
* @param string $operator SQL comparison operator, e.g. =, <, >= etc. You can use "?" as placeholder, e.g. "IN (?)"
* @return $this
*/
public function filterBySizeCosts ($data, $operator = '='): static
{
	$this->getClass()->getFieldDefinition("sizeCosts")->addListingFilter($this, $data, $operator);
	return $this;
}



}
