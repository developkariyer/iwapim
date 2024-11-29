<?php

namespace App\Connector\Marketplace\Amazon;

use App\Connector\Marketplace\Amazon\Constants as AmazonConstants;
use App\Connector\Marketplace\Amazon\Connector as AmazonConnector;
use App\Utils\Utility;

class Orders
{
    public $amazonConnector;

    public $db;

    public function __construct(AmazonConnector $amazonConnector) 
    {
        $this->amazonConnector = $amazonConnector;
        $this->db = \Pimcore\Db::get();
    }

    public function getLastUpdateTime()
    {
        $query = "SELECT MAX(last_updated) as last_updated FROM amazon_orders";
        $stmt = $this->db->query($query);
        $result = $stmt->fetch();
        return $result['last_updated'];
    }

    public function downloadOrders()
    {
        $ordersApi = $this->amazonConnector->amazonSellerConnector->ordersV0();
        $marketplaceIds = [AmazonConstants::amazonMerchant[$this->amazonConnector->mainCountry]['id']];
        $nextToken = null;
        $orders = [];
        $lastUpdatedAfter = gmdate('Y-m-d\TH:i:s\Z', strtotime('-10 years'));
        echo "lastUpdatedAfter: $lastUpdatedAfter\n";
        $rateLimitSleep = 60;
        $burstLimit = 20;
        $tokens = $burstLimit;
        $lastRequestTime = microtime(true);
        do {
            $currentTime = microtime(true);
            $tokensToAdd = floor(($currentTime - $lastRequestTime) / $rateLimitSleep);
            $tokens = min($burstLimit, $tokens + $tokensToAdd);
            $lastRequestTime += $tokensToAdd * $rateLimitSleep;
        
            if ($tokens > 0) {
                $response = $nextToken 
                    ? $ordersApi->getOrders(marketplaceIds: $marketplaceIds, nextToken: $nextToken) 
                    : $ordersApi->getOrders(marketplaceIds: $marketplaceIds, lastUpdatedAfter: $lastUpdatedAfter);
                $dto = $response->dto();
                $orders = array_merge($orders, $dto->payload->orders ?? []);
                $nextToken = $dto->payload->nextToken ?? null;
        
                echo ".";
                $tokens--;
            } else {
                $sleepDuration = $rateLimitSleep - ($currentTime - $lastRequestTime);
                sleep(max(0, $sleepDuration));
            }
        } while ($nextToken);
        echo "Total Orders: " . count($orders) . "\n";
        file_put_contents(PIMCORE_PROJECT_ROOT . "/tmp/amazon-orders.json", json_encode($orders));
    }

}