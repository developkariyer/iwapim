<?php

namespace App\Model\Traits;

use Pimcore\Model\DataObject\Product\Listing;

trait ProductTrait
{
    private function generateCustomString($length = 6) {
        $characters = 'ABCDEFGHJKMNPQRSTVWXYZ123456789';
        $charactersLength = strlen($characters);
        $randomString = '';

        for ($i = 0; $i < $length; $i++) {
            $randomIndex = mt_rand(0, $charactersLength - 1);
            $randomString .= $characters[$randomIndex];
        }

        return $randomString;
    }

    private function generateUniqueCode()
    {
        while (true) {
            $candidateCode = $this->generateCustomString(5);
            if (!$this->isProductCodeExists($candidateCode)) {
                return $candidateCode;
            }
        }
    }

    private function isProductCodeExists($productCode)
    {
        $listing = new Listing();
        $listing->setCondition('productCode = ?', [$productCode]);
        return $listing->count() > 0;
    }
}
