<?php

namespace App\Select;

use Pimcore\Model\DataObject\ClassDefinition\Data;
use Pimcore\Model\DataObject\ClassDefinition\DynamicOptionsProvider\SelectOptionsProviderInterface;

class OzonProductTypes implements SelectOptionsProviderInterface
{
    public function getOptions(array $context, Data $fieldDefinition = null): array
    {
        $ozonCategoryTree = json_decode(file_get_contents(PIMCORE_PROJECT_ROOT . '/tmp/marketplaces/Ozon/CATEGORY_TREE.json'), true);
        $stack = [
            [
                'id' => 0,
                'children' => $ozonCategoryTree,
                'parent_text' => '',
            ],
        ];
        $options = [];
        while (!empty($stack)) {
            $node = array_pop($stack);
            foreach ($node['children'] as $child) {
                $parent_text = $node['parent_text'];
                if (isset($child['description_category_id'])) {
                    $parent_text .= "{$child['category_name']}.";
                } else {
                    $options[] = [
                        'key' => $parent_text . $child['category_name'],
                        'value' => "{$child['id']}_{$child['category_name']}",
                    ];
                }
                if (!empty($child['children'])) {
                    $stack[] = [
                        'id' => $child['id'],
                        'children' => $child['children'],
                        'parent_text' => $parent_text,
                    ];
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
