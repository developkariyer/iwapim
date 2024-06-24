<?php

namespace App\Model\DataObject;

use Pimcore\Model\DataObject\ProductClass;
use Pimcore\Model\DataObject\ClassDefinition\Data;
use Pimcore\Model\DataObject\ClassDefinition\DynamicOptionsProvider\SelectOptionsProviderInterface;

class ProductClassOptionsProvider implements SelectOptionsProviderInterface
{
    public function getOptions(array $context, Data $fieldDefinition = null): array
    {
        $options = [];
        $productClasses = new ProductClass\Listing();
        $productClasses->setOrderKey('order');
        $productClasses->setOrder('asc');
        foreach ($productClasses as $productClass) {
            $options[] = [
                'key' => "{$productClass->getKey()} ({$productClass->getProductClassName()})",
                'value' => $productClass->getKey()
            ];
        }
        return $options;
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
