<?php

namespace App\Connector\Wisersell;

use Doctrine\DBAL\Exception;
use Pimcore\Db;
use Pimcore\Model\DataObject\VariantProduct;
use Pimcore\Model\DataObject\Marketplace;
use Pimcore\Model\DataObject\Product;
use App\Utils\Utility;
use Random\RandomException;
use Symfony\Contracts\HttpClient\Exception\ClientExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\DecodingExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\RedirectionExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\ServerExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;

class ListingSyncService
{
    protected Connector $connector;
    public array $wisersellListings = [];   // code => wisersell listing array (check swagger)
    public array $pimListings = [];   // calculatedWisersellCode => [oo_id, wisersellVariantCode, calculatedWisersellCode]
    public array $bucket = [];
    public array $amazonListings = [];

    public function __construct(Connector $connector)
    {
        $this->connector = $connector;
    }

    /**
     * @throws TransportExceptionInterface
     * @throws ServerExceptionInterface
     * @throws RedirectionExceptionInterface
     * @throws DecodingExceptionInterface
     * @throws ClientExceptionInterface|RandomException
     */
    public function loadWisersell($force = false): int
    {
        if (!$force && !empty($this->wisersellListings)) {
            return time()-filemtime(PIMCORE_PROJECT_ROOT . '/tmp/wisersell/listings.json');
        }
        $this->wisersellListings = json_decode(Utility::getCustomCache('listings.json', PIMCORE_PROJECT_ROOT . '/tmp/wisersell'), true);
        if (!$force && !empty($this->wisersellListings)) {
            return time()-filemtime(PIMCORE_PROJECT_ROOT . '/tmp/wisersell/listings.json');
        }
        $this->wisersellListings = $this->search([]);
        Utility::setCustomCache('listings.json', PIMCORE_PROJECT_ROOT . '/tmp/wisersell', json_encode($this->wisersellListings));
        return time()-filemtime(PIMCORE_PROJECT_ROOT . '/tmp/wisersell/listings.json');
    }

    /**
     * @throws Exception
     */
    public function loadPim($force = false): void
    {
        if (!$force && !empty($this->pimListings)) {
            return;
        }
        $db = Db::get();
        $this->pimListings = [];
        $listings = $db->fetchAllAssociative('SELECT oo_id, wisersellVariantCode, calculatedWisersellCode FROM object_varyantproduct WHERE published = 1');
        foreach ($listings as $listing) {
            if (strlen($listing['calculatedWisersellCode'])<1) {
                continue;
            }
            $this->pimListings[$listing['calculatedWisersellCode']] = $listing;
        }
    }

    /**
     * @param bool $force
     * @throws ClientExceptionInterface
     * @throws DecodingExceptionInterface
     * @throws Exception
     * @throws RedirectionExceptionInterface
     * @throws ServerExceptionInterface
     * @throws TransportExceptionInterface
     */
    public function syncAmazon(bool $force = false): void
    {
        $this->load();
        if ($force || empty($this->amazonListings)) {
            $this->amazonListings = [];
            $listObj = new VariantProduct\Listing();
            $listObj->setUnpublished(false);
            $pageSize = 100;
            $listObj->setLimit($pageSize);
            $offset = 0;
            while (true) {
                echo "\rLooking for Amazon listings in PIM from $offset to ".($offset+$pageSize);
                $listObj->setOffset($offset);
                $variantProducts = $listObj->load();
                if (empty($variantProducts)) {
                    break;
                }
                foreach ($variantProducts as $variantProduct) {
                    if ($variantProduct->getMarketplace()->getMarketplaceType() === 'Amazon') {
                        $asin = $variantProduct->getUniqueMarketplaceId();
                        if (strlen($asin)<1) {
                            continue;
                        }
                        if (!isset($this->amazonListings[$asin])) {
                            $this->amazonListings[$asin] = [];
                        }
                        $this->amazonListings[$asin]['pim'] = $variantProduct;
                    }
                }
                $offset += $pageSize;
            }
            echo "\n";

            foreach ($this->wisersellListings as $listing) {
                if ($listing['store']['source']['name'] === 'Amazon') {
                    $asin = $listing['storeproductid'] ?? null;
                    $storeId = $listing['store']['id'] ?? null;
                    if (is_null($asin) || is_null($storeId)) {
                        continue;
                    }
                    if (!isset($this->amazonListings[$asin])) {
                        $this->amazonListings[$asin] = [];
                    }
                    $this->amazonListings[$asin][$storeId] = $listing;
                }
            }
            file_put_contents(PIMCORE_PROJECT_ROOT . '/tmp/wisersell/listings.amazon.txt', print_r($this->amazonListings, true));
        }

        $amazonStoreIds = $this->connector->storeSyncService->getAmazonStoreIds();
        foreach ($this->amazonListings as $asin => $listings) {
            echo "\rChecking Amazon listing $asin  ";
            if (!isset($listings['pim'])) {
                echo "Amazon listing not found in PIM for $asin\n";
                continue;
            }
            $mainProduct = $listings['pim']->getMainProduct();
            if (is_array($mainProduct)) {
                $mainProduct = reset($mainProduct);
            }
            if (!$mainProduct instanceof Product) {
                echo "Variant {$listings['pim']->getId()} not connected in PIM for $asin\n";
                continue;
            }
            $productId = $mainProduct->getWisersellId();
            if (empty($productId)) {
                echo "Main product {$mainProduct->getId()} in PIM not synced with WS for $asin\n";
                continue;
            }
            foreach ($amazonStoreIds as $storeId) {
                if (!isset($listings[$storeId])) {
                    echo "Amazon listing not found in WS shop $storeId for $asin\n";
                    $this->bucket[] = [
                        'storeproductid' => $asin,
                        'productId' => $productId,
                        'storeId' => $storeId,
                        'variantCode' => (string) null,
                        'variantStr' => "Amazon SId: {$storeId} PId:{$asin} VId:- PimId:{$productId}"
                    ];
                    if (count($this->bucket) >= 100) {
                        $this->flushListingBucketToWisersell(false);
                    }
                    continue;
                }
                $wsProductId = $listings[$storeId]['product']['id'] ?? null;
                if ((empty($wsProductId) || (int)$productId != (int)$wsProductId) && strlen($listings[$storeId]['code'] ?? '')>0) {
                    echo "Product ID mismatch for $asin: WS:{$wsProductId} PIM:{$productId}\n";
                    $this->connector->request(
                        Connector::$apiUrl['listing'], 
                        'PUT', 
                        $listings[$storeId]['code'], 
                        [
                            'productId' => $productId,
                            'storeId' => $storeId,
                        ]
                    );
                }
            }
        }
        $this->flushListingBucketToWisersell(false);
    }

    /**
     * @return array
     * @throws ClientExceptionInterface
     * @throws DecodingExceptionInterface
     * @throws Exception
     * @throws RedirectionExceptionInterface
     * @throws ServerExceptionInterface
     * @throws TransportExceptionInterface
     */
    public function status(): array
    {
        $cacheExpire = $this->load();
        return [
            'wisersell' => count($this->wisersellListings),
            'pim' => count($this->pimListings),
            'expire' => 86400-$cacheExpire
        ];
    }

    /**
     * @param bool $force
     * @return int
     * @throws ClientExceptionInterface
     * @throws DecodingExceptionInterface
     * @throws Exception
     * @throws RedirectionExceptionInterface
     * @throws ServerExceptionInterface
     * @throws TransportExceptionInterface
     */
    public function load(bool $force = false): int
    {
        $this->loadPim($force);
        return $this->loadWisersell($force);
    }

    /**
     * @throws ClientExceptionInterface
     * @throws DecodingExceptionInterface
     * @throws Exception
     * @throws RedirectionExceptionInterface
     * @throws ServerExceptionInterface
     * @throws TransportExceptionInterface
     */
    public function dump(): void
    {
        $this->load();
        file_put_contents(PIMCORE_PROJECT_ROOT . '/tmp/wisersell/listings.wisersell.txt', print_r($this->wisersellListings, true));
        file_put_contents(PIMCORE_PROJECT_ROOT . '/tmp/wisersell/listings.pim.txt', print_r($this->pimListings, true));        
    }

    /**
     * @throws TransportExceptionInterface
     * @throws ServerExceptionInterface
     * @throws RedirectionExceptionInterface
     * @throws DecodingExceptionInterface
     * @throws ClientExceptionInterface
     */
    public function search($params): array
    {
        $params['page'] = 0;
        $params['pageSize'] = 100;
        $listings = $retval = [];
        do {
            $result = $this->connector->request(Connector::$apiUrl['listingSearch'], 'POST', '', $params);
            if (empty($result)) {
                break;
            }
            $response = $result->toArray();
            $listings = array_merge($listings, $response['rows'] ?? []);
            $params['page']++;
            echo "\rLoaded ".count($listings)." listings  ";
        } while (count($response['rows'])>0);
        echo "\n";
        foreach ($listings as $listing) {
            if (!empty($listing['code'])) {
                $retval[$listing['code']] = $listing;
            }
        }
        return $retval;
    }

    /**
     * @throws Exception
     */
    public function prepareListingData($variantProduct): ?array
    {
        if (!$variantProduct instanceof VariantProduct) {
            echo "Not a variant product\n";
            return null;
        }
        $mainProduct = $variantProduct->getMainProduct();
        $mainProduct = reset($mainProduct);
        if (!$mainProduct instanceof Product) {
            return null;
        }
        $marketplace = $variantProduct->getMarketplace();
        $marketplaceType = $marketplace->getMarketplaceType();
        $apiResponse = json_decode($variantProduct->jsonRead('apiResponseJson'), true);
        $parentResponse = json_decode($variantProduct->jsonRead('parentResponseJson'), true);
        $productId = $mainProduct->getWisersellId();
        $storeProductId = match ($marketplaceType) {
            'Etsy', 'Shopify' => $apiResponse['product_id'] ?? null,
            'Amazon' => $variantProduct->getUniqueMarketplaceId(),
            'Trendyol' => $apiResponse['productCode'] ?? null,
            'Hepsiburada' => $apiResponse['attributes']['hbSku'] ?? null,
            default => null,
        };
        $storeId = $marketplace->getWisersellStoreId();
        $variantCode = match ($marketplaceType) {
            'Etsy' => $parentResponse['listing_id'] ?? null,
            'Shopify' => $apiResponse['id'] ?? null,
            'Trendyol' => $apiResponse['platformListingId'] ?? null,
            'Hepsiburada' => $apiResponse['attributes']['barcode'] ?? null,
            default => null,
        };
        if (empty($storeProductId) || empty($storeId) || (empty($variantCode) && $marketplaceType !== 'Amazon') || empty($productId)) {
            echo "Empty data for {$variantProduct->getId()}: {$marketplaceType}, {$storeId}, {$storeProductId}, {$variantCode}, {$productId}\n";
            return null;
        }
        return [
            'storeproductid' => $storeProductId,
            'productId' => $productId,
            'storeId' => $storeId,
            'variantCode' => $variantCode,
            'variantStr' => "{$marketplaceType} SId: {$storeId} PId:{$storeProductId} VId:{$variantCode} PimId:{$productId}"
        ];
    }

    /**
     * @throws RedirectionExceptionInterface
     * @throws DecodingExceptionInterface
     * @throws ClientExceptionInterface
     * @throws TransportExceptionInterface
     * @throws ServerExceptionInterface
     * @throws Exception
     * @throws \Exception
     */
    public function updateWisersellListing($variantProduct): void
    {
        $this->load();
        $code = $variantProduct->getCalculatedWisersellCode();
        if (!isset($this->wisersellListings[$code])) {
            $this->addVariantProductToWisersell($variantProduct);
            return;
        }
        $listingData = $this->prepareListingData($variantProduct);
        if (empty($listingData) || empty($listingData['productId']) || empty($listingData['storeId'])) {
            return;
        }
        $response = $this->connector->request(Connector::$apiUrl['listing'], 'PUT', $code, [
            'productId' => $listingData['productId'],
            'storeId' => $listingData['storeId'],
        ]);
        if (empty($response) || $response->getStatusCode() !== 200) {
            return;
        }
        $response = $response->toArray();
        $this->updatePimVariantProduct($response);
    }

    /**
     * @throws \Exception
     */
    public function updatePimVariantProduct($listing): void
    {
        if (empty($listing['code'])) {
            print_r($listing);
            sleep(1);
            return;
        }
        $variantProduct = VariantProduct::getByCalculatedWisersellCode($listing['code'], 1);
        if (!$variantProduct instanceof VariantProduct) {
            return;
        }
        $variantProduct->setWisersellVariantJson(json_encode($listing));
        $variantProduct->setWisersellVariantCode($listing['code']);
        $variantProduct->save();
    }

    /**
     * @param $variantProduct
     * @throws ClientExceptionInterface
     * @throws DecodingExceptionInterface
     * @throws Exception
     * @throws RedirectionExceptionInterface
     * @throws ServerExceptionInterface
     * @throws TransportExceptionInterface
     */
    public function addVariantProductToWisersell($variantProduct): void
    {
        $listingData = $this->prepareListingData($variantProduct);
        if (empty($listingData) || empty($listingData['productId']) || empty($listingData['storeId'])) {
            return;
        }
        echo "Adding {$listingData['variantStr']}\n";
        $this->bucket[] = $listingData;
        if (count($this->bucket) >= 100) {
            $this->flushListingBucketToWisersell();
        }
    }

    /**
     * @throws TransportExceptionInterface
     * @throws ServerExceptionInterface
     * @throws RedirectionExceptionInterface
     * @throws DecodingExceptionInterface
     * @throws ClientExceptionInterface
     * @throws \Exception
     */
    public function flushListingBucketToWisersell($updatePim = true): void
    {
        if (empty($this->bucket)) {
            return;
        }
        $response = $this->connector->request(Connector::$apiUrl['listing'], 'POST', '', $this->bucket);
        if (empty($response) || $response->getStatusCode() !== 200) {
            return;
        }
        $response = $response->toArray();
        print_r($response);
        $this->bucket = [];
        if (!$updatePim) {
            return;
        }
        foreach (($response['completed'] ?? []) as $listing) {
            echo "Updating {$listing['code']}\n";
            $this->updatePimVariantProduct($listing);
        }
    }

    /**
     * @throws DecodingExceptionInterface
     * @throws ClientExceptionInterface
     * @throws ServerExceptionInterface
     * @throws Exception
     * @throws RedirectionExceptionInterface
     * @throws TransportExceptionInterface
     * @throws \Exception
     */
    public function sync(): void
    {
        $this->updatePimCalculatedWisersellCodes();
        $this->loadPim(true);
        $this->load();
        $pimListings = $this->pimListings;
        $index = 0;
        $totalCount = count($this->wisersellListings);
        foreach ($this->wisersellListings as $listing) {
            $index++;
            echo "\rSyncing $index of $totalCount  ";
            if ($listing['store']['source']['name'] === 'Amazon') {
                continue;
            }
            $code = trim($listing['code']);
            if (isset($pimListings[$code])) {
                $variantProduct = VariantProduct::getById($pimListings[$code]['oo_id']);
                unset($pimListings[$code]);
            } else {
                $variantProduct = VariantProduct::getByWisersellVariantCode($code, 1);
            }
            if (!$variantProduct instanceof VariantProduct) {
                echo "Variant product not found in PIM for {$code}: ".json_encode($listing)."\n";
                $storeId = $listing['store']['id'] ?? null;
                if ($storeId) {
                    $marketplace = Marketplace::getByWisersellStoreId($storeId, 1);
                    if ($marketplace instanceof Marketplace) {
                        echo "(SIMULATE) Deleting {$code} for {$marketplace->getKey()} from WS\n";
                        //$this->deleteFromWisersell($code);
                    }
                }
                continue;
            }
            $mainProduct = $variantProduct->getMainProduct();
            $mainProduct = reset($mainProduct);
            $wisersellProductId = $listing['product']['id'] ?? null;
            if (!$mainProduct instanceof Product) {
                if ($wisersellProductId) {
                    echo "Variant product {$variantProduct->getId()} not connected in PIM for {$listing['code']}\n";
                    echo "But it is connected to $wisersellProductId in WS. Let's try to connect in PIM\n";
                    $mainProduct = Product::getByWisersellId($wisersellProductId, 1);
                    if ($mainProduct instanceof Product) {
                        echo "Main product {$mainProduct->getId()} found in PIM and variant {$variantProduct->getId()} will be connected to it.\n";
                        $mainProduct->addVariant($variantProduct);
                    } else {
                        echo "Main product not found in PIM for {$listing['code']}\n";
                    }
                }
                continue;
            }
            $pimProductId = $mainProduct->getWisersellId();
            if (empty($wisersellProductId) || (($wisersellProductId+0) != ($pimProductId+0))) {
                echo "Product ID mismatch for {$listing['code']} and {$variantProduct->getId()}: WS:{$wisersellProductId} PIM:{$pimProductId}\n";
                $this->updateWisersellListing($variantProduct);
                continue;
            }
            if (empty($variantProduct->getWisersellVariantCode())) {
                echo "Variant code missing for {$listing['code']} in {$variantProduct->getId()}, updating\n";
                $this->updatePimVariantProduct($listing);
            }
        }
        echo "\n";
        echo "**** Adding missing codes from PIM to WS\n";
        foreach ($pimListings as $pimListing) {
            $variantProduct = VariantProduct::getById($pimListing['oo_id']);
            if (!$variantProduct instanceof VariantProduct) {
                continue;
            }
            if (empty($variantProduct->getMarketplace()->getWisersellStoreId())) {
                continue;
            }
            if ($variantProduct->getMarketplace()->getMarketplaceType() === 'Amazon') {
                continue;
            }
            $this->updateWisersellListing($variantProduct);
        }
        $this->flushListingBucketToWisersell();
        $this->syncAmazon();
    }

    /**
     * @throws Exception
     */
    public function calculateWisersellCode($variantProduct): string
    {
        $listingData = $this->prepareListingData($variantProduct);
        $storeId = $variantProduct->getMarketplace()->getWisersellStoreId();
        $data = empty($listingData['variantCode']) ? 
            "{$storeId}_{$listingData['storeproductid']}" : 
            "{$storeId}_{$listingData['storeproductid']}_{$listingData['variantCode']}";
        return hash('sha1', $data);
    }

    /**
     * @throws Exception
     * @throws \Exception
     */
    public function updatePimCalculatedWisersellCodes(): void
    {
        $vpl = new VariantProduct\Listing();
        $vpl->setUnpublished(true);
        $pageSize = 9;
        $offset = 0;
        $vpl->setLimit($pageSize);
        $index = 0;
        while (true) {
            $vpl->setOffset($offset);
            $variantProducts = $vpl->load();
            if (empty($variantProducts)) {
                break;
            }
            foreach ($variantProducts as $variantProduct) {
                $index++;
                echo "\rProcessing $index {$variantProduct->getId()}  ";
                if ($variantProduct->getMarketplace()->getMarketplaceType() === 'Amazon') {
                    continue;
                }
                $listingData = $this->prepareListingData($variantProduct);
                if (empty($listingData)) {
                    continue;
                }
                $calculatedWisersellCode = $this->calculateWisersellCode($variantProduct);
                if ($calculatedWisersellCode !== $variantProduct->getCalculatedWisersellCode()) {
                    echo "Calculated code changed for {$variantProduct->getId()}: {$listingData['variantStr']}\n";
                    $variantProduct->setCalculatedWisersellCode($calculatedWisersellCode);
                    $variantProduct->save();
                }
            }
            $offset += $pageSize;
        }
        echo "\n";
    }

    /**
     * @throws TransportExceptionInterface
     * @throws \Exception
     * @throws DecodingExceptionInterface
     */
    public function deleteFromWisersell($code): void
    {
        $response = $this->connector->request(Connector::$apiUrl['listing'], 'DELETE', $code);
        if (empty($response) || $response->getStatusCode() !== 200) {
            echo "Error deleting {$code}\n";
            return;
        }
        echo "Deleted {$code}\n";
        if (isset($this->wisersellListings[$code])) {
            unset($this->wisersellListings[$code]);
        }
        if (isset($this->pimListings[$code])) {
            $variantProduct = VariantProduct::getById($this->pimListings[$code]['oo_id']);
            if ($variantProduct instanceof VariantProduct) {
                $variantProduct->setWisersellVariantCode(null);
                $variantProduct->setWisersellVariantJson(null);
                $variantProduct->save();
            }
            unset($this->pimListings[$code]);
        }
    }

}