<?php

namespace App\Model\DataObject;

use Pimcore\Model\DataObject\Concrete;
use Pimcore\Model\DataObject\Product\Listing;

class Product extends Concrete
{

    const MAIN_PRODUCT = 'mainProduct';
    const COLOR_VARIANT = 'colorVariant';
    const SIZE_VARIANT = 'sizeVariant';

    public function getLevel()
    {
        error_log('getLevel called for '.$this->key.' with parent object '.$this->getParent()->key);
        $parent = $this->getParent();
        if (!$parent instanceof Product) {
            return self::MAIN_PRODUCT;
        }
        $parent = $parent->getParent();
        if (!$parent instanceof Product) {
            return self::COLOR_VARIANT;
        }
        return self::SIZE_VARIANT;
    }

    public function checkProductCode()
    {
        if (!empty($this->getProductCode)) {
            return;
        }
        switch ($this->getLevel()) {
            case self::SIZE_VARIANT:
            case self::COLOR_VARIANT:
                $numberDigits = 2;
                break;
            case self::MAIN_PRODUCT:
                $numberDigits = 5;
        }
        $productCode = $this->generateUniqueCode($numberDigits);
        $this->setProductCode($productCode);
    }

    private function addVariant($key, $size, $color, $published): Product
    {
        error_log("Add variant called with following parameter: $key, $size, $color, $published. Parent object is ".$this->getKey());
        $variation = new \Pimcore\Model\DataObject\Product();
        $variation->setParent($this);
        $variation->setKey($key);
        $variation->checkProductCode();
        if (!empty($size)) {
            $variation->setVariationSize($size);
        }
        if (!empty($color)) {
            $variation->setVariationColor($color);
        }
        $variation->setPublished($published);
        $variation->setType(\Pimcore\Model\DataObject\AbstractObject::OBJECT_TYPE_VARIANT); 
        $variation->save();
        return $variation;
    }

    public function addColor($color): Product
    {
        return $this->addVariant($color, '', $color, false);
    }

    public function addSize($size): Product
    {
        return $this->addVariant($size, $size, '', true);
    }

    private function generateUniqueCode($numberDigits=5)
    {
        //look for an identical key name first
        $code = $this->findSimilarKey();
        if ($code) {
            return $code;
        }
        while (true) {
            $candidateCode = self::generateCustomString($numberDigits);
            if (!$this->findByField('productCode', $candidateCode)) {
                return $candidateCode;
            }
        }
    }

    private function findByField($field, $value)
    {
        $list = new Listing();
        $list->setCondition("`$field` = ?", [$value]);
        $list->setUnpublished(true);
        $list->setLimit(1);
        return $list->current();
    }

    private function findSimilarKey()
    {
        $current = $this->findByField('key', $this->key);
        return $current ? $current->getProductCode() : null;   
    }

    private static function generateCustomString($length = 6) {
        $characters = 'ABCDEFGHJKMNPQRSTVWXYZ123456789';
        $charactersLength = strlen($characters);
        $randomString = '';

        for ($i = 0; $i < $length; $i++) {
            $randomIndex = mt_rand(0, $charactersLength - 1);
            $randomString .= $characters[$randomIndex];
        }
        return $randomString;
    }

}
