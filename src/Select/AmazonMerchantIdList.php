<?php

namespace App\Select;

use Pimcore\Model\DataObject\ClassDefinition\Data;
use Pimcore\Model\DataObject\ClassDefinition\DynamicOptionsProvider\SelectOptionsProviderInterface;

class AmazonMerchantIdList implements SelectOptionsProviderInterface
{
    public static $amazonMerchantIdList = [
        'CA' => 'A2EUQ1WTGCTBG2',
        'US' => 'ATVPDKIKX0DER',
        'MX' => 'A1AM78C64UM0Y8',
        'BR' => 'A2Q3Y263D00KWC',
        'ES' => 'A1RKKUPIHCS9HS',
        'UK' => 'A1F83G8C2ARO7P',
        'FR' => 'A13V1IB3VIYZZH',
        'NL' => 'A1805IZSGTT6HS',
        'DE' => 'A1PA6795UKMFR9',
        'IT' => 'APJ6JRA9NG5V4',
        'SE' => 'A2NODRKZP88ZB9',
        'PL' => 'A1C3SOZRARQ6R3',
        'EG' => 'ARBP9OOSHTCHU',
        'TR' => 'A33AVAJ2PDY3EV',
        'AE' => 'A17E79C6D8DWNP',
        'IN' => 'A21TJRUUN4KGV',
        'SG' => 'A19VAU5U5O7RUS',
        'AU' => 'A39IBJ37TRP1C6',
        'JP' => 'A1VC38T7YXB528',
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
