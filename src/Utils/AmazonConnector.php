<?php

namespace App\Utils;

use SellingPartnerApi\SellingPartnerApi;
use SellingPartnerApi\Enums\Endpoint;
use SellingPartnerApi\Seller\ReportsV20210630\Dto\CreateReportSpecification;

use Pimcore\Model\DataObject\Marketplace;
use Pimcore\Model\DataObject\Data\Link;
use Pimcore\Model\DataObject\VariantProduct;

use App\Select\AmazonMerchantIdList;
use App\Utils\Utility;

class AmazonConnector implements MarketplaceConnectorInterface
{
    private array $amazonReports = [
        'GET_FLAT_FILE_OPEN_LISTINGS_DATA' => [],
        'GET_MERCHANT_LISTINGS_ALL_DATA' => [],
    ];

    private $amazonSellerConnector = null;
    private $countryCodes = [];
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

        $endpoint = match ($countryCodes[0]) {
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
        print_r($countryCodes);

        $fbaInventory = $this->amazonSellerConnector->fbaInventoryV1();
        $nextToken = null;
        $allInventorySummaries = [];
        do {
            $response = $fbaInventory->getInventorySummaries(
                granularityType: 'Marketplace',
                granularityId: AmazonMerchantIdList::$amazonMerchantIdList['US'],
                marketplaceIds: [AmazonMerchantIdList::$amazonMerchantIdList['US']],
                nextToken: $nextToken
            );
            $responseData = $response->json();
            $inventorySummaries = $responseData['payload']['inventorySummaries'] ?? [];
            $allInventorySummaries = array_merge($allInventorySummaries, $inventorySummaries);
            $nextToken = $responseData['payload']['nextToken'] ?? null;
            print_r($responseData);exit;
        } while ($nextToken);
    
        print_r($allInventorySummaries);

        exit;
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
        $marketplaceKey = urlencode(strtolower($this->marketplace->getKey()));
        $listingsApi = $this->amazonSellerConnector->listingsItemsV20210801();
        $t = 0;
        while (true) {
            $t++;
            $filename = PIMCORE_PROJECT_ROOT . "/tmp/marketplaces/{$marketplaceKey}_".rawurlencode($sku)."_{$country}.json";
            if (file_exists($filename) && filemtime($filename) > time() - 86400) {
                echo " (cached) ";
                return json_decode(file_get_contents($filename), true);
            }
            try {
                $listingItem = $listingsApi->getListingsItem(
                    sellerId: $this->marketplace->getMerchantId(),
                    marketplaceIds: [AmazonMerchantIdList::$amazonMerchantIdList[$country]],
                    sku: rawurlencode($sku),
                    includedData: ['summaries', 'attributes', 'issues', 'offers', 'fulfillmentAvailability', 'procurement']
                );
                if ($listingItem->status() == 200) {
                    $retval = $listingItem->json();
                    file_put_contents($filename, json_encode($retval));
                    return $retval;
                }
            } catch (\Exception $e) {
                echo ''. $e->getMessage() .'';
                if ($t>5) {
                    echo "x";
                    return null;
                }
                echo "w";
                sleep($t);
            }
        }
    }

    public function download($forceDownload = false)
    {
        foreach ($this->countryCodes as $country) {
            echo "\n  Downloading Amazon reports for $country\n";
            foreach (array_keys($this->amazonReports) as $reportType) {
                $this->downloadAmazonReport($reportType, $forceDownload, $country);
            }
            $this->getListings($country);
        }
    }

    public function downloadOrders()
    {
        
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
                $listings[] = $listing['seller-sku'] ?? $listing['sku'] ?? '';
            }
        }
        $this->listings[$country] =  array_unique($listings);
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
        $l->setPath($amazonWebsites[$country].'/dp/' . ($listing['summaries'][0]['asin'] ?? ''));
        return $l;
    }

    private function getAttributes($listing) {
        $attributes = [];    
        if (!empty($listing['attributes']['size']) && is_array($listing['attributes']['size'])) {
            $values = array_filter(array_map(function($value) {
                return str_replace(' ', '', $value);
            }, array_column($listing['attributes']['size'], 'value')));
            if (!empty($values)) {
                $attributes[] = implode('-', $values);
            }
        }
        if (!empty($listing['attributes']['color']) && is_array($listing['attributes']['color'])) {
            $values = array_filter(array_map(function($value) {
                return str_replace(' ', '', $value);
            }, array_column($listing['attributes']['color'], 'value')));
            if (!empty($values)) {
                $attributes[] = implode('-', $values);
            }
        }
        return implode('_', $attributes);
    }

    private function getTitle($listing)
    {
        $title = empty($listing['offers']) ? 'NOSALE ' : '';
        $title .= $listing['summaries'][0]['itemName'] ?? '';
        return trim($title);
    }

    public function import($updateFlag, $importFlag)
    {
        $marketplaceRootFolder = Utility::checkSetPath(
            Utility::sanitizeVariable($this->marketplace->getKey(), 190),
            Utility::checkSetPath('Pazaryerleri')
        );

        foreach ($this->countryCodes as $country) {
            if (empty($this->listings[$country])) {
                echo "Nothing to import in $country\n";
            }
            $marketplaceFolder = Utility::checkSetPath($country, $marketplaceRootFolder);
            $total = count($this->listings[$country]);
            $index = 0;
            foreach ($this->listings[$country] as $sku) {
                echo "($index/$total) Processing SKU $sku ...";
                $listing = $this->downloadAmazonSKU($sku, $country);
                $path = Utility::sanitizeVariable($listing['summaries'][0]['productType'] ?? 'Tasnif-Edilmemiş');
                $parent = null;
                if (isset($listing['attributes']['child_parent_sku_relationship'][0]['parent_sku'])) {
                    $parentSku = $listing['attributes']['child_parent_sku_relationship'][0]['parent_sku'];
                    $parent = VariantProduct::findOneByField('uniqueMarketplaceId', "{$this->marketplace->getKey()}.{$country}.{$parentSku}", unpublished: true);
                    if (!$parent) {
                        $parent = VariantProduct::addUpdateVariant(
                            variant: [
                                'title' => 'TEMPORARY PARENT',
                                'uniqueMarketplaceId' => "{$this->marketplace->getKey()}.{$country}.{$parentSku}",
                                'published' => false,
                            ],
                            importFlag: true,
                            updateFlag: true,
                            marketplace: $this->marketplace,
                            parent: Utility::checkSetPath($path, $marketplaceFolder)
                        );
                    }
                }
                if (!$parent) {
                    $parent = Utility::checkSetPath($path, $marketplaceFolder);
                }
                VariantProduct::addUpdateVariant(
                    variant: [
                        'imageUrl' => $this->getImage($listing),
                        'urlLink' => $this->getUrlLink($listing, $country),
                        'salePrice' => $listing['offers'][0]['price']['amount'] ?? 0,
                        'saleCurrency' => $listing['offers'][0]['price']['currency'] ?? 'USD',
                        'title' => $this->getTitle($listing),
                        'attributes' => $this->getAttributes($listing),
                        'amazonAsin' => $listing['summaries'][0]['asin'] ?? '',
                        'uniqueMarketplaceId' => "{$this->marketplace->getKey()}.{$country}.{$sku}",
                        'apiResponseJson' => json_encode($listing),
                        'published' => empty($listing['offers']) ? false : true,
                    ],
                    importFlag: $importFlag,
                    updateFlag: $updateFlag,
                    marketplace: $this->marketplace,
                    parent: $parent
                );
                echo "OK\n";
                $index++;
            }
        }
    }

}
