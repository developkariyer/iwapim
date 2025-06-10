<?php
namespace App\MessageHandler;


use App\Message\ProductListingMessage;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;
use App\Connector\Gemini\GeminiConnector;
use App\Connector\Marketplace\HepsiburadaConnector;
use App\Model\DataObject\Marketplace;
use App\Model\DataObject\Product;
use App\Model\DataObject\VariantProduct;
use App\Utils\Utility;
use Exception;
use Symfony\Component\HttpClient\HttpClient;
use App\MessageHandler\ListingHelperService;
use Psr\Log\LoggerInterface;
use App\Logger\LoggerFactory;

#[AsMessageHandler(fromTransport: 'hepsiburada')]
class HepsiburadaListingHandler
{
    private LoggerInterface $logger;

    public function __construct(ListingHelperService $listingHelperService)
    {
        $this->listingHelper = $listingHelperService;
    }

    public function __invoke(ProductListingMessage $message)
    {
        if (method_exists($message, 'getLogger') && $message->getLogger() instanceof LoggerInterface) {
            $this->logger = $message->getLogger();
        }
        echo "Hepsiburada Listing Handler\n";
        $this->logger->info("[" . __METHOD__ . "] 🚀 Hepsiburada Listing Handler Started");
        $actionType = $message->getActionType();
        echo "action type: $actionType\n";
        $this->logger->info("[" . __METHOD__ . "] ✅ Action Type: $actionType ");
        match ($actionType) {
            'list' => $this->processNewListing($message),
            //'update_list' => $this->processUpdateListing($message),
            default => throw new \InvalidArgumentException("Unknown Action Type: $actionType")
        };
    }

    private function processNewListing($message)
    {
        $this->logger->info("[" . __METHOD__ . "] ✅ Processing New Listing ");
        $listingInfo = $this->listingHelper->getPimListingsInfo($message, $this->logger);
        $this->logger->info("[" . __METHOD__ . "] ✅ Pim Listings Info Fetched ");
        $categories = $this->getHepsiburadaCategoriesDetails();
        $this->logger->info("[" . __METHOD__ . "] ✅ Category Data Fetched ");
        $geminiFilledData = $this->geminiProcess($listingInfo, $categories);
        $this->logger->info("[" . __METHOD__ . "] ✅ Gemini Data Filled ");
        $filledAttributeData =  $this->fillAttributeData($geminiFilledData);
        $this->logger->info("[" . __METHOD__ . "] ✅ Filled Attribute Data ");
        $normalizedHepsiburadaData = $this->normalizeHepsiburadaData($filledAttributeData);
        $this->logger->info("[" . __METHOD__ . "] ✅ Normalized Hepsiburada Data ");
        print_r($normalizedHepsiburadaData);
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
            $product['attributes'] = $this->buildProductAttributes(
                $product,
                $variantAttributes
            );
        }
        return $data;
    }

//    private function queryHepsiburadaAttribute($attributeName, $categoryId)
//    {
//        $sql = "select attribute_id from iwa_hepsiburada_category_attributes where attribute_name = :attributeName and category_id = :categoryId";
//        $result = Utility::fetchFromSql($sql, ['attribute_name' => $attributeName, 'categoryId' => $categoryId]);
//        if (!$result && empty($result[0])) {
//            return null;
//        }
//        return $result[0]['attribute_id'];
//    }

    private function normalizeHepsiburadaData($data)
    {
        $result = [];
        foreach ($data as $product) {
            $item = [
                'categoryId' => $product['geminiCategoryId'],
                'attributes' => $this->normalizeAttributes($product['Attributes']),
                'merchantSku' => $product['stockCode'],
                'VaryantGroupID' => $product['mainProductCode'],
                'UrunAdi' => $product['geminiTitle'],
                'UrunAciklamasi' =>  $this->normalizeDescription($product['geminiDescription'], $product['sizeLabelMap']),
                'Marka' => 'Colorfullworlds',
                'GarantiSuresi' => 24,
                'price' => $product['salesPrice'],
                'stock' => 5,
            ];
            for ($i = 0; $i < 5; $i++) {
                if (!empty($product['images'][$i]['url'])) {
                    $item['Image' . ($i + 1)] = $product['images'][$i]['url'];
                }
            }
            $result[] = $item;
        }
        $this->logger->info("[" . __METHOD__ . "] 📦 Listing Data Ready " . count($result) . " Product(s) Formatted For Hepsiburada Listing.");
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
        $finalHtml = $description . $appendHtml;
        return $finalHtml;

    }

    private function normalizeAttributes($attributes)
    {
        $result = [];
        foreach ($attributes as $attribute) {
            $result[] = [
                $attribute['id'] => $attribute['ValueId']
            ];
        }
    }

    private function buildProductAttributes(array $product, array $variantAttributes): array
    {
        $attributes = [];
        if (!empty($variantAttributes['color']) && isset($product['geminiColor']) && !empty(trim($product['geminiColor']))) {
            $colorAttrId = $variantAttributes['color']['id'];
            $colorValue = trim($product['geminiColor']);
            $bestColorMatch = $this->findAttributeHepsiburadaAttributeDatabase($colorAttrId, $colorValue);
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
            $attributes[] = [
                'id' => $sizeAttrId,
                'ValueId' => $sizeValue,
                'TextLength' => 0
            ];
            $this->logger->info("[" . __METHOD__ . "] ✅ Size Match Found: {$sizeValue}");
        }
        if (empty($attributes)) {
            $this->logger->warning("[" . __METHOD__ . "] ⚠️ No Attributes Could Be Added For Product: {$product['stockCode']}");
        }
        return $attributes;
    }

    private function findAttributeHepsiburadaAttributeDatabase($attributeId, $searchValue)
    {
        $sql = "SELECT attribute_value_id, name FROM iwa_hepsiburada_category_attributes_values 
            WHERE attribute_id = :attribute_id and name = :searchValue LIMIT 1";
        $result = Utility::fetchFromSql($sql, ['attribute_id' => $attributeId, 'searchValue' => $searchValue]);
        if (empty($result) || !isset($result[0])) {
            $this->logger->warning("[" . __METHOD__ . "] ⚠️ AttributeMatch No Attribute Values Found In DB For attributeId: {$attributeId} searchValue: {$searchValue} ");
            return null;
        }
        return $result[0];
    }

    private function getCategoryInfo(int $categoryId): ?array
    {
        $categorySql = "SELECT category_name FROM iwa_hepsiburada_categories WHERE id = :categoryId";
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
        $attributeColorSql = "SELECT attribute_id, attribute_name FROM iwa_hepsiburada_category_attributes 
                          WHERE category_id = :categoryId 
                          AND type = 'variantAttributes' 
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
        $attributeSizeSql = "SELECT attribute_id, attribute_name FROM iwa_hepsiburada_category_attributes 
                         WHERE category_id = :categoryId 
                         AND type = 'variantAttributes' 
                         AND attribute_name = 'Seçenek' 
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
        $geminiApiResult = GeminiConnector::chat($prompt, 'hepsiburada');
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
            Sen bir e-ticaret uzmanısın ve Hepsiburada pazaryeri için ürün listeleri hazırlıyorsun. 
            Sana gönderdiğim veri dışına çıkma.
            Hiçbir açıklama, kod bloğu, yorum ekleme.  
            Sadece geçerli, düzgün bir JSON üret.
            Bu JSON'da eksik alan olan kategoriyi verdiğim kategori bilgilerine göre bulmanı istiyorum.
            Gönderdiğim veri de stockCode yer almaktadır çıktı formatında bunu kullanacaksın.
            -**title**: Title bilgisini değiştirmeden size veya renk bilgisi içeriyorsa bunu kaldır başka herhangi bir müdahalede bulunma tüm variantlar için aynı.
            -**description**: Açıklama bilgisini değiştirmeden size bilgilerini kaldır başka herhangi bir müdahalede bulunma tüm variantlar için aynı.
            -**categoryId**: Kategori verisinden en uygun kategoriyi bul id sini al ve kaydet
            -**color**: 
                - renk bilgisi verideki color fieldı Türkçe ye çevir çevirdiğinde hepsiburadada bulunan çok bilinen renklerden olsun Eğer iki renk varsa her iki rengi de çevir, teke düşürme iki rengide örneğin:
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























    private function getHepsiburadaCategoriesDetails(): false|array|string
    {
        $categoryIdList = $this->getHepsiburadaListingCategoriesIdList();
        if (empty($categoryIdList)) {
            return [];
        }
        $inClause = implode(',', array_fill(0, count($categoryIdList), '?'));
        $sql = "SELECT * FROM iwa_hepsiburada_categories WHERE id IN ($inClause)";
        $categories = Utility::fetchFromSql($sql, $categoryIdList);
        if (empty($categories)) {
            return [];
        }
        return json_encode($categories, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
    }

    private function getHepsiburadaListingCategoriesIdList(): array
    {
        $sql = "SELECT oo_id FROM `object_query_varyantproduct` WHERE marketplaceType = 'Hepsiburada'";
        $hepsiburadaVariantIds = Utility::fetchFromSql($sql);
        if (!is_array($hepsiburadaVariantIds) || empty($hepsiburadaVariantIds)) {
            return [];
        }
        $categoryIdList = [];
        foreach ($hepsiburadaVariantIds as $hepsiburadaVariantId) {
            $variantProduct = VariantProduct::getById($hepsiburadaVariantId['oo_id']);
            if (!$variantProduct instanceof VariantProduct) {
                continue;
            }
            $apiData = json_decode($variantProduct->jsonRead('apiResponseJson'), true);
            $categoryIdList[] = $apiData['attributes']['categoryId'];
        }
        return array_unique($categoryIdList);
    }

}
