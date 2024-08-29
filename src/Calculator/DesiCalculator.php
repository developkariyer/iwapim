<?php

namespace App\Calculator;

use Pimcore\Model\DataObject\Concrete;
use Pimcore\Model\DataObject\ClassDefinition\CalculatorClassInterface;
use Pimcore\Model\DataObject\Data\CalculatedValue;

class DesiCalculator implements CalculatorClassInterface
{
    public function compute(Concrete $object, CalculatedValue $context): string
    {
        return match ($context->getFieldname()) {
            'desi5000' => $this->calculateDesi($object, 5000),
            default => '',
        };
    }

    private function calculateDesi(Concrete $object, $desi = 0): string
    {
        if ($desi == 0) {
            return "";
        }
        $dimensionDesi = $object->getPackageDimension1() * $object->getPackageDimension2() * $object->getPackageDimension3() / $desi;
        $finalDesi = $object->getPackageWeight() > $dimensionDesi ? $object->getPackageWeight() : $dimensionDesi;
        return number_format($finalDesi + 0, 2, '.', '');
    }

    public function getCalculatedValueForEditMode(Concrete $object, CalculatedValue $context): string
    {
        return $this->compute($object, $context);
    }

}
