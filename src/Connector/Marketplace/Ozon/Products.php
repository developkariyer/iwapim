<?php

namespace App\Connector\Marketplace\Ozon;

use App\Connector\Marketplace\Ozon\Connector as OzonConnector;
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

    public array $categoryTree = [];
    public array $attributes = [];

    public function __construct(OzonConnector $connector)
    {
        $this->connector = $connector;
    }

    /**
     * @throws TransportExceptionInterface|ServerExceptionInterface|RedirectionExceptionInterface|DecodingExceptionInterface|ClientExceptionInterface
     */
    public function getCategoryTreeFromApi(): void
    {
        echo "\n  Getting category tree from API\n";
        $this->categoryTree = $this->connector->getFromCache('CATEGORY_TREE.json');
        if (empty($this->categoryTree)) {
            $this->categoryTree = $this->connector->getApiResponse('POST', self::API_CATEGORY_TREE_URL, ['language' => 'EN']);
            $this->connector->putToCache('CATEGORY_TREE.json', $this->categoryTree);
        } else {
            echo "  Using cached category tree\n";
        }
        $this->buildCategoryTree();
    }

    /**
     * @throws TransportExceptionInterface
     * @throws ServerExceptionInterface
     * @throws RedirectionExceptionInterface
     * @throws DecodingExceptionInterface
     * @throws ClientExceptionInterface
     */
    public function getCategoryAttributesFromApi(): void
    {
        echo "  Getting category attributes from API\n";
        foreach ($this->attributes as $categoryId => $category) {
            echo "    Getting attributes for category {$category['category_name']} ";
            foreach (array_keys($category['products']) as $productId) {
                echo "{$category['products'][$productId]['type_name']}";
                $response = $this->connector->getApiResponse('POST', self::API_CATEGORY_ATTRIBUTE_URL, ['description_category_id' => $categoryId, 'language' => 'EN', 'type_id' => $productId]);
                echo "(".count($response).") ";
                foreach ($response as $attribute) {
                    $this->attributes[$categoryId]['products'][$productId]['attributes'][$attribute['id']] = $attribute;
                }
                echo "\n";
            }
        }
    }

    public function saveAttributes(): void
    {
        $this->connector->putToCache('CATEGORY_ATTRIBUTES.json', $this->attributes);
    }

    public function buildCategoryTree(): void
    {
        $this->attributes = [];
        echo "  Saving category tree to database\n";
        $stack = [[
            'parentId' => null,
            'children' => $this->categoryTree ?? [],
        ]];
        while (!empty($stack)) {
            $current = array_pop($stack);
            $currentParentId = $current['parentId'];
            $currentChildren = $current['children'];

            foreach ($currentChildren as $child) {
                if (isset($child['description_category_id'])) {
                    $this->attributes[$child['description_category_id']] = [
                        'category_name' => $child['category_name'],
                        'parent' => $currentParentId,
                        'products' => []
                    ];
                } elseif (isset($child['type_id'])) {
                    $this->attributes[$currentParentId]['products'][$child['type_id']] = [
                        'type_name' => $child['type_name'],
                        'attributes' => [],
                    ];
                }
                if (!empty($child['children'])) {
                    $stack[] = [
                        'parentId' => $child['description_category_id'],
                        'children' => $child['children'],
                    ];
                }
            }
        }
    }



}