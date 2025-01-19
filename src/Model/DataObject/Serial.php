<?php

namespace App\Model\DataObject;

use Pimcore\Model\DataObject\Concrete;
use Pimcore\Model\DataObject\Serial\Listing;
use Pimcore\Model\Asset;
use Pimcore\Model\DataObject\Product;
use Pimcore\Model\Asset\Listing as AssetListing;
use App\Utils\Utility;
use App\Utils\PdfGenerator;

/**
 * Class Serial
 *
 * This class manages serial numbers for products,
 * including generating unique serial numbers, checking 
 * for existing labels, and creating labels if they do 
 * not exist.
 * 
 * @package App\Model\DataObject
 */
class Serial extends Concrete
{

    /**
    * Generates a unique serial number.
    *
    * @return int Unique serial number.
    */
    public function generateUniqueSerialNumber(): int
    {
        do  {
            $candidateNumber = mt_rand(1, 1000000000);
        } while ($this->findByField('serialNumber', $candidateNumber));
        return $candidateNumber;
    }

    /**
    * Finds a data object by field and value.
    *
    * @param string $field Field name.
    * @param mixed $value Search value.
    * 
    * @return Concrete|null Found data object or null.
    */
    public static function findByField(string $field, mixed $value): ?Concrete
    {
        $list = new Listing();
        $list->setCondition("`$field` = ?", [$value]);
        $list->setUnpublished(true);
        $list->setLimit(1);
        return $list->current();
    }

    /**
    * Checks and generates a label for the serial number.
    *
    * @return void
    */
    public function checkLabel(): void
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
            if (!$label) {
                $label = PdfGenerator::generate2x5($qrcode, $qrlink, $product, $qrfile);
            }
            $this->setLabel($label);
        }
    }

    /**
    * Checks if an asset exists by filename.
    *
    * @param string $filename The name of the asset file.
    * 
    * @return Asset The found asset 
    */
    protected static function checkAsset(string $filename): Asset
    {
        $list = new AssetListing();
        $list->setCondition('filename = ?', [$filename]);
        $list->setLimit(1);
        return $list->current();
    }

}