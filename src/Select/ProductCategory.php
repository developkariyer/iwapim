<?php

namespace App\Select;

use Pimcore\Model\DataObject\ClassDefinition\Data;
use Pimcore\Model\DataObject\ClassDefinition\DynamicOptionsProvider\SelectOptionsProviderInterface;
use Pimcore\Model\DataObject\Category\Listing;

class ProductCategory implements SelectOptionsProviderInterface{
    public function getOptions(array $context, Data $fieldDefinition = null): array{
        $fieldname = $fieldDefinition->name ?? ($context["fieldname"] ?? ($context["object"]->getKey() ?? "unknown"));
        if ($fieldname !== 'productCategory') {
            return [];
        }
        $options = [];
        $categories = new Listing();
        $categories->setOrderKey('category');
        $categories->setOrder('asc');
        foreach ($categories->load() as $category) {
            if ($category->isPublished()) {
                $options[] = [
                    "key" => $category->getCategory(),
                    "value" => $category->getCategory(),
                ];
            }
        }
        return $options;
    }
    public function hasStaticOptions(array $context, Data $fieldDefinition): bool{
        return false; 
    }
    public function getDefaultValue(array $context, Data $fieldDefinition): array|string|null{
        return null;
    }
}
