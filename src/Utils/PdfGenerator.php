<?php

namespace App\Utils;

use chillerlan\QRCode\{Data\QRCodeDataException, Output\QRCodeOutputException, QRCode, QROptions};
use chillerlan\QRCode\Common\EccLevel;
use chillerlan\QRCode\Data\QRMatrix;
use ErrorException;
use Picqer\Barcode\BarcodeGeneratorPNG;
use Picqer\Barcode\Exceptions\UnknownTypeException;
use Pimcore\Model\Element\DuplicateFullPathException;
use setasign\Fpdi\Fpdi;
use Pimcore\Model\DataObject\Product;

use Pimcore\Model\Asset;
use const PIMCORE_PROJECT_ROOT;

class PdfGenerator
{

    /**
     * @throws QRCodeDataException | ErrorException | QRCodeOutputException
     */
    private static function generateQR(string $qrcode, string $qrlink): string
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
        $qrImagePath = PIMCORE_PROJECT_ROOT . "/tmp/qrcode/$qrcode.png";
        $logoPath = PIMCORE_PROJECT_ROOT . '/public/custom/iwapim.png';
        $qrOutputInterface->dump($qrImagePath, $logoPath);
        return $qrImagePath;
    }

    /**
     * @throws QRCodeDataException | QRCodeOutputException | DuplicateFullPathException | ErrorException
     */
    public static function generate2x5(string $qrcode, string $qrlink, Product $product, $qrfile): Asset\Document
    {

        $pdf = new Fpdi('L', 'mm', [50, 25]);
        $pdf->SetAutoPageBreak(false); // Disable automatic page break
        $pdf->AddPage();
        $pdf->SetMargins(0, 0, 0);
        $pdf->Image(self::generateQR($qrcode, $qrlink), 0, 0, 20, 20);    
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

        $pdfFilePath = PIMCORE_PROJECT_ROOT . "/tmp/$qrfile";
        $pdf->Output($pdfFilePath, 'F');

        // Save PDF as Pimcore Asset
        $asset = new Asset\Document();
        $asset->setFilename($qrfile);
        $asset->setData(file_get_contents($pdfFilePath));
        $asset->setParent(Utility::checkSetAssetPath('Ürün', Utility::checkSetAssetPath('Etiketler'))); 
        $asset->save();
        unlink($pdfFilePath); // Clean up the temporary PDF file
        return $asset;
    }

    /**
     * @throws DuplicateFullPathException|UnknownTypeException
     */
    public static function generate4x6iwasku(Product $product, $qrfile): Asset\Document
    {
        if (empty($product->getEanGtin())) {
            error_log("EAN not found for product {$product->getIwasku()}, generating without EAN.");
            return self::generate4x6iwaskuWithoutEan($product, $qrfile);
        }
        error_log("EAN found for product {$product->getIwasku()}, generating with EAN.");
        return self::generate4x6iwaskuWithEan($product, $qrfile);
    }

    /**
     * @throws DuplicateFullPathException
     */
    public static function generate4x6iwaskuWithoutEan(Product $product, $qrfile): Asset\Document
    {
        $pdf = new Fpdi('L', 'mm', [60, 40]); // Landscape mode, 60x40 mm page
        $pdf->SetAutoPageBreak(false); // Disable automatic page break
        $pdf->AddPage();
        $pdf->SetMargins(0, 0, 0);
        $pdf->SetFont('Arial', 'B', 13);

        // Set position for the IWASKU text
        $pdf->SetXY(0, 2); // Adjusted Y position for better placement
        $pdf->Cell(60, 10, "IWASKU: {$product->getIwasku()}", 0, 1, 'C'); // 'C' for center alignment, 1 for moving to the next line

        // Set the font and position for the product details (variation size, color, and identifier)
        $pdf->SetFont('Arial', '', 10); // Slightly smaller font for product details
        $pdf->SetXY(2, 12); // Adjusted to place below the IWASKU text

        // Prepare text
        $text = $product->getInheritedField("productIdentifier") . " ". $product->getInheritedField("nameEnglish") . "\n";
        $text .= "(". $product->getInheritedField("name") . ")\n";
        $text .= "Size: " . $product->getInheritedField("variationSize") . "\n";
        $text .= "Color: " . $product->getInheritedField("variationColor");

        // Adjusted width and height for the MultiCell
        $pdf->MultiCell(56, 4, Utility::keepSafeChars(Utility::removeTRChars($text)), 0, 'C'); // Left align, adjusted width for proper wrapping

        // Output PDF to file
        $pdfFilePath = PIMCORE_PROJECT_ROOT . "/tmp/$qrfile";
        $pdf->Output($pdfFilePath, 'F');

        // Save PDF as Pimcore Asset
        $asset = new Asset\Document();
        $asset->setFilename($qrfile);
        $asset->setData(file_get_contents($pdfFilePath));
        $asset->setParent(Utility::checkSetAssetPath('IWASKU', Utility::checkSetAssetPath('Etiketler'))); // Ensure this folder exists in Pimcore
        $asset->save();
        unlink($pdfFilePath); // Clean up the temporary PDF file
        return $asset;
    }

    /**
     * @throws DuplicateFullPathException|UnknownTypeException
     */
    public static function generate4x6iwaskuWithEan(Product $product, $qrfile): Asset\Document
    {
        error_log("Generating 4x6 label with EAN for product {$product->getIwasku()} for ean {$product->getEanGtin()}");
        $ean = $product->getEanGtin();
        $eanBarcode = self::generateEanBarcode($ean);

        $pdf = new Fpdi('L', 'mm', [60, 40]); // Landscape mode, 60x40 mm page
        $pdf->SetAutoPageBreak(false); // Disable automatic page break
        $pdf->AddPage();
        $pdf->SetMargins(0, 0, 0);
        $pdf->SetFont('Arial', 'B', 13);

        // Set position for the IWASKU text

        $pdf->Image($eanBarcode, 32, 2, 26, 8); // EAN barcode image

        // Set the font and position for the product details (variation size, color, and identifier)
        $pdf->SetFont('Arial', '', 11);
        $pdf->SetXY(0, 2);
        $pdf->Cell(28, 4, trim($product->getIwasku()), 0, 1, 'L'); // 'C' for center alignment, 1 for moving to the next line
        $pdf->Cell(28, 4, trim($product->getEanGtin()), 0, 1, 'L'); // 'C' for center alignment, 1 for moving to the next line

        $pdf->setXY(2, 13); // Adjusted to place below the IWASKU text
        $pdf->SetFont('Arial', '', 9);
        $text = $product->getInheritedField("productIdentifier") . " ". $product->getInheritedField("nameEnglish") . "\n";
        $text .= "(". $product->getInheritedField("name") . ")\n";
        $text .= "Size: " . $product->getInheritedField("variationSize") . "\n";
        $text .= "Color: " . $product->getInheritedField("variationColor");

        // Adjusted width and height for the MultiCell
        $pdf->MultiCell(56, 4, Utility::keepSafeChars(Utility::removeTRChars($text)), 0, 'C'); // Left align, adjusted width for proper wrapping

        // Output PDF to file
        $pdfFilePath = PIMCORE_PROJECT_ROOT . "/tmp/$qrfile";
        $pdf->Output($pdfFilePath, 'F');

        // Save PDF as Pimcore Asset
        $asset = new Asset\Document();
        $asset->setFilename($qrfile);
        $asset->setData(file_get_contents($pdfFilePath));
        $asset->setParent(Utility::checkSetAssetPath('IWASKU', Utility::checkSetAssetPath('Etiketler'))); // Ensure this folder exists in Pimcore
        $asset->save();
        //unlink($pdfFilePath); // Clean up the temporary PDF file
        return $asset;
    }

    /**
     * Generates a high-resolution EAN-13 barcode PNG.
     * If the image’s height is more than 1/3 of its width, trims its height.
     *
     * @throws UnknownTypeException
     */
    public static function generateEanBarcode($ean): string
    {
        $generator = new BarcodeGeneratorPNG();
        $barcodeData = $generator->getBarcode($ean, $generator::TYPE_EAN_13, 2, 300);
        $rawPath = PIMCORE_PROJECT_ROOT . "/tmp/{$ean}_raw.png";
        file_put_contents($rawPath, $barcodeData);
        $img = imagecreatefrompng($rawPath);
        $w = imagesx($img);
        $h = imagesy($img);
        $targetHeight = intval($w / 3);
        if ($h > $targetHeight) {
            $cropped = imagecreatetruecolor($w, $targetHeight);
            imagealphablending($cropped, false);
            imagesavealpha($cropped, true);
            imagecopy($cropped, $img, 0, 0, 0, 0, $w, $targetHeight);
            $finalPath = PIMCORE_PROJECT_ROOT . "/tmp/{$ean}.png";
            imagepng($cropped, $finalPath);
            imagedestroy($cropped);
        } else {
            $finalPath = PIMCORE_PROJECT_ROOT . "/tmp/{$ean}.png";
            copy($rawPath, $finalPath);
        }
        imagedestroy($img);
        unlink($rawPath);
        return $finalPath;
    }

    public static function generateBarcode($value): string
    {
        $generator = new BarcodeGeneratorPNG();
        $barcodeData = $generator->getBarcode($value, $generator::TYPE_CODE_128, 2, 300);
        $rawPath = PIMCORE_PROJECT_ROOT . "/tmp/{$value}_raw.png";
        file_put_contents($rawPath, $barcodeData);
        $img = imagecreatefrompng($rawPath);
        $w = imagesx($img);
        $h = imagesy($img);
        $targetHeight = intval($w / 3);
        if ($h > $targetHeight) {
            $cropped = imagecreatetruecolor($w, $targetHeight);
            imagealphablending($cropped, false);
            imagesavealpha($cropped, true);
            imagecopy($cropped, $img, 0, 0, 0, 0, $w, $targetHeight);
            $finalPath = PIMCORE_PROJECT_ROOT . "/tmp/{$value}.png";
            imagepng($cropped, $finalPath);
            imagedestroy($cropped);
        } else {
            $finalPath = PIMCORE_PROJECT_ROOT ."/tmp/{$value}.png";
            copy($rawPath, $finalPath);
        }
        imagedestroy($img);
        unlink($rawPath);
        return $finalPath;
    }

    public static function generate4x6Fnsku(Product $product, $fnsku, $asin, $qrfile): Asset\Document
    {
        $pdf = new Fpdi('L', 'mm', [60, 40]);
        $pdf->SetAutoPageBreak(false);
        $pdf->AddPage();
        $pdf->SetMargins(0, 0, 0);
        $pdf->SetFont('helvetica', '', 6);
        $fnskuBarcode = self::generateBarcode($fnsku);
        $asinBarcode = self::generateBarcode($asin);

        $pdf->Image(PIMCORE_PROJECT_ROOT . '/public/custom/factory.png', 2, 2, 8, 8);
        $pdf->Image(PIMCORE_PROJECT_ROOT . '/public/custom/eurp.png', 2, 11, 8, 4);
        $pdf->Image(PIMCORE_PROJECT_ROOT . '/public/custom/icons.png', 1, 27, 48, 12);
        $pdf->Image($asinBarcode, 33, 2, 26, 8);
        $pdf->Image($fnskuBarcode,33,14,26,8);

        $pdf->SetXY(39, 11);
        $pdf->MultiCell(56, 2, mb_convert_encoding($asin, 'windows-1254', 'UTF-8'), 0, 'L');

        $pdf->SetXY(39, 23);
        $pdf->MultiCell(56, 2, mb_convert_encoding($fnsku, 'windows-1254', 'UTF-8'), 0, 'L');


        $pdf->SetXY(10, 1.7);
        $pdf->MultiCell(32, 3, mb_convert_encoding("IWA Concept Ltd.Sti.\nAnkara/Türkiye\niwaconcept.com", 'windows-1254', 'UTF-8'), 0, 'L');

        $pdf->SetXY(10, 11.6);
        $pdf->Cell(15, 3, mb_convert_encoding("Emre Bedel", 'windows-1254', 'UTF-8'), 0, 0, 'L');
        $pdf->SetXY(1, 14.5);
        $pdf->Cell(25, 3, mb_convert_encoding("responsible@iwaconcept.com", 'windows-1254', 'UTF-8'), 0, 0, 'L');

        $pdf->SetXY(1, 18);
        $pdf->MultiCell(30, 2, mb_convert_encoding("PN: {$product->getInheritedField("iwasku")}\nSN: {$product->getInheritedField("productIdentifier")}", 'windows-1254', 'UTF-8'), 0, 'L');

        $text =  $product->getInheritedField("productIdentifier") ." ";
        $text .= $product->getInheritedField("variationSize"). " " . $product->getInheritedField("variationColor") ;

        $pdf->SetXY(1, 23);
        $pdf->MultiCell(56, 2, mb_convert_encoding(Utility::keepSafeChars(Utility::removeTRChars($text)), 'windows-1254', 'UTF-8'), 0, 'L');

        $text2 = $product->getInheritedField("name");
        $pdf->SetFont('arial', '', 4);
        $pdf->SetXY(1, 25);
        $pdf->MultiCell(56, 2, mb_convert_encoding(Utility::keepSafeChars(Utility::removeTRChars($text2)), 'windows-1254', 'UTF-8'), 0, 'L');


        $pdf->SetFont('arial', 'B', 6);
        $pdf->SetXY(48, 27);
        $pdf->MultiCell(12, 3, mb_convert_encoding("Complies\nwith\nGPSD\nGPSR", 'windows-1254', 'UTF-8'), 0, 'C');

        $pdfFilePath = PIMCORE_PROJECT_ROOT . "/tmp/$qrfile";
        $pdf->Output($pdfFilePath, 'F');

        $asset = new Asset\Document();
        $asset->setFilename($qrfile);
        $asset->setData(file_get_contents($pdfFilePath));
        $asset->setParent(Utility::checkSetAssetPath('FNSKU', Utility::checkSetAssetPath('Etiketler')));
        $asset->save();
        unlink($pdfFilePath);
        return $asset;
    }


    /**
     * @throws DuplicateFullPathException
     */
    public static function generate4x6eu(Product $product, $qrfile): Asset\Document
    {
        $pdf = new Fpdi('L', 'mm', [60, 40]); 
        $pdf->SetAutoPageBreak(false); 
        $pdf->AddPage();
        $pdf->SetMargins(0, 0, 0);
        $pdf->SetFont('helvetica', '', 6);
    
        $pdf->Image(PIMCORE_PROJECT_ROOT . '/public/custom/factory.png', 2, 2, 8, 8);
        $pdf->Image(PIMCORE_PROJECT_ROOT . '/public/custom/eurp.png', 2, 11, 8, 4);
        $pdf->Image(PIMCORE_PROJECT_ROOT . '/public/custom/icons.png', 1, 27, 48, 12);
        $pdf->Image(PIMCORE_PROJECT_ROOT . '/public/custom/iwablack.png', 40, 2, 18, 18);
    
        $pdf->SetXY(10, 1.7);
        $pdf->MultiCell(32, 3, mb_convert_encoding("IWA Concept Ltd.Sti.\nAnkara/Türkiye\niwaconcept.com", 'windows-1254', 'UTF-8'), 0, 'L');
    
        $pdf->SetXY(10, 11.6);
        $pdf->Cell(15, 3, mb_convert_encoding("Emre Bedel", 'windows-1254', 'UTF-8'), 0, 0, 'L');
        $pdf->SetXY(1, 14.5);
        $pdf->Cell(25, 3, mb_convert_encoding("responsible@iwaconcept.com", 'windows-1254', 'UTF-8'), 0, 0, 'L');

        $pdf->SetXY(1, 18);
        $pdf->MultiCell(30, 2, mb_convert_encoding("PN: {$product->getInheritedField("iwasku")}\nSN: {$product->getInheritedField("productIdentifier")}", 'windows-1254', 'UTF-8'), 0, 'L');
    
        $text =  $product->getInheritedField("productIdentifier") ." ";
        $text .= $product->getInheritedField("variationSize"). " " . $product->getInheritedField("variationColor") ;

        $pdf->SetXY(1, 23);
        $pdf->MultiCell(56, 2, mb_convert_encoding(Utility::keepSafeChars(Utility::removeTRChars($text)), 'windows-1254', 'UTF-8'), 0, 'L');

        $pdf->SetFont('arial', 'B', 6);
        $pdf->SetXY(48, 27);
        $pdf->MultiCell(12, 3, mb_convert_encoding("Complies\nwith\nGPSD\nGPSR", 'windows-1254', 'UTF-8'), 0, 'C');

        $pdfFilePath = PIMCORE_PROJECT_ROOT . "/tmp/$qrfile";
        $pdf->Output($pdfFilePath, 'F');
    
        $asset = new Asset\Document();
        $asset->setFilename($qrfile);
        $asset->setData(file_get_contents($pdfFilePath));
        $asset->setParent(Utility::checkSetAssetPath('EU', Utility::checkSetAssetPath('Etiketler'))); // Ensure this folder exists in Pimcore
        $asset->save();
        unlink($pdfFilePath);
        return $asset;
    }
    
}