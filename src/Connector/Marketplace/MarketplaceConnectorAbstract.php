<?php

namespace App\Connector\Marketplace;

use App\Connector\Marketplace\MarketplaceConnectorInterface;
use Pimcore\Model\DataObject\Marketplace;
use App\Command\CacheImagesCommand;
use Pimcore\Model\DataObject\Data\Link;
use Symfony\Component\HttpClient\HttpClient;

abstract class MarketplaceConnectorAbstract implements MarketplaceConnectorInterface
{
    public $marketplace = null;
    public $listings = [];
    public $httpClient = null;

    public static $marketplaceType = '';

    public function __construct(Marketplace $marketplace)
    {
        if (!$marketplace instanceof Marketplace ||
            !$marketplace->getPublished() ||
            $marketplace->getMarketplaceType() !== static::$marketplaceType 
        ) {
            throw new \Exception("Marketplace is not published, is not ".static::$marketplaceType." or credentials are empty");
        }
        $this->marketplace = $marketplace;
        $this->httpClient = HttpClient::create();
    }

    public function getUrlLink($url)
    {
        if (empty($url)) {
            return null;
        }
        $l = new Link();
        $l->setPath($url);
        return $l;
    }

    public function getLatestOrderUpdate()
    {
        $db = \Pimcore\Db::get();
        return $db->fetchOne(
            "SELECT COALESCE(MAX(json_extract(json, '$.updated_at')), '2000-01-01T00:00:00Z') FROM iwa_marketplace_orders WHERE marketplace_id = ?",
            [$this->marketplace->getId()]
        );
    }

    public function getMarketplace()
    {
        return $this->marketplace;
    }

    public function convertCurrency($amount, $fromCurrency, $toCurrency) //$amount:!String $fromCurrency:!String $toCurrency:!String
    {
        if ($fromCurrency === $toCurrency) {
            return $amount;
        }
        /**
         * get today date
         * get currency rates from database
         * convert amount
         * return converted amount
         */
        $fromCurrencyValue = null;
        $toCurrencyValue = null;
        if ($fromCurrency === 'TRY')
           $fromCurrencyValue = 1;
        if ($toCurrency === 'TRY')
           $toCurrencyValue = 1;
        $today = date('Y-m-d');
        $db = \Pimcore\Db::get();
        $sql = 
        "
        SELECT
            value
        FROM 
            iwa_currency_history
        WHERE 
            currency = :currency
            AND DATE(date) <= :today
        ORDER BY 
            ABS(TIMESTAMPDIFF(DAY, DATE(date), :today)) ASC
        LIMIT 1;
        ";
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

        echo $fromCurrencyValue . ' ' . $toCurrencyValue;
        $scaledPrice = bcmul((string)$amount, "100", 2); 
        $convertedPrice = bcmul($scaledPrice, (string)$fromCurrencyValue, 2);
        $convertedPrice = bcdiv($convertedPrice, (string)$toCurrencyValue, 2);    
        $roundedPrice = bcdiv($convertedPrice, "1", 0); 
        $finalPrice = bcdiv($roundedPrice, "100", 2);
        $finalPrice = (string) $finalPrice;
        echo $finalPrice;
    }

}