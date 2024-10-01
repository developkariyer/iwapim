<?php

namespace App\MarketplaceConnector;

use Pimcore\Model\DataObject\Marketplace;

interface MarketplaceConnectorInterface
{
    public function __construct(Marketplace $marketplace);

    public function download($forceDownload = false);

    public function downloadOrders();
    
    public function downloadInventory();

    public function import($updateFlag, $importFlag);

}
