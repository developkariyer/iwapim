<?php

namespace App\Utils;

use chillerlan\QRCode\{QRCode, QROptions};
use chillerlan\QRCode\Common\EccLevel;
use chillerlan\QRCode\Data\QRMatrix;
use setasign\Fpdi\Fpdi;
use App\Utils\Utility;
use App\Utils\QRImageWithLogo;
use Pimcore\Model\DataObject\Product;

use Pimcore\Model\Asset;

class PdfGenerator
{

    private static function generateQR(string $qrcode, string $qrlink)
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
        $qrImagePath = \PIMCORE_PROJECT_ROOT . "/tmp/qrcode/$qrcode.png";
        $logoPath = \PIMCORE_PROJECT_ROOT . '/public/custom/iwapim.png';
        $qrOutputInterface->dump($qrImagePath, $logoPath);
        return $qrImagePath;
    }

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

        $pdfFilePath = \PIMCORE_PROJECT_ROOT . "/tmp/$qrfile";
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

    public static function generate4x6iwasku(string $qrcode, string $qrlink, Product $product, $qrfile): Asset\Document
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
        $pdfFilePath = \PIMCORE_PROJECT_ROOT . "/tmp/$qrfile";
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

    public static function generate4x6eu(string $qrcode, string $qrlink, Product $product, $qrfile): Asset\Document
    {
        $pdf = new Fpdi('L', 'mm', [60, 40]); 
        $pdf->SetAutoPageBreak(false); 
        $pdf->AddPage();
        $pdf->SetMargins(0, 0, 0);
        $pdf->SetFont('helvetica', '', 6);
    
        $pdf->Image(\PIMCORE_PROJECT_ROOT . '/public/custom/factory.png', 2, 2, 8, 8);
        $pdf->Image(\PIMCORE_PROJECT_ROOT . '/public/custom/eurp.png', 2, 11, 8, 4);
        $pdf->Image(\PIMCORE_PROJECT_ROOT . '/public/custom/icons.png', 1, 28, 48, 12);
        $pdf->Image(\PIMCORE_PROJECT_ROOT . '/public/custom/iwapim.png', 40, 2, 18, 18);
    
        $pdf->SetXY(10, 1.7);
        $pdf->MultiCell(32, 3, mb_convert_encoding("IWA Concept Ltd.Sti.\nAnkara/Türkiye\niwaconcept.com", 'windows-1254', 'UTF-8'), 0, 'L');
    
        $pdf->SetXY(10, 11.6);
        $pdf->Cell(15, 3, mb_convert_encoding("Emre Bedel", 'windows-1254', 'UTF-8'), 0, 0, 'L');
        $pdf->SetXY(1, 14.5);
        $pdf->Cell(25, 3, mb_convert_encoding("responsible@iwaconcept.com", 'windows-1254', 'UTF-8'), 0, 0, 'L');

        $pdf->SetXY(1, 18);
        $pdf->MultiCell(30, 2, mb_convert_encoding("PN: {$product->getInheritedField("iwasku")}\nSN: {$product->getInheritedField("productIdentifier")}", 'windows-1254', 'UTF-8'), 0, 'L');
    
        $text =  $product->getInheritedField("nameEnglish") . " (";
        $text .= $product->getInheritedField("variationSize"). "-" . $product->getInheritedField("variationColor") . ")";

        $pdf->SetXY(1, 23);
        $pdf->MultiCell(56, 2, mb_convert_encoding(Utility::keepSafeChars(Utility::removeTRChars($text)), 'windows-1254', 'UTF-8'), 0, 'L');

        $pdf->SetFont('arial', 'B', 6);
        $pdf->SetXY(48, 28);
        $pdf->MultiCell(12, 3, mb_convert_encoding("Complies\nwith\nGPSD\nGPSR", 'windows-1254', 'UTF-8'), 0, 'C');

        $pdfFilePath = \PIMCORE_PROJECT_ROOT . "/tmp/$qrfile";
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