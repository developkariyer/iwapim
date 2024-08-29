<?php 

namespace App\Calculator;

use Pimcore\Model\DataObject\Concrete;
use Pimcore\Model\DataObject\ClassDefinition\CalculatorClassInterface;
use Pimcore\Model\DataObject\Data\CalculatedValue;
use Pimcore\Model\DataObject\Product;
use Pimcore\Model\DataObject\Serial;
use App\Utils\Utility;


class LinkGenerator implements CalculatorClassInterface
{
    public function compute(Concrete $object, CalculatedValue $context): string
    {
        if ($object instanceof Serial) {
            $product = $object->getProduct();
            if ($product instanceof Product) {
                if (empty($object->getSerialNumber())) {
                    $object->setSerialNumber($object->generateUniqueSerialNumber());    
                    $object->save();
                }
                ///$qrcode = $product->getProductCode().Utility::customBase64Encode($object->getSerialNumber());
                return "https://iwa.web.tr/p/".Utility::customBase64Encode($object->getSerialNumber());
            }
        }
        return "";
    }

    public function getCalculatedValueForEditMode(Concrete $object, CalculatedValue $context): string
    {
        return $this->compute($object, $context);
    }

}