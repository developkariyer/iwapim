<?php

namespace App\Connector\Marketplace\Amazon;

use App\Connector\Marketplace\Amazon\Constants as AmazonConstants;
use App\Connector\Marketplace\Amazon\Connector as AmazonConnector;
use App\Utils\Utility;

class Orders
{
    public $amazonConnector;
    public $ordersApi;
    public $marketplaceIds;

    public $db;
    public $orders = [];

    public function __construct(AmazonConnector $amazonConnector) 
    {
        $this->amazonConnector = $amazonConnector;
        $this->db = \Pimcore\Db::get();
        $this->ordersApi = $amazonConnector->amazonSellerConnector->ordersV0();
        $this->marketplaceIds = [AmazonConstants::amazonMerchant[$amazonConnector->mainCountry]['id']];
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
        return $lastUpdateTime ?? gmdate('Y-m-d\TH:i:s\Z', strtotime('-1 hour'));
    }

    public function getOrders()
    {
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
                    ? $this->ordersApi->getOrders(marketplaceIds: $this->marketplaceIds, nextToken: $nextToken) 
                    : $this->ordersApi->getOrders(marketplaceIds: $this->marketplaceIds, lastUpdatedAfter: $lastUpdatedAfter);
                $responseJson = $response->json();
                $orders = array_merge($orders, $responseJson['payload']['Orders'] ?? []);
                $nextToken = $responseJson['payload']['NextToken'] ?? null;        
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

    public function getOrderItems($amazonOrderId)
    {
        $nextToken = null;
        $orderItems = [];
        $rateLimitSleep = 2;
        do {
            $response = $nextToken 
                ? $this->ordersApi->getOrderItems($amazonOrderId, nextToken: $nextToken) 
                : $this->ordersApi->getOrderItems($amazonOrderId);
            $responseJson = $response->json();
            $orderItems = array_merge($orderItems, $responseJson['payload']['OrderItems'] ?? []);
            $nextToken = $responseJson['payload']['NextToken'] ?? null;        
            echo ".";
            sleep($rateLimitSleep);
        } while ($nextToken);
        return $orderItems;
    }

    public function downloadOrderItems()
    {
        foreach ($this->orders as &$order) {
            $order['OrderItems'] = $this->getOrderItems($order['AmazonOrderId']);
        }
    }

    public function downloadOrders()
    {
        $this->getOrders();
        $this->downloadOrderItems();
        //$this->saveOrders();
        file_put_contents(
            PIMCORE_PROJECT_ROOT.'/tmp/marketplaces/'.urlencode($this->amazonConnector->getMarketplace()->getKey()).'/orders.json', 
            json_encode($this->orders, JSON_PRETTY_PRINT)
        );
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

}