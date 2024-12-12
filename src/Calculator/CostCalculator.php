<?php

namespace App\Calculator;

use Pimcore\Model\DataObject\Concrete;
use Pimcore\Model\DataObject\ClassDefinition\CalculatorClassInterface;
use Pimcore\Model\DataObject\Data\CalculatedValue;
use Pimcore\Model\DataObject\Product;

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
        $bundleItems = $object->getBundleProducts();
        if (!empty($bundleItems)) {
            foreach ($bundleItems as $bundleItem) {
                $product = $bundleItem->getObject();
                $bundleItemCost = $product->getProductCost() ?? '0.00';
                $totalCost = bcadd($totalCost, $bundleItemCost, 4);
            }
            return number_format($totalCost, 4, '.', '');
        }
        foreach ($object->getParent()->getCostModelProduct() as $costModel) {
            $costModelCost = $costModel->getCost($object) ?? '0.00';
            $totalCost = bcadd($totalCost, $costModelCost, 4);
        }
        foreach ($object->getCostModelVariant() as $costModel) {
            $costModelCost = $costModel->getCost($object) ?? '0.00';
            $totalCost = bcadd($totalCost, $costModelCost, 4);
        }
        return number_format($totalCost, 4, '.', '');
    }    

    public function getCalculatedValueForEditMode(Concrete $object, CalculatedValue $context): string
    {
        return $this->compute($object, $context);
    }

}