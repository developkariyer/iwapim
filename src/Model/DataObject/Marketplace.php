<?php

namespace App\Model\DataObject;

use App\MarketplaceConnector\AmazonConstants;
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

    public static function getMarketplaceListAsArrayKeys()
    {
        $marketplaces = self::getMarketplaceList();
        $marketplacesArray = [];
        foreach ($marketplaces as $marketplace) {
            if ($marketplace->getMarketplaceType()!=='Amazon') {
                $marketplacesArray[$marketplace->getKey()] = '';
            }
        }
        foreach (array_keys(AmazonConstants::amazonMerchant) as $key) {
            $marketplacesArray["Amazon_{$key}"] = '';
        }
        return $marketplacesArray;
    }
}
