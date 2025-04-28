<?php
namespace App\MessageHandler;


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
        $jsonString = json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
        $promt = <<<EOD
            Sen bir CicekSepeti Marketplace uzmanısın.
            
            Aşağıda bir ürün listeleme datası (JSON formatında) verilmiştir.  
            Bu JSON'da bazı alanlar eksik veya hatalı olabilir.  
            Gönderdiğim veride ana ürün kodu altında sku lar ve bu skulara ait bilgiler yer almaktadır. Skuların altında variant oluşturacak size ve color bilgisi yer almaktadır.
            ListingItems alanında bu ürüne ait farklı pazaryerlerine yapılmış listingler yer alır. Bunlara benzer ciceksepeti özgün hale getireceğiz.
            Çiceksepeti para birimi TL 'dir.
            
            Gönderdiğim veriye göre çıkarılması gereken ve Ciceksepeti listing formatında istenen alanlar skus dizisi altındaki tüm skulara ayrı olacak şekilde:
            productName: (gönderilen verideki title alanlarıdır bunlardan Türkçe olanı çiceksepetine uygun olarak güncelle.)
            mainProductCode: (gönderilen verideki Ciceksepeti altındaki field genelde 3 haneli ve sayı içeriyor ör: ABC-12)
            stockCode: (ürün sku bilgisi gönderidiğim verideki skus altındaki veriler)
            description: açıklama örnek listinglere bakarak türkçe çiceksepetine uygun açıklama oluştur.
            images: örnek listingler içinden images altındaki resimlerden en fazla 5 tane olacak şekilde al dizi olacak.
            price: fiyatı örnek listingleri kullanarak TL cinsinden belirle.
            
            Her skuya ait farklı olacak örnek response {"sku1: data1"}, {"sku2: data2"}
            Çıktıyı json formatta ver her sku farklı olacak şekilde.
            İşte veri: $jsonString
        EOD;
        $result = $this->getGeminiApi($promt);
        print_r($result);
        /*$messageData = [
            'traceId' => $message->getTraceId(),
            'actionType' => $message->getActionType(),
            'productId' => $message->getProductId(),
            'marketplaceId' => $message->getMarketplaceId(),
            'userId' => $message->getUserName(),
            'variantIds' => $message->getVariantIds(),
            'payload' => $message->getPayload(),
            'priority' => $message->getPriority(),
            'targetAccountKey' => $message->getTargetAccountKey(),
            'createdAt' => $message->getCreatedAt()->format(\DateTimeInterface::ISO8601),
        ];

        $jsonOutput = json_encode($messageData, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);*/
        echo "Ciceksepeti Mesaj İşlendi (JSON)\n";
       // echo $jsonOutput . "\n";
    }

    public function getGeminiApi(string $message): ?array
    {
        $geminiApiKey = $_ENV['GEMINI_API_KEY'];
        $url = "https://generativelanguage.googleapis.com/v1beta/models/gemini-2.0-flash:generateContent?key=" . $geminiApiKey;

        $httpClient = HttpClient::create();
        echo "Gemini istek gonderildi.\n";
        $response = $httpClient->request('POST', $url, [
            'headers' => [
                'Content-Type' => 'application/json',
            ],
            'json' => [
                'contents' => [
                    [
                        'parts' => [
                            ['text' => $message]
                        ]
                    ]
                ]
            ],
        ]);
        echo "Gemini yanit alindi\n";
        if ($response->getStatusCode() === 200) {
            return $response->toArray();
        }
        return null;
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
