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
    public $orderItemRateLimit = 0;
    public $orderItemRateSuccess = 0;
    public $orderRateLimit = 1;

    public $db;
    public $orders = [];

    public function __construct(AmazonConnector $amazonConnector) 
    {
        $this->amazonConnector = $amazonConnector;
        $this->db = \Pimcore\Db::get();
        $this->ordersApi = $amazonConnector->amazonSellerConnector->ordersV0();
        $this->marketplaceIds = [AmazonConstants::amazonMerchant[$amazonConnector->mainCountry]['id']];
        foreach ($amazonConnector->countryCodes as $countryCode) {
            $this->marketplaceIds[] = AmazonConstants::amazonMerchant[$countryCode]['id'];
        }
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
        return $lastUpdateTime ?? gmdate('Y-m-d\TH:i:s\Z', strtotime('-10 years'));
    }

    public function getOrders()
    {
        $nextToken = null;
        $orders = [];
        $lastUpdatedAfter = $this->getLastUpdateTime();
        echo "lastUpdatedAfter: $lastUpdatedAfter\n";
        do {
            try {
                $response = $nextToken 
                    ? $this->ordersApi->getOrders(marketplaceIds: $this->marketplaceIds, nextToken: $nextToken) 
                    : $this->ordersApi->getOrders(marketplaceIds: $this->marketplaceIds, lastUpdatedAfter: $lastUpdatedAfter); //, lastUpdatedBefore: "2022-11-05T00:00:00Z");
                $responseJson = $response->json();
                $orders = array_merge($orders, $responseJson['payload']['Orders'] ?? []);
                $nextToken = $responseJson['payload']['NextToken'] ?? null;        
                echo ($responseJson['payload']['Orders'][0]['LastUpdateDate'] ?? "."). "\n";
            } catch (\Exception $e) {
                $this->orderRateLimit = 60;
                echo "Order rate limit set to 60 seconds\n";
            }
            sleep($this->orderRateLimit);
        } while ($nextToken);
        echo "Total Orders: " . count($orders) . "\n";
        $this->orders = $orders;
    }

    public function getOrderItems($amazonOrderId)
    {
        $nextToken = null;
        $orderItems = [];
        do {
            try {
                $response = $nextToken 
                    ? $this->ordersApi->getOrderItems($amazonOrderId, nextToken: $nextToken) 
                    : $this->ordersApi->getOrderItems($amazonOrderId);
                $responseJson = $response->json();
                $orderItems = array_merge($orderItems, $responseJson['payload']['OrderItems'] ?? []);
                $nextToken = $responseJson['payload']['NextToken'] ?? null;        
                echo ".";
                $this->orderItemRateSuccess++;
                if ($this->orderItemRateSuccess > 10 && $this->orderItemRateLimit > 1) {
                    $this->orderItemRateLimit--;
                    echo "OrderItem rate limit set to {$this->orderItemRateLimit} seconds\n";
                    $this->orderItemRateSuccess = 0;
                }
            } catch (\Exception $e) {
                $this->orderItemRateLimit++;
                echo "OrderItem rate limit set to {$this->orderItemRateLimit} seconds\n";
            }
            sleep($this->orderItemRateLimit);
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
        $this->saveOrders();
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