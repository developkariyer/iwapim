<?php

namespace App\Utils;

use chillerlan\QRCode\Output\QRGdImagePNG;
use chillerlan\QRCode\{Output\QRCodeOutputException, QRCode, QROptions};
use chillerlan\QRCode\Common\EccLevel;
use chillerlan\QRCode\Data\QRMatrix;


class QRImageWithLogo extends QRGdImagePNG 
{
    public function __construct(QROptions $options, QRMatrix $matrix) {
        parent::__construct($options, $matrix);
    }

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