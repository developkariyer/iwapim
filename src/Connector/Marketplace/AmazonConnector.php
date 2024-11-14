<?php

namespace App\Connector\Marketplace;

use SellingPartnerApi\SellingPartnerApi;
use SellingPartnerApi\Enums\Endpoint;
use SellingPartnerApi\Seller\ReportsV20210630\Dto\CreateReportSpecification;
use SellingPartnerApi\Seller\ListingsItemsV20210801\Dto\ListingsItemPatchRequest;

use Pimcore\Model\DataObject\Marketplace;
use Pimcore\Model\DataObject\Product;
use Pimcore\Model\DataObject\VariantProduct;
use Pimcore\Model\DataObject\Fieldcollection\Data\AmazonMarketplace;
use Pimcore\Model\DataObject\Fieldcollection;
use Pimcore\Model\DataObject\Folder;

use App\Connector\Marketplace\AmazonConstants;
use App\Utils\Utility;

class AmazonConnector extends MarketplaceConnectorAbstract
{
    public static $marketplaceType = 'Amazon';

    private array $amazonCountryReports = [
        'GET_MERCHANT_LISTINGS_ALL_DATA' => [],
    ];

    private array $amazonReports = [
        'GET_MERCHANT_LISTINGS_ALL_DATA' => [],
/*        'GET_FBA_MYI_ALL_INVENTORY_DATA' => [],
        'GET_AFN_INVENTORY_DATA_BY_COUNTRY' => [],
        'GET_FLAT_FILE_ALL_ORDERS_DATA_BY_LAST_UPDATE_GENERAL' => [],
        'GET_FLAT_FILE_RETURNS_DATA_BY_RETURN_DATE' => [],
        'GET_SELLER_FEEDBACK_DATA' => [],*/
    ];

    private $amazonSellerConnector = null;
    private $countryCodes = [];
    private $mainCountry = null;
    private $asinBucket = [];
    private $iwaskuList = [];

    public function __construct(Marketplace $marketplace) 
    {
        parent::__construct($marketplace);

        $this->countryCodes = $marketplace->getMerchantIds() ?? [];
        if (!AmazonConstants::checkCountryCodes($this->countryCodes)) {
            throw new \Exception("Country codes are not valid");
        }
        $this->mainCountry = $marketplace->getMainMerchant();
        $endpoint = match ($this->mainCountry) {
            "CA", "US", "MX", "BR" => Endpoint::NA,
            "SG", "AU", "JP", "IN" => Endpoint::FE,
            "UK", "FR", "DE", "IT", "ES", "NL", "SE", "PL", "TR", "SA", "AE", "EG" => Endpoint::EU,
            default => Endpoint::NA,
        };
        $this->amazonSellerConnector = SellingPartnerApi::seller(
            clientId: $marketplace->getClientId(),
            clientSecret: $marketplace->getClientSecret(),
            refreshToken: $marketplace->getRefreshToken(),
            endpoint: $endpoint
        );
        if (!$this->amazonSellerConnector) {
            throw new \Exception("Amazon Seller Connector is not created");
        }
    }

    protected function downloadAmazonReport($reportType, $forceDownload, $country)
    {
        $marketplaceKey = urlencode( $this->marketplace->getKey());
        echo "        Downloading Report $reportType ";
        $report = Utility::getCustomCache(
            "{$reportType}_{$country}.csv", 
            PIMCORE_PROJECT_ROOT . "/tmp/marketplaces/{$marketplaceKey}"
        );
        if ($report === false || $forceDownload) {
            echo "Waiting Report ";
            $reportsApi = $this->amazonSellerConnector->reportsV20210630();
            $response = $reportsApi->createReport(new CreateReportSpecification($reportType, [AmazonConstants::amazonMerchant[$country]['id']]));
            $reportId = $response->json()['reportId'];
            while (true) {
                sleep(10);
                $reportStatus = $reportsApi->getReport($reportId);
                $processingStatus = $reportStatus->json()['processingStatus'];
                if ($processingStatus == 'DONE') {
                    break;
                }
            }
            $reportUrl = $reportsApi->getReportDocument($reportStatus->json()['reportDocumentId'] , $reportStatus->json()['reportType']);
            $url = $reportUrl->json()['url'];
            $report = file_get_contents(filename: $url);
            if (substr(string: $report, offset: 0, length: 2) === "\x1f\x8b") {
                $report = gzdecode(data: $report);
            }
            Utility::setCustomCache(
                "{$reportType}_{$country}.csv",
                PIMCORE_PROJECT_ROOT . "/tmp/marketplaces/{$marketplaceKey}",
                $report
            );
            echo "OK ";
        } else {
            echo "Cached ";
        }
        if (substr(string: $report, offset: 0, length: 2) === "\x1f\x8b") {
            $report = gzdecode(data: $report);
        }
        if (substr(string: $report, offset: 0, length: 3) === "\xEF\xBB\xBF") {
            $report = substr(string: $report, offset: 3);
        }
        return $report;
    }

    protected function downloadAllReports($forceDownload)
    {
        foreach (array_keys($this->amazonReports) as $reportType) {
            echo "\n  Downloading {$reportType} for main Amazon region {$this->mainCountry}\n";
            $this->amazonReports[$reportType] = $this->downloadAmazonReport(reportType: $reportType, forceDownload: $forceDownload, country: $this->mainCountry);
        }
        foreach ($this->countryCodes as $country) {
            foreach (array_keys($this->amazonCountryReports) as $reportType) {
                echo "\n  Downloading {$reportType} for Amazon region $country\n";
                $this->amazonCountryReports[$reportType][$country] = $this->downloadAmazonReport(reportType: $reportType, forceDownload: $forceDownload, country: $country);
            }
        }
    }

    protected function downloadAsinsInBucket()
    {
        if (empty($this->asinBucket)) {
            return;
        }
        $catalogApi = $this->amazonSellerConnector->catalogItemsV20220401();
        $response = $catalogApi->searchCatalogItems(
            marketplaceIds: [AmazonConstants::amazonMerchant[$this->mainCountry]['id']],
            identifiers: array_keys($this->asinBucket),
            identifiersType: 'ASIN',
            includedData: ['attributes', 'classifications', 'dimensions', 'identifiers', 'images', 'productTypes', 'relationships', 'salesRanks', 'summaries'],
            sellerId: $this->marketplace->getMerchantId(),
        );
        $this->asinBucket = [];
        $items = $response->json()['items'] ?? [];
        foreach ($items as $item) {
            $asin = $item['asin'] ?? '';
            $this->listings[$asin]['catalog'] = $item;
            Utility::setCustomCache("ASIN_{$asin}.json", PIMCORE_PROJECT_ROOT . "/tmp/marketplaces/".urlencode($this->marketplace->getKey()), json_encode($item));
            Utility::storeJsonData($this->marketplace->getId(), $asin, $item);
        }
        sleep(1);
    }

    protected function addToAsinBucket($asin, $forceDownload = false)
    {
        $item = Utility::getCustomCache("ASIN_{$asin}.json", PIMCORE_PROJECT_ROOT . "/tmp/marketplaces/".urlencode($this->marketplace->getKey()));
        $item = json_decode($item, true);
        if (empty($item) || $forceDownload) {
            $this->asinBucket[$asin] = 1;
            if (count($this->asinBucket) >= 10) {
                $this->downloadAsinsInBucket();
            }        
        } else {
            $this->listings[$asin]['catalog'] = $item;
            Utility::storeJsonData($this->marketplace->getId(), $asin, $item);
        }
    }

    protected function processListingReport($country, $report)
    {
        $lines = explode("\n", mb_convert_encoding(trim($report), 'UTF-8', 'UTF-8'));
        $header = str_getcsv(array_shift($lines), "\t");
        foreach ($lines as $line) {
            $data = str_getcsv($line, "\t");
            if (count($header) == count($data)) {
                $rowData = array_combine($header, $data);
                $asin = $rowData['asin1'] ?? '';
                if (empty($this->listings[$asin][$country])) {
                    if (empty($this->listings[$asin])) {
                        $this->listings[$asin] = [];
                    }
                    $this->listings[$asin][$country] = [];  // Initialize country array
                }
                $this->listings[$asin][$country][] = $rowData;
            }
        }
    }
    
    public function getListings($forceDownload = false)
    {
        $this->processListingReport($this->mainCountry, $this->amazonReports['GET_MERCHANT_LISTINGS_ALL_DATA']);
        foreach ($this->countryCodes as $country) {
            $this->processListingReport($country, $this->amazonCountryReports['GET_MERCHANT_LISTINGS_ALL_DATA'][$country]);
        }

        $totalCount = count($this->listings);
        $index = 0;
        foreach ($this->listings as $asin=>$listing) {
            $index++; /*
            if (empty($listing[$this->mainCountry])) {
                continue;
            }*/
            echo "($index/$totalCount) Downloading $asin ...\n";
            $this->addToAsinBucket($asin, $forceDownload);
        }
        $this->downloadAsinsInBucket();
    }

    public function download($forceDownload = false): void
    {
        $this->listings = json_Decode(Utility::getCustomCache("LISTINGS.json", PIMCORE_PROJECT_ROOT . "/tmp/marketplaces/".urlencode($this->marketplace->getKey())), true);
        if (empty($this->listings) || $forceDownload) {
            $this->downloadAllReports($forceDownload);
            $this->getListings($forceDownload);
            Utility::setCustomCache("LISTINGS.json", PIMCORE_PROJECT_ROOT . "/tmp/marketplaces/".urlencode($this->marketplace->getKey()), json_encode($this->listings));
        }
        foreach ($this->listings as $asin=>$listing) {
            Utility::setCustomCache("{$asin}.json", PIMCORE_PROJECT_ROOT . "/tmp/marketplaces/tmp/".urlencode($this->marketplace->getKey()), json_encode($listing));
        }
    }

    private function getAttributes($listing) {
        $title = $listing['item-name'];
        if (preg_match('/\(([^()]*)\)[^\(]*$/', $title, $matches)) {
            return trim($matches[1]);
        }
        return '';    
    }

    private function getTitle($listing)
    {
        return trim(str_replace('('.$this->getAttributes($listing).')','',$listing['item-name'] ?? ''));
    }

    private function getFolder($asin): Folder
    {
        $folder = Utility::checkSetPath("Amazon", Utility::checkSetPath('Pazaryerleri'));

        $json = Utility::retrieveJsonData($asin);
        if (!empty($json) && !empty($json['classifications'][0]['classifications'][0]['displayName'])) {
            $folderTree = [];
            $parent = $json['classifications'][0]['classifications'][0];
            while (!empty($parent['displayName'])) {
                if (!in_array($parent['displayName'], ['Categories', 'Subjects', 'Departments'])) {
                    $folderTree[] = $parent['displayName'];
                }
                $parent = $parent['parent'] ?? [];
            }
            while (!empty($folderTree)) {
                $folder = Utility::checkSetPath(array_pop($folderTree), $folder);
            }
            return $folder;
        }
        return Utility::checkSetPath(
            '00 Yeni ASIN',
            $folder
        );
    }

    protected function checkIwasku($iwasku)
    {
        if (empty($this->iwaskuList)) {
            $db = \Pimcore\Db::get();
            $this->iwaskuList = $db->fetchFirstColumn("SELECT DISTINCT iwasku FROM object_store_product WHERE iwasku IS NOT NULL ORDER BY iwasku");
            $this->iwaskuList = array_filter( $this->iwaskuList);
        }
        return in_array($iwasku, $this->iwaskuList);
    }

    public function import($updateFlag, $importFlag)
    {
        if (empty($this->listings)) {
            echo "Nothing to import in {$this->mainCountry}\n";
            return;
        } else {
            echo "Importing {$this->mainCountry}\n";
        }
        $total = count($this->listings);
        $index = 0;
        foreach ($this->listings as $asin=>$listing) {
            $index++;
            if (empty($listing) || empty($listing[$this->mainCountry]) || !is_array($listing[$this->mainCountry])) {
                echo "($index/$total) $asin is empty in {$this->mainCountry}\n";
                continue;
            }
            echo "($index/$total) Processing $asin ...";
            $mainListings = $listing[$this->mainCountry];
            $mainListing = reset($mainListings);
            $variantProduct = VariantProduct::addUpdateVariant(
                variant: [
                    'imageUrl' => null,
                    'urlLink' => $this->getUrlLink(AmazonConstants::amazonMerchant[$this->mainCountry]['url']."/dp/$asin"),
                    'salePrice' => 0,
                    'saleCurrency' => '',
                    'title' => $this->getTitle($mainListing),
                    'attributes' => $this->getAttributes($mainListing),
                    'uniqueMarketplaceId' => $asin,
                    'apiResponseJson' => json_encode($listing),
                    'published' => true,
                ],
                importFlag: $importFlag,
                updateFlag: $updateFlag,
                marketplace: $this->marketplace,
                parent: $this->getFolder($asin),
            );
            $mainProduct = $variantProduct->getMainProduct();
            $skuRequired = empty($mainProduct) ? true : false;
            foreach ($listing as $country=>$countryListings) {
                if ($country === 'catalog') {
                    continue;
                }
                foreach ($countryListings as $countryListing) {
                    echo "$country ";
                    $this->processFieldCollection($variantProduct, $countryListing, $country);
                    if ($skuRequired) {
                        $sku = explode('_', $countryListing['seller-sku'] ?? '')[0] ?? '';
                        if ($this->checkIwasku($sku)) {
                            $mainProduct = Product::getByIwasku($sku, ['limit' => 1]);
                            if ($mainProduct instanceof Product) {
                                echo "Adding variant {$variantProduct->getId()} to {$mainProduct->getId()} ";
                                if ($mainProduct->addVariant($variantProduct)) {
                                    $skuRequired = false;
                                }
                            }
                        }
                    }
                }
            }
            echo "{$variantProduct->getId()} ";
            echo " OK\n";
        }
    }

    protected function processFieldCollection($variantProduct, $listing, $country)
    {
        $collection = $variantProduct->getAmazonMarketplace();
        $newCollection = new Fieldcollection();
        $found = false;
        $active = ($listing['status'] ?? '') === 'Active';
        foreach ($collection ?? [] as $amazonCollection) {
            if (!$amazonCollection instanceof AmazonMarketplace) {
                continue;
            }
            if ($amazonCollection->getListingId() === $listing['listing-id']) {
                $found = true;
                $amazonCollection->setMarketplaceId($country);
                $amazonCollection->setTitle($this->getTitle($listing));
                $amazonCollection->setUrlLink($this->getUrlLink(AmazonConstants::amazonMerchant[$country]['url'].'/dp/' . ($listing['asin1'] ?? '')));
                $amazonCollection->setSalePrice($listing['price'] ?? 0);
                $amazonCollection->setSaleCurrency(AmazonConstants::getAmazonSaleCurrency($country));
                $amazonCollection->setSku($listing['seller-sku'] ?? '');
                $amazonCollection->setQuantity((int)($listing['quantity'] ?? 0)+0);
                $amazonCollection->setMarketplace($this->marketplace);
                $amazonCollection->setStatus($listing['status'] ?? '');
                $amazonCollection->setFulfillmentChannel($listing['fulfillment-channel'] ?? '');
            }
            if ($amazonCollection->getStatus() === 'Active') {
                $active = true;
            }
            $newCollection->add($amazonCollection);
        }
        if (!$found) {
            $amazonCollection = new AmazonMarketplace();
            $amazonCollection->setMarketplaceId($country);
            $amazonCollection->setTitle($this->getTitle($listing));
            $amazonCollection->setUrlLink($this->getUrlLink(AmazonConstants::amazonMerchant[$country]['url'].'/dp/' . ($listing['asin1'] ?? '')));
            $amazonCollection->setSalePrice($listing['price'] ?? 0);
            $amazonCollection->setSaleCurrency(AmazonConstants::getAmazonSaleCurrency($country));
            $amazonCollection->setSku($listing['seller-sku'] ?? '');
            $amazonCollection->setListingId($listing['listing-id'] ?? '');
            $amazonCollection->setMarketplace($this->marketplace);
            $amazonCollection->setQuantity((int)($listing['quantity'] ?? 0)+0);
            $amazonCollection->setStatus($listing['status'] ?? '');
            $amazonCollection->setFulfillmentChannel($listing['fulfillment-channel'] ?? '');
            $newCollection->add($amazonCollection);
        }
        $variantProduct->setAmazonMarketplace($newCollection);
        if ($active) {
            $variantProduct->setPublished(true);
        } else {
            $variantProduct->setPublished(false);
            $variantProduct->setKey(Utility::sanitizeVariable('(Parent or Inactive) '.$variantProduct->getKey().uniqid(), 250));
        }
        $variantProduct->save();
    }

    public function catalogItems()
    {/*
        $catalogConnector = $this->amazonSellerConnector->catalogItemsV20220401();
        foreach (array_merge([$this->mainCountry], $this->countryCodes) as $country) {
            $response = $catalogConnector->searchCatalogItems(
                marketplaceIds: [AmazonMerchantIdList::$amazonMerchantIdList[$country]],
                identifiers: ['09-JWOX-4994'],
                identifiersType: 'SKU',
                includedData: ['attributes', 'classifications', 'dimensions', 'identifiers', 'images', 'productTypes', 'relationships', 'salesRanks', 'summaries'],
                sellerId: $this->marketplace->getMerchantId(),
            );
            file_put_contents(PIMCORE_PROJECT_ROOT."/tmp/TESTcatalogItems_SKU_$country.json", json_encode($response->json()));
            echo "$country OK\n";
            sleep(1); 
        }
        foreach (array_merge([$this->mainCountry], $this->countryCodes) as $country) {
            $response = $catalogConnector->searchCatalogItems(
                marketplaceIds: [AmazonMerchantIdList::$amazonMerchantIdList[$country]],
                identifiers: ['B08B5BJMR5'],
                identifiersType: 'ASIN',
                includedData: ['attributes', 'classifications', 'dimensions', 'identifiers', 'images', 'productTypes', 'relationships', 'salesRanks', 'summaries'],
                sellerId: $this->marketplace->getMerchantId(),
            );
            file_put_contents(PIMCORE_PROJECT_ROOT."/tmp/TESTcatalogItems_ASIN_$country.json", json_encode($response->json()));
            echo "$country OK\n";    
            sleep(1); 
        }*/
        $listingsApi = $this->amazonSellerConnector->listingsItemsV20210801();
        $listingItem = $listingsApi->getListingsItem(
            sellerId: $this->marketplace->getMerchantId(),
            marketplaceIds: [AmazonConstants::amazonMerchant['MX']['id']],
            sku: rawurlencode("09-JWOX-4994"),
            includedData: ['summaries', 'attributes', 'issues', 'offers', 'fulfillmentAvailability', 'procurement']
        );
        file_put_contents(PIMCORE_PROJECT_ROOT."/tmp/TESTlistingsItems_SKU.json", json_encode($listingItem->json()));
    }

    public function downloadOrders(): void
    {
        $db = \Pimcore\Db::get();
        $lastUpdateAt = $this->getLatestOrderUpdate();
        echo "Last Update: $lastUpdateAt\n";
        /*$ordersApi = $this->amazonSellerConnector->ordersV0();
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

    private function getAllMarketplaceIds($asArray = false)
    {
        $ids = array_map(function($country) {
            return AmazonConstants::amazonMerchant[$country]['id'];
        }, $this->countryCodes);

        return $asArray ? $ids : implode(',', $ids);
    }
    
    public function patchListing($sku)
    {
        echo "Getting details for SKU $sku\n";
        $listingsApi = $this->amazonSellerConnector->listingsItemsV20210801();
        $listing = $listingsApi->getListingsItem(
            sellerId: $this->marketplace->getMerchantId(),
            marketplaceIds: [AmazonConstants::amazonMerchant[$this->mainCountry]['id']],
            sku: rawurlencode($sku),
            includedData: ['summaries', 'attributes', 'issues', 'offers', 'fulfillmentAvailability', 'procurement']
        );
        file_put_contents(PIMCORE_PROJECT_ROOT."/tmp/TESTlistingsItems_SKU.json", json_encode($listing->json()));

        $productType = $listing->json()['summaries'][0]['productType'] ?? '';

        if (empty($productType)) { return; }
        echo "Getting definitions for product type $productType\n";
        $productTypeDefinitionsApi = $this->amazonSellerConnector->productTypeDefinitionsV20200901();
        $definitions = $productTypeDefinitionsApi->getDefinitionsProductType(
            marketplaceIds: [AmazonConstants::amazonMerchant[$this->mainCountry]['id']],
            sellerId: $this->marketplace->getMerchantId(),
            productType: $productType
        );
        file_put_contents(PIMCORE_PROJECT_ROOT."/tmp/TESTproductTypeDefinitions.json", json_encode($definitions->json()));

        $patches = [
            [
                "op" => "replace",
                "path" => "/propertyGroups/safety_and_compliance/gpsr_safety_attestation",
                "value" => true,
            ],
            [
                "op" => "replace",
                "path" => "/propertyGroups/safety_and_compliance/dsa_responsible_party_address",
                "value" => "responsible@iwaconcept.com",
            ],
        ];

        echo "Patching SKU $sku\n";
        $patch = $listingsApi->patchListingsItem(
            sellerId: $this->marketplace->getMerchantId(),
            sku: rawurlencode($sku),
            marketplaceIds: [AmazonConstants::amazonMerchant[$this->mainCountry]['id']],
            listingsItemPatchRequest: new ListingsItemPatchRequest($productType, json_decode(json_encode($patches)))
        );
        file_put_contents(PIMCORE_PROJECT_ROOT."/tmp/TESTpatchListingsItem_SKU.json", json_encode($patch->json()));
        print_r($patch->json());
    }

}
