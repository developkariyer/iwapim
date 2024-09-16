<?php

namespace App\Model\DataObject;

use Pimcore\Model\DataObject\Concrete;
use Pimcore\Model\DataObject\Serial\Listing;
use Pimcore\Model\Asset;
use Pimcore\Model\DataObject\Product;
use Pimcore\Model\Asset\Listing as AssetListing;
use App\Utils\Utility;
use App\Utils\PdfGenerator;

class Serial extends Concrete
{

    public function generateUniqueSerialNumber()
    {
        do  {
            $candidateNumber = mt_rand(1, 1000000000);
        } while ($this->findByField('serialNumber', $candidateNumber));
        return $candidateNumber;
    }

    public static function findByField($field, $value)
    {
        $list = new Listing();
        $list->setCondition("`$field` = ?", [$value]);
        $list->setUnpublished(true);
        $list->setLimit(1);
        return $list->current();
    }

    public function checkLabel()
    {
        if (!$this->isPublished()) {
            return;
        }
        $product = $this->getProduct();
        if (!$product instanceof Product) {
            return;
        }
        $label = $this->getLabel();
        if (!$label instanceof Asset\Document) {
            $qrcode = Utility::customBase64Encode($this->getSerialNumber());
            $qrlink = $this->getQrcode();
            $qrfile = "{$product->getProductCode()}_{$this->getSerialNumber()}.pdf";
            $label = self::checkAsset($qrfile);
            if (!$label instanceof Asset) {
                $label = PdfGenerator::generatePdf($qrcode, $qrlink, $product, $qrfile);
            }
            $this->setLabel($label);
        }
    }

    protected static function checkAsset($filename)
    {
        $list = new AssetListing();
        $list->setCondition('filename = ?', [$filename]);
        $list->setLimit(1);
        return $list->current();
    }

}