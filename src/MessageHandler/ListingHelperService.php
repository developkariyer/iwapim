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
        print_r($variantIds);
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
            $referenceMarketplaceMainProductSize = $referenceMarketplaceMainProduct->getVariationSize();
            $referenceMarketplaceMainProductSizeLabel =  $this->findSizeLabelFromMap($referenceMarketplaceMainProductSize, $sizesAndLabels) ?? '';
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
                $additionalData['images'][] = $images;
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
                if ( isset($node['mediaContentType'], $node['preview']['image']['url'], $node['preview']['image']['width'], $node['preview']['image']['height']) && $node['mediaContentType'] === 'IMAGE') {
                    $images[] = [
                            'url' => $node['preview']['image']['url'],
                            'width' => $node['preview']['image']['width'],
                            'height' => $node['preview']['image']['height']
                    ];
                }


//                if (
//                    isset($node['mediaContentType'], $node['preview']['image']['url'], $node['preview']['image']['width'], $node['preview']['image']['height']) &&
//                    $node['mediaContentType'] === 'IMAGE'
//                ) {
//                    $imageUrl = $node['preview']['image']['url'];
//                    $headers = @get_headers($imageUrl);
//                    if ($headers && strpos($headers[0], '200') !== false) {
//                        $images[] = [
//                            'url' => $imageUrl,
//                            'width' => $node['preview']['image']['width'],
//                            'height' => $node['preview']['image']['height'],
//                        ];
//                    }
//                }
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
            $marketplace = $listingItem->getMarketplaceType();
            if (!$listingItem instanceof VariantProduct || $marketplace == 'Etsy') {
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

    private function findSizeLabelFromMap($size, $sizesAndLabels)
    {
        $normalizedSize = trim($size);
        foreach ($sizesAndLabels as $entry) {
            if (trim($entry['original']) === $normalizedSize) {
                return $entry['label'] ?? null;
            }
        }
        return null;
    }

    private function getSizeLabelFromParent($referenceMarketplaceMainProduct)
    {
        $parentProduct = $referenceMarketplaceMainProduct->getParent();
        if (!$parentProduct instanceof Product) {
            return;
        }
        $childProducts = $parentProduct->getChildren();
        $rawVariationSizes = [];
        foreach ($childProducts as $childProduct) {
            if (!$childProduct instanceof Product) {
                continue;
            }
            $controlListings = $childProduct->getListingItems();
            if (empty($controlListings)) {
                continue;
            }
            $size = $childProduct->getVariationSize();
            if (!empty($size)) {
                $rawVariationSizes[] = trim($size);
            }
        }
        if (empty($rawVariationSizes)) {
            return;
        }
        $parsed = [];
        $rawVariationSizes = array_unique($rawVariationSizes);
        foreach ($rawVariationSizes as $line) {
            $original = trim($line);
            if ($original === '') continue;
            $label = null;
            $value = $original;
            $sortKey = null;
            if (preg_match('/^([XSML\d]{1,4})[\s\-:]+(.+)$/iu', $original, $match)) {
                $label = strtoupper(trim($match[1]));
                $value = trim($match[2]);
            }
            elseif (preg_match('/^(XS|S|M|L|XL|2XL|3XL|4XL)$/i', $original, $match)) {
                $label = strtoupper($match[1]);
                $value = $original;
            }
            elseif (preg_match('/(standart|tek\s*ebat)/iu', $original)) {
                $label = 'Standart';
                $value = $original;
            }
            if (preg_match('/(\d+)/', $value, $numMatch)) {
                $sortKey = (int)$numMatch[1];
            }
            $parsed[] = [
                'original' => $original,
                'value' => $value,
                'label' => $label,
                'sortKey' => $sortKey,
            ];
        }
        usort($parsed, function ($a, $b) {
            return ($a['sortKey'] ?? PHP_INT_MAX) <=> ($b['sortKey'] ?? PHP_INT_MAX);
        });
        $autoLabels = ['M', 'L', 'XL', '2XL', '3XL', '4XL'];
        $autoIndex = 0;
        foreach ($parsed as &$item) {
            if ($item['label'] === null) {
                $item['label'] = $autoLabels[$autoIndex] ?? ('+' . end($autoLabels));
                $autoIndex++;
            }
            unset($item['sortKey']);
        }
        return $parsed;
    }

}