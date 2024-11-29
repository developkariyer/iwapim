<?php

namespace App\Select;

use Pimcore\Model\DataObject\ClassDefinition\Data;
use Pimcore\Model\DataObject\ClassDefinition\DynamicOptionsProvider\SelectOptionsProviderInterface;

use App\Connector\Marketplace\Amazon\Constants as AmazonConstants;

class AmazonMerchantIdList implements SelectOptionsProviderInterface
{
    
    public function getOptions(array $context, Data $fieldDefinition = null): array
    {
        $idList = [];
        foreach (AmazonConstants::amazonMerchant as $key=>$value) {
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
