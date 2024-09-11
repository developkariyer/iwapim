<?php

namespace App\Model\DataObject;

use Pimcore\Model\DataObject\Concrete;
use Pimcore\Model\DataObject\Marketplace\Listing;

class Marketplace extends Concrete
{
    public static function getMarketplaceList($type = '')
    {
        $list = new Listing();
        if (!empty($type)) {
            $list->setCondition("`marketplaceType` = ?", [$type]);
        }
        $marketplaces = $list->load();
        return $marketplaces;
    }    
}
