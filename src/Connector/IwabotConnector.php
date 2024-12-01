<?php

namespace App\Connector;

use Pimcore\Model\DataObject\VariantProduct;
use App\Utils\Utility;
use App\Utils\Registry;

class IwabotConnector
{
    public static function downloadReport()
    {
        $db = \Pimcore\Db::get();
        $sql = "INSERT INTO iwa_inventory (inventory_type, warehouse, asin, fnsku, json_data, total_quantity) VALUES ('IWA', 'NJ', ?, ?, ?, ?) ON DUPLICATE KEY UPDATE json_data = ?, total_quantity = ?";
        $report = file_get_contents('https://iwarden.iwaconcept.com/iwabot/warehouse/report.php?csv=1');
        file_put_contents(PIMCORE_PROJECT_ROOT . "/tmp/iwabot.csv", $report);
        $lines = explode("\n", mb_convert_encoding(trim($report), 'UTF-8', 'UTF-8'));
        $header = str_getcsv(array_shift($lines), ",");
        $db->beginTransaction();
        try {
            foreach ($lines as $line) {
                $data = str_getcsv($line, ",");
                if (count($header) == count($data)) {
                    $rowData = array_combine($header, $data);
                    $fnsku = $rowData['FNSKU'] ?? null;
                    if (!$fnsku) {
                        continue;
                    }
                    $asin = Registry::getKey($fnsku, 'fnsku-to-asin');
                    $rowData['ASIN'] = $asin;
                    $jsonData = json_encode($rowData);
                    $db->executeStatement($sql, [$asin, $fnsku, $jsonData, $rowData['Total Count'], $jsonData, $rowData['Total Count']]);
                }
            }
            $db->commit();
        } catch (\Exception $e) {
            $db->rollBack();
            throw $e;
        }

    }

}