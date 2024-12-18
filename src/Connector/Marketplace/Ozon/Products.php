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
    const string OZON_ATTRIBUTE_TABLE = 'iwa_ozon_category_producttype_attribute';
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
        error_reporting(E_ALL);
        ini_set('display_errors', 1);
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
                echo "\r".($currentParentId ?? 'root')."            ";
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
        $productTypes = $db->fetchAllAssociative("SELECT description_category_id, type_id FROM " . self::OZON_PRODUCTTYPE_TABLE);
        $db->beginTransaction();
        try {
            foreach ($productTypes as $productType) {
                $categoryId = $productType['description_category_id'];
                $typeId = $productType['type_id'];
                echo "\r$categoryId.$typeId                 ";
                $response = $this->connector->getFromCache("CATEGORY_ATTRIBUTES_{$categoryId}_{$typeId}.json", 7 * 86400);
                if (empty($response)) {
                    $response = $this->connector->getApiResponse('POST', self::API_CATEGORY_ATTRIBUTE_URL, ['description_category_id' => $categoryId, 'language' => 'EN', 'type_id' => $typeId]);
                    $this->connector->putToCache("CATEGORY_ATTRIBUTES_{$categoryId}_{$typeId}.json", $response);
                }
                foreach ($response as $attribute) {
                    $db->executeStatement("INSERT INTO " . self::OZON_ATTRIBUTE_TABLE . " (description_category_id, type_id, attribute_id, attribute_json) VALUES (?, ?, ?, ?) ON DUPLICATE KEY UPDATE attribute_json = ?", [
                        $categoryId,
                        $typeId,
                        $attribute['id'],
                        json_encode($attribute),
                        json_encode($attribute),
                    ]);
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
        $attributes = $db->fetchAllAssociative("SELECT description_category_id, type_id, attribute_id FROM " . self::OZON_ATTRIBUTE_TABLE);
        $db->beginTransaction();
        try {
            foreach ($attributes as $attribute) {
                $categoryId = $attribute['description_category_id'];
                $typeId = $attribute['type_id'];
                $attributeId = $attribute['attribute_id'];
                echo "\r$categoryId.$typeId.$attributeId               ";
                $response = $this->connector->getFromCache("ATTRIBUTE_VALUES_{$categoryId}_{$typeId}_{$attributeId}.json", 7 * 86400);
                if (empty($response)) {
                    $lastId = 0;
                    $query = ['description_category_id' => $categoryId, 'language' => 'EN', 'limit' => 5000, 'type_id' => $typeId, 'attribute_id' => $attributeId];
                    do {
                        if ($lastId) {
                            $query['last_id'] = $lastId;
                        }
                        $response = $this->connector->getApiResponse('POST', self::API_ATTRIBUTE_VALUE_URL, $query, '');
                        $db->executeStatement("DELETE FROM " . self::OZON_VALUE_TABLE . " WHERE description_category_id = ? AND type_id = ? AND attribute_id = ?", [
                            $categoryId,
                            $typeId,
                            $attributeId,
                        ]);
                        foreach ($response['result'] as $value) {
                            $lastId = max($lastId, $value['id']);
                            $db->executeStatement("INSERT INTO " . self::OZON_VALUE_TABLE . " (description_category_id, type_id, attribute_id, value_id, value_json) VALUES (?, ?, ?, ?, ?)", [
                                $categoryId,
                                $typeId,
                                $attributeId,
                                $value['id'],
                                json_encode($value),
                            ]);
                        }
                    } while ($response['has_next']);
                    $response = $this->connector->getApiResponse('POST', self::API_ATTRIBUTE_VALUE_URL, ['description_category_id' => $categoryId, 'language' => 'EN', 'type_id' => $typeId, 'attribute_id' => $attributeId]);
                    $this->connector->putToCache("ATTRIBUTE_VALUES_{$categoryId}_{$typeId}_{$attributeId}.json", $response);
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