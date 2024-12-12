<?php

namespace App\Connector\Marketplace;

use Pimcore\Model\DataObject\Marketplace;
use Pimcore\Model\DataObject\VariantProduct;

interface MarketplaceConnectorInterface
{
    public function __construct(Marketplace $marketplace);

    public function download($forceDownload = false);

    public function downloadOrders();
    
    public function downloadInventory();

    public function import($updateFlag, $importFlag);

    public function setInventory(VariantProduct $listing, int $targetValue, $sku = null, $country = null);

    public function setPrice(VariantProduct $listing,string $targetPrice, $targetCurrency = null, $sku = null, $country = null);

}
