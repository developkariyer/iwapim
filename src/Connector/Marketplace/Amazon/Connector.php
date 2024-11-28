<?php

namespace App\Connector\Marketplace\Amazon;

use SellingPartnerApi\SellingPartnerApi;
use SellingPartnerApi\Enums\Endpoint;
use SellingPartnerApi\Seller\ListingsItemsV20210801\Dto\ListingsItemPatchRequest;
use SellingPartnerApi\Seller\ListingsItemsV20210801\Dto\PatchOperation;

use Pimcore\Model\DataObject\Marketplace;

use App\Connector\Marketplace\MarketplaceConnectorAbstract;
use App\Connector\Marketplace\Amazon\Constants as AmazonConstants;
use App\Connector\Marketplace\Amazon\Reports as ReportsHelper;
use App\Connector\Marketplace\Amazon\Listings as ListingsHelper;
use App\Connector\Marketplace\Amazon\Import as ImportHelper;
use App\Connector\Marketplace\Amazon\Orders as OrdersHelper;
use App\Utils\Utility;

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
    
        /*
        $db = \Pimcore\Db::get();
        $lastUpdateAt = $this->getLatestOrderUpdate();
        echo "Last Update: $lastUpdateAt\n";
        $ordersApi = $this->amazonSellerConnector->ordersV0();
        $marketplaceIds = array_map(function($country) {
            return AmazonConstants::amazonMerchant[$country]['id'];
        }, $this->countryCodes);
        $marketplaceIds[] = AmazonConstants::amazonMerchant[$this->mainCountry]['id'];

        $orderIds = [];
        $nextToken = null;
        $burst = 0;
    
        do {
            $orders = $nextToken ? $ordersApi->getOrders(nextToken: $nextToken, marketplaceIds: $marketplaceIds) : $ordersApi->getOrders(createdAfter: $lastUpdateAt, marketplaceIds: $marketplaceIds);
            $orders = $orders->json();
            $pageOrderIds = array_map(function($order) {
                return $order['AmazonOrderId'];
            }, $orders['payload']['Orders']);
            $orderIds = array_merge($orderIds, $pageOrderIds);
            echo "Total Orders so far: " . count($orderIds) . "\n";
            $nextToken = $orders['payload']['NextToken'] ?? null;
            $burst++;
            sleep($burst>20 ? 60 : 1);
        } while ($nextToken);
        $orderIds = array_unique($orderIds);
        $orderIds = array_filter($orderIds);
        echo "Final total orders: " . count($orderIds) . "\n";
    
        $db->beginTransaction();
        try {
            foreach ($orderIds as $orderId) {
                echo "    $orderId\n";
                $order = $ordersApi->getOrder(orderId: $orderId);
                $order = $order->json();
                $db->executeStatement(
                    "INSERT INTO iwa_marketplace_orders (marketplace_id, order_id, json) VALUES (?, ?, ?) ON DUPLICATE KEY UPDATE json = VALUES(json)",
                    [
                        $this->marketplace->getId(),
                        $order['payload']['AmazonOrderId'],
                        json_encode($order['payload']),
                    ]
                );
                sleep(2);
            }
            $db->commit();
        } catch (\Exception $e) {
            $db->rollBack();
            echo $e->getMessage();
        }*/
    }

    public function downloadInventory(): void
    {
        foreach ($this->countryCodes as $country) {
            echo "\n    - $country ";
            $filename = PIMCORE_PROJECT_ROOT.'/tmp/marketplaces/'.urlencode(string: $this->marketplace->getKey()).'_'.$country.'_inventory.json';
            if (file_exists(filename: $filename) && filemtime(filename: $filename) > time() - 86400) {
                echo " (cached) ";
                $allInventorySummaries = json_decode(file_get_contents($filename), true);
            } else {
                $inventoryApi = $this->amazonSellerConnector->fbaInventoryV1();
                $nextToken = null;
                $allInventorySummaries = [];
                do {
                    $response = $inventoryApi->getInventorySummaries(
                        granularityType: 'Marketplace',
                        granularityId: AmazonConstants::amazonMerchant[$country]['id'],
                        marketplaceIds: [AmazonConstants::amazonMerchant[$country]['id']],
                        details: true,
                        nextToken: $nextToken
                    );
                    $responseData = $response->json();
                    $inventorySummaries = $responseData['payload']['inventorySummaries'] ?? [];
                    $allInventorySummaries = array_merge($allInventorySummaries, $inventorySummaries);
                    $nextToken = $responseData['pagination']['nextToken'] ?? null;
                    usleep(microseconds: 500000);
                    echo ".";
                } while ($nextToken);
                file_put_contents(filename: $filename, data: json_encode(value: $allInventorySummaries));
            }

            $db = \Pimcore\Db::get();
            $db->beginTransaction();
            try {
                foreach ($allInventorySummaries as $inventory) {
                    $sql = "INSERT INTO iwa_amazon_inventory (";
                    $dbFields = [];
                    foreach ($inventory as $key=>$value) {
                        if (is_array(value: $value)) {
                            $value = json_encode(value: $value);
                        }
                        if ($key === 'condition') {
                            $key = 'itemCondition';
                        }
                        $dbFields[$key] = $value;
                    }
                    $dbFields['countryCode'] = $country;
                    $sql .= implode(separator: ',', array: array_keys($dbFields)) . ") VALUES (";
                    $sql .= implode(separator: ',', array: array_fill(start_index: 0, count: count(value: $dbFields), value: '?')) . ")";
                    $sql .= " ON DUPLICATE KEY UPDATE ";
                    $sql .= implode(separator: ',', array: array_map(callback: function($key): string {
                        return "$key=?";
                    }, array: array_keys($dbFields)));
                    $stmt = $db->prepare($sql);
                    $stmt->execute(array_merge(arrays: array_values(array: $dbFields), array_array: values($dbFields)));
                }
                $db->commit();
            } catch (\Exception $e) {
                $db->rollBack();
                echo $e->getMessage();
            }
        }
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
        $listingsApi = $this->amazonSellerConnector->listingsItemsV20210801();
        $listing = $listingsApi->getListingsItem(
            sellerId: $this->marketplace->getMerchantId(),
            marketplaceIds: [AmazonConstants::amazonMerchant[$country]['id']],
            sku: rawurlencode($sku),
            includedData: ['summaries', 'attributes', 'issues', 'offers', 'fulfillmentAvailability', 'procurement']
        );
        echo json_encode($listing->json(), JSON_PRETTY_PRINT);
        $safeSku = preg_replace('/[^a-zA-Z0-9._-]/', '_', $sku);
        file_put_contents(PIMCORE_PROJECT_ROOT."/tmp/marketplaces/AmazonPatch/INFO_$safeSku.json", json_encode($listing->json()));
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
