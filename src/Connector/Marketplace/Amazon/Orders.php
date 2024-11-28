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
        do {
            $response = $ordersApi->getOrders($marketplaceIds, $nextToken);
            file_put_contents(PIMCORE_PROJECT_ROOT."/tmp/amazon_order_test.txt", print_r($response, true));
            return;
//            $nextToken = $response->getNextToken();
//            $orders = array_merge($orders, $response->getOrders());
        } while ($nextToken);
    }

}