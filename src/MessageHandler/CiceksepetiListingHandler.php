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
use App\MessageHandler\ListingHelperService;

#[AsMessageHandler(fromTransport: 'ciceksepeti')]
class CiceksepetiListingHandler
{

    public function __construct(ListingHelperService $listingHelperService)
    {
        $this->listingHelper = $listingHelperService;
    }

    public function __invoke(ProductListingMessage $message)
    {
        $categories = $this->getCiceksepetiCategoriesDetails();
        $jsonString = $this->listingHelper->getPimListingsInfo($message);
        $messageType = $message->getActionType();
        match ($messageType) {
            'list' => $this->processListingData($jsonString, $categories),
            default => throw new \InvalidArgumentException("Unknown Action Type: $messageType"),
        };

    }

    /**
     * @throws \Exception
     */
    private function processListingData($jsonString, $categories)
    {
        $prompt = $this->generateListingPrompt($jsonString, $categories);
        $result = GeminiConnector::chat($prompt);
        $text = $this->parseResponse($result);
        $data = $this->validateJson($text);

        $this->checkData($data);
        //return $data;
    }

    private function parseResponse($result)
    {
        $text = $result['candidates'][0]['content']['parts'][0]['text'];
        $text = preg_replace('/[\x00-\x1F\x7F]/', '', $text);
        $text = str_replace(['```json', '```'], '', $text);
        return $text;
    }

    private function validateJson($text)
    {
        $data = json_decode($text, true);
        if (json_last_error() !== JSON_ERROR_NONE) {
            throw new \Exception('JSON parsing error: ' . json_last_error_msg());
        }
        return $data;
    }

    private function generateListingPrompt($jsonString, $categories)
    {
        return <<<EOD
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
