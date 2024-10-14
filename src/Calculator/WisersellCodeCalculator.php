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
            'calculatedWisersellCode' => $this->calculateWisersellCode($object),
            //'calculatedWisersellCode' => "WisersellCode",
            default => '',
        };
    }

    private function calculateWisersellCode(Concrete $object): string
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
            'Etsy' => json_decode($variantProduct->jsonRead('parentResponseJson'), true) ["listing_id"],
            'Shopify' => json_decode($variantProduct->jsonRead('apiResponseJson'), true)["id"],  
            'Trendyol' => json_decode($variantProduct->jsonRead('apiResponseJson'), true)["platformListingId"],
        };
        $storeId = match ($marketplaceType) {
            'Etsy' => $marketplace->getShopId(),
            'Amazon' => $marketplace->getMerchantId(),
            //'Shopify' => $marketplace->getShopifyStoreId(),  
            'Trendyol' => $marketplace->getTrendyolSellerId(),
        };
        $data = "";
        if($marketplaceType !== 'Amazon') {
            $data = "{$storeId}_{$storeProductId}_{$variantCode}";
        }
        else {
            $data = "{$storeId}_{$storeProductId}";
        }
        //$hash = hash('sha1', $data);
        //return number_format($data);
        return $data;
    }

    public function getCalculatedValueForEditMode(Concrete $object, CalculatedValue $context): string
    {
        return $this->compute($object, $context);
    }

}
