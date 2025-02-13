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
* Filter by image (Image)
* @param string|int|float|array|Model\Element\ElementInterface $data  comparison data, can be scalar or array (if operator is e.g. "IN (?)")
* @param string $operator  SQL comparison operator, e.g. =, <, >= etc. You can use "?" as placeholder, e.g. "IN (?)"
* @return $this
*/
public function filterByImage ($data, $operator = '='): static
{
	$this->getClass()->getFieldDefinition("image")->addListingFilter($this, $data, $operator);
	return $this;
}

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
* Filter by wisersellId (Wisersell Id)
* @param string|int|float|array|Model\Element\ElementInterface $data  comparison data, can be scalar or array (if operator is e.g. "IN (?)")
* @param string $operator  SQL comparison operator, e.g. =, <, >= etc. You can use "?" as placeholder, e.g. "IN (?)"
* @return $this
*/
public function filterByWisersellId ($data, $operator = '='): static
{
	$this->getClass()->getFieldDefinition("wisersellId")->addListingFilter($this, $data, $operator);
	return $this;
}

/**
* Filter by eanGtin (EAN/GTIN)
* @param string|int|float|array|Model\Element\ElementInterface $data  comparison data, can be scalar or array (if operator is e.g. "IN (?)")
* @param string $operator  SQL comparison operator, e.g. =, <, >= etc. You can use "?" as placeholder, e.g. "IN (?)"
* @return $this
*/
public function filterByEanGtin ($data, $operator = '='): static
{
	$this->getClass()->getFieldDefinition("eanGtin")->addListingFilter($this, $data, $operator);
	return $this;
}

/**
* Filter by requireEan (Ean Al)
* @param string|int|float|array|Model\Element\ElementInterface $data  comparison data, can be scalar or array (if operator is e.g. "IN (?)")
* @param string $operator  SQL comparison operator, e.g. =, <, >= etc. You can use "?" as placeholder, e.g. "IN (?)"
* @return $this
*/
public function filterByRequireEan ($data, $operator = '='): static
{
	$this->getClass()->getFieldDefinition("requireEan")->addListingFilter($this, $data, $operator);
	return $this;
}

/**
* Filter by productIdentifier (Ürün Tanıtıcı Adı)
* @param string|int|float|array|Model\Element\ElementInterface $data  comparison data, can be scalar or array (if operator is e.g. "IN (?)")
* @param string $operator  SQL comparison operator, e.g. =, <, >= etc. You can use "?" as placeholder, e.g. "IN (?)"
* @return $this
*/
public function filterByProductIdentifier ($data, $operator = '='): static
{
	$this->getClass()->getFieldDefinition("productIdentifier")->addListingFilter($this, $data, $operator);
	return $this;
}

/**
* Filter by productCategory (Kategori)
* @param string|int|float|array|Model\Element\ElementInterface $data  comparison data, can be scalar or array (if operator is e.g. "IN (?)")
* @param string $operator  SQL comparison operator, e.g. =, <, >= etc. You can use "?" as placeholder, e.g. "IN (?)"
* @return $this
*/
public function filterByProductCategory ($data, $operator = '='): static
{
	$this->getClass()->getFieldDefinition("productCategory")->addListingFilter($this, $data, $operator);
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
* Filter by variationSizeList (Variation Size List)
* @param string|int|float|array|Model\Element\ElementInterface $data  comparison data, can be scalar or array (if operator is e.g. "IN (?)")
* @param string $operator  SQL comparison operator, e.g. =, <, >= etc. You can use "?" as placeholder, e.g. "IN (?)"
* @return $this
*/
public function filterByVariationSizeList ($data, $operator = '='): static
{
	$this->getClass()->getFieldDefinition("variationSizeList")->addListingFilter($this, $data, $operator);
	return $this;
}

/**
* Filter by variationColorList (Variation Color List)
* @param string|int|float|array|Model\Element\ElementInterface $data  comparison data, can be scalar or array (if operator is e.g. "IN (?)")
* @param string $operator  SQL comparison operator, e.g. =, <, >= etc. You can use "?" as placeholder, e.g. "IN (?)"
* @return $this
*/
public function filterByVariationColorList ($data, $operator = '='): static
{
	$this->getClass()->getFieldDefinition("variationColorList")->addListingFilter($this, $data, $operator);
	return $this;
}

/**
* Filter by fixVariations (Varyasyonları Düzenle)
* @param string|int|float|array|Model\Element\ElementInterface $data  comparison data, can be scalar or array (if operator is e.g. "IN (?)")
* @param string $operator  SQL comparison operator, e.g. =, <, >= etc. You can use "?" as placeholder, e.g. "IN (?)"
* @return $this
*/
public function filterByFixVariations ($data, $operator = '='): static
{
	$this->getClass()->getFieldDefinition("fixVariations")->addListingFilter($this, $data, $operator);
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
* Filter by sticker4x6iwasku (Etiket 4x6 IWASKU)
* @param mixed $data
* @param string $operator SQL comparison operator, e.g. =, <, >= etc. You can use "?" as placeholder, e.g. "IN (?)"
* @return $this
*/
public function filterBySticker4x6iwasku ($data, $operator = '='): static
{
	$this->getClass()->getFieldDefinition("sticker4x6iwasku")->addListingFilter($this, $data, $operator);
	return $this;
}

/**
* Filter by wisersellJson (Wisersell Json)
* @param string|int|float|array|Model\Element\ElementInterface $data  comparison data, can be scalar or array (if operator is e.g. "IN (?)")
* @param string $operator  SQL comparison operator, e.g. =, <, >= etc. You can use "?" as placeholder, e.g. "IN (?)"
* @return $this
*/
public function filterByWisersellJson ($data, $operator = '='): static
{
	$this->getClass()->getFieldDefinition("wisersellJson")->addListingFilter($this, $data, $operator);
	return $this;
}

/**
* Filter by sticker4x6eu (Sticker4x6eu)
* @param mixed $data
* @param string $operator SQL comparison operator, e.g. =, <, >= etc. You can use "?" as placeholder, e.g. "IN (?)"
* @return $this
*/
public function filterBySticker4x6eu ($data, $operator = '='): static
{
	$this->getClass()->getFieldDefinition("sticker4x6eu")->addListingFilter($this, $data, $operator);
	return $this;
}

/**
* Filter by requiresIwasku (Requires Iwasku)
* @param string|int|float|array|Model\Element\ElementInterface $data  comparison data, can be scalar or array (if operator is e.g. "IN (?)")
* @param string $operator  SQL comparison operator, e.g. =, <, >= etc. You can use "?" as placeholder, e.g. "IN (?)"
* @return $this
*/
public function filterByRequiresIwasku ($data, $operator = '='): static
{
	$this->getClass()->getFieldDefinition("requiresIwasku")->addListingFilter($this, $data, $operator);
	return $this;
}

/**
* Filter by variantDescription (Variant Açıklama)
* @param string|int|float|array|Model\Element\ElementInterface $data  comparison data, can be scalar or array (if operator is e.g. "IN (?)")
* @param string $operator  SQL comparison operator, e.g. =, <, >= etc. You can use "?" as placeholder, e.g. "IN (?)"
* @return $this
*/
public function filterByVariantDescription ($data, $operator = '='): static
{
	$this->getClass()->getFieldDefinition("variantDescription")->addListingFilter($this, $data, $operator);
	return $this;
}

/**
* Filter by technicals (Teknik Doküman ve Kılavuzlar)
* @param mixed $data
* @param string $operator SQL comparison operator, e.g. =, <, >= etc. You can use "?" as placeholder, e.g. "IN (?)"
* @return $this
*/
public function filterByTechnicals ($data, $operator = '='): static
{
	$this->getClass()->getFieldDefinition("technicals")->addListingFilter($this, $data, $operator);
	return $this;
}

/**
* Filter by dimensionsPostponed (Ertelendi)
* @param string|int|float|array|Model\Element\ElementInterface $data  comparison data, can be scalar or array (if operator is e.g. "IN (?)")
* @param string $operator  SQL comparison operator, e.g. =, <, >= etc. You can use "?" as placeholder, e.g. "IN (?)"
* @return $this
*/
public function filterByDimensionsPostponed ($data, $operator = '='): static
{
	$this->getClass()->getFieldDefinition("dimensionsPostponed")->addListingFilter($this, $data, $operator);
	return $this;
}

/**
* Filter by productDimension1 (En)
* @param string|int|float|array|Model\Element\ElementInterface $data  comparison data, can be scalar or array (if operator is e.g. "IN (?)")
* @param string $operator  SQL comparison operator, e.g. =, <, >= etc. You can use "?" as placeholder, e.g. "IN (?)"
* @return $this
*/
public function filterByProductDimension1 ($data, $operator = '='): static
{
	$this->getClass()->getFieldDefinition("productDimension1")->addListingFilter($this, $data, $operator);
	return $this;
}

/**
* Filter by productDimension2 (Boy)
* @param string|int|float|array|Model\Element\ElementInterface $data  comparison data, can be scalar or array (if operator is e.g. "IN (?)")
* @param string $operator  SQL comparison operator, e.g. =, <, >= etc. You can use "?" as placeholder, e.g. "IN (?)"
* @return $this
*/
public function filterByProductDimension2 ($data, $operator = '='): static
{
	$this->getClass()->getFieldDefinition("productDimension2")->addListingFilter($this, $data, $operator);
	return $this;
}

/**
* Filter by productDimension3 (Yükseklik)
* @param string|int|float|array|Model\Element\ElementInterface $data  comparison data, can be scalar or array (if operator is e.g. "IN (?)")
* @param string $operator  SQL comparison operator, e.g. =, <, >= etc. You can use "?" as placeholder, e.g. "IN (?)"
* @return $this
*/
public function filterByProductDimension3 ($data, $operator = '='): static
{
	$this->getClass()->getFieldDefinition("productDimension3")->addListingFilter($this, $data, $operator);
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
* Filter by packageDimension1 (En)
* @param string|int|float|array|Model\Element\ElementInterface $data  comparison data, can be scalar or array (if operator is e.g. "IN (?)")
* @param string $operator  SQL comparison operator, e.g. =, <, >= etc. You can use "?" as placeholder, e.g. "IN (?)"
* @return $this
*/
public function filterByPackageDimension1 ($data, $operator = '='): static
{
	$this->getClass()->getFieldDefinition("packageDimension1")->addListingFilter($this, $data, $operator);
	return $this;
}

/**
* Filter by packageDimension2 (Boy)
* @param string|int|float|array|Model\Element\ElementInterface $data  comparison data, can be scalar or array (if operator is e.g. "IN (?)")
* @param string $operator  SQL comparison operator, e.g. =, <, >= etc. You can use "?" as placeholder, e.g. "IN (?)"
* @return $this
*/
public function filterByPackageDimension2 ($data, $operator = '='): static
{
	$this->getClass()->getFieldDefinition("packageDimension2")->addListingFilter($this, $data, $operator);
	return $this;
}

/**
* Filter by packageDimension3 (Yükseklik)
* @param string|int|float|array|Model\Element\ElementInterface $data  comparison data, can be scalar or array (if operator is e.g. "IN (?)")
* @param string $operator  SQL comparison operator, e.g. =, <, >= etc. You can use "?" as placeholder, e.g. "IN (?)"
* @return $this
*/
public function filterByPackageDimension3 ($data, $operator = '='): static
{
	$this->getClass()->getFieldDefinition("packageDimension3")->addListingFilter($this, $data, $operator);
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
* Filter by boxDimension1 (En)
* @param string|int|float|array|Model\Element\ElementInterface $data  comparison data, can be scalar or array (if operator is e.g. "IN (?)")
* @param string $operator  SQL comparison operator, e.g. =, <, >= etc. You can use "?" as placeholder, e.g. "IN (?)"
* @return $this
*/
public function filterByBoxDimension1 ($data, $operator = '='): static
{
	$this->getClass()->getFieldDefinition("boxDimension1")->addListingFilter($this, $data, $operator);
	return $this;
}

/**
* Filter by boxDimension2 (Boy)
* @param string|int|float|array|Model\Element\ElementInterface $data  comparison data, can be scalar or array (if operator is e.g. "IN (?)")
* @param string $operator  SQL comparison operator, e.g. =, <, >= etc. You can use "?" as placeholder, e.g. "IN (?)"
* @return $this
*/
public function filterByBoxDimension2 ($data, $operator = '='): static
{
	$this->getClass()->getFieldDefinition("boxDimension2")->addListingFilter($this, $data, $operator);
	return $this;
}

/**
* Filter by boxDimension3 (Yükseklik)
* @param string|int|float|array|Model\Element\ElementInterface $data  comparison data, can be scalar or array (if operator is e.g. "IN (?)")
* @param string $operator  SQL comparison operator, e.g. =, <, >= etc. You can use "?" as placeholder, e.g. "IN (?)"
* @return $this
*/
public function filterByBoxDimension3 ($data, $operator = '='): static
{
	$this->getClass()->getFieldDefinition("boxDimension3")->addListingFilter($this, $data, $operator);
	return $this;
}

/**
* Filter by boxWeight (Ağırlık)
* @param string|int|float|array|Model\Element\ElementInterface $data  comparison data, can be scalar or array (if operator is e.g. "IN (?)")
* @param string $operator  SQL comparison operator, e.g. =, <, >= etc. You can use "?" as placeholder, e.g. "IN (?)"
* @return $this
*/
public function filterByBoxWeight ($data, $operator = '='): static
{
	$this->getClass()->getFieldDefinition("boxWeight")->addListingFilter($this, $data, $operator);
	return $this;
}

/**
* Filter by inBoxCount (Koli İçi Mevcut)
* @param string|int|float|array|Model\Element\ElementInterface $data  comparison data, can be scalar or array (if operator is e.g. "IN (?)")
* @param string $operator  SQL comparison operator, e.g. =, <, >= etc. You can use "?" as placeholder, e.g. "IN (?)"
* @return $this
*/
public function filterByInBoxCount ($data, $operator = '='): static
{
	$this->getClass()->getFieldDefinition("inBoxCount")->addListingFilter($this, $data, $operator);
	return $this;
}

/**
* Filter by inPaletteCount (Palet İçi Mevcut)
* @param string|int|float|array|Model\Element\ElementInterface $data  comparison data, can be scalar or array (if operator is e.g. "IN (?)")
* @param string $operator  SQL comparison operator, e.g. =, <, >= etc. You can use "?" as placeholder, e.g. "IN (?)"
* @return $this
*/
public function filterByInPaletteCount ($data, $operator = '='): static
{
	$this->getClass()->getFieldDefinition("inPaletteCount")->addListingFilter($this, $data, $operator);
	return $this;
}

/**
* Filter by inContainerCount (Konteyner İçi Mevcut)
* @param string|int|float|array|Model\Element\ElementInterface $data  comparison data, can be scalar or array (if operator is e.g. "IN (?)")
* @param string $operator  SQL comparison operator, e.g. =, <, >= etc. You can use "?" as placeholder, e.g. "IN (?)"
* @return $this
*/
public function filterByInContainerCount ($data, $operator = '='): static
{
	$this->getClass()->getFieldDefinition("inContainerCount")->addListingFilter($this, $data, $operator);
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
* Filter by seoTitleEn (İngilizce)
* @param string|int|float|array|Model\Element\ElementInterface $data  comparison data, can be scalar or array (if operator is e.g. "IN (?)")
* @param string $operator  SQL comparison operator, e.g. =, <, >= etc. You can use "?" as placeholder, e.g. "IN (?)"
* @return $this
*/
public function filterBySeoTitleEn ($data, $operator = '='): static
{
	$this->getClass()->getFieldDefinition("seoTitleEn")->addListingFilter($this, $data, $operator);
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
* Filter by seoDescriptionEn (İngilizce)
* @param string|int|float|array|Model\Element\ElementInterface $data  comparison data, can be scalar or array (if operator is e.g. "IN (?)")
* @param string $operator  SQL comparison operator, e.g. =, <, >= etc. You can use "?" as placeholder, e.g. "IN (?)"
* @return $this
*/
public function filterBySeoDescriptionEn ($data, $operator = '='): static
{
	$this->getClass()->getFieldDefinition("seoDescriptionEn")->addListingFilter($this, $data, $operator);
	return $this;
}

/**
* Filter by seoKeywords (SEO Anahtar Kelimeler)
* @param string|int|float|array|Model\Element\ElementInterface $data  comparison data, can be scalar or array (if operator is e.g. "IN (?)")
* @param string $operator  SQL comparison operator, e.g. =, <, >= etc. You can use "?" as placeholder, e.g. "IN (?)"
* @return $this
*/
public function filterBySeoKeywords ($data, $operator = '='): static
{
	$this->getClass()->getFieldDefinition("seoKeywords")->addListingFilter($this, $data, $operator);
	return $this;
}

/**
* Filter by seoKeywordsEn (İngilizce)
* @param string|int|float|array|Model\Element\ElementInterface $data  comparison data, can be scalar or array (if operator is e.g. "IN (?)")
* @param string $operator  SQL comparison operator, e.g. =, <, >= etc. You can use "?" as placeholder, e.g. "IN (?)"
* @return $this
*/
public function filterBySeoKeywordsEn ($data, $operator = '='): static
{
	$this->getClass()->getFieldDefinition("seoKeywordsEn")->addListingFilter($this, $data, $operator);
	return $this;
}

/**
* Filter by listingItems (Listing Öğeleri)
* @param mixed $data
* @param string $operator SQL comparison operator, e.g. =, <, >= etc. You can use "?" as placeholder, e.g. "IN (?)"
* @return $this
*/
public function filterByListingItems ($data, $operator = '='): static
{
	$this->getClass()->getFieldDefinition("listingItems")->addListingFilter($this, $data, $operator);
	return $this;
}

/**
* Filter by brandItems (Satıldığı Markalar)
* @param mixed $data
* @param string $operator SQL comparison operator, e.g. =, <, >= etc. You can use "?" as placeholder, e.g. "IN (?)"
* @return $this
*/
public function filterByBrandItems ($data, $operator = '='): static
{
	$this->getClass()->getFieldDefinition("brandItems")->addListingFilter($this, $data, $operator);
	return $this;
}

/**
* Filter by identifierControlled (Kontrol Edildi)
* @param string|int|float|array|Model\Element\ElementInterface $data  comparison data, can be scalar or array (if operator is e.g. "IN (?)")
* @param string $operator  SQL comparison operator, e.g. =, <, >= etc. You can use "?" as placeholder, e.g. "IN (?)"
* @return $this
*/
public function filterByIdentifierControlled ($data, $operator = '='): static
{
	$this->getClass()->getFieldDefinition("identifierControlled")->addListingFilter($this, $data, $operator);
	return $this;
}

/**
* Filter by bundleProducts (Set İçeriği)
* @param mixed $data
* @param string $operator SQL comparison operator, e.g. =, <, >= etc. You can use "?" as placeholder, e.g. "IN (?)"
* @return $this
*/
public function filterByBundleProducts ($data, $operator = '='): static
{
	$this->getClass()->getFieldDefinition("bundleProducts")->addListingFilter($this, $data, $operator);
	return $this;
}

/**
* Filter by listingUniqueIds (Listing Unique Ids)
* @param string|int|float|array|Model\Element\ElementInterface $data  comparison data, can be scalar or array (if operator is e.g. "IN (?)")
* @param string $operator  SQL comparison operator, e.g. =, <, >= etc. You can use "?" as placeholder, e.g. "IN (?)"
* @return $this
*/
public function filterByListingUniqueIds ($data, $operator = '='): static
{
	$this->getClass()->getFieldDefinition("listingUniqueIds")->addListingFilter($this, $data, $operator);
	return $this;
}

/**
* Filter by costModelProduct (Genel Maliyet Modeli)
* @param mixed $data
* @param string $operator SQL comparison operator, e.g. =, <, >= etc. You can use "?" as placeholder, e.g. "IN (?)"
* @return $this
*/
public function filterByCostModelProduct ($data, $operator = '='): static
{
	$this->getClass()->getFieldDefinition("costModelProduct")->addListingFilter($this, $data, $operator);
	return $this;
}

/**
* Filter by costModelVariant (Varyant Maliyet Modeli)
* @param mixed $data
* @param string $operator SQL comparison operator, e.g. =, <, >= etc. You can use "?" as placeholder, e.g. "IN (?)"
* @return $this
*/
public function filterByCostModelVariant ($data, $operator = '='): static
{
	$this->getClass()->getFieldDefinition("costModelVariant")->addListingFilter($this, $data, $operator);
	return $this;
}

/**
* Filter by cutComplexity (Kesim Detay)
* @param string|int|float|array|Model\Element\ElementInterface $data  comparison data, can be scalar or array (if operator is e.g. "IN (?)")
* @param string $operator  SQL comparison operator, e.g. =, <, >= etc. You can use "?" as placeholder, e.g. "IN (?)"
* @return $this
*/
public function filterByCutComplexity ($data, $operator = '='): static
{
	$this->getClass()->getFieldDefinition("cutComplexity")->addListingFilter($this, $data, $operator);
	return $this;
}

/**
* Filter by pricingControlled (Kontrol Edildi)
* @param string|int|float|array|Model\Element\ElementInterface $data  comparison data, can be scalar or array (if operator is e.g. "IN (?)")
* @param string $operator  SQL comparison operator, e.g. =, <, >= etc. You can use "?" as placeholder, e.g. "IN (?)"
* @return $this
*/
public function filterByPricingControlled ($data, $operator = '='): static
{
	$this->getClass()->getFieldDefinition("pricingControlled")->addListingFilter($this, $data, $operator);
	return $this;
}

/**
* Filter by pricingCosts (Dağıtım Modelleri)
* @param mixed $data
* @param string $operator SQL comparison operator, e.g. =, <, >= etc. You can use "?" as placeholder, e.g. "IN (?)"
* @return $this
*/
public function filterByPricingCosts ($data, $operator = '='): static
{
	$this->getClass()->getFieldDefinition("pricingCosts")->addListingFilter($this, $data, $operator);
	return $this;
}

/**
* Filter by designFiles (Tasarım Dosyaları)
* @param mixed $data
* @param string $operator SQL comparison operator, e.g. =, <, >= etc. You can use "?" as placeholder, e.g. "IN (?)"
* @return $this
*/
public function filterByDesignFiles ($data, $operator = '='): static
{
	$this->getClass()->getFieldDefinition("designFiles")->addListingFilter($this, $data, $operator);
	return $this;
}



}
