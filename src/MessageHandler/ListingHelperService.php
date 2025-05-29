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
        $logger->info("[" . __METHOD__ . "] Processing Product Listing Message");
        $referenceMarketplaceId = $message->getReferenceMarketplaceId();
        $referenceMarketplace = Marketplace::getById($referenceMarketplaceId);
        if (!$referenceMarketplace instanceof Marketplace) {
            $logger->error("[" . __METHOD__ . "] ❌ Reference marketplace not found: $referenceMarketplaceId");
            return null;
        }
        $referenceMarketplaceType = $referenceMarketplace->getMarketplaceType();
        $referenceMarketplaceKey = $referenceMarketplace->getKey();
        $targetMarketplaceId = $message->getTargetMarketplaceId();
        $targetMarketplace = Marketplace::getById($targetMarketplaceId);
        if (!$targetMarketplace instanceof Marketplace) {
            $logger->error("[" . __METHOD__ . "] ❌ Target marketplace not found: $targetMarketplaceId");
            return null;
        }
        $logger->info("[" . __METHOD__ . "] ✅ Reference marketplace found: $referenceMarketplaceType:$referenceMarketplaceKey");
        $variantIds = $message->getVariantIds();
        $variantIdsCount = count($variantIds);
        if (empty($variantIds)) {
            $logger->error("[" . __METHOD__ . "] ❌ No variant IDs found");
            return null;
        }
        $result = [];
        foreach ($variantIds as $variantId) {
            $referenceMarketplaceVariantProduct = VariantProduct::getById($variantId);
            if (!$referenceMarketplaceVariantProduct instanceof VariantProduct) {
                $logger->error("[" . __METHOD__ . "] ❌ Reference marketplace  variant product not found: $variantId");
                continue;
            }
            $referenceMarketplaceMainProduct = $referenceMarketplaceVariantProduct->getMainProduct()[0];
            if (!$referenceMarketplaceMainProduct instanceof Product) {
                $logger->error("[" . __METHOD__ . "] ❌ Reference marketplace $referenceMarketplaceKey variant product not found $variantId");
                continue;
            }
            $sizesAndLabels = $this->getSizeLabelFromParent($referenceMarketplaceMainProduct);
            $referenceMarketplaceMainProductIdentifier = $referenceMarketplaceMainProduct->getProductIdentifier();
            $referenceMarketplaceMainProductIwasku = $referenceMarketplaceMainProduct->getIwasku();
            echo $referenceMarketplaceMainProductIwasku . "\n";
            $referenceMarketplaceMainProductSize = $referenceMarketplaceMainProduct->getVariationSize();
            $referenceMarketplaceMainProductSizeLabel = $sizesAndLabels[$referenceMarketplaceMainProductSize] ?? null;
            $referenceMarketplaceMainProductColor = $referenceMarketplaceMainProduct->getVariationColor();
            $referenceMarketplaceMainProductEanGtin = $referenceMarketplaceMainProduct->getEanGtin() ?? '';
            if (empty($referenceMarketplaceMainProductIdentifier) || empty($referenceMarketplaceMainProductIwasku) || empty($referenceMarketplaceMainProductSize) || empty($referenceMarketplaceMainProductColor)) {
                $logger->error("[" . __METHOD__ . "] ❌ Reference marketplace $referenceMarketplaceKey variant product empty fields");
                continue;
            }
            $baseProductData = [
              'mainProductCode' => $referenceMarketplaceMainProductIdentifier,
              'stockCode' => $referenceMarketplaceMainProductIwasku,
              'size' => $referenceMarketplaceMainProductSize,
              'sizeLabel' => $referenceMarketplaceMainProductSizeLabel,
              'color' => $referenceMarketplaceMainProductColor,
              'ean' => $referenceMarketplaceMainProductEanGtin,
              'sizeLabelMap' => $sizesAndLabels,
            ];
            $additionalData = match ($referenceMarketplaceType) {
                'Shopify' => $this->getShopifyAdditionalData($referenceMarketplaceVariantProduct),
                default => null,
            };
            if (empty($additionalData)) {
                $logger->error("[" . __METHOD__ . "] ❌ Reference marketplace $referenceMarketplaceKey variant product:$variantId additional data is empty");
                continue;
            }
            $images = $this->mainProductAllListingImages($referenceMarketplaceMainProduct);
            if (!empty($images)) {
                $additionalData['images'] = $images;
            }
            $mergedData = array_merge($baseProductData, $additionalData);
            $result[] = $mergedData;
        }
        $resultCount = count($result);
        $mainProductCodes = array_column($result, 'mainProductCode');
        $mainProductCode = !empty($mainProductCodes) ? $mainProductCodes[0] : 'N/A';
        $statusIcon = $variantIdsCount === $resultCount ? "✅" : "⚠️";
        $logger->info("[" . __METHOD__ . "] $statusIcon Processed Reference Marketplace: $referenceMarketplaceKey variant IDs: $resultCount / $variantIdsCount | Main Product Code: $mainProductCode");
        return $result;
    }

    private function getShopifyAdditionalData($referenceMarketplaceVariantProduct)
    {
        $parentApiJsonShopify = json_decode($referenceMarketplaceVariantProduct->jsonRead('parentResponseJson'), true);
        $apiJsonShopify = json_decode($referenceMarketplaceVariantProduct->jsonRead('apiResponseJson'), true);
        $shopifyIsActive = isset($parentApiJsonShopify['status']) && $parentApiJsonShopify['status'] === 'ACTIVE';
        $title = $parentApiJsonShopify['title'] ?? '';
        $description = $parentApiJsonShopify['descriptionHtml'] ?? '';
        $stockQuantity = $apiJsonShopify['inventoryQuantity'] ?? '';
        $salesPrice = $apiJsonShopify['price'] ?? '';
        $images = $this->getShopifyImages($parentApiJsonShopify);
        if (!$shopifyIsActive || empty($images) || empty($title) || empty($description) || empty($stockQuantity) || empty($salesPrice) ) {
            return [];
        }
        return [
            'title' => $title,
            'description' => $description,
            'stockQuantity' => $stockQuantity,
            'salesPrice' => $salesPrice,
            'images' => $images
        ];
    }

    private function getShopifyImages($parentApiJsonShopify): array | null
    {
        $images = [];
        if (isset($parentApiJsonShopify['media']['nodes'])) {
            foreach ($parentApiJsonShopify['media']['nodes'] as $node) {
                if (
                    isset($node['mediaContentType'], $node['preview']['image']['url'], $node['preview']['image']['width'], $node['preview']['image']['height']) &&
                    $node['mediaContentType'] === 'IMAGE'
                ) {
                    $imageUrl = $node['preview']['image']['url'];
                    $headers = @get_headers($imageUrl);
                    if ($headers && strpos($headers[0], '200') !== false) {
                        $images[] = [
                            'url' => $imageUrl,
                            'width' => $node['preview']['image']['width'],
                            'height' => $node['preview']['image']['height'],
                        ];
                    }
                }
            }
        }
        return $images;
    }

    private function mainProductAllListingImages($referenceMarketplaceMainProduct): array | null
    {
        $images = [];
        $listingItems = $referenceMarketplaceMainProduct->getListingItems();
        if (empty($listingItems)) {
            return null;
        }
        foreach ($listingItems as $listingItem) {
            if (!$listingItem instanceof VariantProduct) {
                continue;
            }
            $images = array_merge($images, $this->getListingImages($listingItem));
        }
        return $images;
    }

    private function getListingImages($listingItem): array
    {
        $images = [];
        $imageGallery = $listingItem->getImageGallery();
        $host = \Pimcore\Tool::getHostUrl();
        foreach ($imageGallery as $hotspotImage) {
            $image = $hotspotImage->getImage();
            if (!$image) {
                continue;
            }
            $imageUrl = $host . $image->getFullPath();
            $imageUrl = preg_replace('/^http:/i', 'https:', $imageUrl);
            $imageUrl = str_replace('mesa.', '', $imageUrl);
            $parsed = parse_url($imageUrl);
            $encodedPath = implode('/', array_map('rawurlencode', explode('/', $parsed['path'] ?? '')));
            $imageUrl = $parsed['scheme'] . '://' . $parsed['host'] . $encodedPath;
            if (!empty($parsed['query'])) {
                $imageUrl .= '?' . $parsed['query'];
            }
            $headers = @get_headers($imageUrl);
            if ($headers && strpos($headers[0], '200') !== false) {
                $images[] = [
                    'url' => $imageUrl,
                    'width' => $image->getWidth(),
                    'height' => $image->getHeight()
                ];
            }
        }
        return $images;
    }

    private function getSizeLabelFromParent($referenceMarketplaceMainProduct)
    {
        $parentProduct = $referenceMarketplaceMainProduct->getParent();
        if (!$parentProduct instanceof Product) {
            return;
        }
        $variationSizeList = $parentProduct->getVariationSizeList();
        $lines = explode("\n", trim($variationSizeList));
        $parsed = [];
        foreach ($lines as $line) {
            $line = trim($line);
            if ($line === '') continue;
            if (preg_match('/^([A-Z]{1,3})[-\s]+(.+)$/i', $line, $matches)) {
                $label = strtoupper($matches[1]);
                $value = trim($matches[2]);
                $parsed[] = ['original' => $line, 'label' => $label, 'value' => $value];
            } else {
                $parsed[] = ['original' => $line, 'label' => null, 'value' => $line];
            }
        }
        $autoLabels = ['M', 'L', 'XL', '2XL', '3XL', '4XL'];
        $autoIndex = 0;
        foreach ($parsed as &$item) {
            if ($item['label'] === null) {
                $item['label'] = $autoLabels[$autoIndex] ?? ('+' . end($autoLabels));
                $autoIndex++;
            }
        }
        return $parsed;
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

    private function getShopifyImages2($mainProduct, $parentApiJsonShopify)
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



    private function calculatePrice($price, $fromCurrency, $toCurrency): ?string
    {
        if (empty($price) || empty($fromCurrency) || empty($toCurrency)) {
            return null;
        }
        return Utility::convertCurrency($price, $fromCurrency, $toCurrency, date('Y-m-d'));
    }

}