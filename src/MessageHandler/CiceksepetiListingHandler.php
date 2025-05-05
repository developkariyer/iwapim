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
        echo "Ciceksepeti Listing Handler\n";
        $sql = 'INSERT INTO iwa_auto_listing_status (trace_id, user_name, current_stage, status, error_message, started_at, updated_at, complated_at, action_type)
                VALUES (:trace_id, :user_name, :current_stage, :status, :error_message, :started_at, :updated_at, :complated_at, :action_type )
                ON DUPLICATE KEY UPDATE
                    current_stage = :current_stage,
                    status = :status,
                    error_message = :error_message,
                    updated_at = :updated_at,
                    complated_at = :complated_at';
        Utility::executeSql($sql, [
            'trace_id' => $message->getTraceId(),
            'user_name' => $message->getUsername(),
            'current_stage' => 'start',
            'status' => 'processing',
            'error_message' => '',
            'started_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s'),
            'complated_at' => null,
            'action_type' => $message->getActionType(),
        ]);


        print_r($message);
        $categories = $this->getCiceksepetiCategoriesDetails();
        Utility::executeSql($sql, [
            'trace_id' => $message->getTraceId(),
            'user_name' => $message->getUsername(),
            'current_stage' => 'categories',
            'status' => 'processing',
            'error_message' => '',
            'updated_at' => date('Y-m-d H:i:s'),
            'action_type' => $message->getActionType(),
        ]);
        /*$jsonString = $this->listingHelper->getPimListingsInfo($message);
        echo "pim getting listing info \n";
        print_r($jsonString);
        $messageType = $message->getActionType();
        match ($messageType) {
            'list' => $this->processListingData($jsonString, $categories),
            default => throw new \InvalidArgumentException("Unknown Action Type: $messageType"),
        };*/

    }

    /**
     * @throws \Exception
     */
    private function processListingData($jsonString, $categories)
    {
        $prompt = $this->generateListingPrompt($jsonString, $categories);
        echo "created prompt\n";
        $result = GeminiConnector::chat($prompt);
        echo "gemini connector result\n";
        $data = $this->parseAndValidateResponse($result);
        echo "parsed and validating response \n";

        $data = $this->fillAttributeData($data);
        echo "filled attributes \n";
        $formattedData = $this->fillMissingListingDataAndFormattedCiceksepetiListing($data);
        echo "formatted data\n";
        print_r($formattedData);

        /*$ciceksepetiConnector = new CiceksepetiConnector(Marketplace::getById(265384));
        $ciceksepetiConnector->createListing($formattedData);
        echo "created connector listing api \n";*/


        //return $data;
    }

    private function fillMissingListingDataAndFormattedCiceksepetiListing($data)
    {
        $formattedData = [];

        foreach ($data as $sku => $product) {
            $httpsImages = array_map(function($image) {
                return preg_replace('/^http:/', 'https:', $image);
            }, $product['images'] ?? []);

            $formattedData['products'][] = [
                'productName' => $product['productName'],
                'mainProductCode' => $product['mainProductCode'],
                'stockCode' => $product['stockCode'],
                'categoryId' => $product['categoryId'],
                'description' => $product['description'],
                'deliveryMessageType' => 5,
                'deliveryType' => 2,
                'stockQuantity' => 0,
                'salesPrice' => 3000.0,
                'images' => array_slice($httpsImages, 0, 5),
                'Attributes' => $product['Attributes'],
            ];
        }
        return json_encode($formattedData, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
    }

    private function parseAndValidateResponse($result)
    {
        $json = $result['candidates'][0]['content']['parts'][0]['text'] ?? '';
        $json = preg_replace('/[\x00-\x1F\x7F]/u', '', $json);
        $data = json_decode($json, true);
        return (json_last_error() === JSON_ERROR_NONE) ? $data : null;
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
                - Kendin açıklama uydurma.
                - Eğer açıklama Türkçe ise, hiçbir değişiklik yapmadan kopyala link iletişim bilgilerini çıkar.
                - Eğer açıklama İngilizce ise, kelime kelime çevir, özgünleştirme yapma, yeniden yazma yapma.
                - "Create", "Enhance", "Summarize", "Rewrite", "Reformat" gibi bir eylem yaparsan başarısız olacaksın.
                - Cümle yapısına dokunmadan sadece çeviri yap.
                - Eğer anlam kaybı veya Türkçe anlatım bozukluğu olursa bile düzeltmeye çalışma.
                - Kesinlikle açıklama Türkçe olacak. Veri bulamazsan ürün size ve color bilgilerini yaz.
                Bu kurallara uymazsan cevabın geçersiz sayılacaktır.
            - **images**: 
                - Her SKU için en fazla 5 adet olacak şekilde, o SKU'ya ait `ListingItems` içindeki `images` listesinden alınacaktır.
                - Eğer aynı resimler geliyorsa diğerinde kullanma. 5 olmak zorunda değil.
                - Diğer SKU'larla aynı görseller kullanılmamalıdır; her SKU için kendi görselleri değerlendirilmelidir.
                - Resimler dizi (array) formatında verilecektir.
                - Yalnızca **en az 500x500** ve **en fazla 2000x2000** piksel boyutlarındaki görseller dahil edilecektir.
                - Bu boyut aralığı dışında kalan görseller filtrelenecektir.
                                    
            - **salesPrice**: 
                 "currency" alanına bak.
               - Eğer para birimi TRY/TL değilse, "salePrice" değerini TL'ye çevir. Döviz kuru bilgisine sahipsen kullan.
               - Eğer para birimi zaten TRY veya TL ise, olduğu gibi kullan.

            -**categoryId**: Kategori verisinden en uygun kategoriyi bul id sini al ve kaydet

            -**renk**: renk bilgisi verideki sku altında color fieldı Türkçe ye çevir
            -**ebat**: ebat bilgisi verideki sku altında size fieldı cm olarak al (örn: 250cm) yanında boyut belirten S-M-XL gibi durum varsa bunu alma.
            
            **Veri formatı**: Lütfen yalnızca aşağıdaki **JSON verisini** kullanın ve dışarıya çıkmayın. Çıkışınızı bu veriye dayalı olarak oluşturun:
            İşte veri: $jsonString
            Kategori Verisi: $categories
        EOD;
    }

    public function fillAttributeData($data)
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
        return $data;
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
