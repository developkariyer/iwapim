<?php

namespace App\Connector\Marketplace\Amazon;

use JsonException;
use Random\RandomException;
use SellingPartnerApi\Seller\ListingsItemsV20210801\Dto\ListingsItemPatchRequest;
use SellingPartnerApi\Seller\ListingsItemsV20210801\Dto\PatchOperation;

use App\Connector\Marketplace\Amazon\Constants as AmazonConstants;

class Utils
{
    public Connector $connector;

    public function __construct(Connector $connector)
    {
        $this->connector = $connector;
    }

    /**
     * @throws JsonException|RandomException
     */
    public function patchCustom($sku, $country, $patches): void // $attribute, $operation, $value = null
    {
        $listingsApi = $this->connector->amazonSellerConnector->listingsItemsV20210801();
        if (empty($country)) {
            $country = $this->connector->mainCountry;
        }
        $safeSku = preg_replace('/[^a-zA-Z0-9._-]/', '_', $sku);
        $listing = $this->connector->getFromCache("LISTING_{$country}_{$safeSku}.json", 7*86400);
        if (empty($listing)) {
            $listing = $listingsApi->getListingsItem(
                sellerId: $this->connector->getMarketplace()->getMerchantId(),
                sku: rawurlencode($sku),
                marketplaceIds: [AmazonConstants::amazonMerchant[$country]['id']],
                includedData: ['summaries', 'attributes', 'issues', 'offers', 'fulfillmentAvailability', 'procurement']
            );
            $listing = $listing->json();
            $this->connector->putToCache("LISTING_{$country}_{$safeSku}.json", $listing);
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
        $this->connector->putToCache("PATCH_{$country}_{$safeSku}.json", ['patches' => $patches, 'response' => $patchOperation->json()]);
        echo $patchOperation->json()['status']."\n";
    }

    /**
     * @throws JsonException|RandomException
     */
    public function getInfo($sku, $country = null): void
    {
        if (empty($country)) {
            $country = $this->connector->mainCountry;
        }
        $safeSku = preg_replace('/[^a-zA-Z0-9._-]/', '_', $sku);
        $listing = $this->connector->getFromCache("LISTING_{$country}_{$safeSku}.json", 7*86400);
        if (empty($listing)) {
            $listingsApi = $this->connector->amazonSellerConnector->listingsItemsV20210801();
            $listing = $listingsApi->getListingsItem(
                sellerId: $this->connector->getMarketplace()->getMerchantId(),
                sku: rawurlencode($sku),
                marketplaceIds: [AmazonConstants::amazonMerchant[$country]['id']],
                includedData: ['summaries', 'attributes', 'issues', 'offers', 'fulfillmentAvailability', 'procurement']
            );
            $listing = $listing->json();
            $this->connector->putToCache("LISTING_{$country}_{$safeSku}.json", $listing);
        }
        $productType = $listing['summaries'][0]['productType'] ?? '';
        if (empty($productType)) { return; }

        $safeProductType = preg_replace('/[^a-zA-Z0-9._-]/', '_', $productType);
        $definition = $this->connector->getFromCache("PRODUCTTYPE_{$country}_{$safeProductType}.json");
        if (empty($definition)) {
            $productTypeDefinitionApi = $this->connector->amazonSellerConnector->productTypeDefinitionsV20200901();
            $definition = $productTypeDefinitionApi->getDefinitionsProductType(
                productType: $productType,
                marketplaceIds: [AmazonConstants::amazonMerchant[$country]['id']],
                sellerId: $this->connector->getMarketplace()->getMerchantId()
            );
            $definition = $definition->json();
            $this->connector->putToCache("PRODUCTTYPE_{$country}_{$safeProductType}.json", $definition);
        }
    }

    /**
     * @throws JsonException|RandomException
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
     * @throws JsonException|RandomException
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

    /**
     * @throws RandomException
     * @throws JsonException
     */
    public function patchSetEan($sku, $ean, $country = null): void
    {
        if (empty($country)) {
            $country = $this->connector->mainCountry;
        }

        $patches = [
            new PatchOperation(
                op: "delete",
                path: "/attributes/externally_assigned_product_identifier",
                value: [
                    [
                        "marketplace_id" => AmazonConstants::amazonMerchant[$country]['id'],
                        "type" => "ean",
                    ]
                ]
            ),
            new PatchOperation(
                op: "replace",
                path: "/attributes/externally_assigned_product_identifier",
                value: [
                    [
                        "marketplace_id" => AmazonConstants::amazonMerchant[$country]['id'],
                        "type" => "ean",
                        "value" => $ean
                    ]
                ]
            ),
            new PatchOperation(
                op: "delete",
                path: "/attributes/externally_assigned_product_identifier",
                value: [
                    [
                        "marketplace_id" => AmazonConstants::amazonMerchant[$country]['id'],
                        "type" => "upc",
                    ]
                ]
            )
        ];
        $this->patchCustom($sku, $country, $patches);
    }




}