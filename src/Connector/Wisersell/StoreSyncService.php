<?php

namespace App\Connector\Wisersell;

use App\Connector\Wisersell\Connector;
use Pimcore\Model\DataObject\Marketplace;
use App\Utils\Utility;


class StoreSyncService
{
    protected $connector;
    protected $wisersellStores = []; // [id => store]
    protected $pimStores = []; // [storeType => [storeId => store]]

    public function __construct(Connector $connector)
    {
        $this->connector = $connector;
    }

    public function loadPimStores($force = false)
    {
        if (!$force && !empty($this->pimStores)) {
            return;
        }
        $this->pimStores = [];
        $stores = new Marketplace\Listing();
        $stores->setUnpublished(false);
        $this->pimStores = [];
        foreach ($stores as $store) {
            $storeType = $store->getMarketplaceType();
            $storeId = match ($storeType) {
                'Etsy' => $store->getShopId(),
                'Shopify' => $store->getShopId(),
                'Amazon' => $store->getMerchantId(),
                'Trendyol' => $store->getTrendyolSellerId(),
                default => null,
            };
            if (empty($storeType) || empty($storeId)) {
                continue;
            }
            if (!isset($this->pimStores[$storeType])) {
                $this->pimStores[$storeType] = [];
            }
            $this->pimStores[$storeType][$storeId] = $store;
        }
    }

    public function loadWisersellStores($force = false)
    {
        if (!$force && !empty($this->wisersellStores)) {
            return;
        }
        $this->wisersellStores = json_decode(Utility::getCustomCache('stores.json', PIMCORE_PROJECT_ROOT . '/tmp/wisersell'), true);
        if (!$force && !empty($this->wisersellStores)) {
            return;
        }
        $this->wisersellStores = [];
        $response = $this->connector->request(Connector::$apiUrl['store'], 'GET');
        if (empty($response)) {
            return;
        }
        foreach ($response->toArray() as $store) {
            $this->wisersellStores[$store['id']] = $store;
        }
        Utility::setCustomCache('stores.json', PIMCORE_PROJECT_ROOT . '/tmp/wisersell', json_encode($this->wisersellStores));
    }

    public function load($force = false)
    {
        $this->loadPimStores($force);
        $this->loadWisersellStores($force);
    }

    public function syncStores()
    {
        $this->load();
        echo "PIM Stores loaded: ";
        foreach ($this->pimStores as $key => $store) {
            echo "$key (" . count($store) . ") ";
        }
        echo PHP_EOL;        
        echo "Wisersell Stores loaded: " .count($this->wisersellStores) .  PHP_EOL;
        return;
        foreach ($this->wisersellStores as $wisersellStore) {
            $storeType = $wisersellStore['source']['name'] ?? null;
            $storeId = $wisersellStore['shopId'] ?? null;
            if (empty($storeType) || empty($storeId) || !isset($this->pimStores[$storeType][$storeId])) {
                continue;
            }
            $pimStore = $this->pimStores[$storeType][$storeId];
            if (!$pimStore instanceof Marketplace) {
                continue;
            }
            $pimStore->setWisersellStoreId($wisersellStore['id']);
            $pimStore->save();
        }
    }

}