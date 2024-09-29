<?php

namespace App\MarketplaceConnector;

class AmazonConstants
{
    const amazonMerchant = [
        'CA' => [
            'id' => 'A2EUQ1WTGCTBG2', // Canada
            'url' => 'https://www.amazon.ca',
            'currency' => 'CAD'
        ],
        'US' => [
            'id' => 'ATVPDKIKX0DER', // United States
            'url' => 'https://www.amazon.com',
            'currency' => 'USD'
        ],
        'MX' => [
            'id' => 'A1AM78C64UM0Y8', // Mexico
            'url' => 'https://www.amazon.com.mx',
            'currency' => 'MXN'
        ],
        'BR' => [
            'id' => 'A2Q3Y263D00KWC', // Brazil
            'url' => 'https://www.amazon.com.br',
            'currency' => 'BRL' // Brazilian Real
        ],
        'ES' => [
            'id' => 'A1RKKUPIHCS9HS', // Spain
            'url' => 'https://www.amazon.es',
            'currency' => 'EUR' // Euro
        ],
        'UK' => [
            'id' => 'A1F83G8C2ARO7P', // United Kingdom
            'url' => 'https://www.amazon.co.uk',
            'currency' => 'GBP'
        ],
        'FR' => [
            'id' => 'A13V1IB3VIYZZH', // France
            'url' => 'https://www.amazon.fr',
            'currency' => 'EUR' // Euro
        ],
        'NL' => [
            'id' => 'A1805IZSGTT6HS', // Netherlands
            'url' => 'https://www.amazon.nl',
            'currency' => 'EUR' // Euro
        ],
        'BE' => [
            'id' => 'A1AG1U8W7YSLMX', // Belgium
            'url' => 'https://www.amazon.com.be',
            'currency' => 'EUR' // Euro
        ],
        'DE' => [
            'id' => 'A1PA6795UKMFR9', // Germany
            'url' => 'https://www.amazon.de',
            'currency' => 'EUR' // Euro
        ],
        'IT' => [
            'id' => 'APJ6JRA9NG5V4', // Italy
            'url' => 'https://www.amazon.it',
            'currency' => 'EUR' // Euro
        ],
        'SE' => [
            'id' => 'A2NODRKZP88ZB9', // Sweden
            'url' => 'https://www.amazon.se',
            'currency' => 'SEK' // Swedish Krona
        ],
        'PL' => [
            'id' => 'A1C3SOZRARQ6R3', // Poland
            'url' => 'https://www.amazon.pl',
            'currency' => 'PLN' // Polish Zloty
        ],
        'SA' => [
            'id' => 'A17E79C6D8DWNP', // Saudi Arabia
            'url' => 'https://www.amazon.sa',
            'currency' => 'SAR' // Saudi Riyal
        ],
        'EG' => [
            'id' => 'ARBP9OOSHTCHU', // Egypt
            'url' => 'https://www.amazon.eg',
            'currency' => 'EGP' // Egyptian Pound
        ],
        'TR' => [
            'id' => 'A33AVAJ2PDY3EV', // Turkey
            'url' => 'https://www.amazon.com.tr',
            'currency' => 'TRY' // Turkish Lira
        ],
        'AE' => [
            'id' => 'A2VIGQ35RCS4UG', // United Arab Emirates
            'url' => 'https://www.amazon.ae',
            'currency' => 'AED' // UAE Dirham
        ],
        'IN' => [
            'id' => 'A21TJRUUN4KGV', // India
            'url' => 'https://www.amazon.in',
            'currency' => 'INR' // Indian Rupee
        ],
        'SG' => [
            'id' => 'A19VAU5U5O7RUS', // Singapore
            'url' => 'https://www.amazon.sg',
            'currency' => 'SGD' // Singapore Dollar
        ],
        'AU' => [
            'id' => 'A39IBJ37TRP1C6', // Australia
            'url' => 'https://www.amazon.com.au',
            'currency' => 'AUD' // Australian Dollar
        ],
        'JP' => [
            'id' => 'A1VC38T7YXB528', // Japan
            'url' => 'https://www.amazon.co.jp',
            'currency' => 'JPY' // Japanese Yen
        ],
        'AR' => [
            'id' => null, // No ID provided
            'url' => 'https://www.amazon.com.ar',
            'currency' => 'ARS' // Argentine Peso
        ],
        'CL' => [
            'id' => null, // No ID provided
            'url' => 'https://www.amazon.cl',
            'currency' => 'CLP' // Chilean Peso
        ],
        'CO' => [
            'id' => null, // No ID provided
            'url' => 'https://www.amazon.com.co',
            'currency' => 'COP' // Colombian Peso
        ],
        'ZA' => [
            'id' => null, // No ID provided
            'url' => 'https://www.amazon.co.za',
            'currency' => 'ZAR' // South African Rand
        ]
    ];

    public static function checkCountryCodes($countryCodes) 
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

