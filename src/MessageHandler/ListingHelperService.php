<?php
namespace App\MessageHandler;


use App\Model\DataObject\Marketplace;
use App\Model\DataObject\Product;
use App\Model\DataObject\VariantProduct;
use App\Message\ProductListingMessage;
use App\Utils\Utility;

class ListingHelperService
{

    public function saveState($trace_id, $current_stage, $status, $error_message)
    {
        $sql = 'INSERT INTO iwa_auto_listing_status (trace_id, current_stage, status, error_message)
                VALUES (:trace_id, :current_stage, :status, :error_message)
                ON DUPLICATE KEY UPDATE
                    current_stage = VALUES(current_stage),
                    status = VALUES(status),
                    error_message = VALUES(error_message)';

        Utility::executeSql($sql, [
            'trace_id' => $trace_id,
            'current_stage' => $current_stage,
            'status' => $status,
            'error_message' => $error_message
        ]);
    }

    public function getPimListingsInfo(ProductListingMessage $message): false|string
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
                        $listingMarketplaceType = $listingItem->getMarketplace()->getMarketplaceType();
                        if ($listingMarketplaceType != "Shopify")
                            continue;
                        $title = $listingItem->getTitle();
                        $salePrice = $listingItem->getSalePrice();
                        $currency = $listingItem->getSaleCurrency();
                        $marketplaceKey = $listingItem->getMarketplace()->getKey();
                        $parentApiJson = json_decode($listingItem->jsonRead('parentResponseJson'), true);

                        $data[$marketplaceName][$productIdentifier]['skus'][$iwasku]['ListingItems'][$marketplaceKey]['title'] = $title;
                        $data[$marketplaceName][$productIdentifier]['skus'][$iwasku]['ListingItems'][$marketplaceKey]['salePrice'] = $salePrice;
                        $data[$marketplaceName][$productIdentifier]['skus'][$iwasku]['ListingItems'][$marketplaceKey]['currency'] = $currency;
                        $data[$marketplaceName][$productIdentifier]['skus'][$iwasku]['ListingItems'][$marketplaceKey]['description'] = $parentApiJson['descriptionHtml'] ?? '';
                        $data[$marketplaceName][$productIdentifier]['skus'][$iwasku]['ListingItems'][$marketplaceKey]['seo'] = $parentApiJson['seo']['description'] ?? '';
                        $data[$marketplaceName][$productIdentifier]['skus'][$iwasku]['ListingItems'][$marketplaceKey]['tags'] = $parentApiJson['tags'] ?? '';

                        $imageGallery = $listingItem->getImageGallery();
                        foreach ($imageGallery as $hotspotImage) {
                            $image = $hotspotImage->getImage();
                            $imageUrl = $image->getFullPath();
                            $host = \Pimcore\Tool::getHostUrl();
                            $data[$marketplaceName][$productIdentifier]['skus'][$iwasku]['ListingItems'][$marketplaceKey]['images'][] = [
                                'url' => $host . $imageUrl,
                                'width' => $image->getWidth(),
                                'height' => $image->getHeight(),
                            ];
                        }
                    }
                }
            }
        }
        return json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
    }

}




