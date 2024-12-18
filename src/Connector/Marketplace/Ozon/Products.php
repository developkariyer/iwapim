<?php

namespace App\Connector\Marketplace\Ozon;

use App\Connector\Marketplace\Ozon\Connector as OzonConnector;
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
    const string API_CATEGORY_ATTRIBUTE_URL = "https://api-seller.ozon.ru/v1/description-category/attribute";
    const string API_ATTRIBUTE_VALUE_URL = "https://api-seller.ozon.ru/v1/description-category/attribute/values";

    const string OZON_CATEGORY_TABLE = 'iwa_ozon_category';
    const string OZON_PRODUCTTYPE_TABLE = 'iwa_ozon_category_producttype';
    const string OZON_CATEGORY_ATTRIBUTE_TABLE = 'iwa_ozon_category_producttype_attribute';
    const string OZON_ATTRIBUTE_TABLE = 'iwa_ozon_attribute';
    const string OZON_VALUE_TABLE = 'iwa_ozon_attribute_value';

    public function __construct(OzonConnector $connector)
    {
        $this->connector = $connector;
    }

    /**
     * @throws TransportExceptionInterface|ServerExceptionInterface|RedirectionExceptionInterface|DecodingExceptionInterface|ClientExceptionInterface|Exception
     */
    public function getCategoryTreeFromApi(): void
    {
        echo "\nGetting category tree from API: ";
        $categoryTree = $this->connector->getFromCache('CATEGORY_TREE.json', 7 * 86400);
        if (empty($categoryTree)) {
            echo "asking Ozon\n";
            $categoryTree = $this->connector->getApiResponse('POST', self::API_CATEGORY_TREE_URL, ['language' => 'EN']);
            $this->connector->putToCache('CATEGORY_TREE.json', $categoryTree);
        } else {
            echo "using cached category tree\n";
        }
        $this->processCategoryTree($categoryTree);
    }

    /**
     * @throws Exception
     */
    public function processCategoryTree($categoryTree): void
    {
        echo "Processing category tree\n";
        $db = Db::get();
        $db->beginTransaction();
        try {
            $stack = [[
                'parentId' => null,
                'children' => $categoryTree ?? [],
            ]];
            while (!empty($stack)) {
                $current = array_pop($stack);
                $currentParentId = $current['parentId'];
                $currentChildren = $current['children'];
                echo "                      \r".($currentParentId ?? 'root');
                foreach ($currentChildren as $child) {
                    if (isset($child['description_category_id'])) {
                        $db->executeStatement("INSERT INTO " . self::OZON_CATEGORY_TABLE . " (description_category_id, parent_id, category_name) VALUES (?, ?, ?) ON DUPLICATE KEY UPDATE parent_id = ?, category_name = ?", [
                            $child['description_category_id'],
                            $currentParentId,
                            $child['category_name'],
                            $currentParentId,
                            $child['category_name'],
                        ]);
                    } elseif (isset($child['type_id'])) {
                        $db->executeStatement("INSERT INTO " . self::OZON_PRODUCTTYPE_TABLE . " (description_category_id, type_id, type_name) VALUES (?, ?, ?) ON DUPLICATE KEY UPDATE type_name = ?", [
                            $currentParentId,
                            $child['type_id'],
                            $child['type_name'],
                            $child['type_name'],
                        ]);
                    }
                    if (!empty($child['children'])) {
                        $stack[] = [
                            'parentId' => $child['description_category_id'],
                            'children' => $child['children'],
                        ];
                    }
                }
            }
            $db->commit();
            echo count($categoryTree) . " categories processed\n";
        } catch (Exception $e) {
            $db->rollBack();
            throw $e;
        }
    }

    /**
     * @throws TransportExceptionInterface|ServerExceptionInterface|RedirectionExceptionInterface|DecodingExceptionInterface|ClientExceptionInterface|Exception
     */
    public function getCategoryAttributesFromApi(): void
    {
        echo "Getting category attributes from API\n";
        $db = Db::get();
        $productTypes = $db->fetchAllAssociative("SELECT description_category_id, type_id FROM " . self::OZON_PRODUCTTYPE_TABLE . " ORDER BY description_category_id, type_id");
        $db->beginTransaction();
        $index = 0;
        try {
            foreach ($productTypes as $productType) {
                $categoryId = $productType['description_category_id'];
                $typeId = $productType['type_id'];
                echo "                \r$categoryId.$typeId";
                $response = $this->connector->getFromCache("CATEGORY_ATTRIBUTES_{$categoryId}_{$typeId}.json", 7 * 86400);
                if (empty($response)) {
                    echo " *";
                    $response = $this->connector->getApiResponse('POST', self::API_CATEGORY_ATTRIBUTE_URL, ['description_category_id' => $categoryId, 'language' => 'EN', 'type_id' => $typeId]);
                    $this->connector->putToCache("CATEGORY_ATTRIBUTES_{$categoryId}_{$typeId}.json", $response);
                }
                foreach ($response as $attribute) {
                    $index++;
                    $db->executeStatement("INSERT IGNORE INTO " . self::OZON_CATEGORY_ATTRIBUTE_TABLE . " (description_category_id, type_id, attribute_id, group_id) VALUES (?, ?, ?, ?)", [
                        $categoryId,
                        $typeId,
                        $attribute['id'],
                        $attribute['group_id'],
                    ]);
                    $db->executeStatement("INSERT INTO " . self::OZON_ATTRIBUTE_TABLE . " (attribute_id, group_id, attribute_json) VALUES (?, ?, ?) ON DUPLICATE KEY UPDATE attribute_json = ?", [
                        $attribute['id'],
                        $attribute['group_id'],
                        json_encode($attribute),
                        json_encode($attribute),
                    ]);
                    if ($index % 1000 === 0) {
                        $db->commit();
                        $db->beginTransaction();
                    }
                }
            }
            $db->commit();
            echo "\n";
        } catch (Exception $e) {
            $db->rollBack();
            throw $e;
        }
    }

    /**
     * @throws TransportExceptionInterface|ServerExceptionInterface|RedirectionExceptionInterface|DecodingExceptionInterface|ClientExceptionInterface|Exception
     */
    public function getAttributeValuesFromApi(): void
    {
        echo "Getting attribute values from API\n";
        $db = Db::get();
        echo "Fetch";
        $attributes = $db->fetchAllAssociative("SELECT MIN(description_category_id) AS description_category_id, MIN(type_id) AS type_id, attribute_id, group_id FROM ".
            self::OZON_CATEGORY_ATTRIBUTE_TABLE . " WHERE group_id > 0 GROUP BY attribute_id, group_id ORDER BY description_category_id, type_id, attribute_id");
        echo count($attributes) . " attributes to process\n";
        $db->beginTransaction();
        $index = 0;
        try {
            foreach ($attributes as $attribute) {
                $categoryId = $attribute['description_category_id'];
                $typeId = $attribute['type_id'];
                $attributeId = $attribute['attribute_id'];
                $groupId = $attribute['group_id'];
                echo "                 \r$categoryId.$typeId.$attributeId";
                $response = $this->connector->getFromCache("ATTRIBUTE_VALUES_{$attributeId}_{$groupId}.json", 7 * 86400);
                if (empty($response)) {
                    $lastId = 0;
                    $response = [];
                    $query = ['description_category_id' => $categoryId, 'language' => 'EN', 'limit' => 5000, 'type_id' => $typeId, 'attribute_id' => $attributeId];
                    do {
                        echo " *";
                        if ($lastId) {
                            $query['last_id'] = $lastId;
                        }
                        $apiResponse = $this->connector->getApiResponse('POST', self::API_ATTRIBUTE_VALUE_URL, $query, '');
                        foreach ($apiResponse['result'] as $value) {
                            $response[] = $value;
                            $lastId = max($lastId, $value['id']);
                        }
                    } while ($apiResponse['has_next']);
                    $this->connector->putToCache("ATTRIBUTE_VALUES_{$attributeId}_{$groupId}.json", $response);
                }
                $db->executeStatement("DELETE FROM " . self::OZON_VALUE_TABLE . " WHERE attribute_id = ? AND group_id = ?", [
                    $attributeId,
                    $groupId,
                ]);
                foreach ($response as $value) {
                    $index++;
                    $db->executeStatement("INSERT INTO " . self::OZON_VALUE_TABLE . " (attribute_id, group_id, value_id, value_json) VALUES (?, ?, ?, ?)", [
                        $attributeId,
                        $groupId,
                        $value['id'],
                        json_encode($value),
                    ]);
                    if ($index % 1000 === 0) {
                        $db->commit();
                        $db->beginTransaction();
                    }
                }
            }
            $db->commit();
            echo "\n";
        } catch (Exception $e) {
            $db->rollBack();
            throw $e;
        }
    }


}