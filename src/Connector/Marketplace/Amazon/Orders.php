<?php

namespace App\Connector\Marketplace\Amazon;

use DateMalformedStringException;
use DateTime;
use DateTimeZone;
use Doctrine\DBAL\Exception;
use SellingPartnerApi\Seller\OrdersV0\Api as SellerOrdersV0;
use Doctrine\DBAL\Connection;
use Pimcore\Db;

use App\Connector\Marketplace\Amazon\Constants as AmazonConstants;

class Orders
{
    public Connector $connector;
    public SellerOrdersV0 $ordersApi;
    public array $marketplaceIds;
    public int $orderItemRateLimit = 0;
    public int $orderItemRateSuccess = 0;
    public int $orderRateLimit = 1;

    public Connection $db;
    public array $orders = [];

    public function __construct(Connector $connector)
    {
        $this->connector = $connector;
        $this->db = Db::get();
        $this->ordersApi = $connector->amazonSellerConnector->ordersV0();
        $this->marketplaceIds = [AmazonConstants::amazonMerchant[$connector->mainCountry]['id']];
        foreach ($connector->countryCodes as $countryCode) {
            $this->marketplaceIds[] = AmazonConstants::amazonMerchant[$countryCode]['id'];
        }
    }

    /**
     * @throws Exception
     */
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
            [$this->connector->getMarketplace()->getId()]
        );
        return $lastUpdateTime ?? gmdate('Y-m-d\TH:i:s\Z', strtotime('-10 years'));
    }

    /**
     * @throws Exception|DateMalformedStringException
     */
    public function getOrders(): void
    {
        $nextToken = null;
        $orders = [];
        $lastUpdatedAfter = $this->getLastUpdateTime();
        $date = new DateTime($lastUpdatedAfter, new DateTimeZone('UTC'));
        $date->modify('+1 day');
        //$lastUpdateBefore = $date->format('Y-m-d\TH:i:s\Z');
        echo "lastUpdatedAfter: $lastUpdatedAfter\n";
        do {
            try {
                $response = $nextToken 
                    ? $this->ordersApi->getOrders(marketplaceIds: $this->marketplaceIds, nextToken: $nextToken) 
                    : $this->ordersApi->getOrders(marketplaceIds: $this->marketplaceIds, lastUpdatedAfter: $lastUpdatedAfter); //, lastUpdatedBefore: $lastUpdateBefore);
                $responseJson = $response->json();
                $orders = array_merge($orders, $responseJson['payload']['Orders'] ?? []);
                $nextToken = $responseJson['payload']['NextToken'] ?? null;        
                echo ($responseJson['payload']['Orders'][0]['LastUpdateDate'] ?? "."). "\n";
            } catch (\Exception $e) {
                echo "Error: " . $e->getMessage() . "\n";
                $this->orderRateLimit = 60;
                echo "Order rate limit set to 60 seconds\n";
            }
            sleep($this->orderRateLimit);
        } while ($nextToken);
        echo "Total Orders: " . count($orders) . "\n";
        $this->orders = $orders;
    }

    public function getOrderItems($amazonOrderId): array
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
                if ($this->orderItemRateSuccess > 1 && $this->orderItemRateLimit > 0) {
                    $this->orderItemRateLimit = $this->orderItemRateLimit>5 ? 5 : $this->orderItemRateLimit-1;
                    echo "{$this->orderItemRateLimit}";
                    $this->orderItemRateSuccess = 0;
                }
            } catch (\Exception) {
                $this->orderItemRateLimit= $this->orderItemRateLimit<5 ? 5 : $this->orderItemRateLimit+1;
                $this->orderItemRateSuccess = 0;
                echo "-{$this->orderItemRateLimit}";
            }
            sleep($this->orderItemRateLimit);
        } while ($nextToken);
        return $orderItems;
    }

    public function downloadOrderItems(): void
    {
        $index = 0;
        foreach ($this->orders as &$order) {
            $index++;
            if ($index%10 == 0) echo "#";
            $order['OrderItems'] = $this->getOrderItems($order['AmazonOrderId']);
        }
    }

    /**
     * @throws Exception|DateMalformedStringException
     */
    public function downloadOrders(): void
    {
        $this->getOrders();
        $this->downloadOrderItems();
        $this->saveOrders();
        file_put_contents(
            PIMCORE_PROJECT_ROOT.'/tmp/marketplaces/'.urlencode($this->connector->getMarketplace()->getKey()).'/orders.json',
            json_encode($this->orders, JSON_PRETTY_PRINT)
        );
    }

    /**
     * @throws Exception
     */
    public function saveOrders(): void
    {
        $this->db->beginTransaction();
        try {
            foreach ($this->orders as $order) {
                $this->db->executeStatement(
                    "INSERT INTO iwa_marketplace_orders (marketplace_id, order_id, json) VALUES (?, ?, ?) ON DUPLICATE KEY UPDATE json = ?",
                    [
                        $this->connector->getMarketplace()->getId(),
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