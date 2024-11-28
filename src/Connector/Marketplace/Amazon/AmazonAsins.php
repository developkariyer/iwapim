<?php

namespace App\Connector\Marketplace\Amazon;

class AmazonAsins
{
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
            marketplaceIds: [AmazonConstants::amazonMerchant[$country]['id']],
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

}

