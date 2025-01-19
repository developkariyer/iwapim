<?php

namespace App\Connector\Marketplace\Amazon;

use App\Connector\Marketplace\Amazon\Constants as AmazonConstants;
use App\Utils\Registry;
use App\Utils\Utility;
use Doctrine\DBAL\Exception;
use JsonException;
use Random\RandomException;

class Listings
{
    public Connector $connector;

    public array $asinBucket = [];

    public function __construct(Connector $connector)
    {
        $this->connector = $connector;
    }

    private function retrieveEan($json): string
    {
        $identifiers = $json['identifiers'][0]['identifiers'] ?? [];
        foreach ($identifiers as $identifier) {
            if ($identifier['identifierType'] === 'EAN') {
                return $identifier['identifier'] ?? '';
            }
        }
        return '';
    }

    /**
     * @throws JsonException
     * @throws Exception
     * @throws RandomException
     */
    protected function downloadAsinsInBucket(): void
    {
        if (empty($this->asinBucket)) {
            echo "AM143 - No ASINs to download.\n";
            return;
        }
        $catalogApi = $this->connector->amazonSellerConnector->catalogItemsV20220401();
        $response = $catalogApi->searchCatalogItems(
            marketplaceIds: [AmazonConstants::amazonMerchant[$this->connector->mainCountry]['id']],
            identifiers: array_keys($this->asinBucket),
            identifiersType: 'ASIN',
            includedData: ['attributes', 'classifications', 'dimensions', 'identifiers', 'images', 'productTypes', 'relationships', 'salesRanks', 'summaries'],
            sellerId: $this->connector->getMarketplace()->getMerchantId(),
        );
        $this->asinBucket = [];
        $items = $response->json()['items'] ?? [];
        foreach ($items as $item) {
            $asin = $item['asin'] ?? '';
            $this->connector->listings[$asin]['catalog'] = $item;
            $this->connector->putToCache("ASIN_{$asin}.json", $item);
            Utility::storeJsonData($this->connector->getMarketplace()->getId(), $asin, $item);
            $ean = $this->retrieveEan($item);
            if (!empty($ean)) {
                Registry::setKey($asin, $ean, 'asin-to-ean');
            }
        }
        sleep(1);
    }

    /**
     * @throws JsonException|Exception|RandomException
     */
    protected function addToAsinBucket($asin, $forceDownload = false): void
    {
        $item = $this->connector->getFromCache("ASIN_{$asin}.json");
        if (empty($item) || $forceDownload) {
            $this->asinBucket[$asin] = 1;
            if (count($this->asinBucket) >= 10) {
                $this->downloadAsinsInBucket();
            }        
        } else {
            $this->connector->listings[$asin]['catalog'] = $item;
            Utility::storeJsonData($this->connector->getMarketplace()->getId(), $asin, $item);
            $ean = $this->retrieveEan($item);
            if (!empty($ean)) {
                Registry::setKey($asin, $ean, 'asin-to-ean');
            }
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
            $this->connector->listings[$asin] = $this->connector->listings[$asin] ?? [];
            $this->connector->listings[$asin][$country] = $this->connector->listings[$asin][$country] ?? [];
            $this->connector->listings[$asin][$country][] = $rowData;
        }
    }

    /**
     * @throws JsonException|Exception|RandomException
     */
    public function getListings($forceDownload = false): void
    {
        echo "getListings called with forceDownload: $forceDownload. Main country is {$this->connector->mainCountry}\n";
        $this->processListingReport($this->connector->mainCountry, $this->connector->reportsHelper->amazonReports['GET_MERCHANT_LISTINGS_ALL_DATA']);
        echo "Main country report processed. Other countries to process are: ".json_encode($this->connector->countryCodes)."\n";
        foreach ($this->connector->countryCodes as $country) {
            $this->processListingReport($country, $this->connector->reportsHelper->amazonCountryReports['GET_MERCHANT_LISTINGS_ALL_DATA'][$country]);
        }
        $totalCount = count($this->connector->listings);
        $index = 0;
        foreach ($this->connector->listings as $asin=> $listing) {
            $index++;
            echo "($index/$totalCount) Downloading $asin ...\n";
            $this->addToAsinBucket($asin, $forceDownload);
        }
        $this->downloadAsinsInBucket();
    }

}
