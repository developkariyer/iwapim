<?php

namespace App\Connector\Marketplace;

use Doctrine\DBAL\Exception;
use Pimcore\Db;
use Pimcore\Model\DataObject\Marketplace;
use Pimcore\Model\DataObject\Data\Link;
use Symfony\Component\HttpClient\HttpClient;
use Symfony\Contracts\HttpClient\HttpClientInterface;
use App\Utils\Utility;

abstract class MarketplaceConnectorAbstract implements MarketplaceConnectorInterface
{
    public Marketplace $marketplace;
    public ?array $listings = [];
    public HttpClientInterface $httpClient;
    public static string $marketplaceType = '';

    const string LISTINGS_FILE_NAME = 'LISTINGS.json';
    const string MARKETPLACE_TEMP_PATH = PIMCORE_PROJECT_ROOT. "/tmp/marketplaces/";

    /**
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

    public function getUrlLink($url): ?Link
    {
        if (empty($url)) {
            return null;
        }
        $l = new Link();
        $l->setPath($url);
        return $l;
    }

    public function getMarketplace(): Marketplace
    {
        return $this->marketplace;
    }

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

    public function putListingsToCache(): void
    {
        Utility::setCustomCache(self::LISTINGS_FILE_NAME, $this->getTempPath(), json_encode($this->listings, JSON_PRETTY_PRINT));
    }

    public function getListingsFromCache($expiration = 86000): bool
    {
        $this->listings = json_decode(Utility::getCustomCache(self::LISTINGS_FILE_NAME, $this->getTempPath(), $expiration), true) ?? [];
        return !empty($this->listings);
    }

    public function getTempPath(): string
    {
        return self::MARKETPLACE_TEMP_PATH.urlencode($this->marketplace->getKey());
    }

}