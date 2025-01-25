<?php

namespace App\Connector\Marketplace;

use Doctrine\DBAL\Exception;
use Pimcore\Db;
use Pimcore\Model\DataObject\Marketplace;
use Pimcore\Model\DataObject\Data\Link;
use Random\RandomException;
use Symfony\Component\HttpClient\HttpClient;
use Symfony\Contracts\HttpClient\HttpClientInterface;
use App\Utils\Utility;

/**
 * Class Marketplace
 * This class is the base class for all marketplace connectors. It provides basic common functionality for all marketplace connectors.
 *
 * @package App\Connector\Marketplace
 * @property Marketplace $marketplace
 * @property array|null $listings
 * @property HttpClientInterface $httpClient
 * @property string $marketplaceType
 * @property string LISTINGS_FILE_NAME
 * @property string MARKETPLACE_TEMP_PATH
 * @property string SQL_PATH
 */
abstract class MarketplaceConnectorAbstract implements MarketplaceConnectorInterface
{
    public Marketplace $marketplace;
    public ?array $listings = [];
    public HttpClientInterface $httpClient;
    public static string $marketplaceType = '';

    const string LISTINGS_FILE_NAME = 'LISTINGS.json';
    const string MARKETPLACE_TEMP_PATH = PIMCORE_PROJECT_ROOT. "/tmp/marketplaces/";
    const string SQL_PATH = PIMCORE_PROJECT_ROOT . '/src/SQL/Connector/';

    /**
     * MarketplaceConnectorAbstract constructor. Requires a Pimcore Marketplace object.
     * @throws \Exception
     */
    public function __construct(Marketplace $marketplace)
    {
        if (!$marketplace->getPublished() ||
            $marketplace->getMarketplaceType() !== static::$marketplaceType 
        ) {
            throw new \Exception("Marketplace is not published, is not ".static::$marketplaceType." or credentials are empty");
        }
        $this->marketplace = $marketplace;
        $this->httpClient = HttpClient::create();
    }

    /**
     * Used to convert url strings received from marketplace APIs to Link objects.
     * If the url is empty or not a valid url, it doesn't throw exception but only returns null.
     * @param string $url The url string to convert.
     * @return Link|null
     */
    public function getUrlLink(string $url): ?Link
    {
        if (empty($url) || !filter_var($url, FILTER_VALIDATE_URL)) {
            return null;
        }
        $l = new Link();
        $l->setPath($url);
        return $l;
    }

    /**
     * Returns the Pimcore Marketplace object attached during initialization.
     * @return Marketplace
     */
    public function getMarketplace(): Marketplace
    {
        return $this->marketplace;
    }

    /**
     * Returns the key of the marketplace object attached during initialization.
     * @return string|null
     */
    public function getMarketplaceKey(): ?string
    {
        return $this->marketplace->getKey();
    }

    /**
     * @throws Exception
     */
    public function convertCurrency($amount, $fromCurrency, $toCurrency): string //$amount:!String $fromCurrency:!String $toCurrency:!String
    {
        if ($fromCurrency === $toCurrency) {
            return $amount;
        }
        $fromCurrencyValue = ($fromCurrency === 'TL') ? 1 : null;
        $toCurrencyValue = ($toCurrency === 'TL') ? 1 : null;
        $today = date('Y-m-d');
        $db = Db::get();
        $sql = "SELECT value FROM iwa_currency_history WHERE currency = :currency AND DATE(date) <= :today ORDER BY ABS(TIMESTAMPDIFF(DAY, DATE(date), :today)) ASC LIMIT 1;";
        if ($fromCurrencyValue === null) {
            $fromCurrencyValue = $db->fetchOne($sql, [
                'today' => $today,
                'currency' => $fromCurrency
            ]);
        }
        if ($toCurrencyValue === null) {
            $toCurrencyValue = $db->fetchOne($sql, [
                'today' => $today,
                'currency' => $toCurrency
            ]);    
        }
        if (!$fromCurrencyValue || !$toCurrencyValue) {
            throw new Exception("Currency values not found for $fromCurrency or $toCurrency");
        }
        return bcmul((string)$amount, (string)($fromCurrencyValue/$toCurrencyValue), 2);
    }

    /**
     * Writes listings to cache file to avoid hammering API endpoints.
     * @return void
     * @throws RandomException
     */
    public function putListingsToCache(): void
    {
        $this->putToCache(self::LISTINGS_FILE_NAME, $this->listings);
    }

    /**
     * Load listings from cache file to listings property
     * @param int $expiration The expiration time in seconds for the cache file.
     * @param bool $lazy If true, it will randomly change expiration time +-[0-50]% to avoid hammering API endpoints at the same time.
     * @return bool
     * @throws RandomException
     */
    public function getListingsFromCache(int $expiration = 86000, bool $lazy = false): bool
    {
        $this->listings = $this->getFromCache(self::LISTINGS_FILE_NAME, $expiration, $lazy);
        return !empty($this->listings);
    }

    /**
     * Writes any data to cache file using a key.
     * @param string $key
     * @param array $data
     * @return void
     * @throws RandomException
     */
    public function putToCache(string $key, array $data): void
    {
        $this->putToCacheRaw($key, json_encode($data, JSON_PRETTY_PRINT));
    }

    /**
     * Puts raw string data to cache subsystem using a key.
     * @param string $key
     * @param string $data
     * @return void
     * @throws RandomException
     */
    public function putToCacheRaw(string $key, string $data): void
    {
        Utility::setCustomCache($key, $this->getTempPath(), $data);
    }

    /**
     * Reads data as json array from cache subsystem  using a key.
     * @param string $key
     * @param int $expires
     * @param bool $lazy
     * @return array
     * @throws RandomException
     */
    public function getFromCache(string $key, int $expires = 86000, bool $lazy = false): array
    {
        return json_decode($this->getFromCacheRaw($key, $expires, $lazy), true) ?? [];
    }

    /**
     * Reads data as raw string from cache subsystem using a key.
     * @param string $key
     * @param int $expires
     * @param bool $lazy
     * @return string
     * @throws RandomException
     */
    public function getFromCacheRaw(string $key, int $expires = 86000, bool $lazy = false): string
    {
        return Utility::getCustomCache($key, $this->getTempPath(), $expires, $lazy) ?? '';
    }

    /**
     * Returns the temporary path for the marketplace cache files.
     * @return string
     */
    public function getTempPath(): string
    {
        return self::MARKETPLACE_TEMP_PATH.urlencode($this->getMarketplaceKey());
    }

}