<?php

namespace App\Model\DataObject;

use App\Connector\Marketplace\Amazon\AmazonConstants;
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
    private $connector;
    
    /**
    * Retrieves a list of marketplaces based on the specified type.
    *
    * @param string $type The type of marketplace to filter by (optional).
    * @return array The list of marketplaces.
    */
    public static function getMarketplaceList($type = '')
    {
        $list = new Listing();
        if (!empty($type)) {
            $list->setCondition("`marketplaceType` = ?", [$type]);
        }
        $marketplaces = $list->load();
        return $marketplaces;
    }    

    /**
    * Retrieves a list of marketplaces and returns them as an associative array
    * with marketplace keys, excluding Amazon marketplaces.
    *
    * @return array The associative array of marketplace keys.
    */
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

    /**
    * Finds a marketplace by a specified field and value.
    *
    * @param string $field The field to search by.
    * @param mixed $value The value to search for.
    * @return Concrete|null The found marketplace or null if not found.
    */
    public static function findByField($field, $value)
    {
        $list = new Listing();
        $list->setCondition("`$field` = ?", [$value]);
        $list->setUnpublished(true);
        $list->setLimit(1);
        return $list->current();
    }

    /**
    * Retrieves variant product IDs related to the current marketplace.
    *
    * @return array An array of variant product IDs.
    */
    public function getVariantProductIds()
    {
        $db = \Pimcore\Db::get();
        $sql = "SELECT DISTINCT src_id FROM object_relations_varyantproduct WHERE dest_id = ?";
        $result = $db->fetchAllAssociative($sql, [$this->getId()]);
        $ids = array_column($result, 'src_id');
        return $ids;
    }

}
