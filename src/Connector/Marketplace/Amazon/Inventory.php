<?php

namespace App\Connector\Marketplace\Amazon;

use App\Connector\Marketplace\Amazon\Constants as AmazonConstants;
use App\Utils\Registry;
use Doctrine\DBAL\Exception;
use Pimcore\Db;

class Inventory
{
    public Connector $connector;
    public int $rateLimit = 0;
    public array $inventory = [];

    public function __construct(Connector $connector)
    {
        $this->connector = $connector;
    }

    /**
     * @throws Exception
     */
    public function downloadInventory(): void
    {
        $this->getInventory();
        $this->processInventory();
    }

    /**
     * @throws Exception
     */
    public function processInventory(): void
    {
        echo "Processing Inventory";
        $db = Db::get();
        $sql = "INSERT INTO iwa_inventory (inventory_type, warehouse, asin, fnsku, iwasku, item_condition, json_data, total_quantity) ".
                "VALUES (?, ?, ?, ?, ?, ?, ?, ?) ON DUPLICATE KEY UPDATE iwasku = ?, item_condition = ?, total_quantity = ?, json_data = ?";
        $inventoryType = 'AMAZON_FBA';
        foreach ($this->inventory as $country => $inventory) {
            $db->beginTransaction();
            try {
                $warehouse = $country;
                foreach ($inventory as $item) {
                    $asin = $item['asin'] ?? null;
                    $fnsku = $item['fnSku'] ?? null;
                    if (empty($asin) || empty($fnsku)) {
                        continue;
                    }
                    $iwasku = Registry::getKey($asin, 'asin-to-iwasku');
                    $itemCondition = $item['condition'] ?? null;
                    $totalQuantity = $item['totalQuantity'] ?? 0;
                    $jsonData = json_encode($item);
                    $db->executeStatement($sql, [
                        $inventoryType, $warehouse, $asin, $fnsku, $iwasku, $itemCondition, $jsonData, $totalQuantity,
                        $iwasku, $itemCondition, $totalQuantity, $jsonData
                    ]);
                    Registry::setKey($fnsku, $asin, 'fnsku-to-asin');
                }
                $db->commit();
            } catch (\Exception $e) {
                $db->rollBack();
                echo $e->getMessage();
            }
        }
        echo "\n";
    }

    public function getInventory(): void
    {
        $inventoryApi = $this->connector->amazonSellerConnector->fbaInventoryV1();
        foreach ($this->connector->getMarketplace()->getFbaRegions() ?? [] as $country) {
            $summary = $this->connector->getFromCache("INVENTORY_{$country}.json");
            if ($summary) {
                $this->inventory[$country] = $summary;
                continue;
            }
            $nextToken = null;
            $summary = [];
            do {
                try {
                    $response = $inventoryApi->getInventorySummaries(
                        granularityType: 'Marketplace',
                        granularityId: AmazonConstants::amazonMerchant[$country]['id'],
                        marketplaceIds: [AmazonConstants::amazonMerchant[$country]['id']],
                        details: true,
                        nextToken: $nextToken,
                    );
                    $responseJson = $response->json();
                    $summary = array_merge($summary, $responseJson['payload']['inventorySummaries'] ?? []);
                    $nextToken = $responseJson['pagination']['nextToken'] ?? null;
                    echo "+";
                } catch (\Exception) {
                    $this->rateLimit++;
                    echo "-{$this->rateLimit}";
                }
                sleep($this->rateLimit);
            } while ($nextToken);
            $this->connector->putToCache("INVENTORY_{$country}.json", $summary);
            $this->inventory[$country] = $summary;
        }
    }
}