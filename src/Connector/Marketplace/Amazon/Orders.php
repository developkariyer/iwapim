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
            $response = $nextToken 
                ? $ordersApi->getOrders(marketplaceIds: $marketplaceIds, nextToken: $nextToken) 
                : $ordersApi->getOrders(marketplaceIds: $marketplaceIds, lastUpdatedAfter: $lastUpdatedAfter);
        
            $responseBody = $response->json();
            $orders = array_merge($orders, $responseBody['payload']['Orders'] ?? []);
            $nextToken = $responseBody['payload']['NextToken'] ?? null;
        
            // Extract rate limit information from headers
            $headers = $response->headers();
            $remaining = $headers['x-amzn-RateLimit-Remaining'] ?? 0;
            $resetTime = $headers['x-amzn-RateLimit-ResetTime'] ?? null;
        
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