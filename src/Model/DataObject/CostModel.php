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
            $totalCost = bcadd(
                $totalCost,
                match ($relationNode->getFactor()) {
                    'Beher Ürün' => bcmul(
                        (string) ($costNode->getUnitCost() ?? '0.00'), 
                        (string) ($relationNode->getSarf() ?? '0.00'),
                        4
                    ),
                    'Ebat m2' => bcmul(
                        (string) ($costNode->getunitCost()), 
                        bcmul(
                            (string) ($relationNode->getSarf() ?? '0.00'), 
                            (string) ($product->getArea() ?? '0.00'),
                            4
                        ),
                        4
                    ),
                    'Ambalaj m2' => bcmul(
                        (string) ($costNode->getunitCost()),
                        bcmul(
                            (string) ($relationNode->getSarf() ?? '0.00'), 
                            (string) ($product->getPackageArea() ?? '0.00'),
                            4
                        ),
                        4
                    ),
                    'Kesim Detay' => bcmul(
                        (string) ($costNode->getunitCost()),
                        bcmul(
                            (string) ($relationNode->getSarf() ?? '0.00'), 
                            (string) ($product->getCutComplexity() ?? '0.00'),
                            4
                        ),
                        4
                    ),
                    default => '0.00',
                },
                4
            );
        }
        return $totalCost;
    }
}
