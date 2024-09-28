<?php

namespace App\MarketplaceConnector;

use SellingPartnerApi\SellingPartnerApi;
use SellingPartnerApi\Enums\Endpoint;
use SellingPartnerApi\Seller\ReportsV20210630\Dto\CreateReportSpecification;

use Pimcore\Model\DataObject\Marketplace;
use Pimcore\Model\DataObject\Data\Link;
use Pimcore\Model\DataObject\VariantProduct;
use Pimcore\Model\DataObject\Fieldcollection\Data\AmazonMarketplace;
use Pimcore\Model\DataObject\Fieldcollection;
use Pimcore\Model\DataObject\Folder;

use App\Constants\AmazonConstants;
use App\Utils\Utility;

class AmazonConnector  extends MarketplaceConnectorAbstract
{
    public static $marketplaceType = 'Amazon';

    private array $amazonCountryReports = [
//        'GET_FLAT_FILE_OPEN_LISTINGS_DATA' => [],
        'GET_MERCHANT_LISTINGS_ALL_DATA' => [],
        //'GET_FBA_MYI_ALL_INVENTORY_DATA' => [],
                //'GET_AFN_INVENTORY_DATA' => [],
        //'GET_FBA_MYI_UNSUPPRESSED_INVENTORY_DATA' => [],
    ];

    private array $amazonReports = [
        'GET_FBA_MYI_ALL_INVENTORY_DATA' => [],
        'GET_AFN_INVENTORY_DATA_BY_COUNTRY' => [],
    ];

    private $amazonSellerConnector = null;
    private $countryCodes = [];
    private $mainCountry = null;

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

    protected function downloadAmazonReport($reportType, $forceDownload, $country): void
    {
        $marketplaceKey = urlencode(string: strtolower(string: $this->marketplace->getKey()));
        if (!in_array(needle: $reportType, haystack: array_keys($this->amazonCountryReports))) {
            throw new \Exception(message: "Report Type $reportType is not in reportNames in AmazonConnector class");
        }
        echo "        Downloading Report $reportType ";
        $report = Utility::getCustomCache(
            "{$reportType}_{$country}.csv", 
            PIMCORE_PROJECT_ROOT . "/tmp/marketplaces/{$marketplaceKey}"
        );
        if ($report === true && !$forceDownload) {
            echo "(Cached) ";
        } else {
            echo "Waiting Report ";
            $reportsApi = $this->amazonSellerConnector->reportsV20210630();
            $response = $reportsApi->createReport(new CreateReportSpecification($reportType, [AmazonConstants::amazonMerchantIdList[$country]]));
            $reportId = $response->json()['reportId'];
            while (true) {
                sleep(seconds: 10);
                echo ".";
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
        }
        if (substr(string: $report, offset: 0, length: 2) === "\x1f\x8b") {
            $report = gzdecode(data: $report);
        }
        if (substr(string: $report, offset: 0, length: 3) === "\xEF\xBB\xBF") {
            $report = substr(string: $report, offset: 3);
        }
        $this->amazonCountryReports[$reportType][$country] = $report;
    }

    public static function findUnboundAsins()
    {
        $asins = [];
        $listingObject = new VariantProduct\Listing();
        $listingObject->setUnpublished(true);
        $pageSize = 50;
        $offset = 0;
        while(true) {
            $listingObject->setLimit($pageSize);
            $listingObject->setOffset($offset);
            $variantProducts = $listingObject->load();
            $offset+=$pageSize;
            if (empty($variantProducts)) {
                break;
            }
            echo "Processing $offset\n";
            foreach ($variantProducts as $variantProduct) {
                if (!$variantProduct instanceof VariantProduct) {
                    echo "Not a variant product\n";
                    continue;
                }
                if (!$variantProduct->getMarketplace() instanceof Marketplace) {
                    echo "No marketplace\n";
                    continue;
                }
                if ($variantProduct->getMarketplace()->getMarketplaceType() !== 'Amazon') {
                    continue;
                }
                echo ".";
                foreach ($variantProduct->getAmazonMarketplace() as $amazonMarketplace) {
                    $activeId = $anyId = null;
                    $amazonMarketplaceId = $anyId = $amazonMarketplace->getMarketplaceId();
                    if (!$amazonMarketplace instanceof AmazonMarketplace) {
                        continue;
                    }
                    if ($amazonMarketplace->getStatus() === 'Active') {
                        $activeId = $amazonMarketplaceId;
                        if ($amazonMarketplaceId === 'US' || $amazonMarketplaceId === 'UK') {
                            break;
                        }
                    }
                }
                $country = $activeId ?? $anyId;
                if (in_array(needle: $country, haystack: ['MX', 'BR'])) {
                    $country = 'US';
                }
                if (in_array(needle: $country, haystack: ['ES', 'FR', 'IT', 'DE', 'NL', 'SE', 'PL', 'SA', 'EG', 'TR', 'AE', 'IN'])) {
                    $country = 'UK';
                }
                if (in_array(needle: $country, haystack: ['SG', 'AU', 'JP'])) {
                    $country = 'AU';
                }
                if (empty($country) || !in_array(needle: $country, haystack: ['US', 'UK', 'AU', 'CA'])) {
                    continue;
                }
                $asins[] = [
                    'asin' => $variantProduct->getUniqueMarketplaceId(),
                    'country' => $country,
                ];
            }
        }
        $asins = array_unique(array: $asins, flags: SORT_REGULAR);
        return $asins;
    }

    public static function downloadAsins(): void
    {
        echo "Finding unbound ASINs ";
        $asins = self::findUnboundAsins();
        echo count(value: $asins) . " ASINs found.\n";
        $connectors = [
            'US' => new AmazonConnector(marketplace: Marketplace::getById(149795)),
            'UK' => new AmazonConnector(marketplace: Marketplace::getById(200568)),
            'AU' => new AmazonConnector(marketplace: Marketplace::getById(200568)),
            'CA' => new AmazonConnector(marketplace: Marketplace::getById(234692)),
        ];
        $buckets = [
            'US' => [],
            'UK' => [],
            'AU' => [],
            'CA' => [],
        ];
        $missings = [];
        while (!empty($asins)) {
            $asin = array_pop($asins);
            $filename = PIMCORE_PROJECT_ROOT."/tmp/marketplaces/Amazon_ASIN_{$asin['asin']}.json";
            if (file_exists(filename: $filename) && filemtime(filename: $filename) > time() - 86400) {
                $item = json_decode(file_get_contents(filename: $filename), true);
                Utility::storeJsonData($connectors[$asin['country']]->marketplace->getId(), $item['asin'], $item);
                echo ".";
                continue;
            }
            $buckets[$asin['country']][] = $asin['asin'];
            if (count(value: $buckets[$asin['country']]) >= 10) {
                $missing = $connectors[$asin['country']]->downloadAmazonAsins(asins: $buckets[$asin['country']], country: $asin['country']);
                $missings = array_merge($missings, $missing);
                $buckets[$asin['country']] = [];
            }
        }
        foreach ($buckets as $country=>$asins) {
            if (!empty($asins)) {
                $missing = $connectors[$country]->downloadAmazonAsins(asins: $asins, country: $country);
                $missings = array_merge($missings, $missing);
            }
        }
        echo "********** Missing ASINs: ".implode(separator: ',', array: $missings)."\n";
    }

    public function downloadAmazonAsins($asins, $country)
    {
        $catalogApi = $this->amazonSellerConnector->catalogItemsV20220401();
        $response = $catalogApi->searchCatalogItems(
            marketplaceIds: [AmazonConstants::amazonMerchantIdList[$country]],
            identifiers: $asins,
            identifiersType: 'ASIN',
            includedData: ['attributes', 'classifications', 'dimensions', 'identifiers', 'images', 'productTypes', 'relationships', 'salesRanks', 'summaries'],
            sellerId: $this->marketplace->getMerchantId(),
        );
        sleep(seconds: 1);
        $items = $response->json()['items'] ?? [];
        $downloadedAsins = [];
        foreach ($items as $item) {
            $downloadedAsins[] = $item['asin'];
            Utility::setCustomCache(
                "{$item['asin']}.json", 
                PIMCORE_PROJECT_ROOT.'/tmp/marketplaces/ASINS', 
                json_encode($item)
            );
            Utility::storeJsonData($this->marketplace->getId(), $item['asin'], $item);
        }
        echo "Asked ".implode(separator: ',', array: $asins)."; downloaded ".implode(separator: ',', array: $downloadedAsins)." from {$country}\n";
        return array_diff($asins, $downloadedAsins);
    }

    public function download($forceDownload = false): void
    {
        foreach (array_merge([$this->mainCountry], $this->countryCodes) as $country) {
            echo "\n  Downloading Amazon reports for $country\n";
            foreach (array_keys($this->amazonCountryReports) as $reportType) {
                $this->downloadAmazonReport(reportType: $reportType, forceDownload: $forceDownload, country: $country);
            }
            $this->getListings(country: $country);
        }
        file_put_contents(filename: PIMCORE_PROJECT_ROOT.'/tmp/marketplaces/'.urlencode(string: $this->marketplace->getKey()).'_listings.json', data: json_encode(value: $this->listings));
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
                        granularityId: AmazonConstants::amazonMerchantIdList[$country],
                        marketplaceIds: [AmazonConstants::amazonMerchantIdList[$country]],
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

    public function getListings($country)
    {
        $listings = [];
        foreach ($this->amazonCountryReports as $reportType=>$report) {
            if (empty($report[$country])) {
                throw new \Exception("Report is empty. Did you first call download() method to populate reports?");
            }
            $lines = explode("\n", mb_convert_encoding($report[$country], 'UTF-8', 'UTF-8'));
            $header = str_getcsv(array_shift($lines), "\t");
            foreach ($lines as $line) {
                $data = str_getcsv($line, "\t");
                if (count($header) != count($data)) {
                    continue;
                }
                $listing = array_combine($header, $data);
                $listings[] = $listing;
            }
        }
        $this->listings[$country] =  $listings;
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
                        'urlLink' => $this->getUrlLink(AmazonConstants::amazonWebsites[$country].'/dp/' . ($listing['asin1'] ?? '')),
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
                $amazonCollection->setUrlLink($this->getUrlLink(AmazonConstants::amazonWebsites[$country].'/dp/' . ($listing['asin1'] ?? '')));
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
            $amazonCollection->setUrlLink($this->getUrlLink(AmazonConstants::amazonWebsites[$country].'/dp/' . ($listing['asin1'] ?? '')));
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
            marketplaceIds: [AmazonConstants::amazonMerchantIdList['MX']],
            sku: rawurlencode("09-JWOX-4994"),
            includedData: ['summaries', 'attributes', 'issues', 'offers', 'fulfillmentAvailability', 'procurement']
        );
        file_put_contents(PIMCORE_PROJECT_ROOT."/tmp/TESTlistingsItems_SKU.json", json_encode($listingItem->json()));
    }
}
