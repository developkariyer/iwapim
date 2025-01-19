<?php

namespace App\Model\DataObject;

use Pimcore\Model\DataObject\Concrete;
use Pimcore\Model\DataObject\Currency\Listing;

/**
* Class Currency
*
* This class represents a currency model in the system. 
* It provides methods for retrieving currency details and converting amounts between different currencies.
* 
* @package App\Model\DataObject
*/
class Currency extends Concrete
{
    /**
    * Retrieves a currency object by its currency code.
    *
    * @param string $currencyCode The currency code to search for.
    * @return Currency|null The currency object or null if not found.
    */
    protected static function getCurrency(string $currencyCode)
    {
        switch ($currencyCode) {
            case 'CAD':
                $currencyCode = 'CANADIAN DOLLAR';
                break;
            case 'TRY':
                $currencyCode = 'TL';
                break;
            case 'EUR':
                $currencyCode = 'EURO';
                break;
            case 'USD':
                $currencyCode = 'US DOLLAR';
                break;
            case 'SEK':
                $currencyCode = 'SWEDISH KRONA';
                break;
            case 'GBP':
                $currencyCode = 'POUND STERLING';
                break;
            default:
                break;
        }

        $list = new Listing();
        $list->setCondition('currencyCode = ?', $currencyCode);
        $list->setUnpublished(true);
        $list->setLimit(1);
        return $list->current();
    }

    /**
    * Converts an amount from one currency to another.
    *
    * @param string $fromCurrency The currency code to convert from.
    * @param string $amount The amount to convert.
    * @param string $toCurrency The currency code to convert to. Defaults to 'TL'.
    * @return string The converted amount or "0.00" if conversion fails.
    */
    public static function convertCurrency(string $fromCurrency, string $amount, string $toCurrency = 'TL'): string
    {
        if ($fromCurrency === $toCurrency) {
            return $amount;
        }

        $fm = static::getCurrency($fromCurrency);
        $to = static::getCurrency($toCurrency);

        if ($fm && $to && $fm->getRate() && $to->getRate()) {
            $fromRate = (string) $fm->getRate();
            $toRate = (string) $to->getRate();

            $result = bcdiv(
                bcmul(
                    $amount, 
                    $fromRate, 
                    4
                ), 
                $toRate,
                4
            );
            return $result;
        }

        error_log("Currency conversion failed. From: $fromCurrency To: $toCurrency");
        return "0.00";
    }
}
