<?php
namespace App\MessageHandler;


use App\Message\ProductListingMessage;
use App\Model\DataObject\Marketplace;
use App\Model\DataObject\Product;
use App\Model\DataObject\VariantProduct;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler(fromTransport: 'ciceksepeti')]
class CiceksepetiListingHandler
{
    public function __invoke(ProductListingMessage $message)
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
                $data[$marketplaceName][$productIdentifier]['sku'][$iwasku]['size'] = $size;
                $data[$marketplaceName][$productIdentifier]['sku'][$iwasku]['color'] = $color;
                $data[$marketplaceName][$productIdentifier]['sku'][$iwasku]['ean'] = $ean;
                $listingItems = $variantProduct->getListingItems();
                foreach ($listingItems as $listingItem) {
                    if ($listingItem instanceof VariantProduct) {
                        $title = $listingItem->getTitle();
                        $urlLink = $listingItem->getUrlLink();
                        $salePrice = $listingItem->getSalePrice();
                        $currency = $listingItem->getSaleCurrency();
                        $marketplaceType = $listingItem->getMarketplace()->getKey();
                        $apiJson = json_decode($listingItem->jsonRead('apiResponseJson'), true);
                        $parentApiJson = json_decode($listingItem->jsonRead('apiResponseJson'), true);

                        $data[$marketplaceName][$productIdentifier]['sku'][$iwasku]['ListingItems'][$marketplaceType]['title'] = $title;
                        $data[$marketplaceName][$productIdentifier]['sku'][$iwasku]['ListingItems'][$marketplaceType]['url'] = $urlLink;
                        $data[$marketplaceName][$productIdentifier]['sku'][$iwasku]['ListingItems'][$marketplaceType]['salePrice'] = $salePrice;
                        $data[$marketplaceName][$productIdentifier]['sku'][$iwasku]['ListingItems'][$marketplaceType]['currency'] = $currency;
                        $data[$marketplaceName][$productIdentifier]['sku'][$iwasku]['ListingItems'][$marketplaceType]['apiJson'] = $apiJson;
                        $data[$marketplaceName][$productIdentifier]['sku'][$iwasku]['ListingItems'][$marketplaceType]['parentApiJson'] = $parentApiJson;

                        $imageGallery = $listingItem->getImageGallery();
                        foreach ($imageGallery as $hotspotImage) {
                            $image = $hotspotImage->getImage();
                            $imageUrl = $image->getFullPath();
                            $host = \Pimcore\Tool::getHostUrl();
                            $data[$marketplaceName][$productIdentifier]['sku'][$iwasku]['ListingItems'][$marketplaceType]['images'][] = $host . $imageUrl ;
                        }
                    }
                }
            }
        }
        print_r($data);

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
        echo "Ciceksepeti Mesaj İşlendi (JSON):\n";
       // echo $jsonOutput . "\n";

    }
}
