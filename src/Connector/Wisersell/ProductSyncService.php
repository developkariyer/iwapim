<?php

namespace App\Connector\Wisersell;

use Pimcore\Model\DataObject\Product;
use App\Connector\Wisersell\Connector;
use App\Utils\Utility;

class ProductSyncService
{
    protected $connector;
    public $wisersellProducts = []; // id => wisersell product
    public $pimProducts = []; // iwasku => pim ID for product, (iwasku is code in wisersell)

    public function __construct(Connector $connector)
    {
        $this->connector = $connector;
    }

    public function loadWisersellProducts($force = false)
    {
        if (!$force && !empty($this->wisersellProducts)) {
            return time()-filemtime(PIMCORE_PROJECT_ROOT . '/tmp/wisersell/products.json');
        }
        $this->wisersellProducts = json_decode(Utility::getCustomCache('products.json', PIMCORE_PROJECT_ROOT . '/tmp/wisersell'), true);
        if (!$force && !empty($this->wisersellProducts)) {
            return time()-filemtime(PIMCORE_PROJECT_ROOT . '/tmp/wisersell/products.json');
        }
        $this->wisersellProducts = $this->search([]);
        Utility::setCustomCache('products.json', PIMCORE_PROJECT_ROOT . '/tmp/wisersell', json_encode($this->wisersellProducts));
        return time()-filemtime(PIMCORE_PROJECT_ROOT . '/tmp/wisersell/products.json');
    }

    public function loadPimProducts($force = false) 
    {
        if (!$force && !empty($this->pimProducts)) {
            return;
        }
        $db = \Pimcore\Db::get();
        $this->pimProducts = [];
        $products = $db->fetchAllAssociative('SELECT oo_id, iwasku FROM object_product WHERE iwasku IS NOT NULL AND published = 1');
        foreach ($products as $product) {
            if (strlen($product['iwasku'])<1) {
                continue;
            }
            $this->pimProducts[$product['iwasku']] = $product['oo_id'];
        }
    }

    public function status()
    {
        $cacheExpire = $this->load();
        return [
            'wisersell' => count($this->wisersellProducts),
            'pim' => count($this->pimProducts),
            'expire' => 86400-$cacheExpire
        ];
    }

    public function load($force = false)
    {
        $this->loadPimProducts($force);
        return $this->loadWisersellProducts($force);
    }

    public function findWisersellProductWithCode($code)
    {
        $this->load();
        foreach ($this->wisersellProducts as $wisersellProduct) {
            if (isset($wisersellProduct['code']) && $wisersellProduct['code'] === $code) {
                return $wisersellProduct;
            }
        }
        return null;
    }

    public function addPimProductsToWisersell($products)
    {
        if (!is_array($products)) {
            $products = [$products];
        }
        $productData = [];
        foreach ($products as $product) {
            if (!($product instanceof Product) || $product->level() != 1) {
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
    {
        $this->load();
        if (!is_array($wisersellProducts)) {
            $wisersellProducts = [$wisersellProducts];
        }
        foreach ($wisersellProducts as $wisersellProduct) {
            $pimProduct = null;
            if (isset($wisersellProduct['code'])) {
                if (isset($this->pimProducts[$wisersellProduct['code']])) {
                    $pimProduct = Product::getById($this->pimProducts[$wisersellProduct['code']]);
                }
                if (!isset($pimProduct) || !($pimProduct instanceof Product)) {
                    $pimProduct = Product::getByIwasku($wisersellProduct['code'], ['limit' => 1]);
                }
            }
            if (!isset($pimProduct) || !($pimProduct instanceof Product)) {
                $pimProduct = Product::getByWisersellId($wisersellProduct['id'], ['limit' => 1]);
            }
            if ($pimProduct instanceof Product) {                    
                $this->updatePimProduct($wisersellProduct);
                continue;
            }
            $pimProduct = $this->addProductToPim($wisersellProduct);
            if ($pimProduct instanceof Product) {
                $this->updateWisersellProduct($pimProduct);
            }
        }
    }

    public function addProductToPim($wisersellProduct)
    {
        if (!isset($wisersellProduct['id'])) {
            return;
        }
        if (empty($wisersellProduct['id'])) {
            echo "Empty Wisersell ID: ".json_encode($wisersellProduct)."\n";
            return;
        }
        $pimProduct = Product::getByWisersellId($wisersellProduct['id'], ['limit' => 1]);
        if (!($pimProduct instanceof Product)) {
            $pimProduct = Product::getByPath("/Ürünler/WISERSELL ERROR/".$wisersellProduct['id']);
        }
        if (!$pimProduct instanceof Product) {
            $pimProduct = new Product();
            $pimProduct->setKey($wisersellProduct['id']);
        }
        $pimProduct->setParent(Utility::checkSetPath("WISERSELL ERROR", Utility::checkSetPath('Ürünler')));
        $pimProduct->setPublished(false);
        $pimProduct->setDescription(json_encode($wisersellProduct, JSON_PRETTY_PRINT));
        $pimProduct->setWisersellJson(json_encode($wisersellProduct));
        $pimProduct->setWisersellId($wisersellProduct['id']);
        $pimProduct->save();
        return $pimProduct;
    }

    public function updatePimProduct($wisersellProduct)
    {
        $this->load();
        if (!isset($wisersellProduct['code']) || !isset($this->pimProducts[$wisersellProduct['code']])) {
            return;
        }
        $pimProduct = Product::getById($this->pimProducts[$wisersellProduct['code']]);
        if ($pimProduct instanceof Product) {
            $pimProduct->setWisersellJson(json_encode($wisersellProduct));
            $pimProduct->save();
        }
    }

    public function updateWisersellProduct($product, $error = false)
    {
        $this->load();
        if (!($product instanceof Product) || $product->level() != 1) {
            return;
        }
        if (empty($product->getWisersellId())) {
            $this->addPimProductsToWisersell($product);
            return;
        }
        $productData = $this->prepareProductData($product);
        if ($error) {
            $productData['name'] = "ERROR: ".$productData['name'];
        }
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
        $this->load();
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
        $this->connector->categorySyncService->load();
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

    public function search($searchData)
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
            echo "\rLoaded " . count($products) . " Wisersell Products   ";
        } while (count($response['rows']) > 0);
        echo "\n";
        foreach ($products as $product) {
            if (!empty($product['id'])) {
                $retval[$product['id']] = $product;
            }
        }
        return $retval;
    }

    public function updatePimWisersellIds()
    {
        $this->load();
        $pimProducts = $this->pimProducts;
        $totalWisersellProducts = count($this->wisersellProducts);
        $totalPimProducts = count($this->pimProducts);
        $wisersellProductWithCode = [];
        $pimProductMatchingCode = [];
        $pimProductMatchingId = [];
        $pimProductCountUpdated = 0;
        $pimProductCountWithNoId = 0;
        $pimProductCountWithWrongId = 0;
        $index = 0;
        foreach ($this->wisersellProducts as $wisersellProduct) {
            $index++;
            $pimNeedsUpdate = false;
            if (isset($wisersellProduct['code'])) {
                $wisersellProductWithCode[] = $wisersellProduct;
                if (isset($this->pimProducts[$wisersellProduct['code']])) {
                    $product = Product::getById($this->pimProducts[$wisersellProduct['code']]);
                    $pimProductMatchingCode[] = $wisersellProduct['code'];
                } else {
                    $product = Product::getByWisersellId($wisersellProduct['id'], ['limit' => 1]);
                    $pimProductMatchingId[] = $wisersellProduct['id'];
                }
                if ($product instanceof Product) {
                    if (isset($pimProducts[$product->getIwasku()])) {
                        unset($pimProducts[$product->getIwasku()]);
                    }
                    $wisersellId = $product->getWisersellId();
                    if (empty($wisersellId)) {
                        $pimNeedsUpdate = true;
                        $pimProductCountWithNoId++;
                    } else {
                        if ($wisersellId != $wisersellProduct['id']) {
                            $pimNeedsUpdate = true;
                            $pimProductCountWithWrongId++;
                        }
                    }
                    if ($pimNeedsUpdate) {
                        $product->setWisersellId($wisersellProduct['id']);
                        $product->setWisersellJson(json_encode($wisersellProduct));
                        $product->save();
                        $pimProductCountUpdated++;
                    }
                }
            }
            echo "\rProcessed: $index / ".count($wisersellProductWithCode)." / $totalWisersellProducts (PIM $totalPimProducts) | Code Matches: ".
            count($pimProductMatchingCode)." | ID Matches: ".
            count($pimProductMatchingId)." | Updated: ".
            $pimProductCountUpdated." | No ID: $pimProductCountWithNoId | Wrong ID: $pimProductCountWithWrongId";
            flush();
        }
        echo "\n";
        echo "Remaining PIM Products: " . count($pimProducts) . "\n";
        foreach ($pimProducts as $iwasku => $pimId) {
            $product = Product::getById($pimId);
            if ($product instanceof Product) {
                echo "Removing Wisersell ID from PIM Product: $iwasku (".($pimId+0).")\n";
                $product->setWisersellId(null);
                $product->setWisersellJson(null);
                $product->save();
            } else {
                echo "Product not found: $iwasku (".($pimId+0).")\n";
            }
        }
        print_r($pimProductMatchingId);
    }

    public function sync($forceUpdate = false)
    {
        $this->load();
        echo "Loaded Products Pim(" . count($this->pimProducts) . ") Wisersell (" . count($this->wisersellProducts) . ")\n";
        $wisersellProducts = $this->wisersellProducts;
        $productBasket = [];
        $totalCount = count($this->pimProducts);
        $index = $skippedPimProduct = $missingIwasku = $updatedPimProduct = 0;
        foreach ($this->pimProducts as $pimId) {
            $index++;
            echo "\rProcessing $index / $totalCount";
            $wisersellProduct = null;
            $updatePimProduct = false;
            $pimProduct = Product::getById($pimId);
            if (!($pimProduct instanceof Product) || $pimProduct->level() != 1) {
                $skippedPimProduct++;
                continue;
            }
            $wisersellId = $pimProduct->getWisersellId();
            $iwasku = $pimProduct->getIwasku();
            if (strlen($iwasku) < 1) {
                echo "Missing iwasku for PIM Product " . $pimProduct->getId() . "\n";
                continue;
            }
            if (!empty($wisersellId) && isset($wisersellProducts[$wisersellId])) {
                $wisersellProduct = $wisersellProducts[$wisersellId];
            } else {
                $wisersellProduct = $this->findWisersellProductWithCode($iwasku);
                if (isset($wisersellProduct['id'])) {
                    echo "Found Wisersell Product " . $wisersellProduct['id'] . " without id in PIM " . $iwasku . " (" . $pimProduct->getId() . ")\n";
                    $updatePimProduct = true;
                }
            }
            if (isset($wisersellProduct['id'])) {
                unset($wisersellProducts[$wisersellProduct['id']]);
                if ($forceUpdate) {
                    $this->updateWisersellProduct($pimProduct);
                    echo "Updated Wisersell " . $wisersellProduct['id'] . " to match PIM " . $pimProduct->getIwasku() . " (" . $pimProduct->getId() . ")\n";
                }
                if ($updatePimProduct) {
                    $pimProduct->setWisersellId($wisersellProduct['id']);
                    $pimProduct->setWisersellJson(json_encode($wisersellProduct));
                    $pimProduct->save();
                    echo "Updated PIM " . $pimProduct->getIwasku() . " (" . $pimProduct->getId() . ") to match Wisersell {$wisersellProduct['id']}\n";
                }
                $updatedPimProduct++;
                continue;
            }
            echo "Adding PIM Product " . $iwasku . " (" . $pimProduct->getId() . ") to basket\n";
            $productBasket[] = $pimProduct;
        }
        echo "\n";
        if (!empty($productBasket)) {
            echo "Adding " . count($productBasket) . " PIM Products to Wisersell\n";
            $this->addPimProductsToWisersell($productBasket);
        }
        $this->addWisersellProductsToPim($wisersellProducts);
    }

}