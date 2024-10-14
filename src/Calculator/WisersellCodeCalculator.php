<?php

namespace App\Calculator;

use Pimcore\Model\DataObject\Concrete;
use Pimcore\Model\DataObject\ClassDefinition\CalculatorClassInterface;
use Pimcore\Model\DataObject\Data\CalculatedValue;

class WisersellCodeCalculator implements CalculatorClassInterface
{
    public function compute(Concrete $object, CalculatedValue $context): string
    {
        return match ($context->getFieldname()) {
            'calculatedWisersellCode ' => $this->calculateDesi($object),
            default => '',
        };
    }

    private function calculateDesi(Concrete $object): string
    {
        $marketplaceObject = $object->getMarketplace();
        $marketplaceType = $marketplaceObject->getMarketplaceType();
        $storeProductId = match ($marketplaceType) {
            'Etsy' => json_decode($object->jsonRead('apiResponseJson'), true)["product_id"],
            'Amazon' =>  json_decode($object->jsonRead('apiResponseJson'), true)["asin"],
            'Shopify' => json_decode($object->jsonRead('apiResponseJson'), true)["product_id"],  
            'Trendyol' => json_decode($object->jsonRead('apiResponseJson'), true)["productCode"],
        };
        $variantCode = match ($marketplaceType) {
            'Etsy' => json_decode($object->jsonRead('parentResponseJson'), true) ["listing_id"],
            'Shopify' => json_decode($object->jsonRead('apiResponseJson'), true)["id"],  
            'Trendyol' => json_decode($object->jsonRead('apiResponseJson'), true)["platformListingId"],
        };
        $storeId = match ($marketplaceType) {
            'Etsy' => $marketplaceObject->getShopId(),
            'Amazon' => $marketplaceObject->getMerchantId(),
            //'Shopify' => $marketplace->getShopifyStoreId(),  
            'Trendyol' => $marketplaceObject->getTrendyolSellerId(),
        };
        $data = "";
        if($marketplaceType !== 'Amazon') {
            $data = "{$storeId}_{$storeProductId}_{$variantCode}";
        }
        else {
            $data = "{$storeId}_{$storeProductId}";
        }
        $hash = hash('sha1', $data);
        return number_format($hash);
    }

    public function getCalculatedValueForEditMode(Concrete $object, CalculatedValue $context): string
    {
        return $this->compute($object, $context);
    }

}
