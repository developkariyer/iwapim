<?php

namespace App\Model\DataObject;

use Pimcore\Model\DataObject\Concrete;
use Pimcore\Model\DataObject\Currency\Listing;

class Currency extends Concrete
{
    protected static function getCurrency($currencyCode)
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
    
    public static function convertCurrency($fromCurrency, $amount, $toCurrency = 'TL')
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
