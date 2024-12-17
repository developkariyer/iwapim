<?php

namespace App\Connector\Marketplace\Ozon;

use App\Connector\Marketplace\Ozon\Connector as OzonConnector;
use App\Utils\Registry;
use Symfony\Contracts\HttpClient\Exception\ClientExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\DecodingExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\RedirectionExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\ServerExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;

class Products
{
    public OzonConnector $connector;

    const string API_CATEGORY_TREE_URL = "https://api-seller.ozon.ru/v1/description-category/tree";

    public array $categoryTree;

    public function __construct(OzonConnector $connector)
    {
        $this->connector = $connector;
    }

    /**
     * @throws TransportExceptionInterface|ServerExceptionInterface|RedirectionExceptionInterface|DecodingExceptionInterface|ClientExceptionInterface
     */
    public function getCategoryTreeFromApi(): void
    {
        $this->categoryTree = $this->connector->getFromCache('CATEGORY_TREE.json');
        if (empty($this->categoryTree)) {
            $this->categoryTree = $this->connector->getApiResponse('POST', self::API_CATEGORY_TREE_URL, ['language' => 'EN']);
            $this->connector->putToCache('CATEGORY_TREE.json', $this->categoryTree);
        } else {
            echo "  Using cached category tree\n";
        }
    }

    /**
     */
    public function saveCategoryTreeToDb(): void
    {
        $this->serializeCategoryTree($this->categoryTree);
        /*
        $serializedCategoryTree = $this->serializeCategoryTree($this->categoryTree);
        $db = Db::get();
        try {
            $db->executeQuery("TRUNCATE TABLE iwa_ozon_description_category_tree");
            $db->beginTransaction();
            foreach ($serializedCategoryTree as $item) {
                $db->executeQuery(
                    "INSERT INTO iwa_ozon_description_category_tree (description_category_id, category_name, type_id, type_name, parent_id) VALUES (?, ?, ?, ?, ?)",
                    [
                        $item['description_category_id'],
                        $item['category_name'],
                        $item['type_id'],
                        $item['type_name'],
                        $item['parent_id'],
                    ]
                );
            }
            $db->commit();
        } catch (\Exception $e) {
            $db->rollBack();
            // Use a logger to log the error
            echo "Database error: " . $e->getMessage() . "\n";
        }
        */
    }

    private function serializeCategoryTree($children): void
    {
        //$serializedCategoryTree = [];
        $stack = [[
            'parentId' => null,
            'children' => $children,
        ]];
        while (!empty($stack)) {
            $current = array_pop($stack);
            $currentParentId = $current['parentId'];
            $currentChildren = $current['children'];

            foreach ($currentChildren as $child) {
                if (isset($child['description_category_id'])) {
                    Registry::setKey($child['description_category_id'], $child['category_name'], 'ozonCategory');
                    if (!is_null($currentParentId)) {
                        Registry::setKey($child['description_category_id'], $currentParentId, 'ozonCategoryParent');
                    }
                } elseif (isset($child['type_id'])) {
                    Registry::setKey($child['type_id'], $child['type_name'], 'ozonProductType');
                    Registry::setKey($child['type_id'], $currentParentId, 'ozonProductTypeParent');
                } /*
                $category = [
                    'description_category_id' => $child['description_category_id'] ?? $currentParentId,
                    'category_name' => $child['category_name'] ?? '',
                    'type_id' => $child['type_id'] ?? '',
                    'type_name' => $child['type_name'] ?? '',
                    'parent_id' => $currentParentId,
                ];
                $serializedCategoryTree[] = $category; */
                if (!empty($child['children'])) {
                    $stack[] = [
                        'parentId' => $child['description_category_id'],
                        'children' => $child['children'],
                    ];
                }
            }
        }
        // return $serializedCategoryTree;
    }

}