<?php

namespace App\Connector;

use Pimcore\Model\DataObject\VariantProduct;
use App\Utils\Utility;

class IwabotConnector
{
    public static function downloadReport()
    {
        $report = file_get_contents('https://iwarden.iwaconcept.com/iwabot/warehouse/report.php?csv=1');
        file_put_contents(PIMCORE_PROJECT_ROOT . "/tmp/iwabot.csv", $report);
        $lines = explode("\n", mb_convert_encoding(trim($report), 'UTF-8', 'UTF-8'));
        $header = str_getcsv(array_shift($lines), "\t");
        foreach ($lines as $line) {
            $data = str_getcsv($line, "\t");
            if (count($header) == count($data)) {
                $rowData = array_combine($header, $data);
                $variantProduct = VariantProduct::getByFnsku($rowData['FNSKU'], ['limit' => 1]);
                if ($variantProduct) {
                    echo "Updating {$rowData['FNSKU']} inventory ";
                    $oldStock = $variantProduct->getStock();
                    $newStock = $oldStock;
                    Utility::upsertRow($newStock, ['ABD Depo', $rowData['Count in Raf'], gmdate('Y-m-d')]);
                    Utility::upsertRow($newStock, ['ABD Gemi', $rowData['Count in Ship'], gmdate('Y-m-d')]);
                    if ($oldStock !== $newStock) {
                        $variantProduct->setStock($newStock);
                        $variantProduct->save();
                        echo "Saved";
                    }
                    echo "\n";
                }
            }
        }

    }

}