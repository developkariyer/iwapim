<?php

namespace App\Connector\Marketplace\Ozon;

use App\Utils\Utility;
use Doctrine\DBAL\Exception;
use Pimcore\Db;
use Random\RandomException;

class Utils
{
    public Connector $connector;

    public function __construct(Connector $connector)
    {
        $this->connector = $connector;
    }

    /**
     * @throws RandomException
     * @throws Exception
     */
    static public function getOzonProductTypes(string $q = ''): array
    {
        $filename = 'OzonProductTypesSelect'.urlencode($q).'.json';
        $cachePath = PIMCORE_PROJECT_ROOT . '/tmp/tmp/';
        $expiration = 7*86400;
        $items = json_decode(Utility::getCustomCache($filename, $cachePath, $expiration), true);
        if (empty($items)) {
            $db = Db::get();
            $results = empty($q) ?
                $db->fetchAllAssociative('SELECT * FROM iwa_ozon_producttype ORDER BY category_full_name') :
                $db->fetchAllAssociative('SELECT * FROM iwa_ozon_producttype WHERE category_full_name LIKE ? ORDER BY category_full_name', ['%'.$q.'%']);
            $items = array_map(function ($result) {
                return [
                    'id' => $result['description_category_id'] . '.' . $result['type_id'],
                    'text' => trim($result['category_full_name']),
                ];
            }, $results);
            Utility::setCustomCache($filename, $cachePath, json_encode($items));
        }
        return $items;
    }

    /**
     * @throws Exception
     */
    static public function isOzonProductType(string $descriptionCategoryId, string $typeId)
    {
        $db = Db::get();
        return $db->fetchOne('SELECT category_full_name FROM iwa_ozon_producttype WHERE description_category_id = ? AND type_id = ?', [$descriptionCategoryId, $typeId]) ?? '';
    }
}