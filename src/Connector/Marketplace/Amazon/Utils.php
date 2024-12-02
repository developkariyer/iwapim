<?php

namespace App\Connector\Marketplace\Amazon;

use SellingPartnerApi\Seller\ListingsItemsV20210801\Dto\ListingsItemPatchRequest;
use SellingPartnerApi\Seller\ListingsItemsV20210801\Dto\PatchOperation;

use App\Connector\Marketplace\Amazon\Constants as AmazonConstants;
use App\Connector\Marketplace\Amazon\Connector as AmazonConnector;
use App\Utils\Utility;

class Utils
{
    public $amazonConnector;

    public function __construct(AmazonConnector $amazonConnector) 
    {
        $this->amazonConnector = $amazonConnector;
    }

    public function patchCustom($sku, $country = null, $patches) // $attribute, $operation, $value = null
    {
        if (empty($country)) {
            $country = $this->amazonConnector->mainCountry;
        }
        $safeSku = preg_replace('/[^a-zA-Z0-9._-]/', '_', $sku);
        $listing = Utility::getCustomCache("$safeSku.json", PIMCORE_PROJECT_ROOT."/tmp/marketplaces/AmazonListing/$country", 86400*7);
        if (empty($listing)) {
            $listingsApi = $this->amazonConnector->amazonSellerConnector->listingsItemsV20210801();
            $listing = $listingsApi->getListingsItem(
                sellerId: $this->amazonConnector->getMarketplace()->getMerchantId(),
                marketplaceIds: [AmazonConstants::amazonMerchant[$country]['id']],
                sku: rawurlencode($sku),
                includedData: ['summaries', 'attributes', 'issues', 'offers', 'fulfillmentAvailability', 'procurement']
            );
            Utility::setCustomCache("$safeSku.json", PIMCORE_PROJECT_ROOT."/tmp/marketplaces/AmazonPatch/$country", json_encode($listing->json(), JSON_PRETTY_PRINT));
            $listing = $listing->json();
        }
        $productType = $listing['summaries'][0]['productType'] ?? '';
        if (empty($productType)) { return; }
        $listingsItemPatchRequest = new ListingsItemPatchRequest(
            productType: $productType,
            patches: $patches,
        );
        echo "Patching ";
        $patchOperation = $listingsApi->patchListingsItem(
            sellerId: $this->amazonConnector->getMarketplace()->getMerchantId(),
            sku: rawurlencode($sku),
            marketplaceIds: [AmazonConstants::amazonMerchant[$country]['id']],
            listingsItemPatchRequest: $listingsItemPatchRequest
        );
        Utility::setCustomCache("$safeSku.json", PIMCORE_PROJECT_ROOT."/tmp/marketplaces/AmazonPatch/$country", json_encode($patchOperation->json(), JSON_PRETTY_PRINT));
        echo $patchOperation->json()['status']."\n";
    }

    public function getInfo($sku, $country = null) 
    {
        if (empty($country)) {
            $country = $this->amazonConnector->mainCountry;
        }
        $safeSku = preg_replace('/[^a-zA-Z0-9._-]/', '_', $sku);

        $listing = Utility::getCustomCache("$safeSku.json", PIMCORE_PROJECT_ROOT."/tmp/marketplaces/AmazonListing/$country", 86400*7);
        if (empty($listing)) {
            $listingsApi = $this->amazonConnector->amazonSellerConnector->listingsItemsV20210801();
            $listing = $listingsApi->getListingsItem(
                sellerId: $this->amazonConnector->getMarketplace()->getMerchantId(),
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
        $definition = Utility::getCustomCache("$safeProductType.json", PIMCORE_PROJECT_ROOT."/tmp/marketplaces/AmazonDefinition/$country", 86400*7);
        if (empty($definition)) {
            $productTypeDefinitionApi = $this->amazonConnector->amazonSellerConnector->productTypeDefinitionsV20200901();
            $definition = $productTypeDefinitionApi->getDefinitionsProductType(
                marketplaceIds: [AmazonConstants::amazonMerchant[$country]['id']],
                sellerId: $this->amazonConnector->getMarketplace()->getMerchantId(),
                productType: $productType
            );
            Utility::setCustomCache("$safeProductType.json", PIMCORE_PROJECT_ROOT."/tmp/marketplaces/AmazonDefinition/$country", json_encode($definition->json(), JSON_PRETTY_PRINT));
        }
    }

    public function patchListing($sku, $country = null)
    {
        if (empty($country)) {
            $country = $this->amazonConnector->mainCountry;
        }

        $patches = [
            new PatchOperation(
                op: "replace", // "replace", // "delete",
                path: "/attributes/gpsr_safety_attestation",
                value: [
                    [
                        "marketplace_id" => AmazonConstants::amazonMerchant[$country]['id'],
                        "value" => true,
                    ]
                ]
            ),
            new PatchOperation(
                op: "replace", //"replace",
                path: "/attributes/dsa_responsible_party_address",
                value: [
                    [
                        "marketplace_id" => AmazonConstants::amazonMerchant[$country]['id'],
                        "value" => "responsible@iwaconcept.com",
                    ]
                ]
            ),
            new PatchOperation(
                op: "replace", //"replace",
                path: "/attributes/gpsr_manufacturer_reference",
                value: [
                    [
                        "marketplace_id" => AmazonConstants::amazonMerchant[$country]['id'],
                        "value" => "handmadeworksshopeu@gmail.com",
                    ]
                ]
            )
        ];
        $this->patchCustom($sku, $country, $patches);
    }

}