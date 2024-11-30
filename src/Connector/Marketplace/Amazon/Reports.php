<?php

namespace App\Connector\Marketplace\Amazon;

use SellingPartnerApi\Seller\ReportsV20210630\Dto\CreateReportSpecification;

use App\Connector\Marketplace\Amazon\Constants as AmazonConstants;
use App\Connector\Marketplace\Amazon\Connector as AmazonConnector;
use App\Utils\Utility;

class Reports 
{
    public $amazonConnector;

    public array $amazonCountryReports = [
        'GET_MERCHANT_LISTINGS_ALL_DATA' => [],
    ];

    public array $amazonReports = [
        'GET_MERCHANT_LISTINGS_ALL_DATA' => [],
     //   'GET_AFN_INVENTORY_DATA_BY_COUNTRY' => [],
/*        'GET_FBA_MYI_ALL_INVENTORY_DATA' => [],
        'GET_AFN_INVENTORY_DATA_BY_COUNTRY' => [],
        'GET_FLAT_FILE_ALL_ORDERS_DATA_BY_LAST_UPDATE_GENERAL' => [],
        'GET_FLAT_FILE_RETURNS_DATA_BY_RETURN_DATE' => [],
        'GET_SELLER_FEEDBACK_DATA' => [],*/
    ];

    public function __construct(AmazonConnector $amazonConnector) 
    {
        $this->amazonConnector = $amazonConnector;
    }

    public function downloadAmazonReport($reportType, $forceDownload, $country, $silent = false)
    {
        $marketplaceKey = urlencode( $this->amazonConnector->getMarketplace()->getKey());
        if (!$silent) {
            echo "        Downloading Report $reportType ";
        }
        $report = Utility::getCustomCache(
            "{$reportType}_{$country}.csv", 
            PIMCORE_PROJECT_ROOT . "/tmp/marketplaces/{$marketplaceKey}"
        );
        if ($report === false || $forceDownload) {
            echo "Waiting Report ";
            $reportsApi = $this->amazonConnector->amazonSellerConnector->reportsV20210630();
            $response = $reportsApi->createReport(new CreateReportSpecification(reportType: $reportType, marketplaceIds: [AmazonConstants::amazonMerchant[$country]['id']], reportOptions: ['custom' => true]));
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
            if (!$silent) {
                echo "Cached ";
            }
        }
        if (substr(string: $report, offset: 0, length: 2) === "\x1f\x8b") {
            $report = gzdecode(data: $report);
        }
        if (substr(string: $report, offset: 0, length: 3) === "\xEF\xBB\xBF") {
            $report = substr(string: $report, offset: 3);
        }
        return $report;
    }

    public function downloadAllReports($forceDownload, $silent = false)
    {
        foreach (array_keys($this->amazonReports) as $reportType) {
            if (!$silent) {
                echo "\n  Downloading {$reportType} for main Amazon region {$this->amazonConnector->mainCountry}\n";
            }
            $this->amazonReports[$reportType] = $this->downloadAmazonReport(reportType: $reportType, forceDownload: $forceDownload, country: $this->amazonConnector->mainCountry, silent: $silent);
        }
        foreach ($this->amazonConnector->countryCodes as $country) {
            foreach (array_keys($this->amazonCountryReports) as $reportType) {
                if (!$silent) {
                    echo "\n  Downloading {$reportType} for Amazon region $country\n";
                }
                $this->amazonCountryReports[$reportType][$country] = $this->downloadAmazonReport(reportType: $reportType, forceDownload: $forceDownload, country: $country, silent: $silent);
            }
        }
    }


}
