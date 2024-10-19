<?php

namespace App\Connector\Wisersell;

use Pimcore\Model\DataObject\Product;
use App\Connector\Wisersell\Connector;
use App\Utils\Utility;

class ProductSyncService
{
    protected $connector;
    protected $wisersellProducts;
    protected $pimProducts; // code => pim ID for product

    public function __construct(Connector $connector)
    {
        $this->connector = $connector;
    }

    public function loadWisersellProducts($force = false)
    {
        if (!$force && !empty($this->wisersellProducts)) {
            return;
        }
        $this->wisersellProducts = $this->searchWisersellProducts([]);
    }

    public function loadPimProducts($force = false) 
    {
        if (!$force && !empty($this->pimProducts)) {
            return;
        }
        $db = \Pimcore\Db::get();
        $this->pimProducts = [];
        $products = $db->fetchAll('SELECT oo_id, iwasku FROM object_product WHERE iwasku IS NOT NULL AND published = 1');
        foreach ($products as $product) {
            $this->pimProducts[$product['iwasku']] = $product['oo_id'];
        }
    }

    public function load($force = false)
    {
        $this->loadWisersellProducts($force);
        $this->loadPimProducts($force);
    }

    public function addPimProductsToWisersell($products)
    {
        $this->connector->categorySyncService->load();
        if (!is_array($products)) {
            $products = [$products];
        }
        $productData = [];
        foreach ($products as $product) {
            if (!($product instanceof Product) || $product->level != 1) {
                continue;
            }
            if (empty($product->getIwasku())) {
                if ($product->checkIwasku(true)) {
                    $product->save();
                } else {
                    continue;
                }
            }
            $productData[] = $this->prepareProductData($product);
            if (count($productData) >= 100) {
                $this->postProductDataToWisersell($productData);
                $productData = [];
            }
        }
        if (count($productData) > 0) {
            $this->postProductDataToWisersell($productData);
        }
    }

    public function addWisersellProductsToPim($wisersellProducts)
    {   //TODO
        if (!is_array($wisersellProducts)) {
            $wisersellProducts = [$wisersellProducts];
        }
        foreach ($wisersellProducts as $wisersellProduct) {
            if (!isset($wisersellProduct['code']) || !isset($this->pimProducts[$wisersellProduct['code']])) {
                continue;
            }
            $pimProduct = Product::getById($this->pimProducts[$wisersellProduct['code']]);
            if (!($pimProduct instanceof Product)) {
                continue;
            }
            $pimProduct->setWisersellId($wisersellProduct['id']);
            $pimProduct->setWisersellJson(json_encode($wisersellProduct));
            $pimProduct->save();
        }
    }

    public function updateWisersellProduct($product)
    {
        if (!($product instanceof Product) || $product->level != 1) {
            return;
        }
        if (empty($product->getWisersellId())) {
            $this->addPimProductsToWisersell($product);
            return;
        }
        $productData = $this->prepareProductData($product);
        $response = $this->connector->request(Connector::$apiUrl['product'] . '/' . $product->getWisersellId(), 'PUT', '', $productData);
        if (empty($response)) {
            return;
        }
        if ($response->getStatusCode() == 200) {
            $product->setWisersellJson(json_encode($response->toArray()));
            $product->save();
        }
    }

    public function postProductDataToWisersell($productData)
    {
        if (empty($productData)) {
            return null;
        }
        $response = $this->connector->request(Connector::$apiUrl['product'], 'POST', '', $productData);
        if (empty($response)) {
            return null;
        }
        foreach ($response->toArray() as $wisersellResponse) {
            if (
                isset($wisersellResponse['id']) && 
                isset($wisersellResponse['code']) && 
                isset($this->pimProducts[$wisersellResponse['code']])
            ) {
                $product = Product::getById($this->pimProducts[$wisersellResponse['code']]);
                if ($product instanceof Product) {
                    $product->setWisersellId($wisersellResponse['id']);
                    $product->setWisersellJson(json_encode($wisersellResponse));
                    $product->save();
                    $this->wisersellProducts[$wisersellResponse['id']] = $wisersellResponse;
                }
            }
        }
    }

    public function prepareProductData($product)
    {
        return [
            "name" => $product->getInheritedField('name'),
            "code" => $product->getIwasku(),
            "categoryId" => $this->connector->categorySyncService->getWisersellCategoryId($product->getInheritedField('productCategory')),
            "weight" => $product->getInheritedField("packageWeight"),
            "width" => $product->getInheritedField("packageDimension1"),
            "length" => $product->getInheritedField("packageDimension2"),
            "height" => $product->getInheritedField("packageDimension3"),
            "extradata" => [
                "Size" => $product->getVariationSize(),
                "Color" => $product->getVariationColor()
            ],
            "subproducts" => []
        ];
    }

    public function searchWisersellProducts($searchData)
    {
        $searchData['page'] = 0;
        $searchData['pageSize'] = 100;
        $products = $retval = [];
        do {
            $result = $this->connector->request(Connector::$apiUrl['productSearch'], 'POST', '', $searchData);
            if (empty($result)) {
                break;
            }
            $response = $result->toArray();
            $products = array_merge($products, $response['rows'] ?? []);
            $searchData['page']++;
        } while (count($response['rows']) > 0);
        foreach ($products as $product) {
            if (!empty($product['id'])) {
                $retval[$product['id']] = $product;
            }
        }
        return $retval;
    }

}