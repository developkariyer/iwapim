<?php

namespace App\Connector\Marketplace\Amazon;

use App\Connector\Marketplace\Amazon\Constants as AmazonConstants;
use App\Connector\Marketplace\Amazon\Connector as AmazonConnector;
use App\Utils\Utility;

class Orders
{
    public $amazonConnector;

    public function __construct(AmazonConnector $amazonConnector) 
    {
        $this->amazonConnector = $amazonConnector;
    }

    public function downloadOrders()
    {
        //$db = \Pimcore\Db::get();
        $ordersApi = $this->amazonConnector->amazonSellerConnector->ordersV0();
        $marketplaceIds = [AmazonConstants::amazonMerchant[$this->amazonConnector->mainCountry]['id']];
        $nextToken = null;
        $orders = [];
        $lastUpdatedAfter = gmdate('Y-m-d\TH:i:s\Z', strtotime('-1 week'));
        echo "lastUpdatedAfter: $lastUpdatedAfter\n";
        do {
            $response = $nextToken ? $ordersApi->getOrders(marketplaceIds: $marketplaceIds, nextToken: $nextToken) : $ordersApi->getOrders(marketplaceIds: $marketplaceIds, lastUpdatedAfter: $lastUpdatedAfter);
            echo array_keys($response->json()) . "\n"; return;
            $orders = array_merge($orders, $response->json()['orders'] ?? [] );
            $nextToken = $response->json()['nextToken'] ?? null;
            if ($nextToken) {
                echo "Next Token: $nextToken\n";
            } else {
                echo "No Next Token\n";
            }
        } while ($nextToken);
        echo "Total Orders: " . count($orders) . "\n";
        file_put_contents(PIMCORE_PROJECT_ROOT . "/tmp/amazon-orders.json", json_encode($orders));
    }

}