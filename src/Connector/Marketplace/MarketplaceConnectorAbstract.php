<?php

namespace App\Connector\Marketplace;

use Exception;
use Pimcore\Db;
use Pimcore\Model\DataObject\Marketplace;
use Pimcore\Model\DataObject\Data\Link;
use Symfony\Component\HttpClient\HttpClient;
use Symfony\Contracts\HttpClient\HttpClientInterface;

abstract class MarketplaceConnectorAbstract implements MarketplaceConnectorInterface
{
    public ?Marketplace $marketplace = null;
    public array $listings = [];
    public ?HttpClientInterface $httpClient = null;

    public static string $marketplaceType = '';

    /**
     * @throws Exception
     */
    public function __construct(?Marketplace $marketplace)
    {
        if (!$marketplace instanceof Marketplace ||
            !$marketplace->getPublished() ||
            $marketplace->getMarketplaceType() !== static::$marketplaceType 
        ) {
            throw new Exception("Marketplace is not published, is not " . static::$marketplaceType . " or credentials are empty");
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

    public function getMarketplace(): ?Marketplace
    {
        return $this->marketplace;
    }

    /**
     * @throws \Doctrine\DBAL\Exception
     */
    public function convertCurrency($amount, $fromCurrency, $toCurrency): string //$amount:!String $fromCurrency:!String $toCurrency:!String
    {
        if ($fromCurrency === $toCurrency) {
            return $amount;
        }
        $today = date('Y-m-d');
        $db = Db::get();
        $sql = "SELECT value FROM iwa_currency_history WHERE currency = :currency AND DATE(date) <= :today ORDER BY ABS(TIMESTAMPDIFF(DAY, DATE(date), :today)) ASC LIMIT 1";
        $fromCurrencyValue = match ($fromCurrency) {
            'TL' => 1,
            default => $db->fetchOne($sql, [
                'today' => $today,
                'currency' => $fromCurrency
            ])
        };
        $toCurrencyValue = match ($toCurrency) {
            'TL' => 1,
            default => $db->fetchOne($sql, [
                'today' => $today,
                'currency' => $toCurrency
            ])
        };
        $scaledPrice = bcmul((string)$amount, "100", 2);
        $convertedPrice = bcmul($scaledPrice, (string)$fromCurrencyValue, 2);
        $convertedPrice = bcdiv($convertedPrice, (string)$toCurrencyValue, 2);
        $roundedPrice = bcdiv($convertedPrice, "1");
        return bcdiv($roundedPrice, "100", 2);
    }

}