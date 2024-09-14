<?php

namespace App\Model\DataObject;

use Pimcore\Model\DataObject\Concrete;
use Pimcore\Model\DataObject\Currency\Listing;

class Currency extends Concrete
{

    protected static function getCurrency($currencyCode)
    {
        $list = new Listing();
        $list->setCondition('currencyCode = ?', $currencyCode);
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
            $result = bcdiv(bcmul($amount, number_format($fm->getRate(), 2, '.', ''), 2), number_format($to->getRate(), 2, '.', ''), 2);
            return $result;
        } 
        return "0.00";
    }

}