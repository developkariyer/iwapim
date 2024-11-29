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
        $lastUpdatedAfter = gmdate('Y-m-d\TH:i:s\Z', strtotime('-1 month'));
        echo "lastUpdatedAfter: $lastUpdatedAfter\n";
        do {
            $response = $nextToken 
                ? $ordersApi->getOrders(marketplaceIds: $marketplaceIds, nextToken: $nextToken) 
                : $ordersApi->getOrders(marketplaceIds: $marketplaceIds, lastUpdatedAfter: $lastUpdatedAfter);

            $dto = $response->dto();

            $orders = array_merge($orders, $dto->payload->orders ?? []);
            $nextToken = $dto->payload->nextToken ?? null;
        
            // Extract rate limit information from headers
            
            print_r($response->headers());exit;

            $remaining = $response->headers()['x-amzn-RateLimit-Remaining'] ?? 0;
            $resetTime = $response->headers()['x-amzn-RateLimit-ResetTime'] ?? null;
        
            echo "Remaining: $remaining, Reset Time: $resetTime\n";

            // Calculate sleep time if rate limit is reached
            if ($remaining == 0 && $resetTime) {
                $resetTimestamp = strtotime($resetTime); // Convert reset time to a timestamp
                $currentTimestamp = time();
                $sleepDuration = $resetTimestamp - $currentTimestamp;
        
                if ($sleepDuration > 0) {
                    echo "Rate limit reached. Sleeping for $sleepDuration seconds...\n";
                    sleep($sleepDuration);
                }
            }
        
            echo "."; // Progress indicator
        } while ($nextToken);
        echo "Total Orders: " . count($orders) . "\n";
        file_put_contents(PIMCORE_PROJECT_ROOT . "/tmp/amazon-orders.json", json_encode($orders));
    }

}