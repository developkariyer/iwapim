<?php
namespace App\MessageHandler;


use App\Model\DataObject\Marketplace;
use App\Model\DataObject\Product;
use App\Model\DataObject\VariantProduct;
use App\Message\ProductListingMessage;
use App\Utils\Utility;

class ListingHelperService
{

    public function getPimlistingsInfo(ProductListingMessage $message, $logger)
    {
        $referenceMarketplaceId = $message->getReferenceMarketplaceId();
        $referenceMarketplace = Marketplace::getById($referenceMarketplaceId);
        if (!$referenceMarketplace instanceof Marketplace) {
            $logger->error("[" . __METHOD__ . "] ❌ Reference marketplace not found: $referenceMarketplaceId");
            return false;
        }
        $referenceMarketplaceName = $referenceMarketplace->getKey();
        $logger->info("[" . __METHOD__ . "] ✅ Reference marketplace found: $referenceMarketplaceName");
        


    }





    public function getPimListingsInfo2(ProductListingMessage $message)
    {
        $marketplace = Marketplace::getById($message->getMarketplaceId());
        if (!$marketplace instanceof Marketplace) {
            return false;
        }
        $product = Product::getById($message->getProductId());
        if (!$product instanceof Product) {
            return false;
        }
        $variantIds = array_unique($message->getVariantIds());
        if (empty($variantIds)) {
            return false;
        }
        $results = [];
        foreach ($variantIds as $variantId) {
            $mainProduct = Product::getById($variantId);
            if (!$mainProduct instanceof Product) {
                continue;
            }
            $listingItems = $mainProduct->getListingItems();
            if (empty($listingItems)) {
                continue;
            }
            foreach ($listingItems as $listingItem) {
                if (!$listingItem instanceof VariantProduct) {
                    continue;
                }
                $marketplaceKey = $listingItem->getMarketplace()->getId();
                if ($marketplaceKey !== 84124) {
                    continue;
                }
                $processed = $this->processVariant($mainProduct, $listingItem);
                if (!empty($processed)) {
                    $results[] = $processed;
                }
            }
        }
        $groupedSizes = [];
        $sizeLabels = ['M', 'L', 'XL', '2XL', '3XL', '4XL'];
        foreach ($results as $product) {
            $identifier = $product['mainProductCode'];
            $size = $product['size'];

            if (!isset($groupedSizes[$identifier])) {
                $groupedSizes[$identifier] = [];
            }

            if (!in_array($size, $groupedSizes[$identifier])) {
                $groupedSizes[$identifier][] = $size;
            }
        }
        $sizeToLabelMap = [];
        foreach ($groupedSizes as $identifier => $sizes) {
            foreach ($sizes as $i => $size) {
                $label = $sizeLabels[$i] ?? 'CUSTOM';
                $sizeToLabelMap[$identifier][$size] = $label;
            }
        }
        foreach ($results as &$product) {
            $identifier = $product['mainProductCode'];
            $size = $product['size'];
            $product['sizeLabel'] = $sizeToLabelMap[$identifier][$size] ?? 'CUSTOM';
        }
        unset($product);
        $groupedDescriptions = [];
        foreach ($results as $product) {
            $identifier = $product['mainProductCode'];
            $size = $product['size'];
            $label = $product['sizeLabel'];

            if (!isset($groupedDescriptions[$identifier])) {
                $groupedDescriptions[$identifier] = [];
            }
            $key = $size . '⇒' . $label;
            $groupedDescriptions[$identifier][$key] = "<li><strong>{$size}</strong> ⇒ <strong>{$label}</strong></li>";
        }
        $descriptionsHtml = [];
        foreach ($groupedDescriptions as $identifier => $items) {
            $html = "<strong>BOYUT SEÇENCEKLERİ:</strong><ul>" . implode('', $items) . "</ul>";
            $descriptionsHtml[$identifier] = $html;
        }
        foreach ($results as &$product) {
            $identifier = $product['mainProductCode'];
            $product['description'] .= "\n" . $descriptionsHtml[$identifier];
        }
        unset($product);
        return json_encode($results, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
    }

    private function processVariant($mainProduct, $variantProduct)
    {
        $parentApiJsonShopify = json_decode($variantProduct->jsonRead('parentResponseJson'), true);
        $apiJsonShopify = json_decode($variantProduct->jsonRead('apiResponseJson'), true);
        $shopifyIsActive = isset($parentApiJsonShopify['status']) && $parentApiJsonShopify['status'] === 'ACTIVE';
        $images = $this->getShopifyImages($mainProduct, $parentApiJsonShopify);
        if (empty($images) || !$shopifyIsActive || count($images) < 2) {
            return [];
        }
        foreach ($images as &$image) {
            if (is_string($image) && strpos($image, 'http://') === 0) {
                $image = preg_replace('/^http:\/\//', 'https://', $image);
            }
        }
        unset($image);
        return [
            'productName' => mb_substr($variantProduct->getTitle(), 0, 255),
            'mainProductCode' => $mainProduct->getProductIdentifier(),
            'stockCode' => $mainProduct->getIwasku(),
            'categoryId' => null,
            'description' => mb_substr($parentApiJsonShopify['descriptionHtml'] ?? '', 0, 19000),
            'deliveryMessageType' => 5,
            'size' => $mainProduct->getVariationSize(),
            'color' => $mainProduct->getVariationColor(),
            'deliveryType' => 2,
            'stockQuantity' => $apiJsonShopify['inventoryQuantity'] ?? 0,
            'salesPrice' => ($apiJsonShopify['price'] ?? 0) * 1.5,
            'attributes' => [],
            'images' => array_slice($images, 0, 5)
        ];
    }

    private function getShopifyImages($mainProduct, $parentApiJsonShopify)
    {
        $images = [];
        $widthThreshold = 2000;
        $heightThreshold = 2000;
        if (isset($parentApiJsonShopify['media']['nodes'])) {
            foreach ($parentApiJsonShopify['media']['nodes'] as $node) {
                if (
                    isset($node['mediaContentType'], $node['preview']['image']['url'], $node['preview']['image']['width'], $node['preview']['image']['height']) &&
                    $node['mediaContentType'] === 'IMAGE' &&
                    ($node['preview']['image']['width'] < $widthThreshold || $node['preview']['image']['height'] < $heightThreshold)
                ) {
                    $images[] = $node['preview']['image']['url'];
                }
            }
        }
        if (empty($images) || count($images) <= 2) {
            $listingItems = $mainProduct->getListingItems();
            if (empty($listingItems)) {
                return;
            }
            foreach ($listingItems as $listingItem) {
                if (!$listingItem instanceof VariantProduct) {
                    continue;
                }
                $images = array_merge($images, $this->getImages($listingItem));
            }

        }
        return $images;
    }

//    public function getPimListingsInfo(ProductListingMessage $message): false|string
//    {
//        $marketplace = Marketplace::getById($message->getMarketplaceId());
//        if (!$marketplace instanceof Marketplace) {
//            return false;
//        }
//        $product = Product::getById($message->getProductId());
//        if (!$product instanceof Product) {
//            return false;
//        }
//        $marketplaceName = $marketplace->getMarketplaceType();
//        $variantIds = $message->getVariantIds();
//        if (empty($variantIds)) {
//            return false;
//        }
//        $marketplaceCurrency = $marketplace->getCurrency();
//        $productIdentifier = $product->getProductIdentifier();
//        $data = [
//            $marketplaceName => [
//                $productIdentifier => [
//                    'category' => $product->getProductCategory(),
//                    'name' => $product->getName(),
//                    'skus' => $this->processVariantProduct($variantIds, $marketplaceCurrency)
//                ]
//            ]
//        ];
//        $data = $this->filterShopifyListingItems($data);
//        return json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
//    }

    private function processVariantProduct($variantIds, $marketplaceCurrency):array
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
            $listingItemsresult = $this->processListingItems($listingItems, $marketplaceCurrency);
            $result[$iwasku] = [
                'size' => $variantProduct->getVariationSize(),
                'color' => $variantProduct->getVariationColor(),
                'ean' => $variantProduct->getEanGtin(),
                'ListingItems' => $listingItemsresult['items'],
                'images' => $listingItemsresult['images'],
                'price' => $listingItemsresult['price'],
            ];
        }
        return $result;
    }

    private function processListingItems($listingItem, $marketplaceCurrency): array
    {
        $marketplaceSalePrice = null;
        $foundSameCurrency = false;
        $result = [];
        $images = [];
        $items = [];
        foreach ($listingItem as $listingItem) {
            if (!$listingItem instanceof VariantProduct) {
                continue;
            }
            $title = $listingItem->getTitle();
            if (strpos(ltrim($title), '🎁') === 0) {
                continue;
            }
            $marketplaceKey = $listingItem->getMarketplace()->getKey();
            $parentApiJson = json_decode($listingItem->jsonRead('parentResponseJson'), true);
            $listingSalePrice = $listingItem->getSalePrice();
            $currency = $listingItem->getSaleCurrency();
            $normalizedCurrency = $this->normalizeCurrency($currency);
            $normalizedMarketplaceCurrency = $this->normalizeCurrency($marketplaceCurrency);
            if (!$foundSameCurrency && $normalizedCurrency === $normalizedMarketplaceCurrency) {
                $marketplaceSalePrice = $listingSalePrice;
                $foundSameCurrency = true;
            } elseif (!$foundSameCurrency && $marketplaceSalePrice === null) {
                $marketplaceSalePrice = $this->calculatePrice($listingSalePrice, $normalizedCurrency, $normalizedMarketplaceCurrency);
            }
            $images = array_merge($images, $this->getImages($listingItem));
            $items[$marketplaceKey] = [
                'title' => $title,
                'salePrice' => $listingSalePrice,
                'currency' => $currency,
                'description' => $parentApiJson['descriptionHtml'] ?? '',
                'seo' => isset($parentApiJson['seo']) ? ($parentApiJson['seo']['description'] ?? '') : '',
                'tags' => $parentApiJson['tags'] ?? ''
            ];
        }
        $result['price'] = $marketplaceSalePrice;
        $result['images'] = $images;
        $result['items'] = $items;
        return $result;
    }

    private function filterShopifyListingItems($data)
    {
        foreach ($data as &$products) {
            foreach ($products as &$product) {
                foreach ($product['skus'] as &$sku) {
                    if (isset($sku['ListingItems']) && is_array($sku['ListingItems'])) {
                        $sku['ListingItems'] = array_filter(
                            $sku['ListingItems'],
                            function($key) {
                                return str_starts_with($key, 'Shopify');
                            },
                            ARRAY_FILTER_USE_KEY
                        );
                        if (empty($sku['ListingItems'])) {
                            unset($sku['ListingItems']);
                        }
                    }
                }
            }
        }
        unset($products, $product, $sku);
        return $data;
    }

    private function normalizeCurrency($currency): string
    {
        $map = [
            'TL' => 'TRY',
            'TRY' => 'TRY',
            'USD' => 'USD',
            'US DOLLAR' => 'USD',
            'Dolar' => 'USD',
            '₺' => 'TRY',
            '$' => 'USD',
        ];
        return $map[trim($currency)] ?? strtoupper(trim($currency));
    }

    private function getImages($listingItem): array
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
                $images[] = $host . $imageUrl;
            }
        }
        return $images;
    }

    private function calculatePrice($price, $fromCurrency, $toCurrency): ?string
    {
        if (empty($price) || empty($fromCurrency) || empty($toCurrency)) {
            return null;
        }
        return Utility::convertCurrency($price, $fromCurrency, $toCurrency, date('Y-m-d'));
    }

}