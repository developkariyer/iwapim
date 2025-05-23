<?php
namespace App\MessageHandler;


use App\Connector\Gemini\GeminiConnector;
use App\Connector\Marketplace\CiceksepetiConnector;
use App\Message\ProductListingMessage;
use App\Model\DataObject\Marketplace;
use App\Model\DataObject\Product;
use App\Model\DataObject\VariantProduct;
use App\Utils\Utility;
use Exception;
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
     * @throws Exception
     */
    public function __invoke(ProductListingMessage $message): void
    {
        sleep(5);
        $traceId = $message->getTraceId();
        echo "Ciceksepeti Listing Handler\n";
        $this->logger->info("🚀 [Listing Started] Automated product listing process started Ciceksepeti | Trace ID: {$traceId}");
        $categories = $this->getCiceksepetiCategoriesDetails();
        echo "ciceksepeti categories fetched\n";
        $this->logger->info("✅ [Category Data] Ciceksepeti category details successfully retrieved.");
        $jsonString = $this->listingHelper->getPimListingsInfo($message);
        print_r($jsonString);
        $this->printProductInfoLogger($jsonString);
        $this->logger->info("✅ [PIM Listings] PIM listings information successfully completed.");
        $messageType = $message->getActionType();
        $this->logger->info("📝 [Action Type] Processing action of type: {$messageType}");
        match ($messageType) {
            'list' => $this->processListingData($jsonString, $categories),
            default => throw new \InvalidArgumentException("Unknown Action Type: $messageType"),
        };
    }

    private function printProductInfoLogger($jsonString): void
    {
        $jsonData = json_decode($jsonString, true);
        if (isset($jsonData['Ciceksepeti']) && is_array($jsonData['Ciceksepeti'])) {
            foreach ($jsonData['Ciceksepeti'] as $productId => $productData) {
                $name = $productData['name'] ?? 'Unknown';
                $this->logger->info("✅ [Product Info] Product ID: {$productId}, Product Name: {$name}");
                if (isset($productData['skus']) && is_array($productData['skus'])) {
                    foreach ($productData['skus'] as $iwasku => $variantProduct) {
                        $size = $variantProduct['size'] ?? 'Unknown';
                        $color = $variantProduct['color'] ?? 'Unknown';
                        $this->logger->info("✅ [Variant Info] IWASKU: {$iwasku}, Size: {$size}, Color: {$color}");
                    }
                } else {
                    $this->logger->error("❌ [SKUs Error] SKUs data is missing or invalid.");
                }
            }
        } else {
            $this->logger->error("❌ [PIM Data Error] PIM data is invalid or missing.");
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

    private function processListingData($jsonString, $categories)
    {
        $fullData = json_decode($jsonString, true);
        if (!$fullData) {
            $this->logger->error("❌ [Invalid JSON] Invalid JSON data received: " . $jsonString);
            throw new Exception("❌ [Invalid JSON] Invalid JSON data");
        }
        $chunks = array_chunk($fullData, 2);
        $mergedResults = [];
        $totalChunks = count($chunks);
        $this->logger->info("✅ [Chunks Processed] Total chunks to process: {$totalChunks}");
        foreach ($chunks as $index => $chunkData) {
            $chunkNumber = $index + 1;
            $this->logger->info("🔄 [Chunk Processing] Processing chunk {$chunkNumber} / {$totalChunks}...");
            echo "\n🔄 Chunk {$chunkNumber} / {$totalChunks} processing...\n";
            $chunkJsonString = json_encode(['products' => $chunkData], JSON_UNESCAPED_UNICODE);
            $prompt = $this->generateListingPrompt($chunkJsonString, $categories);
            $result = GeminiConnector::chat($prompt, 'ciceksepeti');
            $parsedResult = $this->parseGeminiResult($result);
            if (!$parsedResult) {
                $this->logger->error("Gemini result is empty or error gemini api");
                echo "⚠️ Error: Chunk {$chunkNumber} / {$totalChunks} result is empty or error gemini api \n";
                continue;
            }
            foreach ($parsedResult as $updateData) {
                foreach ($chunkData as &$product) {
                    if ($product['stockCode'] === $updateData['stockCode']) {
                        $product['categoryId'] = $updateData['categoryId'] ?? $product['categoryId'];
                        $product['size'] = $updateData['size'] ?? $product['size'];
                        $product['color'] = $updateData['color'] ?? $product['color'];
                    }
                }
            }
            unset($product);
            $mergedResults = array_merge($mergedResults, $chunkData);
            echo "✅ Gemini result success. Chunk {$chunkNumber} complated.\n";
            $this->logger->info("✅ [Gemini Success] Gemini result success. Chunk {$chunkNumber} completed.");
            sleep(5);
        }
        $this->logger->info("Gemini chat result : " . json_encode($mergedResults, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT));
        print_r($mergedResults);

    }

//    private function processListingData($jsonString, $categories)
//    {
//        $fullData = json_decode($jsonString, true);
//        if (!$fullData || !isset($fullData['Ciceksepeti'])) {
//            $this->logger->error("❌ [Invalid JSON] Invalid JSON data received: " . $jsonString);
//            throw new Exception("❌ [Invalid JSON] Invalid JSON data");
//        }
//        $chunks = $this->chunkSkus($fullData['Ciceksepeti']);
//        $mergedResults = [];
//        $totalChunks = count($chunks);
//        $this->logger->info("✅ [Chunks Processed] Total chunks to process: {$totalChunks}");
//        foreach ($chunks as $index => $chunkData) {
//            $chunkNumber = $index + 1;
//            $this->logger->info("🔄 [Chunk Processing] Processing chunk {$chunkNumber} / {$totalChunks}...");
//            echo "\n🔄 Chunk {$chunkNumber} / {$totalChunks} processing...\n";
//            $chunkJsonString = json_encode(['Ciceksepeti' => $chunkData], JSON_UNESCAPED_UNICODE);
//            $prompt = $this->generateListingPrompt($chunkJsonString, $categories);
//            $result = GeminiConnector::chat($prompt, 'ciceksepeti');
//            $parsedResult = $this->parseGeminiResult($result);
//            if (!$parsedResult) {
//                $this->logger->error("Gemini result is empty or error gemini api");
//                echo "⚠️ Error: Chunk {$chunkNumber} / {$totalChunks} result is empty or error gemini api \n";
//                continue;
//            }
//            $mergedResults = array_merge_recursive($mergedResults, $parsedResult);
//            echo "✅ Gemini result success. Chunk {$chunkNumber} complated.\n";
//            $this->logger->info("✅ [Gemini Success] Gemini result success. Chunk {$chunkNumber} completed.");
//            sleep(5);
//        }
//        $this->logger->info("Gemini chat result : " . json_encode($mergedResults, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT));
//        $data = $this->fillAttributeData($mergedResults);
//        if (empty($data)) {
//            $this->logger->error("❌ [No Data] No products found in the data array.");
//            return [];
//        }
//        foreach ($data as $sku => $product) {
//            if (isset($product['Attributes']) && empty($product['Attributes'])) {
//                $this->logger->info("❌ [Attributes Empty] Attributes is empty for SKU: {$product['stockCode']}");
//            } else {
//                $this->logger->info("✔️ [Attributes Found] Attributes filled for SKU: {$product['stockCode']}");
//            }
//        }
//        $this->logger->info("✅ [Filled Attributes Data] All attributes data processed: " . json_encode($data, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT));
//        $formattedData = $this->fillMissingListingDataAndFormattedCiceksepetiListing($data);
//        print_r($formattedData);
//        $this->logger->info("✅ [Formatted Data]: " . $formattedData);
////        $ciceksepetiConnector = new CiceksepetiConnector(Marketplace::getById(265384));
////        $result = $ciceksepetiConnector->createListing($formattedData);
////        $this->logger->info("✅ [CiceksepetiConnector] Result batch:\n" . json_encode($result, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES));
////        print_r($result);
//    }

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
            $stockCode = $product['stockCode'] ?? 'UNKNOWN';
            $hasImages = !empty($httpsImages);
            $hasValidPrice = $salesPrice !== 0 && $salesPrice !== "0";
            $hasAttributes = $attributes !== null;
            $hasValidDescription = mb_strlen($description) >= 30;
            if (!$hasImages) {
                $this->logger->error("❌ [Validation Error] Missing or invalid images for SKU: {$stockCode}");
            }
            if (!$hasValidPrice) {
                $this->logger->error("❌ [Validation Error] Invalid or missing sales price for SKU: {$stockCode}");
            }
            if (!$hasAttributes) {
                $this->logger->error("❌ [Validation Error] Missing attributes for SKU: {$stockCode}");
            }
            if (!$hasValidDescription) {
                $this->logger->error("❌ [Validation Error] Description too short (<30 chars) for SKU: {$stockCode}");
            }
            if (!$hasImages || !$hasValidPrice || !$hasAttributes || !$hasValidDescription) {
                continue;
            }
            $description = str_replace("\n", "<br>", $description);
            $description = str_replace(['\/', '\"', '\\\\', '\\n', '\\r', '\\t'], ['/', '"', '\\', "\n", "\r", "\t"], $description);
            $description = preg_replace_callback('/\\\\u([0-9a-fA-F]{4})/', function ($matches) {
                return mb_convert_encoding(pack('H*', $matches[1]), 'UTF-8', 'UTF-16BE');
            }, $description);
            $description = html_entity_decode($description, ENT_QUOTES | ENT_HTML5, 'UTF-8');
            $formattedProduct = [
                'productName' => mb_strlen($product['productName']) > 255
                    ? mb_substr($product['productName'], 0, 255)
                    : $product['productName'],
                'mainProductCode' => $product['mainProductCode'],
                'stockCode' => $stockCode,
                'categoryId' => $product['categoryId'],
                'description' => mb_strlen($description) > 20000
                    ? mb_substr($description, 0, 20000)
                    : $description,
                'deliveryMessageType' => 5,
                'deliveryType' => 2,
                'stockQuantity' => 3,
                'salesPrice' => $salesPrice * 1.5,
                'images' => $httpsImages,
                'Attributes' => $attributes,
            ];
            $formattedData['products'][] = $formattedProduct;
            $this->logger->info("✅ [Formatted] Product ready for listing ➜ SKU: {$stockCode}");
        }
        $result = json_encode($formattedData, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);
        if ($result === false) {
            $this->logger->error("❌ [JSON Encode Error] Failed to encode formatted listing data.");
            return false;
        }
        $this->logger->info("📦 [Listing Data Ready] " . count($formattedData['products']) . " product(s) formatted for Çiçeksepeti listing.");
        return $result;
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
              {
                "stockCode": AAA11
                "categoryId": 111,
                "color": "Renk bilgisi",
                "size": "Ebat bilgisi"
              },
              {
                ...
              }
            }
            Hiçbir açıklama, kod bloğu, yorum ekleme.  
            Sadece geçerli, düzgün bir JSON üret.
            Bu JSON'da eksik alan olan kategoriyi verdiğim kategori bilgilerine göre bulmanı istiyorum.
            Gönderdiğim veri de stockCode yer almaktadır çıktı formatında bunu kullanacaksın.
           
            -**categoryId**: Kategori verisinden en uygun kategoriyi bul id sini al ve kaydet

            -**renk**: 
                - renk bilgisi verideki color fieldı Türkçe ye çevir çevirdiğinde çiçeksepetinde bulunan çok bilinen renklerden olsun Eğer iki renk varsa her iki rengi de çevir, teke düşürme iki rengide örneğin:
                - Altın, Gümüş, Turkuaz, Kırmızı, Mavi, Bordo, Turuncu, Yeşil, Sarı, Pembe, Füme, Kamuflaj, Kahverengi, Mor, Bej, Lacivert, Metal, Lila, Haki, Taba, Beyaz, Magenta, Mürdüm, Karışık, Gri,
                Antrasit, Açık Mavi, Bakır, Vişne, Açık Pembe, Bronz, Ekru, Taş renklerinden kullan 2 renk varsa ikiside bunlara uyumlu olsun aralarında boşluk olsun.
            
            -**ebat**: ebat bilgisi verideki size fieldı cm olarak al (örn: 250cm) yanında boyut belirten S-M-XL gibi durum varsa bunu alma.
            
            **Veri formatı**: Lütfen yalnızca aşağıdaki **JSON verisini** kullanın ve dışarıya çıkmayın. Çıkışınızı bu veriye dayalı olarak oluşturun:
            İşte veri: $jsonString
            Kategori Verisi: $categories
        EOD;
    }

//    private function generateListingPrompt($jsonString, $categories): string
//    {
//        return <<<EOD
//            Sen bir e-ticaret uzmanısın ve ÇiçekSepeti pazaryeri için ürün listeleri hazırlıyorsun.
//            **Çıkış formatı**:
//            Sadece aşağıdaki gibi bir JSON döndür:
//            {
//              "SKU1": {
//                "productName": "Ürün adı",
//                "mainProductCode": "Ana ürün kodu",
//                "stockCode": "Stok kodu",
//                "description": "Ürün açıklaması",
//                "images": ["resim1", "resim2"],
//                "price": "100",
//                "categoryId": 1234,
//                "renk": "Renk bilgisi",
//                "ebat": "Ebat bilgisi"
//              },
//              "SKU2": {
//                ...
//              }
//            }
//            Hiçbir açıklama, kod bloğu, yorum ekleme.
//            Sadece geçerli, düzgün bir JSON üret.
//            Aşağıda bir ürün listeleme datası (JSON formatında) verilmiştir.
//            Bu JSON'da bazı alanlar eksik veya hatalı olabilir.
//            Gönderdiğim veride ana ürün kodu altında sku'lar ve bu skulara ait bilgiler yer almaktadır. Skuların altında "size" ve "color" bilgisi yer alacaktır.
//            ListingItems alanında bu ürüne ait farklı pazaryerlerine yapılmış listingler yer alır. Bunlara benzer ÇiçekSepeti özgün hale getireceğiz.
//
//            **Uyarı**: Lütfen yalnızca gönderdiğim **JSON verisini** kullanarak işlem yapınız ve dışarı çıkmayınız. Verilen verinin dışında başka veri kullanımı yapılmamalıdır.
//
//            Gönderdiğim veriye göre çıkarılması gereken ve ÇiçekSepeti listing formatında istenen alanlar skus dizisi altındaki tüm skulara ayrı olacak şekilde:
//            - **productName**: Gönderilen verideki **title** alanlarından alınır. Bu başlıklardan Türkçe olanları, ÇiçekSepeti'ne uygun şekilde güncellenmelidir. Bu alan her SKU için aynı olacak. Size ve renk bilgisi olmasın.
//            - **mainProductCode**: Gönderilen verideki ÇiçekSepeti altındaki **field** genelde 3 haneli ve sayı içeriyor. Örnek: ABC-12. Bu alan her SKU için aynı olacak.
//            - **stockCode**: Ürün SKU bilgisi gönderdiğim verideki skus altındaki verilerdir. Bu her SKU'ya özel olacak.
//            - **description**:
//                Açıklama (description) sadece ve sadece aşağıdaki şekilde oluşturulacak:
//                1. Türkçe açıklama: Türkçe açıklama verisi varsa, hiçbir değişiklik yapılmadan olduğu gibi kopyalanacak ve link ya da iletişim bilgileri çıkarılacaktır.
//                2. İngilizce açıklama: İngilizce açıklama verisi varsa, yalnızca doğru ve doğrudan çeviri yapılacak. Cümle yapısı, kelime sırası ve anlam korunacaktır. Yeniden yazma, özgünleştirme, yorum ekleme gibi işlemler yapılmayacaktır.
//                3. Yasaklı işlemler: "Create", "Enhance", "Summarize", "Rewrite", "Reformat" gibi işlemler yapılması halinde işlem başarısız olacaktır.
//                4. Türkçe olmayan açıklama: Eğer açıklama bulunmazsa, ürün adı (product name) ve renk bilgileri (color) yazılacaktır.
//                5. Mağaza bilgileri: Mağazaya dair herhangi bir bilgi veya açıklama silinecektir.
//                6. Çeviri düzeltmeleri: Eğer İngilizce açıklamada cümle bozukluğu varsa, bu bozukluk düzeltilerek cümle anlamı korunacaktır.
//                7. HTML formatı: Açıklama, profesyonel bir şekilde HTML formatında düzenlenecektir
//                Bu kurallara uymazsan cevabın geçersiz sayılacaktır.
//            - **images**:
//                - Her SKU için en fazla 5 adet olacak şekilde,`images` listesinden alınacaktır.
//                - Resimler dizi (array) formatında verilecektir.
//                - Yalnızca **en az 500x500** ve **en fazla 2000x2000** piksel boyutlarındaki görseller dahil edilecektir.
//                - Bu boyut aralığı dışında kalan görseller filtrelenecektir.
//                - Boş bırakma.
//
//            - **salesPrice**: Ürün içinde yer alan **price** alanını direkt kullan her sku için farklı olabilir buna dikkat et.
//
//            -**categoryId**: Kategori verisinden en uygun kategoriyi bul id sini al ve kaydet
//
//            -**renk**:
//                - renk bilgisi verideki sku altında color fieldı Türkçe ye çevir çevirdiğinde çiçeksepetinde bulunan çok bilinen renklerden olsun Eğer iki renk varsa her iki rengi de çevir, teke düşürme iki rengide örneğin:
//                - Altın, Gümüş, Turkuaz, Kırmızı, Mavi, Bordo, Turuncu, Yeşil, Sarı, Pembe, Füme, Kamuflaj, Kahverengi, Mor, Bej, Lacivert, Metal, Lila, Haki, Taba, Beyaz, Magenta, Mürdüm, Karışık, Gri,
//                Antrasit, Açık Mavi, Bakır, Vişne, Açık Pembe, Bronz, Ekru, Taş renklerinden kullan 2 renk varsa ikiside bunlara uyumlu olsun aralarında boşluk olsun.
//
//            -**ebat**: ebat bilgisi verideki sku altında size fieldı cm olarak al (örn: 250cm) yanında boyut belirten S-M-XL gibi durum varsa bunu alma.
//
//            **Veri formatı**: Lütfen yalnızca aşağıdaki **JSON verisini** kullanın ve dışarıya çıkmayın. Çıkışınızı bu veriye dayalı olarak oluşturun:
//            İşte veri: $jsonString
//            Kategori Verisi: $categories
//        EOD;
//    }

    /**
     * Fill attribute data for products with category-specific attributes
     *
     * @param array $data Product data from Gemini
     * @return array The product data with filled attributes
     */
    public function fillAttributeData(array $data): array
    {
        if (empty($data)) {
            $this->logger->error("❌ [Empty Data] No product data provided to fill attributes");
            return [];
        }
        foreach ($data as $sku => &$product) {
            $this->logger->info("🔵 [Product Processing] IWASKU: {$product['stockCode']}");
            if (empty($product['categoryId'])) {
                $this->logger->error("❌ [Missing CategoryId] Product {$product['stockCode']} has no category ID");
                continue;
            }
            $categoryInfo = $this->getCategoryInfo($product['categoryId']);
            if (!$categoryInfo) {
                continue;
            }
            $variantAttributes = $this->getVariantAttributes($product['categoryId']);
            if (empty($variantAttributes['color']) && empty($variantAttributes['size'])) {
                continue;
            }
            $product['Attributes'] = $this->buildProductAttributes(
                $product,
                $variantAttributes
            );
        }
        return $data;
    }

    /**
     * Get category information by ID
     *
     * @param int $categoryId The category ID
     * @return array|null Category data or null if not found
     */
    private function getCategoryInfo(int $categoryId): ?array
    {
        $categorySql = "SELECT category_name FROM iwa_ciceksepeti_categories WHERE id = :categoryId";
        $categoryData = Utility::fetchFromSql($categorySql, ['categoryId' => $categoryId]);
        if (empty($categoryData)) {
            $this->logger->error("❌ [Category Error] Category not found for categoryId: {$categoryId}");
            return null;
        }
        $categoryName = $categoryData[0]['category_name'] ?? null;
        $this->logger->info("✅ [Category Found] CategoryId: {$categoryId}, Name: {$categoryName}");
        return [
            'id' => $categoryId,
            'name' => $categoryName
        ];
    }

    /**
     * Get variant attributes (color and size) for a category
     *
     * @param int $categoryId The category ID
     * @return array Array with color and size attribute information
     */
    private function getVariantAttributes(int $categoryId): array
    {
        $result = [
            'color' => null,
            'size' => null
        ];
        $attributeColorSql = "SELECT attribute_id, attribute_name FROM iwa_ciceksepeti_category_attributes 
                          WHERE category_id = :categoryId 
                          AND type = 'Variant Özelliği' 
                          AND attribute_name = 'Renk' 
                          LIMIT 1";
        $colorData = Utility::fetchFromSql($attributeColorSql, ['categoryId' => $categoryId]);
        if (!empty($colorData)) {
            $result['color'] = [
                'id' => $colorData[0]['attribute_id'],
                'name' => $colorData[0]['attribute_name']
            ];
            $this->logger->info("✅ [Color Attribute] Found: ID: {$result['color']['id']}, Name: {$result['color']['name']}");
        } else {
            $this->logger->error("❌ [Color Attribute] Not found for categoryId: {$categoryId}");
        }
        $attributeSizeSql = "SELECT attribute_id, attribute_name FROM iwa_ciceksepeti_category_attributes 
                         WHERE category_id = :categoryId 
                         AND type = 'Variant Özelliği' 
                         AND (attribute_name = 'Ebat' OR attribute_name = 'Boyut' OR attribute_name = 'Beden') 
                         LIMIT 1";
        $sizeData = Utility::fetchFromSql($attributeSizeSql, ['categoryId' => $categoryId]);
        if (!empty($sizeData)) {
            $result['size'] = [
                'id' => $sizeData[0]['attribute_id'],
                'name' => $sizeData[0]['attribute_name']
            ];
            $this->logger->info("✅ [Size Attribute] Found: ID: {$result['size']['id']}, Name: {$result['size']['name']}");
        } else {
            $this->logger->error("❌ [Size Attribute] Not found for categoryId: {$categoryId}");
        }
        return $result;
    }

    /**
     * Build product attributes array from variant data
     *
     * @param array $product Product data
     * @param array $variantAttributes Available variant attributes
     * @return array Array of product attributes
     */
    private function buildProductAttributes(array $product, array $variantAttributes): array
    {
        $attributes = [];
        if (!empty($variantAttributes['color']) && isset($product['renk']) && !empty(trim($product['renk']))) {
            $colorAttrId = $variantAttributes['color']['id'];
            $colorValue = trim($product['renk']);
            $bestColorMatch = $this->findBestAttributeMatch($colorAttrId, $colorValue, false);
            if ($bestColorMatch) {
                $attributes[] = [
                    'id' => $colorAttrId,
                    'ValueId' => $bestColorMatch['attribute_value_id'],
                    'TextLength' => 0
                ];
                $this->logger->info("✅ [Color Match] Found: {$bestColorMatch['name']} (ID: {$bestColorMatch['attribute_value_id']})");
            } else {
                $this->logger->error("❌ [Color Match] Not found for value: {$colorValue}");
            }
        }
        if (!empty($variantAttributes['size']) && isset($product['ebat']) && !empty(trim($product['ebat']))) {
            $sizeAttrId = $variantAttributes['size']['id'];
            $sizeValue = trim($product['ebat']);
            $bestSizeMatch = $this->findBestAttributeMatch($sizeAttrId, $sizeValue, true);

            if ($bestSizeMatch) {
                $attributes[] = [
                    'id' => $sizeAttrId,
                    'ValueId' => $bestSizeMatch['attribute_value_id'],
                    'TextLength' => 0
                ];
                $this->logger->info("✅ [Size Match] Found: {$bestSizeMatch['name']} (ID: {$bestSizeMatch['attribute_value_id']})");
            } else {
                $this->logger->error("❌ [Size Match] Not found for value: {$sizeValue}");
            }
        }
        if (empty($attributes)) {
            $this->logger->warning("⚠️ [No Attributes] No attributes could be added for product: {$product['stockCode']}");
        }
        return $attributes;
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
    private function findBestAttributeMatch($attributeId, $searchValue, $isSize): ?array
    {
        $searchValueNormalized = $this->normalizeAttributeValue($searchValue);
        $searchDims = $isSize ? $this->parseDimensions($searchValueNormalized) : null;
        $sql = "SELECT attribute_value_id, name FROM iwa_ciceksepeti_category_attributes_values 
            WHERE attribute_id = :attribute_id";
        $allValues = Utility::fetchFromSql($sql, ['attribute_id' => $attributeId]);
        if (empty($allValues)) {
            $this->logger->warning("⚠️ [AttributeMatch] No attribute values found in DB for attributeId: {$attributeId}");
            return null;
        }
        $bestMatch = null;
        $smallestDiff = PHP_INT_MAX;
        foreach ($allValues as $value) {
            $dbValueNormalized = $this->normalizeAttributeValue($value['name']);
            if ($searchValueNormalized === $dbValueNormalized) {
                $this->logger->info("✅ [AttributeMatch] Exact match: '{$searchValue}' ➜ '{$value['name']}' (ID: {$value['attribute_value_id']})");
                return $value;
            }
            if ($isSize && $searchDims) {
                $dbDims = $this->parseDimensions($dbValueNormalized);
                if ($dbDims) {
                    $widthDiff = $searchDims['width'] - $dbDims['width'];
                    $heightDiff = $searchDims['height'] - $dbDims['height'];
                    $totalDiff = $widthDiff + $heightDiff;
                    $widthOk = $widthDiff >= 0 && $widthDiff <= 25;
                    $heightOk = $searchDims['height'] === 0 || ($heightDiff >= 0 && $heightDiff <= 25);
                    if ($widthOk && $heightOk && $totalDiff < $smallestDiff) {
                        $smallestDiff = $totalDiff;
                        $bestMatch = $value;
                    }
                }
            }
        }
        if ($bestMatch) {
            $this->logger->info("🔍 [AttributeMatch] Approximate match: '{$searchValue}' ➜ '{$bestMatch['name']}' (ID: {$bestMatch['attribute_value_id']})");
        } else {
            $this->logger->notice("❌ [AttributeMatch] No match found for: '{$searchValueNormalized}' (attributeId: {$attributeId})");
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

}
