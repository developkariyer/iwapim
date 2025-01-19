<?php

namespace App\Model\DataObject;

use App\Connector\Marketplace\Amazon\Constants as AmazonConstants;
use Pimcore\Model\DataObject\Concrete;
use Pimcore\Model\DataObject\Marketplace\Listing;

/**
* Class Marketplace
*
* Represents a marketplace entity in the system, providing methods
* to retrieve marketplace data and related operations.
*
* @package App\Model\DataObject
*/
class Marketplace extends Concrete
{

    /**
    * Retrieves a list of marketplaces based on the specified type.
    *
    * @param string $type The type of marketplace to filter by (optional).
    * @return array The list of marketplaces.
    */
    public static function getMarketplaceList(string $type = ''): array
    {
        $list = new Listing();
        if (!empty($type)) {
            $list->setCondition("`marketplaceType` = ?", [$type]);
        }
        return $list->load();
    }    

    /**
    * Retrieves a list of marketplaces and returns them as an associative array
    * with marketplace keys, excluding Amazon marketplaces.
    *
    * @return array The associative array of marketplace keys.
    */
    public static function getMarketplaceListAsArrayKeys(): array
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
