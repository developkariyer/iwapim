<?php

namespace App\Connector;

use App\Utils\Registry;

class IwabotConnector
{
    public static function downloadReport()
    {
        $db = \Pimcore\Db::get();
        $sql = "INSERT INTO iwa_inventory (inventory_type, warehouse, asin, fnsku, iwasku, json_data, total_quantity, created_updated_at) VALUES ('IWA', 'NJ', ?, ?, ?, ?, ?, NOW()) ON DUPLICATE KEY UPDATE iwasku = ?, json_data = ?, total_quantity = ?, created_updated_at = NOW()";
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
                    if (!$asin) {
                        continue;
                    }
                    $iwasku = Registry::getKey($asin, 'asin-to-iwasku');
                    $rowData['ASIN'] = $asin;
                    $rowData['IWASKU'] = $iwasku;
                    $jsonData = json_encode($rowData);
                    $db->executeStatement($sql, [$asin, $fnsku, $iwasku, $jsonData, $rowData['Total Count'], $iwasku, $jsonData, $rowData['Total Count']]);
                }
            }
            $db->commit();
        } catch (\Exception $e) {
            $db->rollBack();
            throw $e;
        }

    }

}