<?php

namespace App\Connector\Marketplace\Ozon;

use App\Connector\Marketplace\Ozon\Connector as OzonConnector;
use App\Utils\Utility;
use Doctrine\DBAL\Exception;
use Pimcore\Db;
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
        $this->categoryTree = json_decode(Utility::getCustomCache('CATEGORY_TREE.json', $this->connector->getTempPath()), true) ?? [];
        if (empty($this->categoryTree)) {
            $this->categoryTree = $this->connector->getApiResponse('POST', self::API_CATEGORY_TREE_URL, ['language' => 'EN']);
            Utility::setCustomCache('CATEGORY_TREE.json', $this->connector->getTempPath(), json_encode($this->categoryTree, JSON_PRETTY_PRINT));
        } else {
            echo "  Using cached category tree\n";
        }
    }

    /**
     * @throws Exception
     */
    public function saveCategoryTreeToDb(): void
    {
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
    }

    private function serializeCategoryTree($children): array
    {
        $serializedCategoryTree = [];
        $stack = [[
            'parentId' => null,
            'children' => $children,
        ]];
        while (!empty($stack)) {
            $current = array_pop($stack);
            $currentParentId = $current['parentId'];
            $currentChildren = $current['children'];

            foreach ($currentChildren as $child) {
                $category = [
                    'description_category_id' => $child['description_category_id'] ?? "{$currentParentId}_{$child['type_id']}",
                    'category_name' => $child['category_name'] ?? '',
                    'type_id' => $child['type_id'] ?? '',
                    'type_name' => $child['type_name'] ?? '',
                    'parent_id' => $currentParentId,
                ];
                $serializedCategoryTree[] = $category;
                if (!empty($child['children'])) {
                    $stack[] = [
                        'parentId' => $category['description_category_id'],
                        'children' => $child['children'],
                    ];
                }
            }
        }
        return $serializedCategoryTree;
    }

}