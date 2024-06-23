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
            case 'mainProductCost':
                return $this->calculateMainProductCost($object);
            case 'colorCost':
                return $this->calculateColorCost($object);
            case 'sizeCost':
                return $this->calculateSizeCost($object);
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
        
        return $amount == 0 ? "0.000" : number_format($cost / $amount, 3, '.', '');
    }

    private function calculateMainProductCost(Concrete $object): string
    {
        $totalCost = 0.0;
        $costNodes = $object->getProductCosts();
        foreach ($costNodes as $costNode) {
            $sarf = $costNode->getSarf();
            $uretimMaliyet = $costNode->getObject()->getUnitCost();
            $totalCost += $uretimMaliyet * $sarf;
        }
        return number_format($totalCost + 0, 3, '.', '');
    }

    private function calculateColorCost(Concrete $object): string
    {
        $colorCost = 0.0;
        $colorCostNodes = $object->getColorCosts();
        foreach ($colorCostNodes as $key => $colorCostNode) {
            $sarf = $colorCostNode->getSarf();
            $uretimMaliyet = $colorCostNode->getObject()->getUnitCost();
            $colorCost += $uretimMaliyet * $sarf;
        }
        return number_format($colorCost + 0, 3, '.', '');
    }

    private function calculateSizeCost(Concrete $object): string
    {
        $sizeCost = 0.0;
        $sizeCostNodes = $object->getSizeCosts();
        foreach ($sizeCostNodes as $key => $sizeCostNode) {
            $sarf = $sizeCostNode->getSarf();
            $uretimMaliyet = $sizeCostNode->getObject()->getUnitCost();
            $sizeCost += $uretimMaliyet * $sarf;
        }
        return number_format($sizeCost + 0, 3, '.', '');
    }

    private function calculateProductCost(Concrete $object): string
    {
        return number_format(
            $object->getMainProductCost() + $object->getColorCost() + $object->getSizeCost(),
            3,
            '.',
            ''
        );
    }
}