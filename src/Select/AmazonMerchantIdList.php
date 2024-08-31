<?php

namespace App\Select;

use Pimcore\Model\DataObject\ClassDefinition\Data;
use Pimcore\Model\DataObject\ClassDefinition\DynamicOptionsProvider\SelectOptionsProviderInterface;

class AmazonMerchantIdList implements SelectOptionsProviderInterface
{
    public static $amazonMerchantIdList = [
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
        //'' => '', // South Africa
        'PL' => 'A1C3SOZRARQ6R3', // Poland
        'SA' => 'A17E79C6D8DWNP', // Saudi Arabia
        'EG' => 'ARBP9OOSHTCHU', // Egypt
        'TR' => 'A33AVAJ2PDY3EV', // Turkey
        'AE' => 'A17E79C6D8DWNP', // United Arab Emirates
        'IN' => 'A21TJRUUN4KGV', // India
        'SG' => 'A19VAU5U5O7RUS', // Singapore
        'AU' => 'A39IBJ37TRP1C6', // Australia
        'JP' => 'A1VC38T7YXB528', // Japan
    ];

    public function getOptions(array $context, Data $fieldDefinition = null): array
    {
        $idList = [];
        foreach (static::$amazonMerchantIdList as $key=>$value) {
            $idList[] = [
                'key' => $key,
                'value' => $key,
            ];
        }
        return $idList;
    }

    public function hasStaticOptions(array $context, Data $fieldDefinition): bool
    {
        return false;
    }

    public function getDefaultValue(array $context, Data $fieldDefinition): array|string|null
    {
        return null;
    }

}
