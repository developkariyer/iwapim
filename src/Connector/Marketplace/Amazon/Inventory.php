<?php

namespace App\Connector\Marketplace\Amazon;

use App\Connector\Marketplace\Amazon\Constants as AmazonConstants;
use App\Connector\Marketplace\Amazon\Connector as AmazonConnector;
use App\Utils\Utility;

class Inventory
{
    public $amazonConnector;

    public function __construct(AmazonConnector $amazonConnector) 
    {
        $this->amazonConnector = $amazonConnector;
    }

    public function downloadInventory()
    {
        $inventoryApi = $this->amazonConnector->amazonSellerConnector->fbaInventoryV1();
        foreach ($this->getMarketplace()->getFbaRegions() ?? [] as $country) {
            $nextToken = null;
            $summary = [];
            do {
                $response = $nextToken ?
                    $inventoryApi->getInventorySummaries(
                        nextToken: $nextToken
                    ) :
                    $inventoryApi->getInventorySummaries(
                        granularityType: 'Marketplace', 
                        granularityId: $country,
                    );
                $responseJson = $response->json();
                echo json_encode($responseJson, JSON_PRETTY_PRINT);
                $summary = array_merge($summary, $response['payload']['InventorySummaries'] ?? []);
                $nextToken = $response['pagination']['NextToken'] ?? null;
                sleep(3);
            } while ($nextToken);
        }
    }
}