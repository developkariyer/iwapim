<?php

namespace App\Model\DataObject;

use Pimcore\Model\DataObject\Concrete;
use Pimcore\Model\DataObject\Currency\Listing;

class CostModel extends Concrete
{
    public function getCost($product)
    {
        $totalCost = '0.00';
        foreach ($this->getCostNodes() as $costNode) {
            error_log($costNode->getFactor());
        }
    }
    
}