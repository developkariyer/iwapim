<?php

namespace App\Calculator;

use Pimcore\Model\DataObject\Concrete;
use Pimcore\Model\DataObject\ClassDefinition\CalculatorClassInterface;
use Pimcore\Model\DataObject\Data\CalculatedValue;
use Pimcore\Db;
use Pimcore\Model\DataObject\ShopifyVariant;
use Pimcore\Model\DataObject\TrendyolVariant;
use App\Model\DataObject\VariantProduct;



class OrdersCalculator implements CalculatorClassInterface
{
    public function compute(Concrete $object, CalculatedValue $context): string
    {
        $returnValue = match ($context->getFieldname()) {
            'totalOrders' => $this->totalOrders($object),
            'last7Orders' => $this->last7Orders($object),
            'last30Orders' => $this->last30Orders($object),
            default => '',
        };
        return str_pad($returnValue, 7, '0', STR_PAD_LEFT);
    }

    public function totalOrders(Concrete $object): string
    {
        $db = Db::get();
        $marketplace = $object->getMarketplace();
        $marketplaceType = $marketplace->getMarketPlaceType();
        if ($marketplaceType === 'Trendyol') {
            $variantId = (string) json_decode($object->jsonRead('apiResponseJson'), true)["productCode"];
        }
        else {
            $variantId = (string) $object->getUniqueMarketplaceId();
        }
        $result = $db->fetchOne("SELECT sum(quantity) FROM `iwa_marketplace_orders_line_items` WHERE variant_id = ?", [$variantId] AND marketplace_id = $marketplace->getId());
        return $result + 0;
        /*if ($object instanceof ShopifyVariant) {
            $shopifyId = (string) $object->getShopifyId();
            $result = $db->fetchOne("SELECT sum(quantity) FROM `iwa_shopify_orders_line_items` WHERE variant_id = ? GROUP BY variant_id", [$shopifyId]);
            return $result + 0;
        }*/
    }

    public function last7Orders(Concrete $object): string
    {
        $db = Db::get();
        $marketplace = $object->getMarketplace();
        $marketplaceType = $marketplace->getMarketPlaceType();
        if ($marketplaceType === 'Trendyol') {
            $variantId = (string) json_decode($object->jsonRead('apiResponseJson'), true)["productCode"];
        }
        else {
            $variantId = (string) $object->getUniqueMarketplaceId();
        }
        $result = $db->fetchOne("SELECT sum(quantity) FROM `iwa_marketplace_orders_line_items` WHERE variant_id = ? AND (created_at >= NOW() - INTERVAL 7 DAY)", [$variantId]);
        return $result + 0;
    }

    public function last30Orders(Concrete $object): string
    {
        $db = Db::get();
        $marketplace = $object->getMarketplace();
        $marketplaceType = $marketplace->getMarketPlaceType();
        if ($marketplaceType === 'Trendyol') {
            $variantId = (string) json_decode($object->jsonRead('apiResponseJson'), true)["productCode"];
        }
        else {
            $variantId = (string) $object->getUniqueMarketplaceId();
        }
        $result = $db->fetchOne("SELECT sum(quantity) FROM `iwa_marketplace_orders_line_items` WHERE variant_id = ? AND (created_at >= NOW() - INTERVAL 30 DAY)", [$variantId]);
        return $result + 0;
       /* if ($object instanceof ShopifyVariant) {
            $shopifyId = (string) $object->getShopifyId();
            $result = $db->fetchOne("SELECT sum(quantity) FROM `iwa_shopify_orders_line_items` WHERE variant_id = ? AND (created_at >= NOW() - INTERVAL 30 DAY) GROUP BY variant_id", [$shopifyId]);
            return $result + 0;
        }*/
    }

    public function getCalculatedValueForEditMode(Concrete $object, CalculatedValue $context): string
    {
        return $this->compute($object, $context);
    }

}
