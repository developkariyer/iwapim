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
    const string OZON_PRODUCTTYPE_TABLE = 'iwa_ozon_producttype';
    const string OZON_CATEGORY_ATTRIBUTE_TABLE = 'iwa_ozon_category_attribute';
    const string OZON_ATTRIBUTE_TABLE = 'iwa_ozon_attribute';
    const string OZON_ATTRIBUTE_VALUE_TABLE = 'iwa_ozon_attribute_value';

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
        try {
            $db = Db::get();
            $db->executeQuery("DELETE FROM " . self::OZON_CATEGORY_TABLE);
            $db->executeQuery("DELETE FROM " . self::OZON_PRODUCTTYPE_TABLE);
        } catch (Exception $e) {
            echo "Error: " . $e->getMessage() . "\n";
            exit;
        }
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
                        $db->executeStatement("INSERT INTO " . self::OZON_CATEGORY_TABLE . " (description_category_id, parent_id, category_name) VALUES (?, ?, ?)", [
                            $child['description_category_id'],
                            $currentParentId,
                            $child['category_name'],
                        ]);
                    } elseif (isset($child['type_id'])) {
                        $db->executeStatement("INSERT INTO " . self::OZON_PRODUCTTYPE_TABLE . " (description_category_id, type_id, type_name) VALUES (?, ?, ?)", [
                            $currentParentId,
                            $child['type_id'],
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
            echo "Error: " . $e->getMessage() . "\n";
            $db->rollBack();
            exit;
        }
    }

    /**
     * @throws TransportExceptionInterface|ServerExceptionInterface|RedirectionExceptionInterface|DecodingExceptionInterface|ClientExceptionInterface|Exception
     */
    public function getCategoryAttributesFromApi(): void
    {
        echo "Getting category attributes from API\n";
        try {
            $db = Db::get();
            $db->executeQuery("DELETE FROM " . self::OZON_CATEGORY_ATTRIBUTE_TABLE);
            $db->executeQuery("DELETE FROM " . self::OZON_ATTRIBUTE_TABLE);
            $productTypes = $db->fetchAllAssociative("SELECT description_category_id, type_id FROM " . self::OZON_PRODUCTTYPE_TABLE . " ORDER BY description_category_id, type_id");
        } catch (Exception $e) {
            echo "Error: " . $e->getMessage() . "\n";
            exit;
        }
        echo "Processing " . count($productTypes) . " product types\n";
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
                    $db->executeStatement("INSERT INTO " . self::OZON_CATEGORY_ATTRIBUTE_TABLE . " (description_category_id, type_id, attribute_id, group_id, dictionary_id) VALUES (?, ?, ?, ?, ?)", [
                        $categoryId,
                        $typeId,
                        $attribute['id'],
                        $attribute['group_id'],
                        $attribute['dictionary_id'],
                    ]);
                    $db->executeStatement("INSERT IGNORE INTO " . self::OZON_ATTRIBUTE_TABLE . " (attribute_id, group_id, attribute_json) VALUES (?, ?, ?)", [
                        $attribute['id'],
                        $attribute['group_id'],
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
            echo "Error: " . $e->getMessage() . "\n";
            exit;
        }
    }

    /**
     * @throws TransportExceptionInterface|ServerExceptionInterface|RedirectionExceptionInterface|DecodingExceptionInterface|ClientExceptionInterface|Exception
     */
    public function getAttributeValuesFromApi(): void
    {
        echo "Getting attribute values from API\n";
        try {
            $db = Db::get();
            $attributes = $db->fetchAllAssociative("SELECT MIN(description_category_id) AS description_category_id, MIN(type_id) AS type_id, attribute_id, group_id FROM ".
                self::OZON_CATEGORY_ATTRIBUTE_TABLE . " WHERE dictionary_id > 0 GROUP BY attribute_id, group_id ORDER BY description_category_id, type_id, attribute_id");
            $db->executeQuery("DELETE FROM " . self::OZON_ATTRIBUTE_VALUE_TABLE);
        } catch (Exception $e) {
            echo "Error: " . $e->getMessage() . "\n";
            exit;
        }
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
                foreach ($response as $value) {
                    $index++;
                    $db->executeStatement("INSERT INTO " . self::OZON_ATTRIBUTE_VALUE_TABLE . " (attribute_id, group_id, value_id, value_json) VALUES (?, ?, ?, ?)", [
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