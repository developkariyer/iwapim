<?php

namespace App\Utils;

use SellingPartnerApi\SellingPartnerApi;
use SellingPartnerApi\Enums\Endpoint;
use SellingPartnerApi\Seller\ReportsV20210630\Dto\CreateReportSpecification;

use Pimcore\Model\DataObject\Marketplace;
use Pimcore\Model\DataObject\Data\Link;
use Pimcore\Model\DataObject\VariantProduct;

use App\Utils\Utility;


class AmazonConnector
{
    private static array $merchantIdList = [
        'CA' => 'A2EUQ1WTGCTBG2',
        'US' => 'ATVPDKIKX0DER',
        'MX' => 'A1AM78C64UM0Y8',
        //        'BR' => 'A2Q3Y263D00KWC',
        //sallama        'ES' => 'A1RKKUPIHCS9HS',
        //        'UK' => 'A1F83G8C2ARO7P',
        //        'FR' => 'A13V1IB3VIYZZH',
        //sallama        'NL' => 'A1805IZSGTT6HS',
        //        'DE' => 'A1PA6795UKMFR9',
        //sallama        'IT' => 'APJ6JRA9NG5V4',
        //sallama        'SE' => 'A2NODRKZP88ZB9',
        //sallama        'PL' => 'A1C3SOZRARQ6R3',
        //sallama        'EG' => 'ARBP9OOSHTCHU',
        //sallama        'TR' => 'A33AVAJ2PDY3EV',
        //sallama        'AE' => 'A17E79C6D8DWNP',
        //sallama        'IN' => 'A21TJRUUN4KGV',
        //sallama        'SG' => 'A19VAU5U5O7RUS',
        //sallama        'AU' => 'A39IBJ37TRP1C6',
        //        'JP' => 'A1VC38T7YXB528',
    ];

    private array $amazonReports = [
        'GET_FLAT_FILE_OPEN_LISTINGS_DATA' => null,
        'GET_MERCHANT_LISTINGS_ALL_DATA' => null,
    ];

    private $amazonSellerConnector = null;
    private $country = null;
    private $marketplace = null;
    private $listings = [];

    public function __construct(Marketplace $marketplace, $country = 'US')
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
        if (!in_array($country, array_keys(self::$merchantIdList))) {
            throw new \Exception("Country $country is not in merchantIdList in AmazonConnector class");
        }
        $this->marketplace = $marketplace;
        $this->country = $country;
        $this->amazonSellerConnector = SellingPartnerApi::seller(
            clientId: $marketplace->getClientId(),
            clientSecret: $marketplace->getClientSecret(),
            refreshToken: $marketplace->getRefreshToken(),
            endpoint: Endpoint::NA
        );
        if (!$this->amazonSellerConnector) {
            throw new \Exception("Amazon Seller Connector is not created");
        }
    }

    protected function downloadAmazonReport($reportType, $forceDownload)
    {
        $marketplaceKey = urlencode(strtolower($this->marketplace->getKey()));
        if (!in_array($reportType, array_keys($this->amazonReports))) {
            throw new \Exception("Report Type $reportType is not in reportNames in AmazonConnector class");
        }
        echo "\n        Downloading Report $reportType ";
        $filename = "tmp/{$marketplaceKey}_{$reportType}_{$this->country}.csv";
        
        if (!$forceDownload && file_exists($filename) && filemtime($filename) > time() - 86400) {
            $report = file_get_contents($filename);
            echo "Using cached data ";
        } else {
            echo "Waiting Report ";
            $reportsApi = $this->amazonSellerConnector->reportsV20210630();
            $response = $reportsApi->createReport(new CreateReportSpecification($reportType, [self::$merchantIdList[$this->country]]));
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
        $this->amazonReports[$reportType] = $report;
    }

    public function downloadAmazonSku($sku)
    {
        $listingsApi = $this->amazonSellerConnector->listingsItemsV20210801();
        $t = 0;
        while (true) {
            $t++;
            try {
                $listingItem = $listingsApi->getListingsItem(
                    sellerId: $this->marketplace->getMerchantId(),
                    marketplaceIds: [self::$merchantIdList[$this->country]],
                    sku: $sku,
                    includedData: ['summaries', 'attributes', 'issues', 'offers', 'fulfillmentAvailability', 'procurement']
                );
                if ($listingItem->status() == 200) {
                    return $listingItem->json();
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
        foreach (array_keys($this->amazonReports) as $reportType) {
            $this->downloadAmazonReport($reportType, $forceDownload);
        }
        $this->getListings();
        return count($this->listings);
    }

    public function downloadOrders()
    {
        
    }

    public function getListings()
    {
        $listings = [];
        foreach ($this->amazonReports as $reportType=>$report) {
            if (empty($report)) {
                throw new \Exception("Report is empty. Did you first call download() method to populate reports?");
            }
            $lines = explode("\n", $report);
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
        $this->listings =  array_unique($listings);
        return $this->listings;
    }

    private function getImage($listing) {
        $image = $listing['summaries'][0]['mainImage']['link'] ?? '';
        if (!empty($image)) {
            return new \Pimcore\Model\DataObject\Data\ExternalImage($image);
        }
        return null;
    }

    private function getUrlLink($listing) {
        $l = new Link();
        $l->setPath('https://www.amazon.com/dp/' . ($listing['summaries'][0]['asin'] ?? ''));
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
        if (empty($this->listings)) {
            echo "Nothing to import\n";
        }
        $marketplaceFolder = Utility::checkSetPath(
            Utility::sanitizeVariable($this->marketplace->getKey(), 190),
            Utility::checkSetPath('Pazaryerleri')
        );
        $total = count($this->listings);
        $index = 0;
        foreach ($this->listings as $sku) {
            echo "($index/$total) Processing SKU $sku ...";
            $listing = $this->downloadAmazonSKU($sku);
            $path = Utility::sanitizeVariable($listing['summaries'][0]['productType'] ?? 'Tasnif-Edilmemiş');
            $parent = null;
            if (isset($listing['attributes']['child_parent_sku_relationship'][0]['parent_sku'])) {
                $parentSku = $listing['attributes']['child_parent_sku_relationship'][0]['parent_sku'];
                $parent = VariantProduct::findOneByField('uniqueMarketplaceId', $parentSku, unpublished: true);
                if (!$parent) {
                    $parent = VariantProduct::addUpdateVariant(
                        variant: [
                            'title' => 'TEMPORARY PARENT',
                            'uniqueMarketplaceId' => $parentSku,
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
                    'urlLink' => $this->getUrlLink($listing),
                    'salePrice' => $listing['offers'][0]['price']['amount'] ?? 0,
                    'saleCurrency' => $listing['offers'][0]['price']['currency'] ?? 'USD',
                    'title' => $this->getTitle($listing),
                    'attributes' => $this->getAttributes($listing),
                    'uniqueMarketplaceId' => $sku,
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
