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
    }

    /**
     * @throws Exception
     */
    public function __invoke(ProductListingMessage $message): void
    {
        if (method_exists($message, 'getLogger') && $message->getLogger() instanceof LoggerInterface) {
            $this->logger = $message->getLogger();
        }
        echo "Ciceksepeti Listing Handler\n";
        $this->logger->info("[" . __METHOD__ . "] 🚀 Ciceksepeti Listing Handler Started");
        $actionType = $message->getActionType();
        echo "action type: $actionType\n";
        $this->logger->info("[" . __METHOD__ . "] ✅ Action Type: $actionType ");
        match ($actionType) {
            'list' => $this->processNewListing($message),
            'update_list' => $this->processUpdateListing($message),
            default => throw new \InvalidArgumentException("Unknown Action Type: $actionType")
        };

//        sleep(5);
//        $traceId = $message->getTraceId();
//        echo "Ciceksepeti Listing Handler\n";
//        $this->logger->info("🚀 [Listing Started] Automated product listing process started Ciceksepeti | Trace ID: {$traceId}");
//        $categories = $this->getCiceksepetiCategoriesDetails();
//        echo "ciceksepeti categories fetched\n";
//        $this->logger->info("✅ [Category Data] Ciceksepeti category details successfully retrieved.");
//        $jsonString = $this->listingHelper->getPimListingsInfo($message);
//        $this->printProductInfoLogger($jsonString);
//        $this->logger->info("✅ [PIM Listings] PIM listings information successfully completed.");
//        $messageType = $message->getActionType();
//        $this->logger->info("📝 [Action Type] Processing action of type: {$messageType}");
//        match ($messageType) {
//            'list' => $this->processListingData($jsonString, $categories),
//            default => throw new \InvalidArgumentException("Unknown Action Type: $messageType"),
//        };
    }

    private function processNewListing($message)
    {
        $this->logger->info("[" . __METHOD__ . "] ✅ Processing New Listing ");
        $listingInfo = $this->listingHelper->getPimListingsInfo($message, $this->logger);
        $this->logger->info("[" . __METHOD__ . "] ✅ Pim Listings Info Fetched ");
        $categories = $this->getCiceksepetiCategoriesDetails();
        $this->logger->info("[" . __METHOD__ . "] ✅ Category Data Fetched ");
        print_r(json_encode($listingInfo));
        // normalize ciceksepeti data
        // gemini (color, description, categoryid)
        // find attributes
        // normalize attributes
        // last control
        // ciceksepeti api send





        // tum bilgileri aldık alınan bilgiler tüm mağazacalar için geçerli olur
        // getpimlistings info artık tüm pazaryerleine uygundur
        // şimdi çiçeksepetine özel işlemleri bu sınıfta gerçekleştirmekte özgürüz
        // fieldlar ciceksepeti isterlerine uygun olacak
        // açıklama içerisinden size bilgisi gemini ile çıkartılacak
        // size bmap bilgisi açıklamaya eklecenek
        // title ciceksepeti ilk gönerileni alıyor o yüzden title tüm ürünler için aynı olacak size bilgi renk bilgisi içermeyecek
        print_r($listingInfo);

        // referans alınan marketplace biligisi variant id bilgisi gönderilir variant id ler referansa ait zaten
        // referans kullanılarak her mağaza için alınacak bilgileri alacağız

    }

    private function processUpdateListing($message)
    {
        //
    }

    private function getCiceksepetiCategoriesDetails(): false|array|string
    {
        $categoryIdList = $this->getCiceksepetiListingCategoriesIdList();
        if (empty($categoryIdList)) {
            return [];
        }
        $inClause = implode(',', array_fill(0, count($categoryIdList), '?'));
        $sql = "SELECT * FROM iwa_ciceksepeti_categories WHERE id IN ($inClause)";
        $categories = Utility::fetchFromSql($sql, $categoryIdList);
        if (empty($categories)) {
            return [];
        }
        return json_encode($categories, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
    }

    private function getCiceksepetiListingCategoriesIdList(): array
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

//    private function ciceksepetiNormalizeData($data): array
//    {
//
//
//    }















    private function printProductInfoLogger(string $jsonString): void
    {
        $jsonData = json_decode($jsonString, true);
        if (is_array($jsonData)) {
            foreach ($jsonData as $product) {
                $productName = $product['productName'] ?? 'Unknown';
                $mainProductCode = $product['mainProductCode'] ?? 'Unknown';
                $stockCode = $product['stockCode'] ?? 'Unknown';
                $size = $product['size'] ?? 'Unknown';
                $color = $product['color'] ?? 'Unknown';

                $this->logger->info("✅ [Product Info] Product: {$productName}, MainProductCode: {$mainProductCode}, StockCode: {$stockCode}, Size: {$size}, Color: {$color}");
            }
        } else {
            $this->logger->error("❌ [PIM Data Error] PIM data is invalid or missing.");
        }
    }

    private function processListingData($jsonString, $categories)
    {
        $fullData = json_decode($jsonString, true);
        if (!$fullData) {
            $this->logger->error("❌ [Invalid JSON] Invalid JSON data received: " . $jsonString);
            return;
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
        print_r($mergedResults);
        $this->logger->info("Gemini chat result : " . json_encode($mergedResults, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT));
        $data = $this->fillAttributeData($mergedResults);
        if (empty($data)) {
            $this->logger->error("❌ [No Data] No products found in the data array.");
            return [];
        }
        foreach ($data as $sku => $product) {
            if (isset($product['Attributes']) && empty($product['Attributes'])) {
                $this->logger->info("❌ [Attributes Empty] Attributes is empty for SKU: {$product['stockCode']}");
            } else {
                $this->logger->info("✔️ [Attributes Found] Attributes filled for SKU: {$product['stockCode']}");
            }
        }
        $this->logger->info("✅ [Filled Attributes Data] All attributes data processed: " . json_encode($data, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT));
        $formattedData = $this->fillMissingListingDataAndFormattedCiceksepetiListing($data);
        print_r($formattedData);
        $this->logger->info("✅ [Formatted Data]: " . $formattedData);
        $ciceksepetiConnector = new CiceksepetiConnector(Marketplace::getById(265384));
        $result = $ciceksepetiConnector->createListing($formattedData);
        $this->logger->info("✅ [CiceksepetiConnector] Result batch:\n" . json_encode($result, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES));
        print_r($result);
    }

    private function fillMissingListingDataAndFormattedCiceksepetiListing($data): false|string
    {
        $data = $this->removeCommonAttributes($data);
        $formattedData = [];
        $seenAttributes = [];
        foreach ($data as $sku => $product) {
            $salesPrice = $product['salesPrice'] ?? 0;
            $attributes = $product['Attributes'] ?? null;
            $description = $product['description'];
            $stockCode = $product['stockCode'] ?? 'UNKNOWN';
            $hasValidPrice = $salesPrice !== 0 && $salesPrice !== "0";
            $hasAttributes = $attributes !== null;
            $hasValidDescription = mb_strlen($description) >= 30;
            if (!$hasValidPrice) {
                $this->logger->error("❌ [Validation Error] Invalid or missing sales price for SKU: {$stockCode}");
            }
            if (!$hasAttributes) {
                $this->logger->error("❌ [Validation Error] Missing attributes for SKU: {$stockCode}");
            }
            if (!$hasValidDescription) {
                $this->logger->error("❌ [Validation Error] Description too short (<30 chars) for SKU: {$stockCode}");
            }

            if (!$hasValidPrice || !$hasAttributes || !$hasValidDescription) {
                continue;
            }
            $attributesKey = json_encode($attributes, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
            if (isset($seenAttributes[$attributesKey])) {
                $this->logger->info("🔁 [Duplicate Skipped] SKU: {$stockCode} - Attributes already processed.");
                continue;
            }
            $seenAttributes[$attributesKey] = true;
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
                'deliveryMessageType' => $product['deliveryMessageType'],
                'deliveryType' => $product['deliveryType'],
                'stockQuantity' => $product['stockQuantity'],
                'salesPrice' => $product['salesPrice'],
                'images' => $product['images'],
                'Attributes' => $product['Attributes'],
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
                "color": "Renk bilgisi"
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
                Renk örnekleri:
                    Mixed => Karışık,
                    Tuana => Antrasit,
                    Betül => Açık Meşe,
                    Dark Brown => Kahverengi,
                    Light Brown => Ceviz,
                    Karışık Bordo => Bordo-Siyah,
                    Karışık Gold => Mavi-Altın,
                    Karışık Gri => Siyah-Gri-Beyaz,
                    Crimson => Kırmızı,
                    Navy => Mavi,
                    Sage => Yeşil,
                    Nimbus => Gri,
                    Terracotta => Turuncu,
                    Soil => Kahverengi,
                    Shiny Silver => Gümüş-Sarı,
                    Shiny Gold => Sarı Altın,
                    Shiny Copper => Bakır- Altın,
                    Bu renkleri olduğu gibi kullan '-' ve boşluklara dikkat et bunları kaldırma.  
            **Veri formatı**: Lütfen yalnızca aşağıdaki **JSON verisini** kullanın ve dışarıya çıkmayın. Çıkışınızı bu veriye dayalı olarak oluşturun:
            İşte veri: $jsonString
            Kategori Verisi: $categories
        EOD;
    }

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
            if (empty($variantAttributes['color']) && empty($variantAttributes['sizeLabel'])) {
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
        if (!empty($variantAttributes['color']) && isset($product['color']) && !empty(trim($product['color']))) {
            $colorAttrId = $variantAttributes['color']['id'];
            $colorValue = trim($product['color']);
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
        if (!empty($variantAttributes['size']) && isset($product['sizeLabel']) && !empty(trim($product['sizeLabel']))) {
            $sizeAttrId = $variantAttributes['size']['id'];
            $sizeValue = trim($product['sizeLabel']);
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
                'height' => 0
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
            //$dbValueNormalized = $this->normalizeAttributeValue($value['name']);
            if ($searchValue === $value['name']) {
                $this->logger->info("✅ [AttributeMatch] Exact match: '{$searchValue}' ➜ '{$value['name']}' (ID: {$value['attribute_value_id']})");
                return $value;
            }
//            $searchValueNormalized = $this->normalizeAttributeValue($searchValue);
//            $searchDims = $isSize ? $this->parseDimensions($searchValueNormalized) : null;
//            if ($isSize && $searchDims) {
//                $dbDims = $this->parseDimensions($dbValueNormalized);
//                if ($dbDims) {
//                    $widthDiff = $searchDims['width'] - $dbDims['width'];
//                    $heightDiff = $searchDims['height'] - $dbDims['height'];
//                    $totalDiff = $widthDiff + $heightDiff;
//                    $widthOk = $widthDiff >= 0 && $widthDiff <= 25;
//                    $heightOk = $searchDims['height'] === 0 || ($heightDiff >= 0 && $heightDiff <= 25);
//                    if ($widthOk && $heightOk && $totalDiff < $smallestDiff) {
//                        $smallestDiff = $totalDiff;
//                        $bestMatch = $value;
//                    }
//                }
//            }
        }
//        if ($bestMatch) {
//            $this->logger->info("🔍 [AttributeMatch] Approximate match: '{$searchValue}' ➜ '{$bestMatch['name']}' (ID: {$bestMatch['attribute_value_id']})");
//        } else {
//            $this->logger->notice("❌ [AttributeMatch] No match found for: '{$searchValue}' (attributeId: {$attributeId})");
//        }
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



}
