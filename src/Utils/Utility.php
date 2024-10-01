<?php

namespace App\Utils;

use Pimcore\Model\DataObject\Folder;
use Pimcore\Model\Asset\Folder as AssetFolder;
use Pimcore\Model\Asset\Listing as AssetListing;

class Utility
{
    public static function findImageByName($imageName)
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

    public static function sanitizeVariable($variable, $length = 0)
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
        $sanitized = trim($sanitized, " -");
        return $sanitized;
    }
    
    public static function checkSetPath($name, $parent = null) 
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

    public static function checkSetAssetPath($name, $parent = null) 
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

    protected static function customEncode($base, $characters, $data)
    {
        $result = '';
        while ($data > 0) {
            $remainder = $data % $base;
            $result = $characters[$remainder] . $result;
            $data = floor($data / $base);
        }
        return $result;
    }

    protected static function customDecode($base, $characters, $data)
    {
        $length = strlen($data);
        $result = 0;
        for ($i = 0; $i < $length; $i++) {
            $result = $result * $base + strpos($characters, $data[$i]);
        }
        return $result;
    }

    public static function customBase64Encode($data)
    {
        $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ-_';
        $base = 64;
        return static::customEncode($base, $characters, $data);
    }
    
    public static function customBase64Decode($data)
    {
        $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ-_';
        $base = 64;
        return static::customDecode($base, $characters, $data);
    }

    public static function encodeContainer($message)
    {
        [$part1, $part2] = explode('-', $message);
        return static::customBase64Encode($part1 . str_pad($part2, 5, '0', STR_PAD_LEFT));
    }

    public static function decodeContainer($encodedMessage)
    {
        $decodedNumber = static::customBase64Decode($encodedMessage);
        $decodedString = str_pad((string)$decodedNumber, 8, '0', STR_PAD_LEFT);
        return trim(substr($decodedString, 0, -5), '0'). '-' . intval(substr($decodedString, -5));
    }

    public static function removeTRChars($str)
    {
        return str_ireplace(['ı', 'İ', 'ğ', 'Ğ', 'ü', 'Ü', 'ş', 'Ş', 'ö', 'Ö', 'ç', 'Ç'], ['i', 'I', 'g', 'G', 'u', 'U', 's', 'S', 'o', 'O', 'c', 'C'], $str);    
    }

    public static function keepSafeChars($str)
    {
        $safeChars = "\n abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789-_";
        $str = preg_replace("/[^$safeChars]/", '', $str);
        return str_replace("  ", " ", $str);
    }

    public static function getSetCustomCache($filename, $cachePath, $stringToCache = null, $expiration = 86400)
    {
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

    public static function getCustomCache($filename, $cachePath, $expiration = 86400)
    {
        return static::getSetCustomCache($filename, $cachePath, null, $expiration);
    }

    public static function setCustomCache($filename, $cachePath, $stringToCache, $expiration = 86400)
    {
        return static::getSetCustomCache($filename, $cachePath, $stringToCache, $expiration);
    }

    public static function retrieveJsonData($fieldName)
    {
        $db = \Pimcore\Db::get();
        $sql = "SELECT json_data FROM iwa_json_store WHERE field_name=?";
        $result = $db->fetchAssociative($sql, [$fieldName]);
        if ($result) {
            return json_decode($result['json_data'], true);
        }
        return null;
    }

    public static function storeJsonData($objectId, $fieldName, $data)
    {
        $db = \Pimcore\Db::get();
        try {
            $sql = "INSERT INTO iwa_json_store (object_id, field_name, json_data) VALUES (?, ?, ?) 
                ON DUPLICATE KEY UPDATE json_data=?";
            $db->query($sql, [$objectId, $fieldName, json_encode($data), json_encode($data)]);
        } catch (\Exception $e) {
            echo $e->getMessage();
        }
    }

    public static function checkJwtTokenValidity($token) 
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

    public static function getCachedImage($url)
    {
        if (empty($url) || !filter_var($url, FILTER_VALIDATE_URL)) {
            return null;
        }
        if (strpos($url, 'iwa.web.tr') !== false) {
            return new \Pimcore\Model\DataObject\Data\ExternalImage($url);
        }
        $imageAsset = Utility::findImageByName(CacheImagesCommand::createUniqueFileNameFromUrl($url));
        if ($imageAsset) {
            return new \Pimcore\Model\DataObject\Data\ExternalImage(
                "https://mesa.iwa.web.tr/var/assets/".str_replace(" ", "%20", $imageAsset->getFullPath())
            );
        }
        return new \Pimcore\Model\DataObject\Data\ExternalImage($url);
    }

}