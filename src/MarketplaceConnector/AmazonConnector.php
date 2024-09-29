<?php

namespace App\MarketplaceConnector;

use SellingPartnerApi\SellingPartnerApi;
use SellingPartnerApi\Enums\Endpoint;
use SellingPartnerApi\Seller\ReportsV20210630\Dto\CreateReportSpecification;

use Pimcore\Model\DataObject\Marketplace;
use Pimcore\Model\DataObject\VariantProduct;
use Pimcore\Model\DataObject\Fieldcollection\Data\AmazonMarketplace;
use Pimcore\Model\DataObject\Fieldcollection;
use Pimcore\Model\DataObject\Folder;

use App\MarketplaceConnector\AmazonConstants;
use App\Utils\Utility;

class AmazonConnector extends MarketplaceConnectorAbstract
{
    public static $marketplaceType = 'Amazon';

    private array $amazonCountryReports = [
        'GET_MERCHANT_LISTINGS_ALL_DATA' => [],
    ];

    private array $amazonReports = [
        'GET_MERCHANT_LISTINGS_ALL_DATA' => [],
        'GET_FBA_MYI_ALL_INVENTORY_DATA' => [],
        'GET_AFN_INVENTORY_DATA_BY_COUNTRY' => [],
    ];

    private $amazonSellerConnector = null;
    private $countryCodes = [];
    private $mainCountry = null;
    private $asinBucket = [];

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
        }
    }

    public function getListings($forceDownload = false)
    {
        $lines = explode("\n", mb_convert_encoding(trim($this->amazonReports['GET_MERCHANT_LISTINGS_ALL_DATA']), 'UTF-8', 'UTF-8'));
        $header = str_getcsv(array_shift($lines), "\t");
        $this->listings = [];
        $totalCount = count($lines);
        $index = 0;
        foreach ($lines as $line) {
            $index++;
            $data = str_getcsv($line, "\t");
            if (count($header) == count($data)) {
                $rowData = array_combine($header, $data);
                $asin = $rowData['asin1'] ?? '';
                echo "($index/$totalCount) Downloading $asin ... ";
                if (empty($listings[$asin][$this->mainCountry])) {
                    if (empty($this->listings[$asin])) {
                        $this->listings[$asin] = [];
                    }
                    $this->listings[$asin][$this->mainCountry] = [];
                }
                $this->listings[$asin][$this->mainCountry][] = $rowData;
                $this->addToAsinBucket($asin, $forceDownload);
                echo "OK\n";
            }
        }
        $this->downloadAsinsInBucket();
    }

    public function download($forceDownload = false): void
    {
        $this->listings = json_Decode(Utility::getCustomCache("AMAZON_LISTINGS.json", PIMCORE_PROJECT_ROOT . "/tmp/marketplaces/".urlencode($this->marketplace->getKey())), true);
        if (empty($this->listings) || $forceDownload) {
            $this->downloadAllReports($forceDownload);
            $this->getListings($forceDownload);
            Utility::setCustomCache("AMAZON_LISTINGS.json", PIMCORE_PROJECT_ROOT . "/tmp/marketplaces/".urlencode($this->marketplace->getKey()), json_encode($this->listings));
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
                $folderTree[] = $parent['displayName'];
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

    public function import($updateFlag, $importFlag)
    {
        foreach (array_merge([$this->mainCountry], $this->countryCodes) as $country) {
            if (empty($this->listings[$country])) {
                echo "Nothing to import in $country\n";
            } else {
                echo "Importing $country\n";
            }
            $total = count($this->listings[$country]);
            $index = 0;
            foreach ($this->listings[$country] as $listing) {
                $index++;
                echo "($index/$total) Processing id {$listing['listing-id']} ...";
                if (empty($listing)) {
                    echo " Empty\n";
                    continue;
                }
                $asin = $listing['asin1'] ?? '';
                if (empty($asin)) {
                    echo " Empty ASIN\n";
                    continue;
                }
                $variantProduct = VariantProduct::addUpdateVariant(
                    variant: [
                        'imageUrl' => null,
                        'urlLink' => $this->getUrlLink(AmazonConstants::amazonMerchant[$country]['url'].'/dp/' . ($listing['asin1'] ?? '')),
                        'salePrice' => 0,
                        'saleCurrency' => '',
                        'title' => $this->getTitle($listing),
                        'attributes' => $this->getAttributes($listing),
                        'uniqueMarketplaceId' => $asin,
                        'apiResponseJson' => json_encode([]),
                        'published' => true,
                    ],
                    importFlag: $importFlag,
                    updateFlag: $updateFlag,
                    marketplace: null,
                    parent: $this->getFolder($asin),
                );
                if (empty($variantProduct->getMarketplace())) {
                    $variantProduct->setMarketplace($this->marketplace);
                    $variantProduct->save();
                }
                $this->processFieldCollection(variantProduct: $variantProduct, listing: $listing, country: $country);
                echo $variantProduct->getId();
                echo " OK\n";
            }
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
            $variantProduct->setKey(Utility::sanitizeVariable('(Parent or Inactive) '.$variantProduct->getKey(), 250));
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

}
