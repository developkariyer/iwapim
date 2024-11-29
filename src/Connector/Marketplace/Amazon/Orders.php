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
        $rateLimitSleep = 60;
        $burstLimit = 20; 
        $callCounter = 0; 
        $burstWindowStart = microtime(true);
        do {
            $response = $nextToken 
                ? $ordersApi->getOrders(marketplaceIds: $marketplaceIds, nextToken: $nextToken) 
                : $ordersApi->getOrders(marketplaceIds: $marketplaceIds, lastUpdatedAfter: $lastUpdatedAfter);
        
            $dto = $response->dto();
            $orders = array_merge($orders, $dto->payload->orders ?? []);
            $nextToken = $dto->payload->nextToken ?? null;
        
            echo ".";
        
            $callCounter++;
        
            if ($callCounter >= $burstLimit) {
                $elapsedTime = microtime(true) - $burstWindowStart;
                if ($elapsedTime < $rateLimitSleep) {
                    $sleepDuration = $rateLimitSleep - $elapsedTime;
                    echo "Burst limit reached. Sleeping for $sleepDuration seconds...\n";
                    sleep($sleepDuration);
                }
                $callCounter = 0;
                $burstWindowStart = microtime(true);
            } else {
                usleep($rateLimitSleep * 1e6);
            }
        } while ($nextToken);
        echo "Total Orders: " . count($orders) . "\n";
        file_put_contents(PIMCORE_PROJECT_ROOT . "/tmp/amazon-orders.json", json_encode($orders));
    }

}