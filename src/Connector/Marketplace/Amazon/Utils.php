<?php

namespace App\Connector\Marketplace\Amazon;

use JsonException;
use SellingPartnerApi\Seller\ListingsItemsV20210801\Dto\ListingsItemPatchRequest;
use SellingPartnerApi\Seller\ListingsItemsV20210801\Dto\PatchOperation;

use App\Connector\Marketplace\Amazon\Constants as AmazonConstants;
use App\Utils\Utility;

class Utils
{
    public Connector $connector;

    public function __construct(Connector $connector)
    {
        $this->connector = $connector;
    }

    /**
     * @throws JsonException
     */
    public function patchCustom($sku, $country, $patches): void // $attribute, $operation, $value = null
    {
        $listingsApi = $this->connector->amazonSellerConnector->listingsItemsV20210801();
        if (empty($country)) {
            $country = $this->connector->mainCountry;
        }
        $safeSku = preg_replace('/[^a-zA-Z0-9._-]/', '_', $sku);
        $listing = Utility::getCustomCache("$safeSku.json", PIMCORE_PROJECT_ROOT."/tmp/marketplaces/AmazonListing/$country", 86400*7);
        if (empty($listing)) {
            $listing = $listingsApi->getListingsItem(
                sellerId: $this->connector->getMarketplace()->getMerchantId(),
                sku: rawurlencode($sku),
                marketplaceIds: [AmazonConstants::amazonMerchant[$country]['id']],
                includedData: ['summaries', 'attributes', 'issues', 'offers', 'fulfillmentAvailability', 'procurement']
            );
            Utility::setCustomCache("$safeSku.json", PIMCORE_PROJECT_ROOT."/tmp/marketplaces/AmazonPatch/$country", json_encode($listing->json(), JSON_PRETTY_PRINT));
            $listing = $listing->json();
        } else {
            $listing = json_decode($listing, true);
        }
        $productType = $listing['summaries'][0]['productType'] ?? '';
        if (empty($productType)) { 
            echo "Empty product type\n";
            return;
        }
        $listingsItemPatchRequest = new ListingsItemPatchRequest(
            productType: $productType,
            patches: $patches,
        );
        echo "Patching ";
        $patchOperation = $listingsApi->patchListingsItem(
            sellerId: $this->connector->getMarketplace()->getMerchantId(),
            sku: rawurlencode($sku),
            listingsItemPatchRequest: $listingsItemPatchRequest,
            marketplaceIds: [AmazonConstants::amazonMerchant[$country]['id']]
        );
        Utility::setCustomCache("$safeSku.json", PIMCORE_PROJECT_ROOT."/tmp/marketplaces/AmazonPatch/$country", json_encode(['patches' => $patches, 'response' => $patchOperation->json()], JSON_PRETTY_PRINT));
        echo $patchOperation->json()['status']."\n";
    }

    /**
     * @throws JsonException
     */
    public function getInfo($sku, $country = null): void
    {
        if (empty($country)) {
            $country = $this->connector->mainCountry;
        }
        $safeSku = preg_replace('/[^a-zA-Z0-9._-]/', '_', $sku);
        $listing = Utility::getCustomCache("$safeSku.json", PIMCORE_PROJECT_ROOT."/tmp/marketplaces/AmazonListing/$country");
        if (empty($listing)) {
            $listingsApi = $this->connector->amazonSellerConnector->listingsItemsV20210801();
            $listing = $listingsApi->getListingsItem(
                sellerId: $this->connector->getMarketplace()->getMerchantId(),
                sku: rawurlencode($sku),
                marketplaceIds: [AmazonConstants::amazonMerchant[$country]['id']],
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
            $productTypeDefinitionApi = $this->connector->amazonSellerConnector->productTypeDefinitionsV20200901();
            $definition = $productTypeDefinitionApi->getDefinitionsProductType(
                productType: $productType,
                marketplaceIds: [AmazonConstants::amazonMerchant[$country]['id']],
                sellerId: $this->connector->getMarketplace()->getMerchantId()
            );
            Utility::setCustomCache("$safeProductType.json", PIMCORE_PROJECT_ROOT."/tmp/marketplaces/AmazonDefinition/$country", json_encode($definition->json(), JSON_PRETTY_PRINT));
        }
    }

    /**
     * @throws JsonException
     */
    public function patchGPSR($sku, $country = null): void
    {
        if (empty($country)) {
            $country = $this->connector->mainCountry;
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

    /**
     * @throws JsonException
     */
    public function patchDeleteGPSR($sku, $country = null): void
    {
        if (empty($country)) {
            $country = $this->connector->mainCountry;
        }

        $patches = [
            new PatchOperation(
                op: "delete",
                path: "/attributes/gpsr_safety_attestation",
                value: [
                    [
                        "marketplace_id" => AmazonConstants::amazonMerchant[$country]['id'],
                        "value" => true,
                    ]
                ]
            ),
            new PatchOperation(
                op: "delete",
                path: "/attributes/dsa_responsible_party_address",
                value: [
                    [
                        "marketplace_id" => AmazonConstants::amazonMerchant[$country]['id'],
                    ]
                ]
            ),
            new PatchOperation(
                op: "delete",
                path: "/attributes/gpsr_manufacturer_reference",
                value: [
                    [
                        "marketplace_id" => AmazonConstants::amazonMerchant[$country]['id'],
                    ]
                ]
            )
        ];
        $this->patchCustom($sku, $country, $patches);
    }


}