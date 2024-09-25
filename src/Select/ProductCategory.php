<?php

namespace App\Select;

use Pimcore\Model\DataObject\ClassDefinition\Data;
use Pimcore\Model\DataObject\ClassDefinition\DynamicOptionsProvider\SelectOptionsProviderInterface;
use Pimcore\Model\DataObject\Product;
use Pimcore\Model\DataObject\Folder;
use Pimcore\Model\DataObject\Product\Listing;

class ProductCategory implements SelectOptionsProviderInterface
{
    public function getOptions(array $context, Data $fieldDefinition = null): array
    {
        $fieldname = $fieldDefinition->name ?? ($context["fieldname"] ?? ($context["object"]->getKey() ?? "unknown"));
        if ($fieldname !== 'productCategory') {
            return [];
        }
        $options = [];
        $listingObject = new Listing();
        $listingObject->setUnpublished(false); 
        foreach ($listingObject->load() as $product) {
            if ($product->getLevel() === 0) {
                $parentFolder = $product->getParent();
                if ($parentFolder instanceof Folder) {
                    $folderKey = $parentFolder->getKey();
                    if (strpos($folderKey, '-') !== false) {
                        $parts = explode('-', $folderKey);
                        if (isset($parts[1])) {
                            $category = trim($parts[1]); 
                            if (!in_array($category, array_column($options, 'value'))) {
                                $options[] = [
                                    "key" => $category,
                                    "value" => $category,
                                ];
                            }
                        }
                    }
                }
            }
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
