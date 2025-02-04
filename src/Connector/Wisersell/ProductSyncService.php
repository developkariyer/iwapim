<?php

namespace App\Connector\Wisersell;

use Doctrine\DBAL\Exception;
use Pimcore\Db;
use Pimcore\Model\DataObject\Product;
use App\Utils\Utility;
use Random\RandomException;
use Symfony\Contracts\HttpClient\Exception\ClientExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\DecodingExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\RedirectionExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\ServerExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;

class ProductSyncService
{
    protected Connector $connector;
    public array $wisersellProducts = []; // id => wisersell product
    public array $pimProducts = []; // iwasku => pim ID for product, (iwasku is code in wisersell)

    public function __construct(Connector $connector)
    {
        $this->connector = $connector;
    }

    /**
     * @throws ClientExceptionInterface|DecodingExceptionInterface|RedirectionExceptionInterface|ServerExceptionInterface|TransportExceptionInterface|RandomException
     */
    public function loadWisersellProducts($force = false): int
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

    /**
     * @throws Exception
     */
    public function loadPimProducts($force = false): void
    {
        if (!$force && !empty($this->pimProducts)) {
            return;
        }
        $db = Db::get();
        $this->pimProducts = [];
        $products = $db->fetchAllAssociative('SELECT oo_id, iwasku FROM object_product WHERE iwasku IS NOT NULL AND published = 1');
        foreach ($products as $product) {
            if (strlen($product['iwasku'])<1) {
                continue;
            }
            $this->pimProducts[$product['iwasku']] = $product['oo_id'];
        }
    }

    /**
     * @return array
     * @throws ClientExceptionInterface|DecodingExceptionInterface|Exception|RedirectionExceptionInterface|ServerExceptionInterface|TransportExceptionInterface|RandomException
     */
    public function status(): array
    {
        $cacheExpire = $this->load();
        return [
            'wisersell' => count($this->wisersellProducts),
            'pim' => count($this->pimProducts),
            'expire' => 86400-$cacheExpire
        ];
    }

    /**
     * @param bool $force
     * @return int
     * @throws ClientExceptionInterface|DecodingExceptionInterface|Exception|RedirectionExceptionInterface|ServerExceptionInterface|TransportExceptionInterface|RandomException
     */
    public function load(bool $force = false): int
    {
        $this->loadPimProducts($force);
        return $this->loadWisersellProducts($force);
    }

    /**
     * @param $code
     * @return mixed|null
     * @throws ClientExceptionInterface|DecodingExceptionInterface|Exception|RedirectionExceptionInterface|ServerExceptionInterface|TransportExceptionInterface|RandomException
     */
    public function findWisersellProductWithCode($code): mixed
    {
        $this->load();
        foreach ($this->wisersellProducts as $wisersellProduct) {
            if (isset($wisersellProduct['code']) && $wisersellProduct['code'] === $code) {
                return $wisersellProduct;
            }
        }
        return null;
    }

    /**
     * @throws ClientExceptionInterface|DecodingExceptionInterface|Exception|RedirectionExceptionInterface|ServerExceptionInterface|TransportExceptionInterface|RandomException
     */
    public function dump(): void
    {
        $this->load();
        file_put_contents(PIMCORE_PROJECT_ROOT . '/tmp/wisersell/products.wisersell.txt', print_r($this->wisersellProducts, true));
        file_put_contents(PIMCORE_PROJECT_ROOT . '/tmp/wisersell/products.pim.txt', print_r($this->pimProducts, true));
    }

    /**
     * @param $products
     * @throws ClientExceptionInterface|DecodingExceptionInterface|Exception|RedirectionExceptionInterface|ServerExceptionInterface|TransportExceptionInterface|\Exception
     */
    public function addPimProductsToWisersell($products): void
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

    /**
     * @param $wisersellProducts
     * @throws ClientExceptionInterface|DecodingExceptionInterface|Exception|RedirectionExceptionInterface|ServerExceptionInterface|TransportExceptionInterface|\Exception
     */
    public function addWisersellProductsToPim($wisersellProducts): void
    {
        $this->load();
        if (!is_array($wisersellProducts)) {
            $wisersellProducts = [$wisersellProducts];
        }
        $bucket = [];
        $errorProducts = [];
        foreach ($wisersellProducts as $wisersellProduct) {
            echo "Adding Wisersell Product to PIM Error: ".json_encode($wisersellProduct)."\n";
            $pimProduct = null;
            if (isset($wisersellProduct['code'])) {
                if (isset($this->pimProducts[$wisersellProduct['code']])) {
                    $pimProduct = Product::getById($this->pimProducts[$wisersellProduct['code']]);
                }
                if (!isset($pimProduct) || !($pimProduct instanceof Product)) {
                    $pimProduct = Product::getByIwasku($wisersellProduct['code'], 1);
                }
            }
            if (!isset($pimProduct) || !($pimProduct instanceof Product)) {
                $pimProduct = Product::getByWisersellId($wisersellProduct['id'], 1);
            }
            if ($pimProduct instanceof Product) {                    
                $this->updatePimProduct($wisersellProduct);
                continue;
            }
            $errorProducts[] = $wisersellProduct;
            $wisersellProduct['name'] = "OLMAYAN URUN! BENI SILIN LUTFEN!!!";
            $bucket[] = $wisersellProduct;
            if (count($bucket) >= 100) {
                $response = $this->connector->request(Connector::$apiUrl['product'], 'PUT', '', $bucket);
                if (!empty($response)) {
                    echo "Updated Wisersell Product bucket with status ". $response->getStatusCode()."\n";
                }
                $bucket = [];
            }
        }
        if (!empty($bucket)) {
            $response = $this->connector->request(Connector::$apiUrl['product'], 'PUT', '', $bucket);
            if (!empty($response)) {
                echo "Updated Wisersell Product bucket with status ". $response->getStatusCode()."\n";
            }
        }
        file_put_contents(PIMCORE_PROJECT_ROOT . '/tmp/wisersell/products.error.txt', json_encode($errorProducts));
    }

    /**
     * @param $wisersellProduct
     * @throws ClientExceptionInterface|DecodingExceptionInterface|Exception|RedirectionExceptionInterface|ServerExceptionInterface|TransportExceptionInterface|\Exception
     */
    public function updatePimProduct($wisersellProduct): void
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

    /**
     * @throws ClientExceptionInterface|DecodingExceptionInterface|Exception|RedirectionExceptionInterface|ServerExceptionInterface|TransportExceptionInterface|\Exception
     */
    public function updateWisersellProduct($product, $error = false): void
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

    /**
     * @throws ClientExceptionInterface|DecodingExceptionInterface|Exception|RedirectionExceptionInterface|ServerExceptionInterface|TransportExceptionInterface|\Exception
     */
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

    /**
     * @param $product
     * @return array
     * @throws ClientExceptionInterface|DecodingExceptionInterface|Exception|RedirectionExceptionInterface|ServerExceptionInterface|TransportExceptionInterface
     */
    public function prepareProductData($product): array
    {
        $this->connector->categorySyncService->load();
        $subProducts = $product->getBundleProducts();
        $subProductData = [];
        if (!empty($subProducts) && is_array($subProducts)) {
            foreach ($subProducts as $subProduct) {
                $obj = $subProduct->getObject();
                if (!($obj instanceof Product)) {
                    continue;
                }
                if (empty($obj->getWisersellId())) {
                    continue;
                }
                $amount = $subProduct->getAmount() ?? 1;
                if (!$amount) $amount = 1;
                $subProductData[] = [
                    "subprodId" => $obj->getWisersellId(),
                    "qty" => $amount,
                ];
            }
        }
        return [
            "name" => $product->getKey(),
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
            "arrsku" => [
                $product->getIwasku(),
            ],
            "subproducts" => $subProductData
        ];
    }

    /**
     * @throws ClientExceptionInterface|DecodingExceptionInterface|RedirectionExceptionInterface|ServerExceptionInterface|TransportExceptionInterface
     */
    public function search($searchData): array
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

    /**
     * @throws ClientExceptionInterface|DecodingExceptionInterface|Exception|RedirectionExceptionInterface|ServerExceptionInterface|TransportExceptionInterface|\Exception
     */
    public function updatePimWisersellIds(): void
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
                    $product = Product::getByWisersellId($wisersellProduct['id'], 1);
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

    /**
     * @throws ClientExceptionInterface|DecodingExceptionInterface|Exception|RedirectionExceptionInterface|ServerExceptionInterface|TransportExceptionInterface|\Exception
     */
    public function sync($forceUpdate = false): void
    {
        $this->updatePimWisersellIds();
        $this->loadPimProducts(true);
        $this->load();
        echo "Loaded Products Pim(" . count($this->pimProducts) . ") Wisersell (" . count($this->wisersellProducts) . ")\n";
        $wisersellProducts = $this->wisersellProducts;
        $productBasket = [];
        $totalCount = count($this->pimProducts);
        $index = 0;
        foreach ($this->pimProducts as $pimId) {
            $index++;
            echo "\rProcessing $index / $totalCount";
            $wisersellProduct = null;
            $updatePimProduct = false;
            $pimProduct = Product::getById($pimId);
            if (!($pimProduct instanceof Product) || $pimProduct->level() != 1) {
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
                $updateWisersellProduct = false;
                if ($wisersellProduct['name'] !== $pimProduct->getKey()) {
                    echo "{$wisersellProduct['name']} !== {$pimProduct->getKey()}, {$wisersellProduct['id']}, {$pimProduct->getIwasku()}, ({$pimProduct->getId()})\n";
                    $updateWisersellProduct = true;
                }
                if ($wisersellProduct['categoryId'] != $this->connector->categorySyncService->getWisersellCategoryId($pimProduct->getInheritedField('productCategory'))) {
                    echo "Category Mismatch: {$wisersellProduct['categoryId']} != {$pimProduct->getInheritedField('productCategory')}, {$wisersellProduct['id']}, {$pimProduct->getIwasku()}, ({$pimProduct->getId()})\n";
                    $updateWisersellProduct = true;
                }
                if (count($wisersellProduct['subproducts']) != count($pimProduct->getBundleProducts())) {
                    echo "Subproduct Mismatch: ".count($wisersellProduct['subproducts'])." != ".count($pimProduct->getBundleProducts()).", {$wisersellProduct['id']}, {$pimProduct->getIwasku()}, ({$pimProduct->getId()})\n";
                    $updateWisersellProduct = true;
                }
                if ($wisersellProduct['weight'] != $pimProduct->getInheritedField('packageWeight')) {
                    echo "Weight Mismatch: {$wisersellProduct['weight']} != {$pimProduct->getInheritedField('packageWeight')}, {$wisersellProduct['id']}, {$pimProduct->getIwasku()}, ({$pimProduct->getId()})\n";
                    $updateWisersellProduct = true;
                }
                if ($wisersellProduct['width'] != $pimProduct->getInheritedField('packageDimension1')) {
                    echo "Width Mismatch: {$wisersellProduct['width']} != {$pimProduct->getInheritedField('packageDimension1')}, {$wisersellProduct['id']}, {$pimProduct->getIwasku()}, ({$pimProduct->getId()})\n";
                    $updateWisersellProduct = true;
                }
                if ($wisersellProduct['length'] != $pimProduct->getInheritedField('packageDimension2')) {
                    echo "Length Mismatch: {$wisersellProduct['length']} != {$pimProduct->getInheritedField('packageDimension2')}, {$wisersellProduct['id']}, {$pimProduct->getIwasku()}, ({$pimProduct->getId()})\n";
                    $updateWisersellProduct = true;
                }
                if ($wisersellProduct['height'] != $pimProduct->getInheritedField('packageDimension3')) {
                    echo "Height Mismatch: {$wisersellProduct['height']} != {$pimProduct->getInheritedField('packageDimension3')}, {$wisersellProduct['id']}, {$pimProduct->getIwasku()}, ({$pimProduct->getId()})\n";
                    $updateWisersellProduct = true;
                }
                if (!is_array($wisersellProduct['arrsku']) || !in_array($pimProduct->getIwasku(), $wisersellProduct['arrsku'])) {
                    echo "SKU Mismatch: ".json_encode($wisersellProduct['arrsku'])." != ".$pimProduct->getIwasku().", {$wisersellProduct['id']}, {$pimProduct->getIwasku()}, ({$pimProduct->getId()})\n";
                    $updateWisersellProduct = true;
                }
                if ($forceUpdate || $updateWisersellProduct) {
                    $this->updateWisersellProduct($pimProduct);
                    echo "Updated Wisersell " . $wisersellProduct['id'] . " to match PIM " . $pimProduct->getIwasku() . " (" . $pimProduct->getId() . ")\n";
                }
                unset($wisersellProducts[$wisersellProduct['id']]);
                if ($updatePimProduct) {
                    $pimProduct->setWisersellId($wisersellProduct['id']);
                    $pimProduct->setWisersellJson(json_encode($wisersellProduct));
                    $pimProduct->save();
                    echo "Updated PIM " . $pimProduct->getIwasku() . " (" . $pimProduct->getId() . ") to match Wisersell {$wisersellProduct['id']}\n";
                }
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

    /**
     * @throws ClientExceptionInterface|DecodingExceptionInterface|Exception|RedirectionExceptionInterface|ServerExceptionInterface|TransportExceptionInterface|\Exception
     */
    public function fixWisersellNames(): void
    {
        $this->load();
        $bucket = [];
        $pimProducts = [];
        $index = 0;
        $totalCount = count($this->wisersellProducts);
        foreach ($this->wisersellProducts as $wisersellProduct) {
            $index++;
            echo "\rProcessing $index / $totalCount";
            $pimProduct = Product::getByWisersellId($wisersellProduct['id'], 1);
            if (!$pimProduct instanceof Product) {
                continue;
            }
            $pimKey = $pimProduct->getKey();
            if ($pimKey === $wisersellProduct['name']) {
                continue;
            }
            $wisersellProduct['name'] = $pimKey;
            $bucket[] = $wisersellProduct;
            $pimProducts[$wisersellProduct['id']] = $pimProduct;
            if (count($bucket) >= 100) {
                $response = $this->connector->request(Connector::$apiUrl['product'], 'PUT', '', $bucket);
                if (!empty($response)) {
                    echo "Updated Wisersell Product bucket with status ". $response->getStatusCode()."\n";
                    $completed = $response->toArray()['completed'] ?? [];
                    foreach ($completed as $wsProduct) {
                        if (isset($pimProducts[$wsProduct['id'] ?? 0])) {
                            $pimProducts[$wsProduct['id']]->setWisersellJson(json_encode($wsProduct));
                            $pimProducts[$wsProduct['id']]->save();
                        }
                    }
                }
                $bucket = [];
            }
        }
        echo "\n";
        if (!empty($bucket)) {
            $response = $this->connector->request(Connector::$apiUrl['product'], 'PUT', '', $bucket);
            if (!empty($response)) {
                echo "Updated Wisersell Product bucket with status ". $response->getStatusCode()."\n";
                $completed = $response->toArray();
                foreach ($completed as $wsProduct) {
                    if (isset($pimProducts[$wsProduct['id'] ?? 0])) {
                        $pimProducts[$wsProduct['id']]->setWisersellJson(json_encode($wsProduct));
                        $pimProducts[$wsProduct['id']]->save();
                    }
                }
            }
        }
    }

}