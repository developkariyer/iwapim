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
        function serializeCategoryTree($parentId, $children): array
        {
            $serializedCategoryTree = [];
            foreach ($children as $child) {
                $serializedCategoryTree[] = [
                    'description_category_id' => $child['description_category_id'] ?? "{$parentId}_{$child['type_id']}",
                    'category_name' => $child['category_name'] ?? '',
                    'type_id' => $child['type_id'] ?? '',
                    'type_name' => $child['type_name'] ?? '',
                    'parent_id' => $parentId,
                ];
                if (!empty($child['children'])) {
                    $serializedCategoryTree = array_merge($serializedCategoryTree, serializeCategoryTree($child['description_category_id'] ?? $child['type_id'], $child['children']));
                }
            }
            echo count($serializedCategoryTree) . " categories\n";
            return $serializedCategoryTree;
        }

        $serializedCategoryTree = serializeCategoryTree(null, $this->categoryTree);

        $db = Db::get();
        $db->executeQuery("TRUNCATE TABLE ozon_category_tree");
        foreach ($serializedCategoryTree as $item) {
            $db->insert('iwa_ozon_description_category_tree', [
                'description_category_id' => $item['description_category_id'],
                'category_name' => $item['category_name'],
                'type_id' => $item['type_id'],
                'type_name' => $item['type_name'],
                'parent_id' => $item['parent_id'],
            ]);
        }
    }
}