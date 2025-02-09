<?php

namespace App\Connector\Marketplace\Amazon;

use SellingPartnerApi\Enums\Endpoint;

class Constants
{
    const array amazonMerchant = [
        'CA' => [
            'id' => 'A2EUQ1WTGCTBG2',
            'url' => 'https://www.amazon.ca',
            'currency' => 'CAD',
            'endpoint' => Endpoint::NA,
            'country' => 'Canada',
        ],
        'US' => [
            'id' => 'ATVPDKIKX0DER',
            'url' => 'https://www.amazon.com',
            'currency' => 'USD',
            'endpoint' => Endpoint::NA,
            'country' => 'United States of America',
        ],
        'MX' => [
            'id' => 'A1AM78C64UM0Y8', // Mexico
            'url' => 'https://www.amazon.com.mx',
            'currency' => 'MXN',
            'endpoint' => Endpoint::NA,
            'country' => 'Mexico',
        ],
        'BR' => [
            'id' => 'A2Q3Y263D00KWC',
            'url' => 'https://www.amazon.com.br',
            'currency' => 'BRL',
            'endpoint' => Endpoint::NA,
            'country' => 'Brazil',
        ],
        'IE' => [
            'id' => 'A28R8C7NBKEWEA',
            'url' => 'https://www.amazon.ie',
            'currency' => 'EUR',
            'endpoint' => Endpoint::EU,
            'country' => 'Ireland',
        ],
        'ES' => [
            'id' => 'A1RKKUPIHCS9HS',
            'url' => 'https://www.amazon.es',
            'currency' => 'EUR',
            'endpoint' => Endpoint::EU,
            'country' => 'Spain',
        ],
        'UK' => [
            'id' => 'A1F83G8C2ARO7P',
            'url' => 'https://www.amazon.co.uk',
            'currency' => 'GBP',
            'endpoint' => Endpoint::EU,
            'country' => 'United Kingdom',
        ],
        'FR' => [
            'id' => 'A13V1IB3VIYZZH',
            'url' => 'https://www.amazon.fr',
            'currency' => 'EUR',
            'endpoint' => Endpoint::EU,
            'country' => 'France',
        ],
        'BE' => [
            'id' => 'AMEN7PMS3EDWL',
            'url' => 'https://www.amazon.be',
            'currency' => 'EUR',
            'endpoint' => Endpoint::EU,
            'country' => 'Belgium',
        ],
        'NL' => [
            'id' => 'A1805IZSGTT6HS',
            'url' => 'https://www.amazon.nl',
            'currency' => 'EUR',
            'endpoint' => Endpoint::EU,
            'country' => 'Netherlands',
        ],
        'DE' => [
            'id' => 'A1PA6795UKMFR9',
            'url' => 'https://www.amazon.de',
            'currency' => 'EUR',
            'endpoint' => Endpoint::EU,
            'country' => 'Germany',
        ],
        'IT' => [
            'id' => 'APJ6JRA9NG5V4',
            'url' => 'https://www.amazon.it',
            'currency' => 'EUR',
            'endpoint' => Endpoint::EU,
            'country' => 'Italy',
        ],
        'SE' => [
            'id' => 'A2NODRKZP88ZB9',
            'url' => 'https://www.amazon.se',
            'currency' => 'SEK',
            'endpoint' => Endpoint::EU,
            'country' => 'Sweden',
        ],
        'ZA' => [
            'id' => 'AE08WJ6YKNBMC',
            'url' => 'https://www.amazon.ae',
            'currency' => 'ZAR',
            'endpoint' => Endpoint::EU,
            'country' => 'South Africa',
        ],
        'PL' => [
            'id' => 'A1C3SOZRARQ6R3',
            'url' => 'https://www.amazon.pl',
            'currency' => 'PLN',
            'endpoint' => Endpoint::EU,
            'country' => 'Poland',
        ],
        'EG' => [
            'id' => 'ARBP9OOSHTCHU',
            'url' => 'https://www.amazon.eg',
            'currency' => 'EGP',
            'endpoint' => Endpoint::EU,
            'country' => 'Egypt',
        ],
        'TR' => [
            'id' => 'A33AVAJ2PDY3EV',
            'url' => 'https://www.amazon.com.tr',
            'currency' => 'TRY',
            'endpoint' => Endpoint::EU,
            'country' => 'Turkey',
        ],
        'SA' => [
            'id' => 'A17E79C6D8DWNP',
            'url' => 'https://www.amazon.sa',
            'currency' => 'SAR',
            'endpoint' => Endpoint::EU,
            'country' => 'Saudi Arabia',
        ],
        'AE' => [
            'id' => 'A2VIGQ35RCS4UG',
            'url' => 'https://www.amazon.ae',
            'currency' => 'AED',
            'endpoint' => Endpoint::EU,
            'country' => 'United Arab Emirates',
        ],
        'IN' => [
            'id' => 'A21TJRUUN4KGV',
            'url' => 'https://www.amazon.in',
            'currency' => 'INR',
            'endpoint' => Endpoint::EU,
            'country' => 'India',
        ],
        'SG' => [
            'id' => 'A19VAU5U5O7RUS',
            'url' => 'https://www.amazon.sg',
            'currency' => 'SGD',
            'endpoint' => Endpoint::FE,
            'country' => 'Singapore',
        ],
        'AU' => [
            'id' => 'A39IBJ37TRP1C6',
            'url' => 'https://www.amazon.com.au',
            'currency' => 'AUD',
            'endpoint' => Endpoint::FE,
            'country' => 'Australia',
        ],
        'JP' => [
            'id' => 'A1VC38T7YXB528',
            'url' => 'https://www.amazon.co.jp',
            'currency' => 'JPY',
            'endpoint' => Endpoint::FE,
            'country' => 'Japan',
        ],
    ];

    public static function checkCountryCodes($countryCodes): bool
    {
        if (!empty($countryCodes)) {
            $missingCodes = array_diff($countryCodes, array_keys(static::amazonMerchant));
            if (!empty($missingCodes)) {
                return false;
            }
        }
        return true;
    }

    public static function getAmazonSaleCurrency($country)
    {
        return static::amazonMerchant[$country]['currency'] ?? 'USD';
    }

}

