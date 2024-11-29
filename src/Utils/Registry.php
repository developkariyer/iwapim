<?php

namespace App\Utils;

class Registry
{
    public static function getKey($regkey, $regtype = 'DEFAULT')
    {
        $db = \Pimcore\Db::get();
        $sql = "SELECT regvalue FROM iwa_registry WHERE regkey = ? AND regtype = ?";
        return $db->fetchOne($sql, [$regkey, $regtype]);
    }

    public static function setKey($regkey, $regvalue, $regtype = 'DEFAULT')
    {
        $db = \Pimcore\Db::get();
        $sql = "INSERT INTO iwa_registry (regkey, regvalue, regtype) VALUES (?, ?, ?) ON DUPLICATE KEY UPDATE regvalue = ?";
        $db->executeStatement($sql, [$regkey, $regvalue, $regtype, $regvalue]);
    }

    public static function searchValue($regvalue, $regtype = 'DEFAULT')
    {   // DO NOT USE THIS FUNCTION 
        $db = \Pimcore\Db::get();
        $sql = "SELECT regkey FROM iwa_registry WHERE regvalue = ? AND regtype = ? LIMIT 1";
        return $db->fetchOne($sql, [$regvalue, $regtype]);
    }
}