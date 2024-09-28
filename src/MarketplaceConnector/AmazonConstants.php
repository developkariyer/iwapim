<?php

namespace App\MarketplaceConnector;

class AmazonConstants
{
    const amazonMerchantIdList = [
        'CA' => 'A2EUQ1WTGCTBG2', // Canada
        'US' => 'ATVPDKIKX0DER', // United States
        'MX' => 'A1AM78C64UM0Y8', // Mexico
        'BR' => 'A2Q3Y263D00KWC', // Brazil
        'ES' => 'A1RKKUPIHCS9HS', // Spain
        'UK' => 'A1F83G8C2ARO7P', // United Kingdom
        'FR' => 'A13V1IB3VIYZZH', // France
        'NL' => 'A1805IZSGTT6HS', // Netherlands
        'BE' => 'A1AG1U8W7YSLMX', // Belgium
        'DE' => 'A1PA6795UKMFR9', // Germany
        'IT' => 'APJ6JRA9NG5V4', // Italy
        'SE' => 'A2NODRKZP88ZB9', // Sweden
        'PL' => 'A1C3SOZRARQ6R3', // Poland
        'SA' => 'A17E79C6D8DWNP', // Saudi Arabia
        'EG' => 'ARBP9OOSHTCHU', // Egypt
        'TR' => 'A33AVAJ2PDY3EV', // Turkey
        'AE' => 'A2VIGQ35RCS4UG', // United Arab Emirates
        'IN' => 'A21TJRUUN4KGV', // India
        'SG' => 'A19VAU5U5O7RUS', // Singapore
        'AU' => 'A39IBJ37TRP1C6', // Australia
        'JP' => 'A1VC38T7YXB528', // Japan
    ];

    const amazonWebsites = [
        'CA' => 'https://www.amazon.ca',       // Canada
        'US' => 'https://www.amazon.com',      // United States
        'MX' => 'https://www.amazon.com.mx',   // Mexico
        'BR' => 'https://www.amazon.com.br',   // Brazil
        'ES' => 'https://www.amazon.es',       // Spain
        'UK' => 'https://www.amazon.co.uk',    // United Kingdom
        'FR' => 'https://www.amazon.fr',       // France
        'NL' => 'https://www.amazon.nl',       // Netherlands
        'BE' => 'https://www.amazon.com.be',   // Belgium
        'DE' => 'https://www.amazon.de',       // Germany
        'IT' => 'https://www.amazon.it',       // Italy
        'SE' => 'https://www.amazon.se',       // Sweden
        'PL' => 'https://www.amazon.pl',       // Poland
        'SA' => 'https://www.amazon.sa',       // Saudi Arabia
        'TR' => 'https://www.amazon.com.tr',   // Turkey
        'AE' => 'https://www.amazon.ae',       // United Arab Emirates
        'IN' => 'https://www.amazon.in',       // India
        'SG' => 'https://www.amazon.sg',       // Singapore
        'AU' => 'https://www.amazon.com.au',   // Australia
        'JP' => 'https://www.amazon.co.jp',    // Japan
        'AR' => 'https://www.amazon.com.ar',   // Argentina
        'CL' => 'https://www.amazon.cl',       // Chile
        'CO' => 'https://www.amazon.com.co',   // Colombia
        'ZA' => 'https://www.amazon.co.za',    // South Africa
    ];
    
    const API_URL = 'https://api.amazon.com';
    const API_VERSION = 'v1';

    public static function checkCountryCodes($countryCodes) 
    {
        if (!empty($countryCodes)) {
            $missingCodes = array_diff($countryCodes, array_keys(static::amazonMerchantIdList));
            if (!empty($missingCodes)) {
                return false;
            }
        }
        return true;
    }

    public static function getAmazonSaleCurrency($country)
    {
        return match ($country) {
            'CA' => 'CAD',
            'US' => 'USD',
            'MX' => 'MXN',
            'TR' => 'TRY',
            'UK' => 'GBP',
            'PL' => 'PLN',
            'SE' => 'SEK',
            'SA' => 'SAR',
            'EG' => 'EGP',
            'AE' => 'AED',
            'IN' => 'INR',
            'SG' => 'SGD',
            'AU' => 'AUD',
            'JP' => 'JPY',
            default => 'EURO',
        };
    }

}

