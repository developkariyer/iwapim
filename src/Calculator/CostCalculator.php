<?php

namespace App\Calculator;

use Pimcore\Logger;
use Pimcore\Model\DataObject\Concrete;
use Pimcore\Model\DataObject\ClassDefinition\CalculatorClassInterface;
use Pimcore\Model\DataObject\Data\CalculatedValue;


class CostCalculator implements CalculatorClassInterface
{
    
    public function compute(Concrete $object, CalculatedValue $context): string
    {
        switch($context->getFieldname()) {
            case 'unitCost':
                return $this->calculateUnitCost($object);
            case 'productCost':
                return $this->calculateProductCost($object);
            default:
                return '';
        }
    }

    public function getCalculatedValueForEditMode(Concrete $object, CalculatedValue $context): string
    {
        return $this->compute($object, $context);
    }

    private function calculateUnitCost($object): string
    {
        $cost = $object->getCost();
        $amount = $object->getAmount();
        if ($amount == 0) {
            return "0.000";
        }
        return number_format($cost / $amount, 3, '.', '');
    }

    private function calculateProductCost(Concrete $object): string
    {
        $totalCost = 0.0;
        $costNodes = $object->getProductCosts();
        foreach ($costNodes as $key => $costNode) {
            $sarf = $costNode->getSarf();
            $uretimMaliyet = $costNode->getObject()->getUnitCost();
            $totalCost += $uretimMaliyet * $sarf;
        }
        return number_format($totalCost + 0, 3, '.', '');
    }
}