<?php

namespace App\Connector\Marketplace\Amazon;

use SellingPartnerApi\SellingPartnerApi;
use SellingPartnerApi\Enums\Endpoint;
use SellingPartnerApi\Seller\ListingsItemsV20210801\Dto\ListingsItemPatchRequest;
use SellingPartnerApi\Seller\ListingsItemsV20210801\Dto\PatchOperation;

use Pimcore\Model\DataObject\Marketplace;
use Pimcore\Model\DataObject\VariantProduct;

use App\Connector\Marketplace\MarketplaceConnectorAbstract;
use App\Connector\Marketplace\Amazon\Constants as AmazonConstants;
use App\Connector\Marketplace\Amazon\Reports as ReportsHelper;
use App\Connector\Marketplace\Amazon\Listings as ListingsHelper;
use App\Connector\Marketplace\Amazon\Import as ImportHelper;
use App\Connector\Marketplace\Amazon\Orders as OrdersHelper;
use App\Utils\Utility;
use App\Utils\Registry;

class Connector extends MarketplaceConnectorAbstract
{
    public static $marketplaceType = 'Amazon';

    public $reportsHelper;
    public $listingsHelper;
    public $importHelper;
    public $ordersHelper;
    

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
            Utility::setCustomCache("LISTINGS.json", PIMCORE_PROJECT_ROOT . "/tmp/marketplaces/".urlencode($this->marketplace->getKey()), json_encode($this->listings));
        }
        foreach ($this->listings as $asin=>$listing) {
            Utility::setCustomCache("{$asin}.json", PIMCORE_PROJECT_ROOT . "/tmp/marketplaces/tmp/".urlencode($this->marketplace->getKey()), json_encode($listing));
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
        $inventory = [];
        $fnsku = [];
        $this->reportsHelper->downloadAllReports(forceDownload: false, silent: true);
        $lines = explode("\n", mb_convert_encoding(trim($this->reportsHelper->amazonReports['GET_AFN_INVENTORY_DATA_BY_COUNTRY']), 'UTF-8', 'UTF-8'));
        $header = str_getcsv(array_shift($lines), "\t");
        foreach ($lines as $line) {
            $data = str_getcsv($line, "\t");
            if (count($header) == count($data)) {
                $rowData = array_combine($header, $data);
                if (!isset($inventory[$rowData['asin']])) {
                    $inventory[$rowData['asin']] = [];
                }
                $inventory[$rowData['asin']][$rowData['country']] = $rowData['quantity-for-local-fulfillment'];
                $fnsku[$rowData['asin']] = $rowData['fulfillment-channel-sku'];                
            }
        }
        foreach ($inventory as $asin=>$data) {
            $variantObject = VariantProduct::getByUniqueMarketplaceId($asin, ['limit' => 1]);
            if ($variantObject) {
                echo "Updating $asin inventory ";
                $oldStock = $variantObject->getStock();
                $newStock = $oldStock;
                foreach ($data as $country=>$amount) {
                    echo "$country: $amount ";
                    Utility::upsertRow($newStock, [$country, $amount, gmdate('Y-m-d')]);
                }
                if ($oldStock !== $newStock) {
                    $variantObject->setStock($newStock);
                    $variantObject->save();
                    echo "Saved";
                }
                if (!empty($fnsku[$asin])) {
                    Registry::setKey($fnsku[$asin], $asin, 'fnsku-to-asin');
                }
                echo "\n";
            }
        }
        file_put_contents(PIMCORE_PROJECT_ROOT."/tmp/inventory_test.json", json_encode($inventory, JSON_PRETTY_PRINT));
    }

    public function patchCustom($sku, $country = null, $attribute, $operation, $value = null)
    {
        if (empty($country)) {
            $country = $this->mainCountry;
        }
        $listingsApi = $this->amazonSellerConnector->listingsItemsV20210801();
        $listing = $listingsApi->getListingsItem(
            sellerId: $this->marketplace->getMerchantId(),
            marketplaceIds: [AmazonConstants::amazonMerchant[$country]['id']],
            sku: rawurlencode($sku),
            includedData: ['summaries', 'attributes', 'issues', 'offers', 'fulfillmentAvailability', 'procurement']
        );
        $safeSku = preg_replace('/[^a-zA-Z0-9._-]/', '_', $sku);
        file_put_contents(PIMCORE_PROJECT_ROOT."/tmp/marketplaces/AmazonPatch/CUSTOM_PATCH_LISTING_$safeSku.json", json_encode($listing->json()));
        $productType = $listing->json()['summaries'][0]['productType'] ?? '';
        if (empty($productType)) { return; }
        $patches = [
            new PatchOperation(
                op: $operation,
                path: "/attributes/$attribute",
                value: [
                    [
                        "marketplace_id" => AmazonConstants::amazonMerchant[$country]['id'],
                        "value" => $value,
                    ]
                ]
            )
        ];
        $listingsItemPatchRequest = new ListingsItemPatchRequest(
            productType: $productType,
            patches: $patches,
        );
        echo "Patching\n";
        $patchOperation = $listingsApi->patchListingsItem(
            sellerId: $this->marketplace->getMerchantId(),
            sku: rawurlencode($sku),
            marketplaceIds: [AmazonConstants::amazonMerchant[$country]['id']],
            listingsItemPatchRequest: $listingsItemPatchRequest
        );
        echo json_encode($patchOperation->json(), JSON_PRETTY_PRINT);
        file_put_contents(PIMCORE_PROJECT_ROOT."/tmp/marketplaces/AmazonPatch/CUSTOM_PATCH_RESPONSE_$safeSku.json", json_encode($patchOperation->json()));        
    }

    public function getInfo($sku, $country = null) 
    {
        if (empty($country)) {
            $country = $this->mainCountry;
        }
        $safeSku = preg_replace('/[^a-zA-Z0-9._-]/', '_', $sku);

        $listing = Utility::getCustomCache("$safeSku.json", PIMCORE_PROJECT_ROOT."/tmp/marketplaces/AmazonListing/$country");
        if (empty($listing)) {
            $listingsApi = $this->amazonSellerConnector->listingsItemsV20210801();
            $listing = $listingsApi->getListingsItem(
                sellerId: $this->marketplace->getMerchantId(),
                marketplaceIds: [AmazonConstants::amazonMerchant[$country]['id']],
                sku: rawurlencode($sku),
                includedData: ['summaries', 'attributes', 'issues', 'offers', 'fulfillmentAvailability', 'procurement']
            );
            $listing = $listing->json();
            Utility::setCustomCache("$safeSku.json", PIMCORE_PROJECT_ROOT."/tmp/marketplaces/AmazonListing/$country", json_encode($listing, JSON_PRETTY_PRINT));
        }
        $productType = $listing['summaries'][0]['productType'] ?? '';
        if (empty($productType)) { return; }

        $safeProductType = preg_replace('/[^a-zA-Z0-9._-]/', '_', $productType);
        $definition = Utility::getCustomCache("$safeProductType.json", PIMCORE_PROJECT_ROOT."/tmp/marketplaces/AmazonDefinition/$country");
        if (empty($definition)) {
            $productTypeDefinitionApi = $this->amazonSellerConnector->productTypeDefinitionsV20200901();
            $definition = $productTypeDefinitionApi->getDefinitionsProductType(
                marketplaceIds: [AmazonConstants::amazonMerchant[$country]['id']],
                sellerId: $this->marketplace->getMerchantId(),
                productType: $productType
            );
            Utility::setCustomCache("$safeProductType.json", PIMCORE_PROJECT_ROOT."/tmp/marketplaces/AmazonDefinition/$country", json_encode($definition->json(), JSON_PRETTY_PRINT));
        }
    }

    public function patchListing($sku, $country = null)
    {
        if (empty($country)) {
            $country = $this->mainCountry;
        }
        $listingsApi = $this->amazonSellerConnector->listingsItemsV20210801();
        
        echo "Processing $sku details";
        $listing = $listingsApi->getListingsItem(
            sellerId: $this->marketplace->getMerchantId(),
            marketplaceIds: [AmazonConstants::amazonMerchant[$country]['id']],
            sku: rawurlencode($sku),
            includedData: ['summaries', 'attributes', 'issues', 'offers', 'fulfillmentAvailability', 'procurement']
        );

        $productType = $listing->json()['summaries'][0]['productType'] ?? '';

        if (empty($productType)) { return; }
        echo " $productType";

        /*
        echo " $productType definitions";
        $productTypeDefinitionsApi = $this->amazonSellerConnector->productTypeDefinitionsV20200901();
        $definitions = $productTypeDefinitionsApi->getDefinitionsProductType(
            marketplaceIds: [AmazonConstants::amazonMerchant[$country]['id']],
            sellerId: $this->marketplace->getMerchantId(),
            productType: $productType
        );
        */
        $patches = [
            new PatchOperation(
                op: "add", // "replace", // "delete",
                path: "/attributes/gpsr_safety_attestation",
                value: [
                    [
                        "marketplace_id" => AmazonConstants::amazonMerchant[$country]['id'],
                        "value" => true,
                    ]
                ]
            ),
            new PatchOperation(
                op: "add", //"replace",
                path: "/attributes/dsa_responsible_party_address",
                value: [
                    [
                        "marketplace_id" => AmazonConstants::amazonMerchant[$country]['id'],
                        "value" => "responsible@iwaconcept.com",
                    ]
                ]
            ),
            new PatchOperation(
                op: "add", //"replace",
                path: "/attributes/gpsr_manufacturer_reference",
                value: [
                    [
                        "marketplace_id" => AmazonConstants::amazonMerchant[$country]['id'],
                        "value" => "handmadeworksshopeu@gmail.com",
                    ]
                ]
            )
        ];

        $listingsItemPatchRequest = new ListingsItemPatchRequest(
            productType: $productType,
            patches: $patches,
        );

        echo " patching ";
        $patch = $listingsApi->patchListingsItem(
            sellerId: $this->marketplace->getMerchantId(),
            sku: rawurlencode($sku),
            marketplaceIds: [AmazonConstants::amazonMerchant[$country]['id']],
            listingsItemPatchRequest: $listingsItemPatchRequest
        );
        echo $patch->json()['status'] ?? " ??";
        echo " OK\n";
        // fix $sku to generate a valid file name
        $sku = preg_replace('/[^a-zA-Z0-9._-]/', '_', $sku);
        file_put_contents(PIMCORE_PROJECT_ROOT."/tmp/marketplaces/AmazonPatch/$sku.json", json_encode($patch->json()));
    }

}
