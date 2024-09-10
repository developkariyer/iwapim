<?php

namespace App\Utils;

use Normalizer;
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
        $sanitized = preg_replace('/[^\p{L}\p{N}çÇğĞıİöÖşŞüÜ\-_]/u', '-', $variable);
        $sanitized = preg_replace('/-+/', '-', $sanitized);
        if ($length > 0 && mb_strlen($sanitized) > $length) {
            $half = floor($length / 2);
            $sanitized = mb_substr($sanitized, 0, $half) . mb_substr($sanitized, -($length - $half), null);
        }
    
        $sanitized = trim(trim($sanitized, '-'));
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
}
