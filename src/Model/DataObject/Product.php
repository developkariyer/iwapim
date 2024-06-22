<?php

namespace App\Model\DataObject;

use Pimcore\Model\DataObject\Concrete;

class Product extends Concrete
{

    const MAIN_PRODUCT = 'mainProduct';
    const COLOR_VARIANT = 'colorVariant';
    const SIZE_VARIANT = 'sizeVariant';

    public function getLevel()
    {
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
        $baseCode = '';
        switch ($this->getLevel()) {
            case self::SIZE_VARIANT:
                $baseCode = $this->getParent()->getParent()->getProductCode() . '_';
            case self::COLOR_VARIANT:
                $baseCode .= $this->getParent()->getProductCode() . '_';
                $numberDigits = 2;
                break;
            case self::MAIN_PRODUCT:
                $numberDigits = 5;
        }
        $productCode = self::generateUniqueCode($baseCode, $numberDigits);
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
        try {
            return $this->addVariant($color, '', $color, false);
        } catch (\Exception $e) {
            throw $e;
        }
    }

    public function addSize($size): Product
    {
        try {
            return $this->addVariant($size, $size, '', true);
        } catch (\Exception $e) {
            throw $e;
        }
    }

    private static function generateUniqueCode($baseCode='', $numberDigits=5)
    {
        while (true) {
            $candidateCode = self::generateCustomString($numberDigits);
            if (!self::getByProductCode($baseCode.$candidateCode, ['limit' => 1,'unpublished' => true])) {
                return $candidateCode;
            }
        }
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
