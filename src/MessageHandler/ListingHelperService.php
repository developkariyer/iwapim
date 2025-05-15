<?php
namespace App\MessageHandler;


use App\Model\DataObject\Marketplace;
use App\Model\DataObject\Product;
use App\Model\DataObject\VariantProduct;
use App\Message\ProductListingMessage;
use App\Utils\Utility;

class ListingHelperService
{

    public function saveMessage($message)
    {
        $sql = 'INSERT INTO iwa_product_listing_message (trace_id, action_type, product_id, marketplace_id, user_name, variant_ids, payload, priority, target_account_key)
                VALUES (:trace_id, :action_type, :product_id, :marketplace_id, :user_name, :variant_ids, :payload, :priority, :target_account_key)
                ON DUPLICATE KEY UPDATE
                    action_type = VALUES(action_type),
                    product_id = VALUES(product_id),
                    marketplace_id = VALUES(marketplace_id),
                    user_name = VALUES(user_name),
                    variant_ids = VALUES(variant_ids),
                    payload = VALUES(payload),
                    priority = VALUES(priority),
                    target_account_key = VALUES(target_account_key)';
        Utility::executeSql($sql, [
            'trace_id' => $message->getTraceId(),
            'action_type' => $message->getActionType(),
            'product_id' => $message->getProductId(),
            'marketplace_id' => $message->getMarketplaceId(),
            'user_name' => $message->getUserName(),
            'variant_ids' => json_encode($message->getVariantIds(), false),
            'payload' => json_encode($message->getPayload(), false),
            'priority' => $message->getPriority(),
            'target_account_key' => $message->getTargetAccountKey(),
        ]);
    }

    public function getPimListingsInfo(ProductListingMessage $message): false|string
    {
        $marketplace = Marketplace::getById($message->getMarketplaceId());
        if (!$marketplace instanceof Marketplace) {
            return false;
        }
        $product = Product::getById($message->getProductId());
        if (!$product instanceof Product) {
            return false;
        }
        $marketplaceName = $marketplace->getMarketplaceType();
        $variantIds = $message->getVariantIds();
        if (empty($variantIds)) {
            return false;
        }
        $productIdentifier = $product->getProductIdentifier();
        $data = [
            $marketplaceName => [
                $productIdentifier => [
                    'category' => $product->getProductCategory(),
                    'name' => $product->getName(),
                    'skus' => $this->processVariantProduct($variantIds)
                ]
            ]
        ];
        $data = $this->filterShopifyListingItems($data);
        return json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
    }

    private function processVariantProduct($variantIds):array
    {
        $result = [];
        foreach ($variantIds as $variantId) {
            $variantProduct = Product::getById($variantId);
            if (!$variantProduct instanceof Product) {
                continue;
            }
            $listingItems = $variantProduct->getListingItems();
            if (empty($listingItems)) {
                continue;
            }
            $iwasku = $variantProduct->getIwasku();
            $listingItemsData = [];
            $images = [];
            foreach ($listingItems as $listingItem)
            {
                if (!$listingItem instanceof VariantProduct) {
                    continue;
                }
                $listingItemsData[] = $this->processListingItems($listingItem);
                $images = array_merge($images, $this->getImages($listingItem));
            }
            $result[$iwasku] = [
                'size' => $variantProduct->getVariationSize(),
                'color' => $variantProduct->getVariationColor(),
                'ean' => $variantProduct->getEanGtin(),
                'ListingItems' => $listingItemsData,
                'images' => $images
            ];
        }
        return $result;
    }

    private function processListingItems($listingItem)
    {
        $title = $listingItem->getTitle();
        if (strpos(ltrim($title), '🎁') === 0) {
            return [];
        }
        $marketplaceKey = $listingItem->getMarketplace()->getKey();
        $parentApiJson = json_decode($listingItem->jsonRead('parentResponseJson'), true);
        return [
            $marketplaceKey => [
                'title' => $title,
                'salePrice' => $listingItem->getSalePrice(),
                'currency' => $listingItem->getSaleCurrency(),
                'description' => $parentApiJson['descriptionHtml'] ?? '',
                'seo' => $parentApiJson['seo']['description'] ?? '',
                'tags' => $parentApiJson['tags'] ?? ''
            ]
        ];
    }

    private function filterShopifyListingItems($data)
    {
        foreach ($data as &$products) {
            foreach ($products as &$product) {
                foreach ($product['skus'] as &$sku) {
                    $shopifyListingItems = array_filter(
                        $sku['ListingItems'] ?? [],
                        fn($v, $k) => str_starts_with($k, 'Shopify'),
                        ARRAY_FILTER_USE_BOTH
                    );
                    if (!empty($shopifyListingItems)) {
                        $sku['ListingItems'] = $shopifyListingItems;
                    } else {
                        unset($sku['ListingItems']);
                    }
                }
            }
        }
        unset($products, $product, $sku);
        return $data;
    }


    public function getPimListingsInfoN(ProductListingMessage $message): false|string
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
                $priceTL = 0;
                foreach ($listingItems as $listingItem) {
                    if ($listingItem instanceof VariantProduct) {
                        $title = $listingItem->getTitle();
                        if (strpos(ltrim($title), '🎁') === 0) {
                            continue;
                        }
                        $salePrice = $listingItem->getSalePrice();
                        $currency = $listingItem->getSaleCurrency();
                        if ($currency == "TL") {
                            $priceTL = $salePrice;
                        }

                        $marketplaceKey = $listingItem->getMarketplace()->getKey();
                        $parentApiJson = json_decode($listingItem->jsonRead('parentResponseJson'), true);
                        $data[$marketplaceName][$productIdentifier]['skus'][$iwasku]['ListingItems'][$marketplaceKey]['title'] = $title;
                        $data[$marketplaceName][$productIdentifier]['skus'][$iwasku]['ListingItems'][$marketplaceKey]['salePrice'] = $salePrice;
                        $data[$marketplaceName][$productIdentifier]['skus'][$iwasku]['ListingItems'][$marketplaceKey]['currency'] = $currency;
                        $data[$marketplaceName][$productIdentifier]['skus'][$iwasku]['ListingItems'][$marketplaceKey]['description'] = $parentApiJson['descriptionHtml'] ?? '';
                        $data[$marketplaceName][$productIdentifier]['skus'][$iwasku]['ListingItems'][$marketplaceKey]['seo'] = $parentApiJson['seo']['description'] ?? '';
                        $data[$marketplaceName][$productIdentifier]['skus'][$iwasku]['ListingItems'][$marketplaceKey]['tags'] = $parentApiJson['tags'] ?? '';
                        $data[$marketplaceName][$productIdentifier]['skus'][$iwasku]['images'][] = $this->getImages($listingItem);
                    }
                }
                if ($priceTL != 0) {
                    $data[$marketplaceName][$productIdentifier]['skus'][$iwasku]['price'] = $priceTL;
                }
                else {
                    $data[$marketplaceName][$productIdentifier]['skus'][$iwasku]['price'] = $this->calculatePrice($salePrice, $currency);
                }
            }
        }
        // Shopify Filter
        foreach ($data as &$products) {
            foreach ($products as &$product) {
                foreach ($product['skus'] as &$sku) {
                    $sku['ListingItems'] = array_filter(
                        $sku['ListingItems'] ?? [],
                        fn($v, $k) => str_starts_with($k, 'Shopify'),
                        ARRAY_FILTER_USE_BOTH
                    );
                    if (empty($sku['ListingItems'])) {
                        unset($sku['ListingItems']);
                    }
                }
            }
        }
        unset($products, $product, $sku);
        return json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
    }

    private function getImages($listingItem)
    {
        $images = [];
        $imageGallery = $listingItem->getImageGallery();
        foreach ($imageGallery as $hotspotImage) {
            $image = $hotspotImage->getImage();
            $width = $image->getWidth();
            $height = $image->getHeight();
            if ($width >= 500 && $width <= 2000 && $height >= 500 && $height <= 2000) {
                $imageUrl = $image->getFullPath();
                $host = \Pimcore\Tool::getHostUrl();
                $images[] = [
                    'url' => $host . $imageUrl,
                    'width' => $width,
                    'height' => $height
                ];
            }
        }
        return $images;
    }

    private function calculatePrice($price, $currency)
    {
        if ($currency == "TRY" or $currency == "TL") {
            return $price;
        }
        if ($currency == "US DOLLAR") {
            return Utility::convertCurrency($price, "USD", "TRY", date('Y-m-d'));
        }
    }

}