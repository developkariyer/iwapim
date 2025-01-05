<?php

namespace App\Utils;

use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Exception;
use Pimcore\Db;

class Registry
{

    private static bool $transactionActive = false;
    private static ?Connection $transactionDb = null;

    /**
     * @throws Exception
     */
    public static function beginTransaction(): void
    {
        if (self::$transactionActive) {
            throw new Exception('Transaction already active');
        }
        self::$transactionActive = true;
        self::$transactionDb = Db::get();
        self::$transactionDb->beginTransaction();
    }

    /**
     * @throws Exception
     */
    public static function commit(): void
    {
        if (!self::$transactionActive) {
            throw new Exception('No active transaction');
        }
        self::$transactionDb->commit();
        self::$transactionActive = false;
        self::$transactionDb = null;
    }

    /**
     * @throws Exception
     */
    public static function rollback(): void
    {
        if (!self::$transactionActive) {
            throw new Exception('No active transaction');
        }
        self::$transactionDb->rollBack();
        self::$transactionActive = false;
        self::$transactionDb = null;
    }

    /**
     * @throws Exception
     */
    public static function getJsonKey($regkey, $regtype='DEFAULT', $useTransaction = false): array
    {
        $db = $useTransaction ? (self::$transactionDb ?? Db::get()) : Db::get();
        $sql = "SELECT regjson FROM iwa_registry_json WHERE regkey = ? AND regtype = ?";
        $json = $db->fetchOne($sql, [$regkey, $regtype]);
        return json_decode($json, true) ?? [];
    }

    /**
     * @throws Exception
     */
    public static function setJsonKey($regkey, $regjson, $regtype='DEFAULT'): void
    {
        $db = self::$transactionDb ?? Db::get();
        $sql = "INSERT INTO iwa_registry_json (regkey, regjson, regtype) VALUES (?, ?, ?) ON DUPLICATE KEY UPDATE regjson = ?";
        $db->executeStatement($sql, [$regkey, json_encode($regjson), $regtype, json_encode($regjson)]);
    }

    /**
     * @throws Exception
     */
    public static function getKey($regkey, $regtype = 'DEFAULT', $useTransaction = false): string
    {
        $db = $useTransaction ? (self::$transactionDb ?? Db::get()) : Db::get();
        $sql = "SELECT regvalue FROM iwa_registry WHERE regkey = ? AND regtype = ?";
        return $db->fetchOne($sql, [$regkey, $regtype]) ?? '';
    }

    /**
     * @throws Exception
     */
    public static function setKey($regkey, $regvalue, $regtype = 'DEFAULT'): void
    {
        $db = self::$transactionDb ?? Db::get();
        $sql = "INSERT INTO iwa_registry (regkey, regvalue, regtype) VALUES (?, ?, ?) ON DUPLICATE KEY UPDATE regvalue = ?";
        $db->executeStatement($sql, [$regkey, $regvalue, $regtype, $regvalue]);
    }

    /**
     * @throws Exception
     */
    public static function searchKeys($regvalue, $regtype = 'DEFAULT', $limit = 0)
    {
        $db = Db::get();
        $sql = "SELECT regkey FROM iwa_registry WHERE regvalue = ? AND regtype = ? LIMIT $limit";
        return $limit == 1 ? $db->fetchOne($sql, [$regvalue, $regtype]) : $db->fetchFirstColumn($sql, [$regvalue, $regtype]);
    }
}