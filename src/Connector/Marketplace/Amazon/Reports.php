<?php

namespace App\Connector\Marketplace\Amazon;

use JsonException;
use Random\RandomException;
use SellingPartnerApi\Seller\ReportsV20210630\Dto\CreateReportSpecification;

use App\Connector\Marketplace\Amazon\Constants as AmazonConstants;

class Reports 
{
    public Connector $connector;

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

    public function __construct(Connector $connector)
    {
        $this->connector = $connector;
    }

    /**
     * @throws JsonException|RandomException
     */
    public function downloadAmazonReport($reportType, $forceDownload, $country, $silent = false): bool|string
    {
        if (!$silent) {
            echo "        Downloading Report $reportType ";
        }
        $report = $this->connector->getFromCacheRaw(key:"{$reportType}_{$country}.csv");
        if (empty($report) || $forceDownload) {
            echo "Waiting Report ";
            $reportsApi = $this->connector->amazonSellerConnector->reportsV20210630();
            $response = $reportsApi->createReport(new CreateReportSpecification(reportType: $reportType, marketplaceIds: [AmazonConstants::amazonMerchant[$country]['id']], reportOptions: ["custom" => "true"]));
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
            if (str_starts_with($report, "\x1f\x8b")) {
                $report = gzdecode(data: $report);
            }
            $this->connector->putToCacheRaw("{$reportType}_{$country}.csv", $report);
            echo "OK ";
        } else {
            if (!$silent) {
                echo "Cached ";
            }
        }
        if (str_starts_with($report, "\x1f\x8b")) {
            $report = gzdecode(data: $report);
        }
        if (str_starts_with($report, "\xEF\xBB\xBF")) {
            $report = substr(string: $report, offset: 3);
        }
        return $report;
    }

    /**
     * @throws JsonException|RandomException
     */
    public function downloadAllReports($forceDownload, $silent = false): void
    {
        foreach (array_keys($this->amazonReports) as $reportType) {
            if (!$silent) {
                echo "\n  Downloading {$reportType} for main Amazon region {$this->connector->mainCountry}\n";
            }
            $this->amazonReports[$reportType] = $this->downloadAmazonReport(reportType: $reportType, forceDownload: $forceDownload, country: $this->connector->mainCountry, silent: $silent);
        }
        foreach ($this->connector->countryCodes as $country) {
            foreach (array_keys($this->amazonCountryReports) as $reportType) {
                if (!$silent) {
                    echo "\n  Downloading {$reportType} for Amazon region $country\n";
                }
                $this->amazonCountryReports[$reportType][$country] = $this->downloadAmazonReport(reportType: $reportType, forceDownload: $forceDownload, country: $country, silent: $silent);
            }
        }
    }


}
