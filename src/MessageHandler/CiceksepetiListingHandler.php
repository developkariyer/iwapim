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
    }

    private function processNewListing($message)
    {
//        $this->logger->info("[" . __METHOD__ . "] ✅ Processing New Listing ");
//        $listingInfo = $this->listingHelper->getPimListingsInfo($message, $this->logger);
//        $this->logger->info("[" . __METHOD__ . "] ✅ Pim Listings Info Fetched ");
//        $categories = $this->getCiceksepetiCategoriesDetails();
//        $this->logger->info("[" . __METHOD__ . "] ✅ Category Data Fetched ");
//        $geminiFilledData = $this->geminiProcess($listingInfo, $categories);
//        $controlGeminiResult = $this->controlGeminiFilledData($geminiFilledData);
//        if (!$controlGeminiResult) {
//            $this->logger->error("[" . __METHOD__ . "] ❌ Gemini Api Data Control Failed  ");
//            return;
//        }
//        $this->logger->info("[" . __METHOD__ . "] ✅ Gemini Data Filled ");
//        $filledAttributeData =  $this->fillAttributeData($geminiFilledData);
//        $this->logger->info("[" . __METHOD__ . "] ✅ Filled Attribute Data ");
//        $normalizedCiceksepetiData = $this->normalizeCiceksepetiData($filledAttributeData);
//        $this->logger->info("[" . __METHOD__ . "] ✅ Normalized Ciceksepeti Data ");
//        print_r($normalizedCiceksepetiData);
//        $ciceksepetiConnector = new CiceksepetiConnector(Marketplace::getById(265384));
//        $result = $ciceksepetiConnector->createListing($normalizedCiceksepetiData);
//        $this->logger->info("✅ [CiceksepetiConnector] Result batch:\n" . json_encode($result, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES));
//        print_r($result);
    }

    private function controlGeminiFilledData($data)
    {
         foreach ($data as $product) {
             if (
                 is_null($product['geminiCategoryId']) ||
                 is_null($product['geminiTitle']) ||
                 is_null($product['geminiDescription']) ||
                 is_null($product['geminiColor'])
             ) {
                 return false;
             }
         }
         return true;
    }

    private function geminiProcess($data, $categories)
    {
        $firstProduct = $data[0];
        $geminiData = [
            'title'       => $firstProduct['title'],
            'description' => $firstProduct['description'] ?? null,
            'categoryId'  => null,
            'variants'    => []
        ];
        foreach ($data as $product) {
            $geminiData['variants'][] = [
                'stockCode' => $product['stockCode'] ?? null,
                'color'     => $product['color'] ?? null,
            ];
        }
        $this->logger->info("[" . __METHOD__ . "] ✅ Gemini Data Created Variant Count: " . count($geminiData['variants']));
        $prompt = $this->generateListingPrompt(json_encode(['products' => $geminiData], JSON_UNESCAPED_UNICODE), $categories);
        $this->logger->info("[" . __METHOD__ . "] ✅ Gemini Api Send Data ");
        $geminiApiResult = GeminiConnector::chat($prompt, 'ciceksepeti');
        $geminiResult = $this->parseGeminiResult($geminiApiResult);
        foreach ($data as &$product) {
            $product['geminiCategoryId']   = $geminiResult['categoryId'] ?? null;
            $product['geminiTitle']        = $geminiResult['title'] ?? null;
            $product['geminiDescription']  = $geminiResult['description'] ?? null;
            $product['geminiColor'] = null;
            foreach ($geminiResult['variants'] as $variant) {
                if ($variant['stockCode'] === $product['stockCode']) {
                    $product['geminiColor'] = $variant['color'];
                    break;
                }
            }
        }
        return $data;
    }

    private function generateListingPrompt($jsonString, $categories): string
    {
        return <<<EOD
            Sen bir e-ticaret uzmanısın ve ÇiçekSepeti pazaryeri için ürün listeleri hazırlıyorsun. 
            Sana gönderdiğim veri dışına çıkma.
            Hiçbir açıklama, kod bloğu, yorum ekleme.  
            Sadece geçerli, düzgün bir JSON üret.
            Bu JSON'da eksik alan olan kategoriyi verdiğim kategori bilgilerine göre bulmanı istiyorum.
            Gönderdiğim veri de stockCode yer almaktadır çıktı formatında bunu kullanacaksın.
            -**title**: Title bilgisini değiştirmeden size veya renk bilgisi içeriyorsa bunu kaldır başka herhangi bir müdahalede bulunma tüm variantlar için aynı.
            -**description**: Açıklama bilgisini değiştirmeden size bilgilerini kaldır başka herhangi bir müdahalede bulunma tüm variantlar için aynı.
            -**categoryId**: Kategori verisinden en uygun kategoriyi bul id sini al ve kaydet
            -**color**: 
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
                    Tek Renk => Standart,
                    Cherry  Black   => Siyah,
                    Cherry  Copper  => Bakır,
                    Cherry  Gold    => Altın,
                    Cherry  Silver  => Gümüş,
                    Naturel Black   => Beyaz-Siyah,
                    Naturel Copper  => Beyaz-Bakır,
                    Naturel Gold    => Beyaz-Altın,
                    Naturel Silver  => Beyaz - Gümüş
                    Bu renkleri olduğu gibi kullan '-' ve boşluklara dikkat et bunları kaldırma.  
            **Veri formatı**: Lütfen yalnızca aşağıdaki **JSON verisini** kullanın ve dışarıya çıkmayın. Çıkışınızı bu veriye dayalı olarak oluşturun:
            İşte veri: $jsonString
            Kategori Verisi: $categories
        EOD;
    }

    private function parseGeminiResult($result)
    {
        $json = $result['candidates'][0]['content']['parts'][0]['text'] ?? '';
        $json = preg_replace('/[\x00-\x1F\x7F]/u', '', $json);
        $data = json_decode($json, true);
        return (json_last_error() === JSON_ERROR_NONE) ? $data : null;
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

    public function fillAttributeData(array $data): array
    {
        if (empty($data)) {
            $this->logger->error("[" . __METHOD__ . "] ❌  No product data provided to fill attributes ");
            return [];
        }
        foreach ($data as $sku => &$product) {
            $this->logger->info("[" . __METHOD__ . "] 🔵 IWASKU: {$product['stockCode']} ");
            if (empty($product['geminiCategoryId'])) {
                $this->logger->error("[" . __METHOD__ . "] ❌ Missing CategoryId Product {$product['stockCode']} Has No CategoryID ");
                continue;
            }
            $categoryInfo = $this->getCategoryInfo($product['geminiCategoryId']);
            if (!$categoryInfo) {
                continue;
            }
            $variantAttributes = $this->getVariantAttributes($product['geminiCategoryId']);
            if (empty($variantAttributes['color']) && empty($variantAttributes['size'])) {
                $this->logger->error("[" . __METHOD__ . "] ❌ Size AttributeId Not Found: {$product['stockCode']} ");
                continue;
            }
            $product['Attributes'] = $this->buildProductAttributes(
                $product,
                $variantAttributes
            );
        }
        return $data;
    }

    private function getCategoryInfo(int $categoryId): ?array
    {
        $categorySql = "SELECT category_name FROM iwa_ciceksepeti_categories WHERE id = :categoryId";
        $categoryData = Utility::fetchFromSql($categorySql, ['categoryId' => $categoryId]);
        if (empty($categoryData) || !isset($categoryData[0])) {
            $this->logger->error("[" . __METHOD__ . "] ❌ Category Error Category Not Found For categoryId: {$categoryId}");
            return null;
        }
        $categoryName = $categoryData[0]['category_name'] ?? null;
        $this->logger->info("[" . __METHOD__ . "] ✅ Category Found CategoryId: {$categoryId}, Name: {$categoryName}");
        return [
            'id' => $categoryId,
            'name' => $categoryName
        ];
    }

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
        if (!empty($colorData) && isset($colorData[0])) {
            $result['color'] = [
                'id' => $colorData[0]['attribute_id'],
                'name' => $colorData[0]['attribute_name']
            ];
            $this->logger->info("[" . __METHOD__ . "] ✅ Color Attribute Found: ID: {$result['color']['id']}, Name: {$result['color']['name']}");
        } else {
            $this->logger->error("[" . __METHOD__ . "] ❌ Color Attribute Not Found For CategoryId: {$categoryId}");
        }
        $attributeSizeSql = "SELECT attribute_id, attribute_name FROM iwa_ciceksepeti_category_attributes 
                         WHERE category_id = :categoryId 
                         AND type = 'Variant Özelliği' 
                         AND (attribute_name = 'Ebat' OR attribute_name = 'Boyut' OR attribute_name = 'Beden') 
                         LIMIT 1";
        $sizeData = Utility::fetchFromSql($attributeSizeSql, ['categoryId' => $categoryId]);
        if (!empty($sizeData) && isset($sizeData[0])) {
            $result['size'] = [
                'id' => $sizeData[0]['attribute_id'],
                'name' => $sizeData[0]['attribute_name']
            ];
            $this->logger->info("[" . __METHOD__ . "] ✅ Size Attribute Found: ID: {$result['size']['id']}, Name: {$result['size']['name']}");
        } else {
            $this->logger->error("[" . __METHOD__ . "] ❌ [Size Attribute] Not Found For CategoryId: {$categoryId}");
        }
        return $result;
    }

    private function buildProductAttributes(array $product, array $variantAttributes): array
    {
        $attributes = [];
        if (!empty($variantAttributes['color']) && isset($product['geminiColor']) && !empty(trim($product['geminiColor']))) {
            $colorAttrId = $variantAttributes['color']['id'];
            $colorValue = trim($product['geminiColor']);
            $bestColorMatch = $this->findAttributeCiceksepetiAttributeDatabase($colorAttrId, $colorValue);
            if ($bestColorMatch) {
                $attributes[] = [
                    'id' => $colorAttrId,
                    'ValueId' => $bestColorMatch['attribute_value_id'],
                    'TextLength' => 0
                ];
                $this->logger->info("[" . __METHOD__ . "] ✅ Color Match Found: {$bestColorMatch['name']} (ID: {$bestColorMatch['attribute_value_id']})");
            } else {
                $this->logger->error("[" . __METHOD__ . "] ❌ Color Match Not Found For Value: {$colorValue}");
            }
        }
        if (!empty($variantAttributes['size']) && isset($product['sizeLabel']) && !empty(trim($product['sizeLabel']))) {
            $sizeAttrId = $variantAttributes['size']['id'];
            $sizeValue = trim($product['sizeLabel']);
            $bestSizeMatch = $this->findAttributeCiceksepetiAttributeDatabase($sizeAttrId, $sizeValue);
            if ($bestSizeMatch) {
                $attributes[] = [
                    'id' => $sizeAttrId,
                    'ValueId' => $bestSizeMatch['attribute_value_id'],
                    'TextLength' => 0
                ];
                $this->logger->info("[" . __METHOD__ . "] ✅ Size Match Found: {$bestSizeMatch['name']} (ID: {$bestSizeMatch['attribute_value_id']})");
            } else {
                $this->logger->error("[" . __METHOD__ . "] ❌ Size Match Not Found For Value: {$sizeValue}");
            }
        }
        if (empty($attributes)) {
            $this->logger->warning("[" . __METHOD__ . "] ⚠️ No Attributes Could Be Added For Product: {$product['stockCode']}");
        }
        return $attributes;
    }

    private function findAttributeCiceksepetiAttributeDatabase($attributeId, $searchValue)
    {
        $sql = "SELECT attribute_value_id, name FROM iwa_ciceksepeti_category_attributes_values 
            WHERE attribute_id = :attribute_id and name = :searchValue LIMIT 1";
        $result = Utility::fetchFromSql($sql, ['attribute_id' => $attributeId, 'searchValue' => $searchValue]);
        if (empty($result) || !isset($result[0])) {
            $this->logger->warning("[" . __METHOD__ . "] ⚠️ AttributeMatch No Attribute Values Found In DB For attributeId: {$attributeId} searchValue: {$searchValue} ");
            return null;
        }
        return $result[0];
    }

    private function normalizeCiceksepetiData($data)
    {
        $result = [];
        foreach ($data as $product) {
            $result['products'][] = [
                'productName' =>  mb_strlen($product['geminiTitle']) > 255
                    ? mb_substr($product['geminiTitle'], 0, 255)
                    : $product['geminiTitle'],
                'mainProductCode' => $product['mainProductCode'],
                'stockCode' => $product['stockCode'],
                'categoryId' => $product['geminiCategoryId'],
                'description' => $this->normalizeDescription($product['geminiDescription'], $product['sizeLabelMap']),
                'deliveryMessageType' => 5,
                'deliveryType' => 2,
                'stockQuantity' => $product['stockQuantity'],
                'salesPrice' => $product['salesPrice'] * 1.5,
                'images' => $this->normalizeImages($product['images']),
                'Attributes' => $product['Attributes']
            ];
        }
        $result = $this->removeCommonAttributes($result);
        $this->logger->info("[" . __METHOD__ . "] 📦 Listing Data Ready " . count($result['products']) . " Product(s) Formatted For Çiçeksepeti Listing.");
        $result = json_encode($result, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);
        if ($result === false) {
            $this->logger->error("[" . __METHOD__ . "] ❌ JSON Encode Error Failed To Encode Formatted Listing Data.");
            return false;
        }
        return $result;
    }

    private function normalizeDescription($description, $sizeLabelMap)
    {
        $appendHtml = '';
        if (!empty($sizeLabelMap)) {
            $appendHtml .= '<h4>Boyut Bilgileri</h4>';
            $appendHtml .= '<ul>';
            foreach ($sizeLabelMap as $item) {
                $original = htmlspecialchars((string) ($item['original'] ?? ''));
                $label = htmlspecialchars((string) ($item['label'] ?? ''));
                $appendHtml .= "<li>{$original} → {$label}</li>";
            }
            $appendHtml .= '</ul>';
        }
        $maxLength = 20000;
        $extraLength = mb_strlen($appendHtml, 'UTF-8');
        $availableForDescription = $maxLength - $extraLength;
        $trimmedDescription = mb_substr($description, 0, $availableForDescription, 'UTF-8');
        $finalHtml = $trimmedDescription . $appendHtml;
        return $finalHtml;
    }

    private function normalizeImages($images)
    {
        $normalized = [];
        foreach ($images as $image) {
            $width = $image['width'] ?? 0;
            $height = $image['height'] ?? 0;
            $url = $image['url'] ?? null;

            if (!$url || !is_string($url)) {
                continue;
            }
            if ($width >= 500 && $height >= 500 && $width <= 2000 && $height <= 2000) {
                $normalized[] = $url;
            }
            if (count($normalized) >= 5) {
                break;
            }
        }
        return $normalized;
    }

    private function removeCommonAttributes($data): array
    {
        $valueIdCount = [];
        $totalProducts = count($data['products']);
        foreach ($data['products'] as $product) {
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
        foreach ($data['products'] as &$product) {
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

    private function processUpdateListing($message)
    {
        $updateProductList = $message->getVariantIds();
        print_r($updateProductList);

    }

}
