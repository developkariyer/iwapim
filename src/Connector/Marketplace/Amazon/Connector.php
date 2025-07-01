<?php

namespace App\Connector\Marketplace\Amazon;

use App\Connector\Marketplace\Amazon\Constants as AmazonConstants;
use App\Connector\Marketplace\MarketplaceConnectorAbstract;
use App\Utils\Utility;
use DateMalformedStringException;
use Exception;
use JsonException;
use Pimcore\Model\DataObject\Marketplace;
use Pimcore\Model\DataObject\VariantProduct;
use Pimcore\Model\Element\DuplicateFullPathException;
use Random\RandomException;
use SellingPartnerApi\Enums\Endpoint;
use SellingPartnerApi\Seller\SellerConnector;
use SellingPartnerApi\SellingPartnerApi;

class Connector extends MarketplaceConnectorAbstract
{
    public static string $marketplaceType = 'Amazon';

    /**
     * @var Reports $reportsHelper Helper class for Amazon reports
     */
    public Reports $reportsHelper;
    /**
     * @var Listings $listingsHelper Helper class for Amazon listings
     */
    public Listings $listingsHelper;
    /**
     * @var Import $importHelper Helper class for Amazon importing
     */
    public Import $importHelper;
    /**
     * @var Orders $ordersHelper Helper class for Amazon order management
     */
    public Orders $ordersHelper;
    /**
     * @var Utils $utilsHelper Helper class for Amazon related miscellaneous methods
     */
    public Utils $utilsHelper;
    /**
     * @var Inventory $inventoryHelper Helper class for Amazon inventory management
     */
    public Inventory $inventoryHelper;

    /**
     * @var SellerConnector|null $amazonSellerConnector The regional seller connector for Amazon
     */
    public ?SellerConnector $amazonSellerConnector = null;
    /**
     * @var array|string[] $countryCodes The authorized country codes for the marketplace
     */
    public array $countryCodes = [];
    /**
     * @var string|null $mainCountry The main country code for the marketplace
     */
    public ?string $mainCountry = null;

    /**
     * {@inheritDoc}
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

    /**
     * Initializes the regional seller connector for Amazon
     * @throws Exception
     */
    private function initSellerConnector($marketplace): SellerConnector
    {
        $endpoint = Constants::amazonMerchant[$marketplace->getMainMerchant()]['endpoint'] ?? null;
/*
            match ($marketplace->getMainMerchant()) {
            "SG", "AU", "JP", "IN" => Endpoint::FE,
            "UK", "FR", "DE", "IT", "ES", "NL", "SE", "PL", "TR", "SA", "AE", "EG" , "IE" => Endpoint::EU,
            "CA", "US", "MX", "BR" => Endpoint::NA,
            default => null
        };*/
        if ($endpoint === null) {
            throw new Exception("Country code is not valid");
        }
        return SellingPartnerApi::seller(
            clientId: $marketplace->getClientId(),
            clientSecret: $marketplace->getClientSecret(),
            refreshToken: $marketplace->getRefreshToken(),
            endpoint: $endpoint
        );
    }

    /**
     * {@inheritDoc}
     * @throws JsonException|\Doctrine\DBAL\Exception|RandomException
     */
    public function download(bool $forceDownload = false): void
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
     * {@inheritDoc}
     * @throws DuplicateFullPathException|\Doctrine\DBAL\Exception
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
     * {@inheritDoc}
     * @throws \Doctrine\DBAL\Exception|DateMalformedStringException
     */
    public function downloadOrders(): void
    {
        $this->ordersHelper->downloadOrders();
    }

    public function downloadReturns(): void
    {
        $sql = "SELECT * FROM `iwa_marketplace_orders_line_items` WHERE marketplace_type = 'Amazon' and is_canceled = 'cancelled'";
        $returnOrders = Utility::fetchFromSql($sql, []);
        foreach ($returnOrders as $return) {
            $sqlInsertMarketplaceReturn = "
                            INSERT INTO iwa_marketplace_returns (marketplace_id, return_id, json) 
                            VALUES (:marketplace_id, :return_id, :json) ON DUPLICATE KEY UPDATE json = VALUES(json)";
            Utility::executeSql($sqlInsertMarketplaceReturn, [
                'marketplace_id' => $this->marketplace->getId(),
                'return_id' => $return['order_id'],
                'json' => json_encode($return)
            ]);
            echo "Inserting order: " . $return['order_id'] . "\n";
        }
    }

    /**
     * {@inheritDoc}
     * @throws \Doctrine\DBAL\Exception
     */
    public function downloadInventory(): void
    {
        $this->inventoryHelper->downloadInventory();
    }

    /**
     * {@inheritDoc}
     */
    public function setInventory(VariantProduct $listing, int $targetValue, $sku = null, $country = null): void
    {
        // TODO: Implement setInventory() method. Needs to be implemented in harmony with PIM Inventory management.
    }

    /**
     * {@inheritDoc}
     */
    public function setPrice(VariantProduct $listing,string $targetPrice, $targetCurrency = null, $sku = null, $country = null): void
    {
        // TODO: Implement setPrice() method.
    }
}
