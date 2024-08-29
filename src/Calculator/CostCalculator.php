<?php

namespace App\Calculator;

use Pimcore\Model\DataObject\Concrete;
use Pimcore\Model\DataObject\ClassDefinition\CalculatorClassInterface;
use Pimcore\Model\DataObject\Data\CalculatedValue;
use Pimcore\Model\DataObject\CostModel;
use Pimcore\Model\DataObject\Currency\Listing as CurrencyListing;

class CostCalculator implements CalculatorClassInterface
{
    
    public function compute(Concrete $object, CalculatedValue $context): string
    {
        return match ($context->getFieldname()) {
            'productCost' => '', //$this->productCost($object),
            default => '',
        };
    }

    protected static function convertCurrency($currency, $amount): float
    {
        $list = new CurrencyListing();
        $list->setCondition('currencyCode = ?', $currency);
        $list->setLimit(1);
        $currencyObject = $list->current();
        if ($currencyObject && $currencyObject->getRate() > 0) {
            return $amount * $currencyObject->getRate();
        }
        return $amount;
    }


    public function getCalculatedValueForEditMode(Concrete $object, CalculatedValue $context): string
    {
        return $this->compute($object, $context);
    }


    private function calculateMainProductCost(Concrete $object): string
    {
        $totalCost = 0.0;
        $costNodes = $object->getProductCosts();
        foreach ($costNodes as $costNode) {
            $uretimMaliyet = $costNode->getObject()->getUnitCost();
            $sarf = $costNode->getSarf();
            if ($sarf == 0) {
                $sarf = 1;
            }
            $factor = $costNode->getFactor();
            switch ($factor) {
                case "Ebat m2":
                    $ebat = $object->getProductDimension1() * $object->getProductDimension2();
                    $totalCost += $sarf * $uretimMaliyet * $ebat / 10000;
                    break;
                case "Ambalaj m2":
                    $ambalaj = $object->getPackageDimension1() * $object->getPackageDimension2();
                    $totalCost += $sarf * $uretimMaliyet * $ambalaj / 10000;
                    break;
                default:
                    $totalCost += $uretimMaliyet * $sarf;
            }
        }
        return number_format($totalCost + 0, 2, '.', '');
    }

    private function calculateVariationCost(Concrete $object): string
    {
        $variationCost = 0.0;
        $variationCostNodes = $object->getVariationCosts();
        foreach ($variationCostNodes as $key => $variationCostNode) {
            $sarf = $variationCostNode->getSarf();
            $uretimMaliyet = $variationCostNode->getObject()->getUnitCost();
            if ($sarf == 0) {
                $sarf = 1;
            }
            $factor = $variationCostNode->getFactor();
            switch ($factor) {
                case "Ebat m2":
                    $ebat = $object->getProductDimension1() * $object->getProductDimension2();
                    $variationCost += $sarf * $uretimMaliyet * $ebat / 10000;
                    break;
                case "Ambalaj m2":
                    $ambalaj = $object->getPackageDimension1() * $object->getPackageDimension2();
                    $variationCost += $sarf * $uretimMaliyet * $ambalaj / 10000;
                    break;
                default:
                    $variationCost += $uretimMaliyet * $sarf;
            }
        }
        return number_format($variationCost + 0, 2, '.', '');
    }

    private function calculateProductCost(Concrete $object): string
    {
        return number_format($object->getMainProductCost() + $object->getVariationCost(), 2, '.', '');
    }
}