<?php

namespace App\Select;

use Pimcore\Model\DataObject\ClassDefinition\Data;
use Pimcore\Model\DataObject\ClassDefinition\DynamicOptionsProvider\SelectOptionsProviderInterface;
use Pimcore\Model\DataObject\Currency\Listing;

class CurrencyCodes implements SelectOptionsProviderInterface
{
    public function getOptions(array $context, Data $fieldDefinition = null): array
    {
        $fieldname = $fieldDefinition->name ?? ($context["fieldname"] ?? ($context["object"]->getKey() ?? "unknown"));
        if ($fieldname !== 'currency') {
            return [];
        }
        $currencyCodes = [];
        $currencies = new Listing();
        $currencies->setUnpublished(false);
        $currencies->setOrderKey('currencyCode');
        $currencies->setOrder('asc');
        foreach ($currencies->load() as $currency) {
            if ($currency->isPublished()) {
                $currencyCodes[] = [
                    "key" => "{$currency->getKey()} ({$currency->getRate()})",
                    "value" => $currency->getKey(),
                ];
            }
        }
        return $currencyCodes;
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
