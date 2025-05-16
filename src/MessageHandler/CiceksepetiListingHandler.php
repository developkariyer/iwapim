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
use Psr\Log\LoggerInterface;
use App\Logger\LoggerFactory;

#[AsMessageHandler(fromTransport: 'ciceksepeti')]
class CiceksepetiListingHandler
{
    private LoggerInterface $logger;
    public function __construct(ListingHelperService $listingHelperService)
    {
        $this->listingHelper = $listingHelperService;
        $this->logger = LoggerFactory::create('ciceksepeti','auto_listing');
    }

    /**
     * @throws \Exception
     */
    public function __invoke(ProductListingMessage $message): void
    {
        sleep(5);
        $traceId = $message->getTraceId();
        echo "Ciceksepeti Listing Handler\n";
        $this->logger->info("Auto listing process started trace id: {$traceId}.");
        $categories = $this->getCiceksepetiCategoriesDetails();
        echo "ciceksepeti categories fetched\n";
        $this->logger->info("Ciceksepeti categories details complated");
        $jsonString = $this->listingHelper->getPimListingsInfo($message);
        $this->printProductInfoLogger($jsonString);
        $this->logger->info("Pim listings info complated");
        $messageType = $message->getActionType();
        match ($messageType) {
            'list' => $this->processListingData($traceId, $jsonString, $categories),
            default => throw new \InvalidArgumentException("Unknown Action Type: $messageType"),
        };
    }

    private function printProductInfoLogger($jsonString): void
    {
        $jsonData = json_decode($jsonString, true);
        if (isset($jsonData['Ciceksepeti']) && is_array($jsonData['Ciceksepeti'])) {
            foreach ($jsonData['Ciceksepeti'] as $productId => $productData) {
                $name = $productData['name'] ?? 'Unknown';
                $skus = array_keys($productData['skus'] ?? []);
                $skuList = implode(', ', $skus);
                $this->logger->info("Product ID: {$productId}, Name: {$name}, SKUs: {$skuList}");
            }
        } else {
            $this->logger->error("Invalid or missing Ciceksepeti data in JSON");
        }
    }

    private function chunkSkus($data): array
    {
        $chunks = [];
        foreach ($data as $productCode => $productData) {
            $skus = $productData['skus'];
            $skuChunks = array_chunk($skus, 2, true);
            foreach ($skuChunks as $chunk) {
                $chunks[] = [
                    $productCode => [
                        'category' => $productData['category'],
                        'name' => $productData['name'],
                        'skus' => $chunk
                    ]
                ];
            }
        }
        return $chunks;
    }

    private function processListingData($traceId, $jsonString, $categories)
    {
        $fullData = json_decode($jsonString, true);
        if (!$fullData || !isset($fullData['Ciceksepeti'])) {
            $this->logger->error("Invalid JSON data: " . $jsonString);
            throw new \Exception("Invalid JSON data:");
        }
        $chunks = $this->chunkSkus($fullData['Ciceksepeti']);
        $mergedResults = [];
        $totalChunks = count($chunks);
        foreach ($chunks as $index => $chunkData) {
            $chunkNumber = $index + 1;
            echo "\n🔄 Chunk {$chunkNumber} / {$totalChunks} processing...\n";
            $this->logger->info("Chunk {$chunkNumber} / {$totalChunks} processing...");
            $chunkJsonString = json_encode(['Ciceksepeti' => $chunkData], JSON_UNESCAPED_UNICODE);
            $prompt = $this->generateListingPrompt($chunkJsonString, $categories);
            $result = GeminiConnector::chat($prompt, 'ciceksepeti');
            $parsedResult = $this->parseGeminiResult($result);
            if (!$parsedResult) {
                $this->logger->error("Gemini result is empty or error gemini api");
                echo "⚠️ Error: Chunk {$chunkNumber} / {$totalChunks} result is empty or error gemini api \n";
                continue;
            }
            $mergedResults = array_merge_recursive($mergedResults, $parsedResult);
            echo "✅ Gemini result success. Chunk {$chunkNumber} complated.\n";
            $this->logger->info("Gemini chat result success. Chunk {$chunkNumber} complated.");
            sleep(5);
        }
        $this->logger->info("Gemini chat result : " . json_encode($mergedResults, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT));
        $data = $this->fillAttributeData($mergedResults);
        foreach ($data as $sku => $product) {
            if (isset($product['Attributes']) && empty($product['Attributes'])) {
                $this->logger->info("Attributes is empty for sku: {$sku}");
            }
        }
        $this->logger->info("filled attributes data: " . json_encode($data, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT));
        if (empty($data)) {
            $this->logger->error("No products found in data");
            return [];
        }
        $formattedData = $this->fillMissingListingDataAndFormattedCiceksepetiListing($data);
        print_r($formattedData);
//        $this->logger->info("filled attributes data: " . $formattedData);
//        $ciceksepetiConnector = new CiceksepetiConnector(Marketplace::getById(265384));
//        $result = $ciceksepetiConnector->createListing($formattedData);
//        $this->logger->info("ciceksepetiConnector result batch id: " . json_encode($result, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT));
//        print_r($result);
    }

    private function fillMissingListingDataAndFormattedCiceksepetiListing($data): false|string
    {
        $data = $this->removeCommonAttributes($data);
        $formattedData = [];
        foreach ($data as $sku => $product) {
            $httpsImages = array_map(function($image) {
                return preg_replace('/^http:/', 'https:', $image);
            }, $product['images'] ?? []);
            $salesPrice = $product['salesPrice'] ?? 0;
            $attributes = $product['Attributes'] ?? null;
            $description = $product['description'];
            if (empty($httpsImages) || $salesPrice === 0 || $salesPrice === "0" || $attributes === null || mb_strlen($description) < 30) {
                $this->logger->error("Missing data for sku: {$product['stockCode']}");
                continue;
            }
            $description = str_replace("\n", "<br>", $description);
            $description = str_replace(['\/', '\"', '\\\\', '\\n', '\\r', '\\t'], ['/', '"', '\\', "\n", "\r", "\t"], $description);
            $description = preg_replace_callback('/\\\\u([0-9a-fA-F]{4})/', function ($matches) {
                return mb_convert_encoding(pack('H*', $matches[1]), 'UTF-8', 'UTF-16BE');
            }, $description);
            $description = html_entity_decode($description, ENT_QUOTES | ENT_HTML5, 'UTF-8');
            $formattedData['products'][] = [
                'productName' => mb_strlen($product['productName']) > 255
                    ? mb_substr($product['productName'], 0, 255)
                    : $product['productName'],
                'mainProductCode' => $product['mainProductCode'],
                'stockCode' => $product['stockCode'],
                'categoryId' => $product['categoryId'],
                'description' => mb_strlen($description) > 20000
                    ? mb_substr($description, 0, 20000)
                    : $description,
                'deliveryMessageType' => 5,
                'deliveryType' => 2,
                'stockQuantity' => 0,
                'salesPrice' => $salesPrice,
                'images' => $httpsImages,
                'Attributes' => $attributes,
            ];
        }
        return json_encode($formattedData, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);
    }

    private function removeCommonAttributes($data): array
    {
        $valueIdCount = [];
        $totalProducts = count($data);
        foreach ($data as $product) {
            if (!isset($product['Attributes']) || empty($product['Attributes'])) {
                continue;
            }
            foreach ($product['Attributes'] as $attribute) {
                $valueId = $attribute['ValueId'];
                $valueIdCount[$valueId] = ($valueIdCount[$valueId] ?? 0) + 1;
            }
        }
        $commonValueIds = array_filter($valueIdCount, function ($count) use ($totalProducts) {
            return $count === $totalProducts;
        });
        foreach ($data as &$product) {
            if (!isset($product['Attributes']) || empty($product['Attributes'])) {
                continue;
            }
            $product['Attributes'] = array_filter($product['Attributes'], function ($attribute) use ($commonValueIds) {
                return !isset($commonValueIds[$attribute['ValueId']]);
            });
            $product['Attributes'] = array_values($product['Attributes']);
            if (empty($product['Attributes'])) {
                $product['Attributes'] = [];
            }
        }
        return $data;
    }

    private function parseGeminiResult($result)
    {
        $json = $result['candidates'][0]['content']['parts'][0]['text'] ?? '';
        $json = preg_replace('/[\x00-\x1F\x7F]/u', '', $json);
        $data = json_decode($json, true);
        return (json_last_error() === JSON_ERROR_NONE) ? $data : null;
    }

    private function generateListingPrompt($jsonString, $categories): string
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
            
            **Uyarı**: Lütfen yalnızca gönderdiğim **JSON verisini** kullanarak işlem yapınız ve dışarı çıkmayınız. Verilen verinin dışında başka veri kullanımı yapılmamalıdır.
            
            Gönderdiğim veriye göre çıkarılması gereken ve ÇiçekSepeti listing formatında istenen alanlar skus dizisi altındaki tüm skulara ayrı olacak şekilde:
            - **productName**: Gönderilen verideki **title** alanlarından alınır. Bu başlıklardan Türkçe olanları, ÇiçekSepeti'ne uygun şekilde güncellenmelidir. Bu alan her SKU için aynı olacak. Size ve renk bilgisi olmasın.
            - **mainProductCode**: Gönderilen verideki ÇiçekSepeti altındaki **field** genelde 3 haneli ve sayı içeriyor. Örnek: ABC-12. Bu alan her SKU için aynı olacak.
            - **stockCode**: Ürün SKU bilgisi gönderdiğim verideki skus altındaki verilerdir. Bu her SKU'ya özel olacak.
            - **description**: 
                Açıklama (description) sadece ve sadece aşağıdaki şekilde oluşturulacak:
                1. Türkçe açıklama: Türkçe açıklama verisi varsa, hiçbir değişiklik yapılmadan olduğu gibi kopyalanacak ve link ya da iletişim bilgileri çıkarılacaktır.
                2. İngilizce açıklama: İngilizce açıklama verisi varsa, yalnızca doğru ve doğrudan çeviri yapılacak. Cümle yapısı, kelime sırası ve anlam korunacaktır. Yeniden yazma, özgünleştirme, yorum ekleme gibi işlemler yapılmayacaktır.
                3. Yasaklı işlemler: "Create", "Enhance", "Summarize", "Rewrite", "Reformat" gibi işlemler yapılması halinde işlem başarısız olacaktır.
                4. Türkçe olmayan açıklama: Eğer açıklama bulunmazsa, ürün adı (product name) ve renk bilgileri (color) yazılacaktır.
                5. Mağaza bilgileri: Mağazaya dair herhangi bir bilgi veya açıklama silinecektir.
                6. Çeviri düzeltmeleri: Eğer İngilizce açıklamada cümle bozukluğu varsa, bu bozukluk düzeltilerek cümle anlamı korunacaktır.
                7. HTML formatı: Açıklama, profesyonel bir şekilde HTML formatında düzenlenecektir
                Bu kurallara uymazsan cevabın geçersiz sayılacaktır.
            - **images**: 
                - Her SKU için en fazla 5 adet olacak şekilde,`images` listesinden alınacaktır.
                - Resimler dizi (array) formatında verilecektir.
                - Yalnızca **en az 500x500** ve **en fazla 2000x2000** piksel boyutlarındaki görseller dahil edilecektir.
                - Bu boyut aralığı dışında kalan görseller filtrelenecektir. 
                - Boş bırakma.
                                    
            - **salesPrice**: Ürün içinde yer alan **price** alanını direkt kullan her sku için farklı olabilir buna dikkat et.
                
            -**categoryId**: Kategori verisinden en uygun kategoriyi bul id sini al ve kaydet

            -**renk**: 
                - renk bilgisi verideki sku altında color fieldı Türkçe ye çevir çevirdiğinde çiçeksepetinde bulunan çok bilinen renklerden olsun örneğin:
                - Altın, Gümüş, Turkuaz, Kırmızı, Mavi, Bordo, Turuncu, Yeşil, Sarı, Pembe, Füme, Kamuflaj, Kahverengi, Mor, Bej, Lacivert, Metal, Lila, Haki, Taba, Beyaz, Magenta, Mürdüm, Karışık, Gri,
                Antrasit, Açık Mavi, Bakır, Vişne, Açık Pembe, Bronz, Ekru, Taş
            
            -**ebat**: ebat bilgisi verideki sku altında size fieldı cm olarak al (örn: 250cm) yanında boyut belirten S-M-XL gibi durum varsa bunu alma.
            
            **Veri formatı**: Lütfen yalnızca aşağıdaki **JSON verisini** kullanın ve dışarıya çıkmayın. Çıkışınızı bu veriye dayalı olarak oluşturun:
            İşte veri: $jsonString
            Kategori Verisi: $categories
        EOD;
    }

    public function fillAttributeData($data)
    {
        $categorySql = "SELECT category_name FROM iwa_ciceksepeti_categories WHERE id = :categoryId";
        $attributeColorSql = "SELECT attribute_id, attribute_name from iwa_ciceksepeti_category_attributes 
                             WHERE category_id = :categoryId 
                             AND type = 'Variant Özelliği' 
                             AND attribute_name = 'Renk' 
                             LIMIT 1";
        $attributeSizeSql = "SELECT attribute_id, attribute_name from iwa_ciceksepeti_category_attributes 
                            WHERE category_id = :categoryId 
                            AND type = 'Variant Özelliği' 
                            AND (attribute_name = 'Ebat' OR attribute_name = 'Boyut' OR attribute_name = 'Beden') 
                            LIMIT 1";
        foreach ($data as $sku => &$product) {
            $this->logger->info("iwasku: " . $product['stockCode']);
            $categoryId = $product['categoryId'];
            $categoryName = Utility::fetchFromSql($categorySql, ['categoryId' => $categoryId])[0]['category_name'] ?? null;
            if (!$categoryName) {
                $this->logger->error("categoryName not found for categoryId: " . $categoryId);
                continue;
            }

            $attributeColorSqlResult = Utility::fetchFromSql($attributeColorSql, ['categoryId' => $categoryId]);
            $attributeColorId = $attributeColorSqlResult[0]['attribute_id'] ?? null;
            $attributeColorName = $attributeColorSqlResult[0]['attribute_name'] ?? null;

            $attributeSizeSqlResult = Utility::fetchFromSql($attributeSizeSql, ['categoryId' => $categoryId]);
            $attributeSizeId = $attributeSizeSqlResult[0]['attribute_id'] ?? null;
            $attributeSizeName = $attributeSizeSqlResult[0]['attribute_name'] ?? null;

            $this->logger->info("categoryName: " . $categoryName . " attributeColorId: " . $attributeColorId  .
                " attributeColorName: " . $attributeColorName . " attributeSizeId: " . $attributeSizeId . " attributeName: " . $attributeSizeName);
            if (!$attributeColorId || !$attributeColorName || !$attributeSizeId || !$attributeSizeName) {
                $this->logger->error("attribute color or size not found in DB");
                continue;
            }

            $attributes = [];
            $hasColor = isset($product['renk']) && !empty(trim($product['renk']));
            $hasSize = isset($product['ebat']) && !empty(trim($product['ebat']));
            if ($hasColor && $hasSize) {
                $colorValue = trim($product['renk']);
                $bestColorMatch = $this->findBestAttributeMatch($attributeColorId, $colorValue, 0);

                $sizeValue = trim($product['ebat']);
                $bestSizeMatch = $this->findBestAttributeMatch($attributeSizeId, $sizeValue, 1);

                if ($bestColorMatch && $bestSizeMatch) {
                    $attributes[] = [
                        'id' => $attributeColorId,
                        'ValueId' => $bestColorMatch['attribute_value_id'],
                        "TextLength" => 0
                    ];
                    $attributes[] = [
                        'id' => $attributeSizeId,
                        'ValueId' => $bestSizeMatch['attribute_value_id'],
                        "TextLength" => 0
                    ];
                    $this->logger->info("best color match: {$bestColorMatch['name']}:{$bestColorMatch['attribute_value_id']}");
                    $this->logger->info("best size match: {$bestSizeMatch['name']}:{$bestSizeMatch['attribute_value_id']}");
                }
                else {
                    $this->logger->error("best color match not found: {$colorValue}");
                    $this->logger->error("best size match not found: {$sizeValue}");
                    continue;
                }
            }
            $product['Attributes'] = $attributes;
        }
        return $data;
    }

    private function parseDimensions($value): ?array
    {
        $normalized = strtolower(trim($value));
        $normalized = str_replace(',', '.', $normalized);
        $normalized = preg_replace('/[^0-9.x]/', '', $normalized);
        $parts = explode('x', $normalized);
        if (count($parts) >= 2 && is_numeric($parts[0]) && is_numeric($parts[1])) {
            return [
                'width' => (int) round((float) $parts[0]),
                'height' => (int) round((float) $parts[1]),
            ];
        }
        if (count($parts) === 1 && is_numeric($parts[0])) {
            return [
                'width' => (int) round((float) $parts[0]),
                'height' => 0,
            ];
        }
        return null;
    }

    /**
     * @param int $attributeId
     * @param string $searchValue
     * @param int $threshold
     * @return array|null
     */
    private function findBestAttributeMatch($attributeId, $searchValue, $isSize, $threshold = 80): ?array
    {
        $searchValueNormalized = $this->normalizeAttributeValue($searchValue);
        if ($isSize) {
            $searchDims = $this->parseDimensions($searchValueNormalized);
        }
        $sql = "SELECT attribute_value_id, name FROM iwa_ciceksepeti_category_attributes_values 
            WHERE attribute_id = :attribute_id";
        $allValues = Utility::fetchFromSql($sql, ['attribute_id' => $attributeId]);
        if (empty($allValues)) {
            return null;
        }
        $bestMatch = null;
        $highestSimilarity = 0;
        $smallestDiff = PHP_INT_MAX;
        foreach ($allValues as $value) {
            $dbValueNormalized  = $this->normalizeAttributeValue($value['name']);
            if ($searchValueNormalized === $dbValueNormalized) {
                $this->logger->info("fully matched Pim Value -> Ciceksepeti DB Value : {$searchValueNormalized} -> {$dbValueNormalized}");
                return $value;
            }
            if ($isSize) {
                $dbDims = $this->parseDimensions($dbValueNormalized);
                if ($searchDims && $dbDims) {
                    $widthDiff = $searchDims['width'] - $dbDims['width'];
                    $heightDiff = $searchDims['height'] - $dbDims['height'];
                    $totalDiff = $widthDiff + $heightDiff;
                    if ($widthDiff >= 0 && $widthDiff <= 25 && ($searchDims['height'] === 0 || ($heightDiff >= 0 && $heightDiff <= 25)) && $totalDiff < $smallestDiff) {
                        $smallestDiff = $totalDiff;
                        $bestMatch = $value;
                    }
                }
            }
            else {
                $levenDistance = levenshtein($searchValueNormalized, $dbValueNormalized);
                $maxLength = max(mb_strlen($searchValueNormalized), mb_strlen($dbValueNormalized));
                $similarity = 100 - ($levenDistance * 100 / ($maxLength > 0 ? $maxLength : 1));
                if ($similarity >= $threshold && $similarity > $highestSimilarity) {
                    $highestSimilarity = $similarity;
                    $bestMatch = $value;
                }
            }
        }
        if ($bestMatch) {
            $this->logger->info("best match Pim Value -> Ciceksepeti DB Value : {$searchValueNormalized} -> {$bestMatch['attribute_value_id']}:{$bestMatch['name']}");
        }
        else {
            $this->logger->info("best match null");
        }
        return $bestMatch;
    }

    /**
     * @param string $value
     * @return string
     */
    private function normalizeAttributeValue($value): string
    {
        if (!empty($value)) {
            $value = trim($value);
            $search = ['ı', 'ğ', 'ü', 'ş', 'ö', 'ç', 'İ', 'Ğ', 'Ü', 'Ş', 'Ö', 'Ç'];
            $replace = ['i', 'g', 'u', 's', 'o', 'c', 'i', 'g', 'u', 's', 'o', 'c'];
            $value = str_replace($search, $replace, $value);
            $value = mb_strtolower($value, 'UTF-8');
            $value = preg_replace('/\s+/', '', $value);
        }
        return $value;
    }

    public function categoryAttributeUpdate($marketplaceId): void
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

    public function getCiceksepetiCategoriesDetails(): false|array|string
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
