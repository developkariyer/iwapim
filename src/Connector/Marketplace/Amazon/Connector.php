<?php

namespace App\Connector\Marketplace\Amazon;

use SellingPartnerApi\SellingPartnerApi;
use SellingPartnerApi\Enums\Endpoint;

use Pimcore\Model\DataObject\Marketplace;

use App\Connector\Marketplace\MarketplaceConnectorAbstract;
use App\Connector\Marketplace\Amazon\Constants as AmazonConstants;
use App\Connector\Marketplace\Amazon\Reports as ReportsHelper;
use App\Connector\Marketplace\Amazon\Listings as ListingsHelper;
use App\Connector\Marketplace\Amazon\Import as ImportHelper;
use App\Connector\Marketplace\Amazon\Orders as OrdersHelper;
use App\Connector\Marketplace\Amazon\Utils as UtilsHelper;
use App\Connector\Marketplace\Amazon\Inventory as InventoryHelper;
use App\Utils\Utility;
use App\Utils\Registry;

class Connector extends MarketplaceConnectorAbstract
{
    public static $marketplaceType = 'Amazon';

    public $reportsHelper;
    public $listingsHelper;
    public $importHelper;
    public $ordersHelper;
    public $utilsHelper;
    public $inventoryHelper;

    public $amazonSellerConnector = null;
    public $countryCodes = [];
    public $mainCountry = null;

    public function __construct(Marketplace $marketplace) 
    {
        parent::__construct($marketplace);
        $this->countryCodes = $marketplace->getMerchantIds() ?? [];
        if (!AmazonConstants::checkCountryCodes($this->countryCodes)) {
            throw new \Exception("Country codes are not valid");
        }
        $this->mainCountry = $marketplace->getMainMerchant();
        $this->amazonSellerConnector = $this->initSellerConnector($marketplace);
        if (!$this->amazonSellerConnector) {
            throw new \Exception("Amazon Seller Connector is not created");
        }
        $this->reportsHelper = new ReportsHelper($this);
        $this->listingsHelper = new ListingsHelper($this);
        $this->importHelper = new ImportHelper($this);
        $this->ordersHelper = new OrdersHelper($this);
        $this->utilsHelper = new UtilsHelper($this);
        $this->inventoryHelper = new InventoryHelper($this);
    }

    private function initSellerConnector($marketplace)
    {
        $endpoint = match ($marketplace->getMainMerchant()) {
            "CA", "US", "MX", "BR" => Endpoint::NA,
            "SG", "AU", "JP", "IN" => Endpoint::FE,
            "UK", "FR", "DE", "IT", "ES", "NL", "SE", "PL", "TR", "SA", "AE", "EG" => Endpoint::EU,
            default => Endpoint::NA,
        };
        return SellingPartnerApi::seller(
            clientId: $marketplace->getClientId(),
            clientSecret: $marketplace->getClientSecret(),
            refreshToken: $marketplace->getRefreshToken(),
            endpoint: $endpoint
        );
    }

    public function download($forceDownload = false): void
    {
        $this->listings = json_Decode(Utility::getCustomCache("LISTINGS.json", PIMCORE_PROJECT_ROOT . "/tmp/marketplaces/".urlencode($this->marketplace->getKey())), true);
        if (empty($this->listings) || $forceDownload) {
            $this->reportsHelper->downloadAllReports($forceDownload);
            $this->listingsHelper->getListings($forceDownload);
            Utility::setCustomCache("LISTINGS.json", PIMCORE_PROJECT_ROOT . "/tmp/marketplaces/".urlencode($this->marketplace->getKey()), json_encode($this->listings, JSON_PRETTY_PRINT));
        }
        foreach ($this->listings as $asin=>$listing) {
            Utility::setCustomCache("{$asin}.json", PIMCORE_PROJECT_ROOT . "/tmp/marketplaces/tmp/".urlencode($this->marketplace->getKey()), json_encode($listing, JSON_PRETTY_PRINT));
        }
    }

    public function import($updateFlag, $importFlag)
    {
        if (empty($this->listings)) {
            echo "Nothing to import in {$this->mainCountry}\n";
            return;
        } else {
            echo "Importing {$this->mainCountry}\n";
        }
        $this->importHelper->import($updateFlag, $importFlag);
    }

    public function downloadOrders(): void
    {
        $this->ordersHelper->downloadOrders();
    }

    public function downloadInventory(): void
    {
        $this->inventoryHelper->downloadInventory();
    }

}
