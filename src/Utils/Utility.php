<?php

namespace App\Utils;

use Doctrine\DBAL\Exception;
use PDOException;
use Pimcore\Db;
use Pimcore\Model\Asset;
use Pimcore\Model\DataObject\Data\ExternalImage;
use Pimcore\Model\DataObject\Folder;
use Pimcore\Model\Asset\Folder as AssetFolder;
use Pimcore\Model\Asset\Listing as AssetListing;
use App\Command\CacheImagesCommand;
use Pimcore\Model\Element\DuplicateFullPathException;
use Random\RandomException;

class Utility
{

    public static function findImageByName($imageName): false|Asset
    {
        if (strlen($imageName)) {
            $assetList = new AssetListing();
            $assetList->setCondition("filename = ?", [$imageName]);
            $assetList->setLimit(1);
            return $assetList->current();
        } else {
            return false;
        }
    }

    public static function sanitizeVariable($variable, $length = 0): string
    {
        $ellipsis = '...';
        if (empty($variable)) {
            return '';
        }
        if (!mb_check_encoding($variable, 'UTF-8')) {
            $variable = mb_convert_encoding($variable, 'UTF-8', 'auto');
        }
        $sanitized = preg_replace('/[^\p{L}\p{N}çÇğĞıİöÖşŞüÜ\-_ ]/u', ' ', $variable);
        $sanitized = preg_replace('/ +/', ' ', $sanitized);
        if ($length > 0 && mb_strlen($sanitized) > $length) {
            $half = floor(($length - mb_strlen($ellipsis)) / 2);
            $sanitized = mb_substr($sanitized, 0, $half) . $ellipsis . mb_substr($sanitized, -$half);
        }
        return trim($sanitized, " -");
    }

    /**
     * @throws DuplicateFullPathException
     */
    public static function checkSetPath($name, $parent = null): ?Folder
    {
        $targetPath = $parent ? $parent->getFullPath()."/$name" : "/$name";
        $parent ??= Folder::getByPath('/');
        $folderObject = Folder::getByPath($targetPath);
        if (!$folderObject) {
            $folderObject = new Folder();
            $folderObject->setKey($name);
            $folderObject->setParent($parent);
            $folderObject->save();
        }
        return $folderObject;
    }

    /**
     * @throws DuplicateFullPathException
     */
    public static function checkSetAssetPath($name, $parent = null): ?AssetFolder
    {
        $targetPath = $parent ? $parent->getFullPath()."/$name" : "/$name";
        $parent ??= AssetFolder::getByPath('/');
        $folderObject = AssetFolder::getByPath($targetPath);
        if (!$folderObject) {
            $folderObject = new AssetFolder();
            $folderObject->setKey($name);
            $folderObject->setParent($parent);
            $folderObject->save();
        }
        return $folderObject;
    }

    protected static function customEncode($base, $characters, $data): string
    {
        $result = '';
        while ($data > 0) {
            $remainder = $data % $base;
            $result = $characters[$remainder] . $result;
            $data = floor($data / $base);
        }
        return $result;
    }

    protected static function customDecode($base, $characters, $data): float|int
    {
        $length = strlen($data);
        $result = 0;
        for ($i = 0; $i < $length; $i++) {
            $result = $result * $base + strpos($characters, $data[$i]);
        }
        return $result;
    }

    public static function customBase64Encode($data): string
    {
        $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ-_';
        $base = 64;
        return static::customEncode($base, $characters, $data);
    }
    
    public static function customBase64Decode($data): float|int
    {
        $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ-_';
        $base = 64;
        return static::customDecode($base, $characters, $data);
    }

    public static function encodeContainer($message): string
    {
        [$part1, $part2] = explode('-', $message);
        return static::customBase64Encode($part1 . str_pad($part2, 5, '0', STR_PAD_LEFT));
    }

    public static function decodeContainer($encodedMessage): string
    {
        $decodedNumber = static::customBase64Decode($encodedMessage);
        $decodedString = str_pad((string)$decodedNumber, 8, '0', STR_PAD_LEFT);
        return trim(substr($decodedString, 0, -5), '0'). '-' . intval(substr($decodedString, -5));
    }

    public static function removeTRChars($str): array|string
    {
        return str_ireplace(['ı', 'İ', 'ğ', 'Ğ', 'ü', 'Ü', 'ş', 'Ş', 'ö', 'Ö', 'ç', 'Ç'], ['i', 'I', 'g', 'G', 'u', 'U', 's', 'S', 'o', 'O', 'c', 'C'], $str);    
    }

    public static function keepSafeChars($str): array|string
    {
        $safeChars = "\n abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789-_";
        $str = preg_replace("/[^$safeChars]/", '', $str);
        return str_replace("  ", " ", $str);
    }

    /**
     * @throws RandomException
     */
    public static function getSetCustomCache($filename, $cachePath, $stringToCache = null, $expiration = 86400, bool $lazy = false): bool|string
    {
        if (empty($cachePath)) {
            $cachePath = PIMCORE_PROJECT_ROOT . "/tmp";
        }
        if ($lazy) {
            // Randomize expiration by %50
            $expiration += random_int(-($expiration / 2), $expiration / 2);
        }
        $cachePath = rtrim($cachePath, '/');
        if (!is_dir($cachePath)) {
            if (!mkdir($cachePath, 0777, true)) {
                $cachePath = PIMCORE_PROJECT_ROOT . "/tmp";
            }
        }
        $cacheFile = "$cachePath/$filename";
        if ($stringToCache) {
            file_put_contents($cacheFile, $stringToCache);
            return true;
        } else {
            if (file_exists($cacheFile) && filemtime($cacheFile) > time()-$expiration) {
                return file_get_contents($cacheFile);
            }
        }
        return false;
    }

    /**
     * @throws RandomException
     */
    public static function getCustomCache($filename, $cachePath, $expiration = 86400, bool $lazy = false): string
    {
        return static::getSetCustomCache($filename, $cachePath, null, $expiration, $lazy);
    }

    /**
     * @throws RandomException
     */
    public static function setCustomCache($filename, $cachePath, $stringToCache, $expiration = 86400): bool
    {
        return static::getSetCustomCache($filename, $cachePath, $stringToCache, $expiration);
    }

    /**
     * @throws Exception
     */
    public static function retrieveJsonData($fieldName)
    {
        $db = Db::get();
        $sql = "SELECT json_data FROM iwa_json_store WHERE field_name=?";
        $result = $db->fetchAssociative($sql, [$fieldName]);
        if ($result) {
            return json_decode($result['json_data'], true);
        }
        return null;
    }

    public static function storeJsonData($objectId, $fieldName, $data): void
    {
        $db = Db::get();
        try {
            $sql = "INSERT INTO iwa_json_store (object_id, field_name, json_data) VALUES (?, ?, ?) ON DUPLICATE KEY UPDATE json_data=?";
            $db->executeQuery($sql, [$objectId, $fieldName, json_encode($data), json_encode($data)]);
        } catch (\Exception $e) {
            echo $e->getMessage();
            echo $objectId, '-', $fieldName, '-', json_encode($data);
            if ($e instanceof PDOException) {
                echo "SQLSTATE code: " . $e->errorInfo[0] . "\n";
                echo "SQL error: " . $e->errorInfo[2] . "\n"; 
            }
            exit;
        }
    }

    public static function checkJwtTokenValidity($token): bool
    {
        $tokenParts = explode(".", $token);
        if (count($tokenParts) !== 3) {
            return false;
        }
        $tokenPayload = base64_decode($tokenParts[1]);
        $jwtPayload = json_decode($tokenPayload, true);
        $currentTimestamp = time() + 60;
        if ($jwtPayload['exp'] < $currentTimestamp) {
            return false;
        }
        return true;
    }

    public static function getCachedImage($url): ?ExternalImage
    {
        if (empty($url) || !filter_var($url, FILTER_VALIDATE_URL)) {
            return null;
        }
        if (str_contains($url, 'iwa.web.tr')) {
            return new ExternalImage($url);
        }
        $imageAsset = Utility::findImageByName(CacheImagesCommand::createUniqueFileNameFromUrl($url));
        if ($imageAsset) {
            return new ExternalImage(
                "https://mesa.iwa.web.tr/var/assets/".str_replace(" ", "%20", $imageAsset->getFullPath())
            );
        }
        return new ExternalImage($url);
    }

    /**
     * @throws Exception
     */
    public static function convertCurrency($amount, $fromCurrency, $toCurrency, $date): string
    {
        if ($fromCurrency === $toCurrency) {
            return (string)$amount;
        }
        $fromCurrencyValue = ($fromCurrency === 'TRY') ? 1 : null;
        $toCurrencyValue = ($toCurrency === 'TRY') ? 1 : null;
        $db = Db::get();
        $sql = "SELECT value FROM iwa_currency_history WHERE currency = :currency AND DATE(date) <= :today ORDER BY ABS(TIMESTAMPDIFF(DAY, DATE(date), :today)) ASC LIMIT 1;";
        if (!isset($fromCurrencyValue)) {
            $fromCurrencyValue = $db->fetchOne($sql, [
                'today' => $date,
                'currency' => $fromCurrency
            ]);
        }
        if (!isset($toCurrencyValue)) {
            $toCurrencyValue = $db->fetchOne($sql, [
                'today' => $date,
                'currency' => $toCurrency
            ]);
        }
        if (!$fromCurrencyValue || !$toCurrencyValue) {
            echo "Currency values not found for $fromCurrency or $toCurrency";
            return "0";
        }
        return bcmul((string)$amount, (string)($fromCurrencyValue/$toCurrencyValue), 2);
    }

    /**
     * @throws Exception
     */
    public static function getCurrencyValueByDate($currency, $date): float
    {
        $db = Db::get();
        if ($currency === 'TRY') {
            return "1.0";
        }
        $sql = "SELECT value FROM iwa_currency_history WHERE currency = :currency AND DATE(date) <= :today ORDER BY ABS(TIMESTAMPDIFF(DAY, DATE(date), :today)) ASC LIMIT 1;";
        return $db->fetchOne($sql, [
            'today' => $date,
            'currency' => $currency
        ]);
    }

    /**
     * @throws Exception
     */
    public static function executeSql(string $sql, array $params = []): void
    {
        try {
            $db = Db::get();
            $stmt = $db->prepare($sql);
            $stmt->executeStatement($params);
        } catch (\Exception $e) {
            echo "Execute SQL Error: " . $sql . "\n" . $e->getMessage() . "\n";
        }
    }

    /**
     * @throws Exception
     */
    public static function fetchFromSql(string $sql, array $params = [])
    {
        try {
            $db = Db::get();
            return $db->fetchAllAssociative($sql, $params);
        } catch (\Exception $e) {
            echo "Fetch From SQL Error: " . $sql . "\n" . $e->getMessage() . "\n";
        }
    }

    /**
     * @throws Exception
     */
    public static function executeSqlFile(string $filePath, array $params = []): void
    {
        if (!file_exists($filePath)) {
            throw new Exception("SQL file not found.");
        }
        try {
            $db = Db::get();
            $sql = file_get_contents($filePath);
            $stmt = $db->prepare($sql);
            $stmt->executeStatement($params);
        } catch (\Exception $e) {
            echo "Execute SQL File Error: " . $filePath . "\n" . $e->getMessage() . "\n";
        }
    }

    /**
     * @throws Exception
     */
    public static function fetchFromSqlFile(string $filePath, array $params = [])
    {
        if (!file_exists($filePath)) {
            throw new Exception("SQL file not found.");
        }
        try {
            $db = Db::get();
            $sql = file_get_contents($filePath);
            return $db->fetchAllAssociative($sql, $params);
        } catch (\Exception $e) {
            echo "Fetch From SQL File Error: " . $filePath . "\n" . $e->getMessage() . "\n";
        }
    }

}
