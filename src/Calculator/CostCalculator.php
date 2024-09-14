<?php

namespace App\Calculator;

use Pimcore\Model\DataObject\Concrete;
use Pimcore\Model\DataObject\ClassDefinition\CalculatorClassInterface;
use Pimcore\Model\DataObject\Data\CalculatedValue;
use Pimcore\Model\DataObject\Product;
use Pimcore\Model\DataObject\CostModel;
use Pimcore\Model\DataObject\Currency\Listing as CurrencyListing;

class CostCalculator implements CalculatorClassInterface
{

    public function compute(Concrete $object, CalculatedValue $context): string
    {
        if (!($object instanceof Product) || $object->level() !== 1) {
            return '';
        }
        return match ($context->getFieldname()) {
            'productCost' => $this->calculateProductCost($object),
            default => '',
        };
    }

    private function calculateProductCost(Product $object): string
    {
        $totalCost = '0.00';
        $bundleItems = $object->getBundleItems();
        if (!empty($bundleItems)) {
            foreach ($bundleItems as $bundleItem) {
                $totalCost = bcadd($totalCost, $bundleItem->getCost(), 2);
            }
            return $totalCost;
        }
        foreach ($object->getParent()->getCostModelProduct() as $costModel) {
            $totalCost = bcadd($totalCost, $costModel->getCost($object), 2);
        }
        foreach ($object->getCostModelVariant() as $costModel) {
            $totalCost = bcadd($totalCost, $costModel->getCost($object), 2);
        }
    
        return $totalCost;
    }    

    public function getCalculatedValueForEditMode(Concrete $object, CalculatedValue $context): string
    {
        return $this->compute($object, $context);
    }

}