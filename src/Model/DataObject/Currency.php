<?php

namespace App\Model\DataObject;

use Pimcore\Model\DataObject\Concrete;
use Pimcore\Model\DataObject\Currency\Listing;

class Currency extends Concrete
{


    
    public static function convertCurrency($fromCurrency, $amount, $toCurrency = null)
    {
        $list = new Listing();
        $list->setCondition('currencyCode = ?', $fromCurrency);
        $list->setLimit(1);
        $currencyObject = $list->current();
        $amount = number_format($amount, 2, '.', '');
        if ($currencyObject && bccomp($currencyObject->getRate(), '0', 2) > 0) {
            $result = bcmul($amount, number_format($currencyObject->getRate(), 2, '.', ''), 2);
            return $result;
        }
        return $amount;
    }

}