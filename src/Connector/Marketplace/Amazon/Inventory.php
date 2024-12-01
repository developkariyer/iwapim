<?php

namespace App\Connector\Marketplace\Amazon;

use App\Connector\Marketplace\Amazon\Constants as AmazonConstants;
use App\Connector\Marketplace\Amazon\Connector as AmazonConnector;
use App\Utils\Utility;

class Inventory
{
    public $amazonConnector;
    public $rateLimit = 0;
    public $inventory = [];

    public function __construct(AmazonConnector $amazonConnector) 
    {
        $this->amazonConnector = $amazonConnector;
    }

    public function downloadInventory()
    {
        $this->getInventory();
        $this->processInventory();
    }

    public function processInventory()
    {
        echo "Processing Inventory";
        $db = \Pimcore\Db::get();
        $sql = "INSERT INTO iwa_inventory (inventory_type, warehouse, asin, fnsku, item_condition, json_data, total_quantity) ".
                "VALUES (?, ?, ?, ?, ?, ?, ?) ON DUPLICATE KEY UPDATE item_condition = ?, total_quantity = ?, json_data = ?";
        $inventoryType = 'AMAZON_FBA';
        foreach ($this->inventory as $country => $inventory) {
            $db->beginTransaction();
            try {
                $warehouse = $country;
                foreach ($inventory as $item) {
                    $asin = $item['asin'];
                    $fnsku = $item['fnSku'];
                    $itemCondition = $item['condition'];
                    $totalQuantity = $item['totalQuantity'];
                    $jsonData = json_encode($item);
                    $db->executeStatement($sql, [
                        $inventoryType, $warehouse, $asin, $fnsku, $itemCondition, $jsonData, $totalQuantity,
                        $itemCondition, $totalQuantity, $jsonData
                    ]);
                }
                $db->commit();
            } catch (\Exception $e) {
                $db->rollBack();
                echo $e->getMessage();
            }
        }
        echo "\n";
    }

    public function getInventory()
    {
        $inventoryApi = $this->amazonConnector->amazonSellerConnector->fbaInventoryV1();
        foreach ($this->amazonConnector->getMarketplace()->getFbaRegions() ?? [] as $country) {
            $summary = Utility::getCustomCache("{$country}_inventory.json", PIMCORE_PROJECT_ROOT . "/tmp/marketplaces/AmazonInventory");
            if ($summary) {
                $this->inventory[$country] = json_decode($summary, true);
                continue;
            }
            $nextToken = null;
            $summary = [];
            do {
                try {
                    $response = $inventoryApi->getInventorySummaries(
                        granularityType: 'Marketplace', 
                        granularityId: AmazonConstants::amazonMerchant[$country]['id'],
                        details: true,
                        nextToken: $nextToken,
                        marketplaceIds: [AmazonConstants::amazonMerchant[$country]['id']],
                    );
                    $responseJson = $response->json();
                    $summary = array_merge($summary, $responseJson['payload']['inventorySummaries'] ?? []);
                    $nextToken = $responseJson['pagination']['nextToken'] ?? null;
                    echo "+";
                } catch (\Exception $e) {
                    $this->rateLimit++;
                    echo "-{$this->rateLimit}";
                }
                sleep($this->rateLimit);
            } while ($nextToken);
            Utility::setCustomCache(
                filename: "{$country}_inventory.json",
                cachePath: PIMCORE_PROJECT_ROOT . "/tmp/marketplaces/AmazonInventory", 
                stringToCache: json_encode($summary, JSON_PRETTY_PRINT)
            );
            $this->inventory[$country] = $summary;
        }
    }
}