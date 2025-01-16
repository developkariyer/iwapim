<?php

namespace App\Controller;

use Doctrine\DBAL\Exception;
use Pimcore\Db;
use Pimcore\Controller\FrontendController;
use Pimcore\Model\DataObject\AbstractObject;
use Pimcore\Model\DataObject\Product;
use Pimcore\Model\DataObject\Product\Listing;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Utils\Utility;
use Pimcore\Model\DataObject\Serial;

class ProductsController extends FrontendController
{

    /**
     * @Route("/products", name="products_list")
     */
    public function listAction(Request $request): Response
    {
        if (!$request->cookies->get('id_token')) {
            return $this->redirectToRoute('default_homepage');
        }
        return $this->render('iwapim/products.html.twig', [
            //'products' => $this->listProducts(level:1),
            'logged_in' => (bool)$request->cookies->get('id_token'),
        ]);
    }

    /**
     * @Route("/products/api", name="products_api")
     */
    public function apiAction(Request $request): Response
    {
        if (!$request->cookies->get('id_token')) {
            return $this->json([]);
        }
        $search = trim(str_replace('  ', ' ',$request->get('q')));
        if (empty($search) || strlen($search) < 3) {
            return $this->json([]);
        }
        $results = [];
        foreach ($this->listProducts(level: 1, search: $search) as $product) {
            $results[] = [
                'id' => $product['id'],
                'text' => $product['key']. ($product['published'] ? '' : ' (taslak)'),
            ];
        }
        return $this->json(['results' => $results]);
    }

    /**
     * @Route("/products/sizes", name="products_sizes_menu")
     * @throws Exception
     */
    public function sizesMenuAction(Request $request): Response
    {
        if (!$request->cookies->get('id_token')) {
            return $this->redirectToRoute('default_homepage');
        }
        $db = Db::get();
        $query = "SELECT DISTINCT LOWER(LEFT(`key`, 2)) AS category FROM object_product WHERE `key` LIKE '%-%' ORDER BY category";
        $result = $db->fetchAllAssociative($query);
        $categories = array_column($result, 'category');
        return $this->render('iwapim/product_sizes_menu.html.twig', [
            'categories' => $categories,
            'logged_in' => (bool)$request->cookies->get('id_token'),
        ]);
    }

    /**
     * @Route("/products/sizes/{category}", name="products_sizes")
     * @throws \Exception
     */
    public function sizeAction(Request $request): Response
    {
        if (!$request->cookies->get('id_token')) {
            return $this->redirectToRoute('default_homepage');
        }
        if ($request->isMethod('POST')) {
            $productId = $request->get('product_id');
            $productDimension1 = $request->get('productDimension1');
            $productDimension2 = $request->get('productDimension2');
            $packageDimension1 = $request->get('packageDimension1');
            $packageDimension2 = $request->get('packageDimension2');
            $packageDimension3 = $request->get('packageDimension3');
            $packageWeight = $request->get('packageWeight');
            $keepEmpty = $request->get('keep');
            $product = Product::getById($productId);
            if ($product instanceof Product) {
                $noPublish = 5;
                if ($keepEmpty === 'empty') {
                    $product->setDimensionsPostponed(true);
                    $noPublish--;
                }
                if ($productDimension1>0) {
                    $product->setProductDimension1(floatval($productDimension1));
                    $noPublish--;
                }
                if ($productDimension2> 0) {
                    $product->setProductDimension2(floatval($productDimension2));
                    $noPublish--;
                }
                if ($packageDimension1> 0) {
                    $product->setPackageDimension1(floatval($packageDimension1));
                    $noPublish--;
                }
                if ($packageDimension2> 0) {
                    $product->setPackageDimension2(floatval($packageDimension2));
                    $noPublish--;
                }
                if ($packageDimension3> 0) {
                    $product->setPackageDimension3(floatval($packageDimension3));
                    $noPublish--;
                }
                if ($packageWeight> 0) {
                    $product->setPackageWeight(floatval($packageWeight));
                }
                if ($noPublish<5) {
                    $product->setPublished($noPublish==0);
                    if ($product->save()) {
                        $this->addFlash('success','Product sizes saved successfully');
                        error_log("Product sizes saved successfully for product $productId");
                    } else {
                        $this->addFlash('error','Product sizes could not be saved');
                        error_log("Product sizes could not be saved for product $productId");
                    }    
                }
            } else {
                $this->addFlash('error','Product not found');
            }
            return $this->redirectToRoute('products_sizes', ['category' => $request->get('category')]);
        }
        $category = $request->get('category');
        if (empty($category)) {
            return $this->redirectToRoute('products_sizes_menu');
        }
        $conditions = [
            [
                'condition' => "(dimensionsPostponed IS NULL OR dimensionsPostponed = FALSE) AND variationSize IS NOT NULL AND variationSize != '' AND (productDimension1 IS NULL OR productDimension1 = 0 OR productDimension1 = '' OR productDimension2 IS NULL OR productDimension2 = 0 OR productDimension2 = '' OR packageDimension1 IS NULL OR packageDimension1 = 0 OR packageDimension1 = '' OR packageDimension2 IS NULL OR packageDimension2 = 0 OR packageDimension2 = '' OR packageDimension3 IS NULL OR packageDimension3 = 0 OR packageDimension3 = '')",
                'params' => [],
            ],
            [
                'condition' => 'LOWER(`key`) LIKE ?',
                'params' => strtolower($category)."%",
            ],
        ];
        return $this->render('iwapim/product_sizes.html.twig', [
            'category' => $category,
            'products' => $this->listProducts(level: 3, otherConditions: $conditions),
            'logged_in' => (bool)$request->cookies->get('id_token'),
        ]);
    }


    /**
     * @Route("/product/{id}", name="products_detail")
     */
    public function detailAction(Request $request): Response
    {/*
        if (!$request->cookies->get('id_token')) {
            return $this->redirectToRoute('default_homepage');
        }*/
        $productId = $request->get('id');
        $product = Product::getById($productId);
        if (!$product instanceof Product) {
            return $this->render('iwapim/products.html.twig', [
                'messages' => [
                    [
                        'type' => 'error',
                        'text' => 'Product not found',
                    ],
                ],
                'logged_in' => (bool)$request->cookies->get('id_token'),
            ]);
        }
        [$sizes, $colors] = $product->listVariations();
        return $this->render('iwapim/products.html.twig', [
            'product' => [
                'id' => $product->getId(),
                'key' => $product->getKey(),
                'sizes' => $sizes,
                'colors' => $colors,
            ],
            'productobject' => $product,
            'logged_in' => (bool)$request->cookies->get('id_token'),
        ]);
    }

    /**
     * @Route("/p/{serial_number}", name="product")
     */
    public function productFromSerial(Request $request): Response
    {
        error_log("Product from serial number");
        $serialNumber = Utility::customBase64Decode($request->get('serial_number'));
        $serial = Serial::getBySerialNumber($serialNumber);
        $product = $serial?->current()->getProduct();
        if (!$serial->current() instanceof Serial || !$product instanceof Product) {
            error_log("Product not found for serial number $serialNumber");
            return $this->render('iwapim/products.html.twig', [
                'messages' => [
                    [
                        'type' => 'error',
                        'text' => 'Product not found',
                    ],
                ],
                'logged_in' => (bool)$request->cookies->get('id_token'),
            ]);
        }
        error_log("Product with id {$product->getId()} found for serial number $serialNumber");
        return $this->redirectToRoute('products_detail', ['id' => $product->getId()]);
    }

    /**
     * Get a list of products for router functions
     * @param int|null $level 0: all, 1: only first level, 2: only second level, 4: only third level, 3: only first and second level, 5: only first and third level, 6: only second and third level, 7: same as 0
     * @param string $search search term to filter products, minimum 3 characters, multiple terms separated by space, case-insensitive, search is done on product key
     * @param array $otherConditions
     * @return array
     */
    private function listProducts(int $level = null, string $search = '', array $otherConditions = []): array
    {
        $level ??= 7;
        $products = new Listing();
        $products->setOrderKey('key');
        $products->setOrder('asc');
        $products->setObjectTypes([AbstractObject::OBJECT_TYPE_OBJECT]);
        $conditions = $params = [];
        if (!empty($search)) {
            $searchTerms = explode(' ', $search);
            foreach ($searchTerms as $term) {
                if (empty($term) || strlen($term) < 3) {
                    continue;
                }
                $conditions[] = 'LOWER(`key`) LIKE ?';
                $params[] = "%" . strtolower($term) . "%"; 
            }
        }
        foreach ($otherConditions as $condition) {
            $conditions[] = $condition['condition'];
            if (!empty($condition['params'])) {
                $params[] = $condition['params'];
            }
        }
        if (!empty($conditions)) {
            $products->setCondition(implode(' AND ', $conditions), $params);
        }
        $products->setUnpublished(true);
        $products->setLimit(500);
        $productList = $products->load();
        $productArray = [];
        foreach ($productList as $product) {

            $includeProduct = false;
            if (($level & 1) && !($product->getParent() instanceof Product)) {
                $includeProduct = true;
            }        
            if (($level & 2) && ($product->getParent() instanceof Product) && !($product->getParent()->getParent() instanceof Product)) {
                $includeProduct = true;
            }
            if (($level & 4) && ($product->getParent() instanceof Product) && ($product->getParent()->getParent() instanceof Product)) {
                $includeProduct = true;
            }
            if (!$includeProduct) {
                continue;
            }
            [$sizes, $colors] = $product->listVariations();
            $productArray[] = [
                'id' => $product->getId(),
                'key' => $product->getKey(),
                'sizes' => $sizes,
                'colors' => $colors,
                'published' => $product->isPublished(),
                'productDimension1' => $product->getProductDimension1(),
                'productDimension2' => $product->getProductDimension2(),
                'packageDimension1' => $product->getPackageDimension1(),
                'packageDimension2' => $product->getPackageDimension2(),
                'packageDimension3' => $product->getPackageDimension3(),
                'packageWeight'=> $product->getPackageWeight(),
            ];
        }
        return $productArray;
    }

}
