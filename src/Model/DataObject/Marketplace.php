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

    public static function findByField($field, $value)
    {
        $list = new Listing();
        $list->setCondition("`$field` = ?", [$value]);
        $list->setUnpublished(true);
        $list->setLimit(1);
        return $list->current();
    }

    public function getVariantProductIds()
    {
        $db = \Pimcore\Db::get();
        $sql = "SELECT DISTINCT src_id FROM object_relations_varyantproduct WHERE dest_id = ?";
        $result = $db->fetchAllAssociative($sql, [$this->getId()]);
        $ids = array_column($result, 'src_id');
        return $ids;
    }

}
