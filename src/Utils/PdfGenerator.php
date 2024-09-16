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
    public static function generate2x5(string $qrcode, string $qrlink, Product $product, $qrfile): Asset\Document
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

    public static function generate4x6(string $qrcode, string $qrlink, Product $product, $qrfile): Asset\Document
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
        $pdf->SetFont('Arial', '', 11); // Slightly smaller font for product details
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
        $asset->setParent(Asset::getByPath('/Etiketler/Ürün')); // Ensure this folder exists in Pimcore
        $asset->save();
        unlink($pdfFilePath); // Clean up the temporary PDF file
        $product->setSticker4x6($asset);
        $product->save();
    
        return $asset;
    }
    
}