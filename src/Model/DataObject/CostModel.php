<?php

namespace App\Model\DataObject;

use Pimcore\Model\DataObject\Concrete;

/**
* Class CostModel
*
* This class represents a model for calculating costs associated with a product. 
* It handles the retrieval and computation of various cost factors related to the product.
* 
* @package App\Model\DataObject
*/
class CostModel extends Concrete
{
    /**
    * Calculates the total cost of a product based on various cost nodes.
    *
    * @param Product $product The product for which the cost is calculated.
    * @return string The total cost as a string formatted to two decimal places.
    */
    public function getCost(Product $product): string
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
