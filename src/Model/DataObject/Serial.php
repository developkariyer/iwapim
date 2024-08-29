<?php

namespace App\Model\DataObject;

use Pimcore\Model\DataObject\Concrete;
use Pimcore\Model\DataObject\Serial\Listing;
use Pimcore\Model\Asset;
use Pimcore\Model\DataObject\Product;
use Pimcore\Model\Asset\Listing as AssetListing;
use chillerlan\QRCode\{QRCode, QROptions};
use chillerlan\QRCode\Common\EccLevel;
use chillerlan\QRCode\Data\QRMatrix;
use chillerlan\QRCode\Output\QRGdImagePNG;
use setasign\Fpdi\Fpdi;
use setasign\Fpdf\Fpdf;
use App\Utils\Utility;

class QRImageWithLogo extends QRGdImagePNG 
{
    public function dump(string|null $file = null, string|null $logo = null): string {
        $logo ??= '';
        $this->options->returnResource = true;
        if (!is_file($logo) || !is_readable($logo)) {
            throw new QRCodeOutputException('Invalid logo');
        }
        parent::dump($file);
        $im = imagecreatefrompng($logo);
        if ($im === false) {
            throw new QRCodeOutputException('imagecreatefrompng() error');
        }
        $w = imagesx($im);
        $h = imagesy($im);
        $lw = ($this->options->logoSpaceWidth - 2) * $this->options->scale;
        $lh = ($this->options->logoSpaceHeight - 2) * $this->options->scale;
        $ql = $this->matrix->getSize() * $this->options->scale;
        imagecopyresampled($this->image, $im, ($ql - $lw) / 2, ($ql - $lh) / 2, 0, 0, $lw, $lh, $w, $h);
        $imageData = $this->dumpImage();
        $this->saveToFile($imageData, $file);
        if ($this->options->outputBase64) {
            $imageData = $this->toBase64DataURI($imageData);
        }
        return $imageData;
    }
}

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
                $label = self::generatePdf($qrcode, $qrlink, $product, $qrfile);
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

    protected static function generatePdf(string $qrcode, string $qrlink, Product $product, $qrfile): Asset\Document
    {
        $options = new QROptions;
        $options->version = 5;
        $options->outputBase64 = false;
        $options->scale = 6;
        $options->imageTransparent = false;
        $options->drawCircularModules = true;
        $options->circleRadius = 0.45;
        $options->keepAsSquare = [
            QRMatrix::M_FINDER,
            QRMatrix::M_FINDER_DOT,
        ];
        $options->eccLevel = EccLevel::H;
        $options->addLogoSpace = true;
        $options->logoSpaceWidth = 13;
        $options->logoSpaceHeight = 13;

        $qrCode = new QRCode($options);
        $qrCode->addByteSegment($qrlink);
        $qrOutputInterface = new QRImageWithLogo($options, $qrCode->getQRMatrix());
        $qrImagePath = \PIMCORE_PROJECT_ROOT . "/tmp/$qrcode.png";
        $logoPath = \PIMCORE_PROJECT_ROOT . '/public/custom/iwapim.png';
        $qrOutputInterface->dump($qrImagePath, $logoPath);


        $pdf = new Fpdi('L', 'mm', [50, 25]);
        $pdf->SetAutoPageBreak(false); // Disable automatic page break
        $pdf->AddPage();
        $pdf->SetMargins(0, 0, 0);
        $pdf->Image($qrImagePath, 0, 0, 20, 20);    
        $pdf->SetFont('Arial', '', 8);
        $pdf->SetXY(0, 18);
        $pdf->Cell(20, 7, "s/n: $qrcode", 0, 0, 'C');

        $pdf->SetFont('Arial', 'B', 8);
        $pdf->SetXY(20, 1);
        $text = $product->getInheritedField("variationSize");
        $text .= "\n".$product->getInheritedField("variationColor");
        $text .= "\n".$product->getInheritedField("productIdentifier")." ".$product->getInheritedField("name");
        $pdf->MultiCell(30, 4, Utility::keepSafeChars(Utility::removeTRChars($text)), 0, 'C');
        $pdf->Line(20, 1, 20, 24);

        $pdfFilePath = \PIMCORE_PROJECT_ROOT . "/tmp/$qrfile";
        $pdf->Output($pdfFilePath, 'F');

        unlink($qrImagePath); // Clean up the temporary QR code image

        // Save PDF as Pimcore Asset
        $asset = new Asset\Document();
        $asset->setFilename($qrfile);
        $asset->setData(file_get_contents($pdfFilePath));
        $asset->setParent(Asset::getByPath('/Etiketler/Ürün')); // Ensure this folder exists in Pimcore
        $asset->save();

        unlink($pdfFilePath); // Clean up the temporary PDF file

        return $asset;
    }




}