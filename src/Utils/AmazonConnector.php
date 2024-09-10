<?php

namespace App\Utils;

use SellingPartnerApi\SellingPartnerApi;
use SellingPartnerApi\Enums\Endpoint;
use SellingPartnerApi\Seller\ReportsV20210630\Dto\CreateReportSpecification;

use Pimcore\Model\DataObject\Marketplace;
use Pimcore\Model\DataObject\Data\Link;
use Pimcore\Model\DataObject\VariantProduct;
use Pimcore\Model\DataObject\Fieldcollection\Data\AmazonMarketplace;
use Pimcore\Model\DataObject\Fieldcollection;


use App\Select\AmazonMerchantIdList;
use App\Utils\Utility;

class AmazonConnector implements MarketplaceConnectorInterface
{
    private array $amazonReports = [
//        'GET_FLAT_FILE_OPEN_LISTINGS_DATA' => [],
        'GET_MERCHANT_LISTINGS_ALL_DATA' => [],
        //'GET_AFN_INVENTORY_DATA' => [],
        //'GET_AFN_INVENTORY_DATA_BY_COUNTRY' => [],
        //'GET_FBA_MYI_UNSUPPRESSED_INVENTORY_DATA' => [],
    ];

    private $amazonSellerConnector = null;
    private $countryCodes = [];
    private $mainCountry = null;
    private $marketplace = null;
    private $listings = [];

    public function __construct(Marketplace $marketplace)
    {
        if (!$marketplace instanceof Marketplace ||
            !$marketplace->getPublished() ||
            $marketplace->getMarketplaceType() !== 'Amazon' ||
            empty($marketplace->getClientId()) ||
            empty($marketplace->getClientSecret()) ||
            empty($marketplace->getRefreshToken())
        ) {
            throw new \Exception("Marketplace is not published, is not Amazon or credentials are empty");
        }

        $countryCodes = $marketplace->getMerchantIds();
        $missingCodes = array_diff($countryCodes, array_keys(AmazonMerchantIdList::$amazonMerchantIdList));
        if (!empty($missingCodes)) {
            $missingCodesStr = implode(', ', $missingCodes);
            throw new \Exception("The following country codes are not in merchantIdList in AmazonConnector class: $missingCodesStr");
        }

        $this->mainCountry = $marketplace->getMainMerchant();
        $endpoint = match ($this->mainCountry) {
            "CA","US","MX","BR" => Endpoint::NA,
            "SG","AU","JP" => Endpoint::FE,
            default => Endpoint::EU,
        };

        $this->marketplace = $marketplace;
        $this->countryCodes = $countryCodes;
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
        $marketplaceKey = urlencode(strtolower($this->marketplace->getKey()));
        if (!in_array($reportType, array_keys($this->amazonReports))) {
            throw new \Exception("Report Type $reportType is not in reportNames in AmazonConnector class");
        }
        echo "        Downloading Report $reportType ";
        $filename = PIMCORE_PROJECT_ROOT . "/tmp/marketplaces/{$marketplaceKey}_{$reportType}_{$country}.csv";
        
        if (!$forceDownload && file_exists($filename) && filemtime($filename) > time() - 86400) {
            $report = file_get_contents($filename);
            echo "Using cached data ";
        } else {
            echo "Waiting Report ";
            $reportsApi = $this->amazonSellerConnector->reportsV20210630();
            $response = $reportsApi->createReport(new CreateReportSpecification($reportType, [AmazonMerchantIdList::$amazonMerchantIdList[$country]]));
            $reportId = $response->json()['reportId'];
            while (true) {
                sleep(10);
                echo ".";
                $reportStatus = $reportsApi->getReport($reportId);
                $processingStatus = $reportStatus->json()['processingStatus'];
                if ($processingStatus == 'DONE') {
                    break;
                }
            }
            $reportUrl = $reportsApi->getReportDocument($reportStatus->json()['reportDocumentId'] , $reportStatus->json()['reportType']);
            $url = $reportUrl->json()['url'];
            $report = file_get_contents($url);
            if (substr($report, 0, 2) === "\x1f\x8b") {
                $report = gzdecode($report);
            }
            file_put_contents($filename, $report);
            echo "OK ";
        }
        if (substr($report, 0, 2) === "\x1f\x8b") {
            $report = gzdecode($report);
        }
        $this->amazonReports[$reportType][$country] = $report;
    }

    public function downloadAmazonSku($sku, $country)
    {
        echo " (download) ";
        $marketplaceKey = urlencode(strtolower($this->marketplace->getKey()));
        $catalogApi = $this->amazonSellerConnector->catalogItemsV20220401();
        //find at least 10 empty SKUs
        $identifiers = [rawurlencode($sku)];
        foreach ($this->listings[$country] as $sku=>$listing) {
            if (empty($listing)) {
                $identifiers[] = rawurlencode($sku);
            }
            if (count($identifiers) >= 9) {
                break;
            }
        }
        $response = $catalogApi->searchCatalogItems(
            marketplaceIds: [AmazonMerchantIdList::$amazonMerchantIdList[$country]],
            identifiers: $identifiers,
            identifiersType: 'SKU',
            includedData: ['attributes', 'classifications', 'dimensions', 'identifiers', 'images', 'productTypes', 'relationships', 'salesRanks', 'summaries'],
            sellerId: $this->marketplace->getMerchantId(),
        );
        //print_r($identifiers); exit;
        //echo json_encode($response->json());exit;
        sleep(1);
        $items = $response->json()['items'] ?? [];
        foreach ($items as $item) {
            foreach ($item['identifiers'][0]['identifiers'] ?? [] as $identifier) {
                if ($identifier['identifierType'] === 'SKU') {
                    $this->listings[$country][$identifier['identifier']] = $item;
                    file_put_contents(PIMCORE_PROJECT_ROOT . "/tmp/marketplaces/{$marketplaceKey}_{$identifier['identifier']}_{$country}.json", json_encode($item));
                }
            }
        }
        return $this->listings[$country][$sku];
    }

    public function download($forceDownload = false)
    {
        foreach (array_merge([$this->mainCountry], $this->countryCodes) as $country) {
            echo "\n  Downloading Amazon reports for $country\n";
            foreach (array_keys($this->amazonReports) as $reportType) {
                $this->downloadAmazonReport($reportType, $forceDownload, $country);
            }
            $this->getListings($country);
        }
        file_put_contents(PIMCORE_PROJECT_ROOT.'/tmp/marketplaces/'.urlencode($this->marketplace->getKey()).'_listings.json', json_encode($this->listings));
    }

    public function downloadOrders()
    {
        
    }

    public function downloadInventory()
    {
        foreach ($this->countryCodes as $country) {
            echo "\n    - $country ";
            $filename = PIMCORE_PROJECT_ROOT.'/tmp/marketplaces/'.urlencode($this->marketplace->getKey()).'_'.$country.'_inventory.json';
            if (file_exists($filename) && filemtime($filename) > time() - 86400) {
                echo " (cached) ";
                $allInventorySummaries = json_decode(file_get_contents($filename), true);
            } else {
                $inventoryApi = $this->amazonSellerConnector->fbaInventoryV1();
                $nextToken = null;
                $allInventorySummaries = [];
                do {
                    $response = $inventoryApi->getInventorySummaries(
                        granularityType: 'Marketplace',
                        granularityId: AmazonMerchantIdList::$amazonMerchantIdList[$country],
                        marketplaceIds: [AmazonMerchantIdList::$amazonMerchantIdList[$country]],
                        details: true,
                        nextToken: $nextToken
                    );
                    $responseData = $response->json();
                    $inventorySummaries = $responseData['payload']['inventorySummaries'] ?? [];
                    $allInventorySummaries = array_merge($allInventorySummaries, $inventorySummaries);
                    $nextToken = $responseData['pagination']['nextToken'] ?? null;
                    usleep(500000);
                    echo ".";
                } while ($nextToken);
                file_put_contents($filename, json_encode($allInventorySummaries));
            }

            $db = \Pimcore\Db::get();
            $db->beginTransaction();
            try {
                foreach ($allInventorySummaries as $inventory) {
                    $sql = "INSERT INTO iwa_amazon_inventory (";
                    $dbFields = [];
                    foreach ($inventory as $key=>$value) {
                        if (is_array($value)) {
                            $value = json_encode($value);
                        }
                        if ($key === 'condition') {
                            $key = 'itemCondition';
                        }
                        $dbFields[$key] = $value;
                    }
                    $dbFields['countryCode'] = $country;
                    $sql .= implode(',', array_keys($dbFields)) . ") VALUES (";
                    $sql .= implode(',', array_fill(0, count($dbFields), '?')) . ")";
                    $sql .= " ON DUPLICATE KEY UPDATE ";
                    $sql .= implode(',', array_map(function($key) {
                        return "$key=?";
                    }, array_keys($dbFields)));
                    $stmt = $db->prepare($sql);
                    $stmt->execute(array_merge(array_values($dbFields), array_values($dbFields)));
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
        foreach ($this->amazonReports as $reportType=>$report) {
            if (empty($report[$country])) {
                throw new \Exception("Report is empty. Did you first call download() method to populate reports?");
            }
            $lines = explode("\n", $report[$country]);
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

    private function getImage($listing) {
        $image = $listing['summaries'][0]['mainImage']['link'] ?? '';
        if (!empty($image)) {
            return new \Pimcore\Model\DataObject\Data\ExternalImage($image);
        }
        return null;
    }

    private function getUrlLink($listing, $country) {
        $amazonWebsites = [
            'CA' => 'https://www.amazon.ca',       // Canada
            'US' => 'https://www.amazon.com',      // United States
            'MX' => 'https://www.amazon.com.mx',   // Mexico
            'BR' => 'https://www.amazon.com.br',   // Brazil
            'ES' => 'https://www.amazon.es',       // Spain
            'UK' => 'https://www.amazon.co.uk',    // United Kingdom
            'FR' => 'https://www.amazon.fr',       // France
            'NL' => 'https://www.amazon.nl',       // Netherlands
            'BE' => 'https://www.amazon.com.be',   // Belgium
            'DE' => 'https://www.amazon.de',       // Germany
            'IT' => 'https://www.amazon.it',       // Italy
            'SE' => 'https://www.amazon.se',       // Sweden
            //'' => '',      // South Africa
            'PL' => 'https://www.amazon.pl',       // Poland
            'SA' => 'https://www.amazon.sa',       // Saudi Arabia
            'EG' => 'https://www.amazon.eg',       // Egypt
            'TR' => 'https://www.amazon.com.tr',   // Turkey
            'AE' => 'https://www.amazon.ae',       // United Arab Emirates
            'IN' => 'https://www.amazon.in',       // India
            'SG' => 'https://www.amazon.sg',       // Singapore
            'AU' => 'https://www.amazon.com.au',   // Australia
            'JP' => 'https://www.amazon.co.jp',    // Japan
        ];
        
        $l = new Link();
        $l->setPath($amazonWebsites[$country].'/dp/' . ($listing['asin1'] ?? ''));
        return $l;
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
        $title = str_replace('('.$this->getAttributes($listing).')', '', $listing['item-name'] ?? '');
        return trim($title);
    }

    public function import($updateFlag, $importFlag)
    {
        $marketplaceFolder = Utility::checkSetPath(
            "Amazon",
            Utility::checkSetPath('Pazaryerleri')
        );

        foreach (array_merge([$this->mainCountry], $this->countryCodes) as $country) {
            if (empty($this->listings[$country])) {
                echo "Nothing to import in $country\n";
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

                // find a VariantProduct with ASIN
                // if not found, create a new one
                // if found, add our listing as data collection

                $asin = $listing['asin1'] ?? '';
                if (empty($asin)) {
                    echo " Empty ASIN\n";
                    continue;
                }
                $variantProduct = VariantProduct::findOneByField('uniqueMarketplaceId', $asin);
                if (!$variantProduct) {
                    $variantProduct = VariantProduct::addUpdateVariant(
                        variant: [
                            'imageUrl' => null,
                            'urlLink' => $this->getUrlLink($listing, $country),
                            'salePrice' => 0,
                            'saleCurrency' => '',
                            'title' => $this->getTitle($listing),
                            'attributes' => $this->getAttributes($listing),
                            'amazonAsin' => $asin,
                            'uniqueMarketplaceId' => $asin,
                            'apiResponseJson' => json_encode([]),
                            'published' => true,
                        ],
                        importFlag: $importFlag,
                        updateFlag: $updateFlag,
                        marketplace: $this->marketplace,
                        parent: Utility::checkSetPath('00 Yeni ASIN', $marketplaceFolder)
                    );
                }
                $this->processFieldCollection($variantProduct, $listing, $country);
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
        foreach ($collection ?? [] as $amazonCollection) {
            if (!$amazonCollection instanceof AmazonMarketplace) {
                continue;
            }
            if ($amazonCollection->getListingId() === $listing['listing-id']) {
                $found = true;
                $amazonCollection->setMarketplaceId($country);
                $amazonCollection->setTitle($this->getTitle($listing));
                $amazonCollection->setUrlLink($this->getUrlLink($listing, $country));
                $amazonCollection->setSalePrice($listing['price'] ?? 0);
                $amazonCollection->setSku($listing['seller-sku'] ?? '');
                $amazonCollection->setQuantity((int)($listing['quantity'] ?? 0)+0);
                $amazonCollection->setStatus($listing['status'] ?? '');
                $amazonCollection->setFulfillmentChannel($listing['fulfillment-channel'] ?? '');
            }
            $newCollection->add($amazonCollection);
        }
        if (!$found) {
            $amazonCollection = new AmazonMarketplace();
            $amazonCollection->setMarketplaceId($country);
            $amazonCollection->setTitle($this->getTitle($listing));
            $amazonCollection->setUrlLink($this->getUrlLink($listing, $country));
            $amazonCollection->setSalePrice($listing['price'] ?? 0);
            $amazonCollection->setSku($listing['seller-sku'] ?? '');
            $amazonCollection->setListingId($listing['listing-id'] ?? '');
            $amazonCollection->setQuantity((int)($listing['quantity'] ?? 0)+0);
            $amazonCollection->setStatus($listing['status'] ?? '');
            $amazonCollection->setFulfillmentChannel($listing['fulfillment-channel'] ?? '');
            $newCollection->add($amazonCollection);
        }
        $variantProduct->setAmazonMarketplace($newCollection);
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
            marketplaceIds: [AmazonMerchantIdList::$amazonMerchantIdList['MX']],
            sku: rawurlencode("09-JWOX-4994"),
            includedData: ['summaries', 'attributes', 'issues', 'offers', 'fulfillmentAvailability', 'procurement']
        );
        file_put_contents(PIMCORE_PROJECT_ROOT."/tmp/TESTlistingsItems_SKU.json", json_encode($listingItem->json()));
    }
}
