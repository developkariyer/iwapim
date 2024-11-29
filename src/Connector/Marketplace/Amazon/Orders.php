<?php

namespace App\Connector\Marketplace\Amazon;

use App\Connector\Marketplace\Amazon\Constants as AmazonConstants;
use App\Connector\Marketplace\Amazon\Connector as AmazonConnector;
use App\Utils\Utility;

class Orders
{
    public $amazonConnector;

    public $db;
    public $orders = [];

    public function __construct(AmazonConnector $amazonConnector) 
    {
        $this->amazonConnector = $amazonConnector;
        $this->db = \Pimcore\Db::get();
    }

    public function getLastUpdateTime()
    {
        $lastUpdateTime = $this->db->fetchOne(
            "SELECT CONCAT(
                DATE_FORMAT(
                    MAX(STR_TO_DATE(JSON_UNQUOTE(JSON_EXTRACT(json, '$.LastUpdateDate')), '%Y-%m-%dT%H:%i:%sZ')),
                    '%Y-%m-%dT%H:%i:%s'
                ),
                'Z'
            ) AS lastUpdatedAt
            FROM iwa_marketplace_orders
            WHERE marketplace_id = ?",
            [$this->amazonConnector->getMarketplace()->getId()]
        );
        return $lastUpdateTime ?? gmdate('Y-m-d\TH:i:s\Z', strtotime('-1 day'));
    }

    public function getOrders()
    {
        $ordersApi = $this->amazonConnector->amazonSellerConnector->ordersV0();
        $marketplaceIds = [AmazonConstants::amazonMerchant[$this->amazonConnector->mainCountry]['id']];
        $nextToken = null;
        $orders = [];
        $lastUpdatedAfter = $this->getLastUpdateTime();
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
        $this->orders = $orders;
    }

    public function getOrderItems()
    {

    }

    public function saveOrders()
    {
        $this->db->beginTransaction();
        try {
            foreach ($this->orders as $order) {
                $this->db->executeStatement(
                    "INSERT INTO iwa_marketplace_orders (marketplace_id, order_id, json) VALUES (?, ?, ?) ON DUPLICATE KEY UPDATE json = ?",
                    [
                        $this->amazonConnector->getMarketplace()->getId(),
                        $order['AmazonOrderId'],
                        json_encode($order),
                        json_encode($order)
                    ]
                );
            }
            $this->db->commit();
        } catch (\Exception $e) {
            $this->db->rollBack();
            throw $e;
        }
    }

    public function downloadOrders()
    {
        $this->getOrders();
        $this->getOrderItems();
        $this->saveOrders();
    }

}