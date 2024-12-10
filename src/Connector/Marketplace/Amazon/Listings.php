<?php

namespace App\Connector\Marketplace\Amazon;

use App\Connector\Marketplace\Amazon\Constants as AmazonConstants;
use App\Connector\Marketplace\Amazon\Connector as AmazonConnector;
use App\Utils\Utility;
use JsonException;

class Listings
{
    public AmazonConnector $amazonConnector;

    public array $asinBucket = [];

    public function __construct(AmazonConnector $amazonConnector) 
    {
        $this->amazonConnector = $amazonConnector;
    }

    /**
     * @throws JsonException
     */
    protected function downloadAsinsInBucket(): void
    {
        if (empty($this->asinBucket)) {
            return;
        }
        $catalogApi = $this->amazonConnector->amazonSellerConnector->catalogItemsV20220401();
        $response = $catalogApi->searchCatalogItems(
            marketplaceIds: [AmazonConstants::amazonMerchant[$this->amazonConnector->mainCountry]['id']],
            identifiers: array_keys($this->asinBucket),
            identifiersType: 'ASIN',
            includedData: ['attributes', 'classifications', 'dimensions', 'identifiers', 'images', 'productTypes', 'relationships', 'salesRanks', 'summaries'],
            sellerId: $this->amazonConnector->getMarketplace()->getMerchantId(),
        );
        $this->asinBucket = [];
        $items = $response->json()['items'] ?? [];
        foreach ($items as $item) {
            $asin = $item['asin'] ?? '';
            $this->amazonConnector->listings[$asin]['catalog'] = $item;
            Utility::setCustomCache("ASIN_{$asin}.json", PIMCORE_PROJECT_ROOT . "/tmp/marketplaces/".urlencode($this->amazonConnector->getMarketplace()->getKey()), json_encode($item, JSON_PRETTY_PRINT));
            Utility::storeJsonData($this->amazonConnector->getMarketplace()->getId(), $asin, $item);
        }
        sleep(1);
    }

    /**
     * @throws JsonException
     */
    protected function addToAsinBucket($asin, $forceDownload = false): void
    {
        $item = Utility::getCustomCache("ASIN_{$asin}.json", PIMCORE_PROJECT_ROOT . "/tmp/marketplaces/".urlencode($this->amazonConnector->getMarketplace()->getKey()));
        $item = json_decode($item, true);
        if (empty($item) || $forceDownload) {
            $this->asinBucket[$asin] = 1;
            if (count($this->asinBucket) >= 10) {
                $this->downloadAsinsInBucket();
            }        
        } else {
            $this->amazonConnector->listings[$asin]['catalog'] = $item;
            Utility::storeJsonData($this->amazonConnector->getMarketplace()->getId(), $asin, $item);
        }
    }

    protected function processListingReport($country, $report): void
    {
        $possibleEncodings = ['UTF-8', 'ISO-8859-1', 'Windows-1252', 'ASCII'];
        $encoding = mb_detect_encoding($report, $possibleEncodings, true) ?: 'UTF-8';
        if (empty(trim($report))) {
            error_log("Ignoring empty or invalid report for country: $country");
            return;
        }
        $lines = explode("\n", mb_convert_encoding(trim($report), 'UTF-8', $encoding));
        if (empty($lines)) {
            error_log("Ignoring report with no data lines for country: $country");
            return;
        }
        $header = str_getcsv(array_shift($lines), "\t");
        if (empty($header)) {
            error_log("Ignoring report with no valid header for country: $country");
            return;
        }
        echo count($lines)." lines found in $country report. ".count($header)." headers.\n";
        $index = 0;
        foreach ($lines as $line) {
            $index++;
            $data = str_getcsv($line, "\t");
            while (count($header) > count($data)) {
                $data[] = '';
            }
            if (count($header) !== count($data)) {
                error_log("Column mismatch in line ($index): ".count($header)." < ".count($data)." Skipping this row.");
                continue;
            }
            $rowData = array_combine($header, $data);
            $asin = $rowData['asin1'] ?? $rowData['product-id'] ??'';
            if (empty($asin) || !str_starts_with($asin, 'B')) {
                error_log("Missing ASIN in line ($index): " . json_encode($rowData) . ". Skipping this row.");
                continue;
            }
            $this->amazonConnector->listings[$asin] = $this->amazonConnector->listings[$asin] ?? [];
            $this->amazonConnector->listings[$asin][$country] = $this->amazonConnector->listings[$asin][$country] ?? [];
            $this->amazonConnector->listings[$asin][$country][] = $rowData;
        }
    }

    /**
     * @throws JsonException
     */
    public function getListings($forceDownload = false): void
    {
        $this->processListingReport($this->amazonConnector->mainCountry, $this->amazonConnector->reportsHelper->amazonReports['GET_MERCHANT_LISTINGS_ALL_DATA']);
        foreach ($this->amazonConnector->countryCodes as $country) {
            $this->processListingReport($country, $this->amazonConnector->reportsHelper->amazonCountryReports['GET_MERCHANT_LISTINGS_ALL_DATA'][$country]);
        }

        $totalCount = count($this->amazonConnector->listings);
        $index = 0;
        foreach ($this->amazonConnector->listings as $asin=>$listing) {
            $index++;
            echo "($index/$totalCount) Downloading $asin ...\n";
            $this->addToAsinBucket($asin, $forceDownload);
        }
        $this->downloadAsinsInBucket();
    }

}
