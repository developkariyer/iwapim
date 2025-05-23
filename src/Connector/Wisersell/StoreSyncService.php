<?php

namespace App\Connector\Wisersell;

use Pimcore\Model\DataObject\Marketplace;
use App\Utils\Utility;
use Random\RandomException;
use Symfony\Contracts\HttpClient\Exception\ClientExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\DecodingExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\RedirectionExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\ServerExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;


class StoreSyncService
{
    protected Connector $connector;
    public array $wisersellStores = []; // [id => store]
    public array $pimStores = []; // [storeType => [storeId => store]]

    public function __construct(Connector $connector)
    {
        $this->connector = $connector;
    }

    public function loadPimStores($force = false): void
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
                'Etsy', 'Shopify' => $store->getShopId(),
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

    /**
     * @throws TransportExceptionInterface
     * @throws ServerExceptionInterface
     * @throws RedirectionExceptionInterface
     * @throws DecodingExceptionInterface
     * @throws ClientExceptionInterface|RandomException
     */
    public function loadWisersellStores($force = false): int
    {
        if (!$force && !empty($this->wisersellStores)) {
            return time()-filemtime(PIMCORE_PROJECT_ROOT . '/tmp/wisersell/stores.json');
        }
        $this->wisersellStores = json_decode(Utility::getCustomCache('stores.json', PIMCORE_PROJECT_ROOT . '/tmp/wisersell'), true) ?? [];
        if (!$force && !empty($this->wisersellStores)) {
            return time()-filemtime(PIMCORE_PROJECT_ROOT . '/tmp/wisersell/stores.json');
        }
        $this->wisersellStores = [];
        $response = $this->connector->request(Connector::$apiUrl['store'], 'GET');
        if (empty($response)) {
            return time()-filemtime(PIMCORE_PROJECT_ROOT . '/tmp/wisersell/stores.json');
        }
        foreach ($response->toArray() as $store) {
            $this->wisersellStores[$store['id']] = $store;
        }
        Utility::setCustomCache('stores.json', PIMCORE_PROJECT_ROOT . '/tmp/wisersell', json_encode($this->wisersellStores));
        return time()-filemtime(PIMCORE_PROJECT_ROOT . '/tmp/wisersell/stores.json');
    }

    /**
     * @throws TransportExceptionInterface
     * @throws ServerExceptionInterface
     * @throws RedirectionExceptionInterface
     * @throws DecodingExceptionInterface
     * @throws ClientExceptionInterface|RandomException
     */
    public function load($force = false): int
    {
        $this->loadPimStores($force);
        return $this->loadWisersellStores($force);
    }

    /**
     * @throws TransportExceptionInterface
     * @throws ServerExceptionInterface
     * @throws RedirectionExceptionInterface
     * @throws DecodingExceptionInterface
     * @throws ClientExceptionInterface|RandomException
     */
    public function status(): array
    {
        $cacheExpire = $this->load();
        return [
            'pim' => array_sum(array_map('count', $this->pimStores)),
            'wisersell' => count($this->wisersellStores),
            'expire' => 86400-$cacheExpire,
        ];
    }

    /**
     * @throws TransportExceptionInterface
     * @throws ServerExceptionInterface
     * @throws RedirectionExceptionInterface
     * @throws DecodingExceptionInterface
     * @throws ClientExceptionInterface|RandomException
     */
    public function dump(): void
    {
        $this->load();
        file_put_contents(PIMCORE_PROJECT_ROOT . '/tmp/wisersell/stores.wisersell.txt', print_r($this->wisersellStores, true));
        file_put_contents(PIMCORE_PROJECT_ROOT . '/tmp/wisersell/stores.pim.txt', print_r($this->pimStores, true));
    }

    /**
     * @throws TransportExceptionInterface
     * @throws ServerExceptionInterface
     * @throws RedirectionExceptionInterface
     * @throws DecodingExceptionInterface
     * @throws ClientExceptionInterface|RandomException
     */
    public function sync(): void
    {
        $this->load();
        echo "Stores loaded: ";
        foreach ($this->pimStores as $key => $store) {
            echo "$key(" . count($store) . ") ";
        }
        echo "Wisersell(" .count($this->wisersellStores) . ")" .  PHP_EOL;
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
            if ($pimStore->getMarketplaceType() === 'Amazon') {
                continue;
            }
            //$pimStore->setWisersellStoreId($wisersellStore['id']);
            //$pimStore->save();
            echo "  Matched Wisersell $storeType $storeId to {$pimStore->getId()} {$pimStore->getKey()}" . PHP_EOL;
        }
    }

    /**
     * @throws TransportExceptionInterface
     * @throws ServerExceptionInterface
     * @throws RedirectionExceptionInterface
     * @throws DecodingExceptionInterface
     * @throws ClientExceptionInterface|RandomException
     */
    public function getAmazonStoreIds(): array
    {
        $this->load();
        $storeIds = [];
        foreach ($this->wisersellStores as $wisersellStore) {
            if ($wisersellStore['source']['name'] === 'Amazon') {
                if (strlen($wisersellStore['id']) > 0) {
                    $storeIds[] = $wisersellStore['id'];
                }
            }
        }
        return array_unique($storeIds);
    }

}