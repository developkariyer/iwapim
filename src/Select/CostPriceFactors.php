<?php

namespace App\Select;

use Pimcore\Model\DataObject\ClassDefinition\Data;
use Pimcore\Model\DataObject\ClassDefinition\DynamicOptionsProvider\SelectOptionsProviderInterface;
use Pimcore\Model\DataObject\Currency\Listing;

class CostPriceFactors implements SelectOptionsProviderInterface
{
    public function getOptions(array $context, Data $fieldDefinition = null): array
    {
        $nodeArray = [
            'desi3000' => 'Desi 3000',
            'desi5000' =>'Desi 5000',
            'unit'=> 'Birim',
            'package'=> 'Koli',
            'palet'=> 'Palet',
            'container'=> 'Konteyner',
            'category'=> 'Kategori',
            'valueDeclared' => 'Beyan Değeri',
            'finalPrice'=> 'Satış Fiyatı',
        ];

        $fieldname = $fieldDefinition->name ?? ($context["fieldname"] ?? ($context["object"]->getKey() ?? "unknown"));
        if ($fieldname !== 'pricingType') {
            return [];
        }
        
        return array_map(function($key, $value) {
            return ['value' => $key, 'key' => $value];
        }, array_keys($nodeArray), $nodeArray);
    }

    public function hasStaticOptions(array $context, Data $fieldDefinition): bool
    {
        return false;
    }

    public function getDefaultValue(array $context, Data $fieldDefinition): array|string|null
    {
        return 'unit';
    }

}
