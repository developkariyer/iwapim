<?php

namespace App\Connector\Wisersell;

use App\Connector\Wisersell\Connector;
use Pimcore\Model\DataObject\VariantProduct;

class ListingSyncService
{
    protected $connector;
    public $wisersellListings = [];   // code => wisersell listing array (check swagger)
    public $pimListings = [];   // calculatedWisersellCode => [oo_id, wisersellVariantCode, calculatedWisersellCode]
    public $bucket = [];
    public function __construct(Connector $connector)
    {
        $this->connector = $connector;
    }

    public function loadWisersell($force = false)
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

    public function loadPim($force = false) 
    {
        if (!$force && !empty($this->pimListings)) {
            return;
        }
        $db = \Pimcore\Db::get();
        $this->pimListings = [];
        $listings = $db->fetchAllAssociative('SELECT oo_id, wisersellVariantCode, calculatedWisersellCode FROM object_varyantproduct WHERE published = 1');
        foreach ($listings as $listing) {
            if (strlen($listing['calculatedWisersellCode'])<1) {
                continue;
            }
            $this->pimListings[$listing['calculatedWisersellCode']] = $listing;
        }
    }

    public function status()
    {
        $cacheExpire = $this->load();
        return [
            'wisersell' => count($this->wisersellListings),
            'pim' => count($this->pimListings),
            'expire' => 86400-$cacheExpire
        ];
    }

    public function load($force = false)
    {
        $this->loadPim($force);
        return $this->loadWisersell($force);
    }

    public function search($params)
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

    public function prepareListingData($variantProduct)
    {
        if (!$variantProduct instanceof VariantProduct) {
            echo "Not a variant product\n";
            return null;
        }
        $mainProduct = $variantProduct->getMainProduct()[0] ?? null;
        if (!$mainProduct instanceof Product) {
            echo "{$variantProduct->getId()} do not have a main product\n";
            return null;
        }
        $marketplace = $variantProduct->getMarketplace();
        $marketplaceType = $marketplace->getMarketplaceType();
        $apiResponse = json_decode($variantProduct->jsonRead('apiResponseJson'), true);
        $parentResponse = json_decode($variantProduct->jsonRead('parentResponseJson'), true);
        $productId = $mainProduct->getWisersellId();
        $storeProductId = match ($marketplaceType) {
            'Etsy' => $apiResponse['product_id'] ?? null,
            'Amazon' => $apiResponse['asin'] ?? null,
            'Shopify' => $apiResponse['product_id'] ?? null,
            'Trendyol' => $apiResponse['productCode'] ?? null,
            default => null,
        };
        $shopId = match ($marketplaceType) {
            'Etsy' => $marketplace->getShopId(),
            'Amazon' => $marketplace->getMerchantId(),
            'Shopify' => $marketplace->getShopId(),
            'Trendyol' => $marketplace->getTrendyolSellerId(),
            default => null,
        };
        $variantCode = match ($marketplaceType) {
            'Etsy' => $parentResponse['listing_id'] ?? null,
            'Amazon' => null,
            'Shopify' => $apiResponse['id'] ?? null,
            'Trendyol' => $apiResponse['platformListingId'] ?? null,
            default => null,
        };
        if (empty($storeProductId) || empty($shopId) || (empty($variantCode) && $marketplaceType !== 'Amazon') || empty($productId)) {
            echo "Empty data for {$variantProduct->getId()}: {$marketplaceType}, {$shopId}, {$storeProductId}, {$variantCode}, {$productId}\n";
            return null;
        }
        return [
            'storeproductid' => $storeProductId,
            'productid' => $productId,
            'shopId' => $shopId,
            'variantCode' => $variantCode,
            'variantStr' => "{$marketplaceType} PId:{$storeProductId} VId:{$variantCode} PimId:{$productId}"
        ];
    }

    public function updateWisersellListing($variantProduct)
    {
        $this->load();
        $code = $variantProduct->getCalculatedWisersellCode();
        if (!isset($this->wisersellListings[$code])) {
            $this->addVariantProductToWisersell($variantProduct);
            return;
        }
        $listingData = $this->prepareListingData($variantProduct);
        if (empty($listingData)) {
            return;
        }
        $response = $this->connector->request(Connector::$apiUrl['listing'], 'PUT', $code, [
            'productId' => $listingData['productid'],
            'shopId' => $listingData['shopId'],
        ]);
        if (empty($response) || $response->getStatusCode() !== 200) {
            return;
        }
        $response = $response->toArray();
        $this->updatePimVariantProduct($response);
    }

    public function updatePimVariantProduct($listing)
    {
        $variantProduct = VariantProduct::getByCalculatedWisersellCode($listing['code'], ['limit' => 1]);
        if (!$variantProduct instanceof VariantProduct) {
            return;
        }
        $variantProduct->setWisersellVariantJson(json_encode($listing));
        $variantProduct->setWisersellVariantCode($listing['code']);
        $variantProduct->save();
    }

    public function addVariantProductToWisersell($variantProduct)
    {
        $listingData = $this->prepareListingData($variantProduct);
        if (empty($listingData)) {
            return;
        }
        $this->bucket[] = $listingData;
        if (count($this->bucket) >= 100) {
            $this->flushListingBucketToWisersell();
        }
    }

    public function flushListingBucketToWisersell()
    {
        if (empty($this->bucket)) {
            return;
        }
        $response = $this->connector->request(Connector::$apiUrl['listing'], 'POST', '', $this->bucket);
        if (empty($response) || $response->getStatusCode() !== 200) {
            return;
        }
        $response = $response->toArray();
        foreach ($response as $listing) {
            $this->updatePimVariantProduct($listing);
        }
        $this->bucket = [];
    }

    public function sync()
    {
        $this->load();
        foreach ($this->wisersellListings as $listing) {
            $variantProduct = VariantProduct::getByCalculatedWisersellCode($listing['code'], ['limit' => 1]);
            if (!$variantProduct instanceof VariantProduct) {
                // delete listing from wisersell
                continue;
            }
            $mainProduct = $variantProduct->getMainProduct()[0] ?? null;
            if (!$mainProduct instanceof Product) {
                // not connected or multiple connected in pim
                continue;
            }
            $wisersellProductId = $listing['product']['id'] ?? null;
            $pimProductId = $variantProduct->getMainProduct()->getWisersellId();
            if (!is_null($wisersellProductId) && $wisersellProductId !== $pimProductId) {
                $this->updateWisersellListing($variantProduct);
                continue;
            }
            if (empty($variantProduct->getWisersellVariantCode())) {
                $this->updatePimVariantProduct($listing);
                continue;
            }
        }
    }

    public function calculateWisersellCode($listingData)
    {
        $data = empty($listingData['variantCode']) ? 
            "{$listingData['shopId']}_{$listingData['storeproductid']}" : 
            "{$listingData['shopId']}_{$listingData['storeproductid']}_{$listingData['variantCode']}";
        return hash('sha1', $data);
    }

    public function updatePimCalculatedWisersellCodes()
    {
        $vpl = new VariantProduct\Listing();
        $vpl->setUnpublished(true);
        $pageSize = 50;
        $offset = 0;
        $emptyData = $fullData = 0;
        $vpl->setLimit($pageSize);
        while (true) {
            echo "Processing $pageSize from $offset, until now $emptyData/$fullData\n";
            $vpl->setOffset($offset);
            $variantProducts = $vpl->load();
            if (empty($variantProducts)) {
                break;
            }
            foreach ($variantProducts as $variantProduct) {
                $listingData = $this->prepareListingData($variantProduct);
                if (empty($listingData)) {
                    $emptyData++;
                    continue;
                }
                $fullData++;
                $calculatedWisersellCode = $this->calculateWisersellCode($listingData);
                echo "{$variantProduct->getId()}: ".($calculatedWisersellCode === $variantProduct->getCalculatedWisersellCode())."\n";
//                $variantProduct->setCalculatedWisersellCode($calculatedWisersellCode);
//                $variantProduct->save();
            }
            $offset += $pageSize;
        }

    }
}