<?php

namespace App\Connector\Marketplace\Amazon;

use App\Connector\Marketplace\Amazon\Constants as AmazonConstants;
use App\Connector\Marketplace\MarketplaceConnectorAbstract;
use Exception;
use JsonException;
use Pimcore\Model\DataObject\Marketplace;
use Pimcore\Model\DataObject\VariantProduct;
use Pimcore\Model\Element\DuplicateFullPathException;
use SellingPartnerApi\Enums\Endpoint;
use SellingPartnerApi\Seller\SellerConnector;
use SellingPartnerApi\SellingPartnerApi;

class Connector extends MarketplaceConnectorAbstract
{
    public static string $marketplaceType = 'Amazon';

    public Reports $reportsHelper;
    public Listings $listingsHelper;
    public Import $importHelper;
    public Orders $ordersHelper;
    public Utils $utilsHelper;
    public Inventory $inventoryHelper;

    public ?SellerConnector $amazonSellerConnector = null;
    public array $countryCodes = [];
    public ?string $mainCountry = null;

    /**
     * @throws Exception
     */
    public function __construct(Marketplace $marketplace)
    {
        parent::__construct($marketplace);
        $this->countryCodes = $marketplace->getMerchantIds() ?? [];
        if (!AmazonConstants::checkCountryCodes($this->countryCodes)) {
            throw new Exception("Country codes are not valid");
        }
        $this->mainCountry = $marketplace->getMainMerchant();
        $this->amazonSellerConnector = $this->initSellerConnector($marketplace);
        $this->reportsHelper = new Reports($this);
        $this->listingsHelper = new Listings($this);
        $this->importHelper = new Import($this);
        $this->ordersHelper = new Orders($this);
        $this->utilsHelper = new Utils($this);
        $this->inventoryHelper = new Inventory($this);
    }

    private function initSellerConnector($marketplace): SellerConnector
    {
        $endpoint = match ($marketplace->getMainMerchant()) {
            "SG", "AU", "JP", "IN" => Endpoint::FE,
            "UK", "FR", "DE", "IT", "ES", "NL", "SE", "PL", "TR", "SA", "AE", "EG" , "IE" => Endpoint::EU,
            default => Endpoint::NA,  //"CA", "US", "MX", "BR"
        };
        return SellingPartnerApi::seller(
            clientId: $marketplace->getClientId(),
            clientSecret: $marketplace->getClientSecret(),
            refreshToken: $marketplace->getRefreshToken(),
            endpoint: $endpoint
        );
    }

    /**
     * @throws JsonException|\Doctrine\DBAL\Exception
     */
    public function download($forceDownload = false): void
    {
        if ($forceDownload || !$this->getListingsFromCache($forceDownload)) {
            $this->reportsHelper->downloadAllReports($forceDownload);
            $this->listingsHelper->getListings($forceDownload);
            $this->putListingsToCache();
        }
        foreach ($this->listings as $asin=>$listing) {
            $this->putToCache("ASIN_{$asin}.json", $listing);
        }
    }

    /**
     * @throws DuplicateFullPathException
     * @throws \Doctrine\DBAL\Exception
     */
    public function import($updateFlag, $importFlag): void
    {
        if (empty($this->listings)) {
            echo "Nothing to import in {$this->mainCountry}\n";
            return;
        } else {
            echo "Importing {$this->mainCountry}\n";
        }
        $this->importHelper->import($updateFlag, $importFlag);
    }

    /**
     * @throws \Doctrine\DBAL\Exception
     */
    public function downloadOrders(): void
    {
        $this->ordersHelper->downloadOrders();
    }

    /**
     * @throws \Doctrine\DBAL\Exception
     */
    public function downloadInventory(): void
    {
        $this->inventoryHelper->downloadInventory();
    }

    public function setInventory(VariantProduct $listing, int $targetValue, $sku = null, $country = null)
    {
        // TODO: Implement setInventory() method.
    }

    public function setPrice(VariantProduct $listing,string $targetPrice, $targetCurrency = null, $sku = null, $country = null)
    {
        // TODO: Implement setPrice() method.
    }
}
