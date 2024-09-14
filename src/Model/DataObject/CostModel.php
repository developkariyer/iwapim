<?php

namespace App\Model\DataObject;

use Pimcore\Model\DataObject\Concrete;
use Pimcore\Model\DataObject\Currency\Listing;

class CostModel extends Concrete
{
    public function getCost($product)
    {
        return "10.05";
    }
    
}