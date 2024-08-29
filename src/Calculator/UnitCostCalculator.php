<?php

namespace App\Calculator;

use Pimcore\Model\DataObject\Concrete;
use Pimcore\Model\DataObject\ClassDefinition\CalculatorClassInterface;
use Pimcore\Model\DataObject\Data\CalculatedValue;

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
            $amount = ($object->getAmount() == 0) ? 1 : $object->getAmount();
            $cost = static::convertCurrency($object->getCurrency(), $object->getCost() / $amount);
            $cost += static::combinedCost($object);
            return number_format($cost + 0, 2, '.', '');
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
                $cost += $unitCost * $amount;
            }
        }
        return $cost;
    }

    protected static function convertCurrency($currency, $amount): float
    {
        $list = new \Pimcore\Model\DataObject\Currency\Listing();
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

}