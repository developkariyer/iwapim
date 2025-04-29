<?php
namespace App\MessageHandler;


use App\Connector\Gemini\GeminiConnector;
use App\Connector\Marketplace\CiceksepetiConnector;
use App\Message\ProductListingMessage;
use App\Model\DataObject\Marketplace;
use App\Model\DataObject\Product;
use App\Model\DataObject\VariantProduct;
use App\Utils\Utility;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;
use Symfony\Component\HttpClient\HttpClient;

#[AsMessageHandler(fromTransport: 'ciceksepeti')]
class CiceksepetiListingHandler
{
    public function __invoke(ProductListingMessage $message)
    {
        //$this->categoryAttributeUpdate($message->getMarketplaceId());
        $data = $this->getListingInfoJson($message);
        $categories = $this->getCiceksepetiCategoriesDetails();
        $jsonString = json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
        $prompt = <<<EOD
            Sen bir e-ticaret uzmanısın ve ÇiçekSepeti pazaryeri için ürün listeleri hazırlıyorsun.
            **Çıkış formatı**:  
            Sadece aşağıdaki gibi bir JSON döndür:
            {
              "SKU1": {
                "productName": "Ürün adı",
                "mainProductCode": "Ana ürün kodu",
                "stockCode": "Stok kodu",
                "description": "Ürün açıklaması",
                "images": ["resim1", "resim2"],
                "price": "100",
                "categoryId": 1234,
                "renk": "Renk bilgisi",
                "ebat": "Ebat bilgisi"
              },
              "SKU2": {
                ...
              }
            }
            
            Hiçbir açıklama, kod bloğu, yorum ekleme.  
            Sadece geçerli, düzgün bir JSON üret.
            Aşağıda bir ürün listeleme datası (JSON formatında) verilmiştir.  
            Bu JSON'da bazı alanlar eksik veya hatalı olabilir.  
            Gönderdiğim veride ana ürün kodu altında sku'lar ve bu skulara ait bilgiler yer almaktadır. Skuların altında "size" ve "color" bilgisi yer alacaktır.
            ListingItems alanında bu ürüne ait farklı pazaryerlerine yapılmış listingler yer alır. Bunlara benzer ÇiçekSepeti özgün hale getireceğiz.
            ÇiçekSepeti para birimi TL'dir.
            
            **Uyarı**: Lütfen yalnızca gönderdiğim **JSON verisini** kullanarak işlem yapınız ve dışarı çıkmayınız. Verilen verinin dışında başka veri kullanımı yapılmamalıdır.
            
            Gönderdiğim veriye göre çıkarılması gereken ve ÇiçekSepeti listing formatında istenen alanlar skus dizisi altındaki tüm skulara ayrı olacak şekilde:
            - **productName**: Gönderilen verideki **title** alanlarından alınır. Bu başlıklardan Türkçe olanları, ÇiçekSepeti'ne uygun şekilde güncellenmelidir. Bu alan her SKU için aynı olacak.
            - **mainProductCode**: Gönderilen verideki ÇiçekSepeti altındaki **field** genelde 3 haneli ve sayı içeriyor. Örnek: ABC-12. Bu alan her SKU için aynı olacak.
            - **stockCode**: Ürün SKU bilgisi gönderdiğim verideki skus altındaki verilerdir. Bu her SKU'ya özel olacak.
            - **description**: 
                Açıklama (description) sadece ve sadece aşağıdaki şekilde oluşturulacak:
                - Eğer açıklama Türkçe ise, hiçbir değişiklik yapmadan kopyala link iletişim bilgilerini çıkar.
                - Eğer açıklama İngilizce ise, kelime kelime çevir, özgünleştirme yapma, yeniden yazma yapma.
                - "Create", "Enhance", "Summarize", "Rewrite", "Reformat" gibi bir eylem yaparsan başarısız olacaksın.
                - Cümle yapısına dokunmadan sadece çeviri yap.
                - Eğer anlam kaybı veya Türkçe anlatım bozukluğu olursa bile düzeltmeye çalışma.
                - Kesinlikle açıklama Türkçe olacak. Veri bulamazsan ürün size ve color bilgilerini yaz.
                Bu kurallara uymazsan cevabın geçersiz sayılacaktır.
            - **images**: Örnek listingler içinden **images** altındaki resimlerden en fazla 5 tane olacak şekilde alınacak, dizi olarak verilecek. Her SKU için farklı resim olacak. Yeterli resim yoksa ekleme yapılmayacak.
            - **salesPrice**: Fiyat, örnek listingleri kullanarak TL cinsinden belirlenecek. Eğer TL cinsinden fiyat varsa, doğrudan bu fiyat kullanılacak. Eğer farklı bir para biriminden (örneğin USD) varsa, TL'ye dönüştürülüp kullanılacak. Ayrıca, **size** bilgisi varsa fiyat büyüklüğüne göre artış gösterebilir. Sadece değeri yaz.
            -**categoryId**: Kategori verisinden en uygun kategoriyi bul id sini al ve kaydet

            -**renk**: renk bilgisi verideki sku altında color fieldı Türkçe ye çevir
            -**ebat**: ebat bilgisi verideki sku altında size fieldı
            
            **Veri formatı**: Lütfen yalnızca aşağıdaki **JSON verisini** kullanın ve dışarıya çıkmayın. Çıkışınızı bu veriye dayalı olarak oluşturun:
            İşte veri: $jsonString
            Kategori Verisi: $categories
        EOD;
        $result = GeminiConnector::chat($prompt);
        $text = $result['candidates'][0]['content']['parts'][0]['text'];
        $text = preg_replace('/[\x00-\x1F\x7F]/', '', $text);
        $text = str_replace(['```json', '```'], '', $text);
        $data = json_decode($text, true);

        if (json_last_error() !== JSON_ERROR_NONE) {
            throw new \Exception('JSON parsing hatası: ' . json_last_error_msg());
        }
        $this->checkData($data);

        echo "Ciceksepeti Mesaj İşlendi (JSON)\n";
       // echo $jsonOutput . "\n";
    }

    public function checkData($data)
    {
        foreach ($data as $sku => &$product) {
            $categoryId = $product['categoryId'];
            $attributeColorSql = "SELECT attribute_id from iwa_ciceksepeti_category_attributes where category_id = :categoryId and type= 'Variant Özelliği' and attribute_name= 'Renk' limit 1";
            $attributeColorSqlResult = Utility::fetchFromSql($attributeColorSql, ['categoryId' => $categoryId]);
            $attributeColorId = $attributeColorSqlResult[0]['attribute_id'];

            $attributeSizeSql = "SELECT attribute_id from iwa_ciceksepeti_category_attributes where category_id = :categoryId and type= 'Variant Özelliği' and 
                                                                   (attribute_name= 'Ebat' or attribute_name= 'Boyut' or attribute_name= 'Beden' ) limit 1";
            $attributeSizeSqlResult = Utility::fetchFromSql($attributeSizeSql, ['categoryId' => $categoryId]);
            $attributeSizeId = $attributeSizeSqlResult[0]['attribute_id'];


            $attributeValueSql = "SELECT attribute_value_id FROM iwa_ciceksepeti_category_attributes_values where attribute_id = :attribute_id and name = :name limit 1";
            $attributeColorValueSqlResult = Utility::fetchFromSql($attributeValueSql, [
                'attribute_id' => $attributeColorId,
                'name' => $product['renk']
            ]);
            $attributeSizeValueSqlResult = Utility::fetchFromSql($attributeValueSql, [
                'attribute_id' => $attributeSizeId,
                'name' => $product['ebat']
            ]);

            $attributes = [];
            if ($attributeColorValueSqlResult) {
                $attributes[] = [
                    'id' => $attributeColorId,
                    'ValueId' => $attributeColorValueSqlResult[0]['attribute_value_id']
                ];
            }

            if ($attributeSizeValueSqlResult) {
                $attributes[] = [
                    'id' => $attributeSizeId,
                    'ValueId' => $attributeSizeValueSqlResult[0]['attribute_value_id']
                ];
            }
            $product['Attributes'] = $attributes;
        }
        print_r($data);

    }

    public function getListingInfoJson($message)
    {
        $data = [];
        $marketplace = Marketplace::getById($message->getMarketplaceId());
        $marketplaceName = $marketplace->getMarketplaceType();
        $product = Product::getById($message->getProductId());
        $variantIds = $message->getVariantIds();
        if ($product instanceof Product) {
            $productIdentifier = $product->getProductIdentifier();
            $productCategory = $product->getProductCategory();
            $data[$marketplaceName][$productIdentifier]['category'] = $productCategory;
            $productName = $product->getName();
            $data[$marketplaceName][$productIdentifier]['name'] = $productName;
        }
        foreach ($variantIds as $variantId) {
            echo $variantId . "\n";
            $variantProduct = Product::getById($variantId);
            if ($variantProduct instanceof Product) {
                $iwasku = $variantProduct->getIwasku();
                $size = $variantProduct->getVariationSize();
                $color = $variantProduct->getVariationColor();
                $ean = $variantProduct->getEanGtin();
                $data[$marketplaceName][$productIdentifier]['skus'][$iwasku]['size'] = $size;
                $data[$marketplaceName][$productIdentifier]['skus'][$iwasku]['color'] = $color;
                $data[$marketplaceName][$productIdentifier]['skus'][$iwasku]['ean'] = $ean;
                $listingItems = $variantProduct->getListingItems();
                foreach ($listingItems as $listingItem) {
                    if ($listingItem instanceof VariantProduct) {
                        $title = $listingItem->getTitle();
                        $salePrice = $listingItem->getSalePrice();
                        $currency = $listingItem->getSaleCurrency();
                        $marketplaceType = $listingItem->getMarketplace()->getKey();
                        $apiJson = json_decode($listingItem->jsonRead('apiResponseJson'), true);
                        $parentApiJson = json_decode($listingItem->jsonRead('parentResponseJson'), true);

                        $data[$marketplaceName][$productIdentifier]['skus'][$iwasku]['ListingItems'][$marketplaceType]['title'] = $title;
                        $data[$marketplaceName][$productIdentifier]['skus'][$iwasku]['ListingItems'][$marketplaceType]['salePrice'] = $salePrice;
                        $data[$marketplaceName][$productIdentifier]['skus'][$iwasku]['ListingItems'][$marketplaceType]['currency'] = $currency;
                        $data[$marketplaceName][$productIdentifier]['skus'][$iwasku]['ListingItems'][$marketplaceType]['apiJson'] = $apiJson;
                        $data[$marketplaceName][$productIdentifier]['skus'][$iwasku]['ListingItems'][$marketplaceType]['parentApiJson'] = $parentApiJson;

                        $imageGallery = $listingItem->getImageGallery();
                        foreach ($imageGallery as $hotspotImage) {
                            $image = $hotspotImage->getImage();
                            $imageUrl = $image->getFullPath();
                            $host = \Pimcore\Tool::getHostUrl();
                            $data[$marketplaceName][$productIdentifier]['skus'][$iwasku]['ListingItems'][$marketplaceType]['images'][] = $host . $imageUrl ;
                        }
                    }
                }
            }
        }
        return json_encode($data);
    }

    public function categoryAttributeUpdate($marketplaceId)
    {
        $this->ciceksepetiConnector = new CiceksepetiConnector(Marketplace::getById($marketplaceId));
        echo "Ciceksepeti Connector Created\n";
        $this->ciceksepetiConnector->downloadCategories();
        echo "Ciceksepeti Downloaded Categories\n";
        $categoryIdList = $this->getCiceksepetiListingCategoriesIdList();
        echo "Ciceksepeti Category List Updated\n";
        foreach ($categoryIdList as $categoryId) {
            $this->ciceksepetiConnector->getCategoryAttributesAndSaveDatabase($categoryId);
        }
        echo "Ciceksepeti Category Attributes Updated\n";
    }

    public function categoryAttributeInfo()
    {
        $categoryAttributeSql = "
            SELECT category_id, attribute_name, attribute_id 
            FROM iwa_ciceksepeti_category_attributes 
            WHERE category_id = :categoryId 
            AND type = 'Variant Özelliği'
        ";

        $categoryAttributeValueSql = "
            SELECT attribute_value_id, attribute_id, name 
            FROM iwa_ciceksepeti_category_attributes_values 
            WHERE attribute_id = :attributeId
        ";
        $categoryInfo = [];
        $attributesGlobal = [];
        $attributeValuesGlobal = [];

        $categoryIdList = $this->getCiceksepetiListingCategoriesIdList();

        foreach ($categoryIdList as $categoryId) {
            $attributes = Utility::fetchFromSql($categoryAttributeSql, ['categoryId' => $categoryId]);

            $categoryInfo[$categoryId] = [
                'category_id' => $categoryId,
                'attribute_ids' => [],
            ];

            foreach ($attributes as $attribute) {
                $attributeId = $attribute['attribute_id'];

                if (!isset($attributesGlobal[$attributeId])) {
                    $attributesGlobal[$attributeId] = [
                        'attribute_id' => $attributeId,
                        'attribute_name' => $attribute['attribute_name'],
                    ];

                    $attributeValues = Utility::fetchFromSql($categoryAttributeValueSql, ['attributeId' => $attributeId]);
                    $attributeValuesGlobal[$attributeId] = $attributeValues;
                }

                $categoryInfo[$categoryId]['attribute_ids'][] = $attributeId;
            }
        }

        $finalResult = [
            'categories' => array_values($categoryInfo),
            'attributes' => array_values($attributesGlobal),
            'attribute_values' => $attributeValuesGlobal
        ];

        return json_encode($finalResult, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
    }

    public function getCiceksepetiListingCategoriesIdList(): array
    {
        $sql = "SELECT oo_id FROM `object_query_varyantproduct` WHERE marketplaceType = 'Ciceksepeti'";
        $ciceksepetiVariantIds = Utility::fetchFromSql($sql);
        if (!is_array($ciceksepetiVariantIds) || empty($ciceksepetiVariantIds)) {
            return [];
        }
        $categoryIdList = [];
        foreach ($ciceksepetiVariantIds as $ciceksepetiVariantId) {
            $variantProduct = VariantProduct::getById($ciceksepetiVariantId['oo_id']);
            if (!$variantProduct instanceof VariantProduct) {
                continue;
            }
            $apiData = json_decode($variantProduct->jsonRead('apiResponseJson'), true);
            $categoryIdList[] = $apiData['categoryId'];
        }
        return array_unique($categoryIdList);
    }

    public function getCiceksepetiCategoriesDetails()
    {
        $categoryIdList = $this->getCiceksepetiListingCategoriesIdList();

        if (empty($categoryIdList)) {
            return [];
        }

        $inClause = implode(',', array_fill(0, count($categoryIdList), '?'));
        $sql = "SELECT * FROM iwa_ciceksepeti_categories WHERE id IN ($inClause)";
        $categories = Utility::fetchFromSql($sql, $categoryIdList);

        return json_encode($categories, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
    }

}
