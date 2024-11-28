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
            $response = $response->json();
            $orders = array_merge($orders, $response['payload']['Orders'] ?? [] );
            $nextToken = $response['payload']['NextToken'] ?? null;
            echo ".";
            usleep(100000);
        } while ($nextToken);
        echo "Total Orders: " . count($orders) . "\n";
        file_put_contents(PIMCORE_PROJECT_ROOT . "/tmp/amazon-orders.json", json_encode($orders));
    }

}