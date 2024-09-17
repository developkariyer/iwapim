<?php

namespace App\Calculator;

use Pimcore\Model\DataObject\Concrete;
use Pimcore\Model\DataObject\ClassDefinition\CalculatorClassInterface;
use Pimcore\Model\DataObject\Data\CalculatedValue;
use Pimcore\Model\DataObject\Currency;

class UnitCostCalculator implements CalculatorClassInterface
{

    public function compute(Concrete $object, CalculatedValue $context): string
    {
        return match ($context->getFieldname()) {
            'unitCost' => static::unitCost($object),
            default => '',
        };
    }

    protected static function unitCost($object): string
    {
        if ($object->isPublished()) {
            $amount = empty($object->getAmount()) ? 1 : $object->getAmount();
            $cost = Currency::convertCurrency($object->getCurrency(), bcdiv($object->getCost(), $amount, 4));
            $cost += static::combinedCost($object);
            return number_format($cost + 0, 4, '.', '');
        }
        return '';
    }

    protected static function combinedCost($object): float
    {
        $combinedCosts = $object->getCombinedCost();
        $cost = 0;
        if (is_iterable($combinedCosts)) {
            foreach ($combinedCosts as $combinedCost) {
                $unitCost = $combinedCost->getObject()->getUnitCost();
                $amount = $combinedCost->getAmount();
                $cost = bcadd($cost, bcmul($unitCost, $amount, 4), 4);
            }
        }
        return $cost;
    }

    public function getCalculatedValueForEditMode(Concrete $object, CalculatedValue $context): string
    {
        return $this->compute($object, $context);
    }

}