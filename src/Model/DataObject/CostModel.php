<?php

namespace App\Model\DataObject;

use Pimcore\Model\DataObject\Concrete;

class CostModel extends Concrete
{
    public function getCost($product): string
    {
        $totalCost = '0.00';
        foreach ($this->getCostNodes() as $relationNode) {
            $costNode = $relationNode->getObject();            
            $totalCost = bcAdd(
                $totalCost,
                match ($relationNode->getFactor()) {
                    'Beher Ürün' => bcmul(
                        $costNode->getUnitCost(), 
                        $relationNode->getSarf()
                    ),
                    'Ebat m2' => bcmul(
                        $costNode->getunitCost(), 
                        bcmul($relationNode->getSarf(), number_format($product->getArea(), 2, '.', ''))
                    ),
                    'Ambalaj m2' => bcmul(
                        $costNode->getunitCost(),
                        bcmul($relationNode->getSarf(), number_format($product->getPackageArea(), 2, '.', ''))
                    ),
                    'Kesim Detay' => bcmul(
                        $costNode->getunitCost(),
                        bcmul($relationNode->getSarf(), number_format($product->getCutComplexity(), 2, '.', ''))
                    ),
                    default => '0.00',
                }
            );
        }
        return $totalCost;
    }

}