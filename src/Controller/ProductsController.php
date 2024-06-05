<?php

namespace App\Controller;

use Pimcore\Controller\FrontendController;
use Pimcore\Model\DataObject\Product\Listing;
use Pimcore\Model\DataObject\Product;
use Pimcore\Model\DataObject\ProductClass\Listing as ProductClassListing;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ProductsController extends FrontendController
{
    /**
     * @Route("/products", name="products_list")
     */
    public function listAction(Request $request): Response
    {
        $products = new Listing();
        $products->setOrderKey('key');
        $products->setOrder('asc');
        $productList = $products->load();
        $parentProducts = [];
        foreach ($productList as $product) {
            $sizes = [];
            $colors = [];
            foreach ($product->getChildren() as $variant) {
                var_dump($variant);
                if ($variation = $variant->getBricks()->getVariation()) {
                    $size = $variation->getVariationSize();
                    $color = $variation->getVariationColor();
                }
                if ($size && !in_array($size, $sizes)) {
                    $sizes[] = $size;
                }
                if ($color && !in_array($color, $colors)) {
                    $colors[] = $color;
                }                                
            }
            $parentProducts[] = [
                'product' => $product,
                'sizes' => $sizes,
                'colors' => $colors,
            ];
        }
        $productClassListing = new ProductClassListing();
        $productClasses = $productClassListing->load();
        return $this->render('products/list.html.twig', [
            'products' => $parentProducts,
            'productClasses' => $productClasses,
        ]);
    }

    /**
     * @Route("/product/{id}", name="product_detail")
     */
    public function detailAction(Request $request, $id): Response
    {
        $product = Product::getById($id);

        if (!$product) {
            throw $this->createNotFoundException('Product not found');
        }

        $parentProduct = $product->getParent();
        $parentProduct = $parentProduct instanceof Product ? $parentProduct : null;

        return $this->render('products/detail.html.twig', [
            'parentProduct' => $parentProduct,
            'product' => $product,
        ]);
    }
}


/*            
            if ($product->getParent() instanceof Product) {
                continue; // Skip child products
            }
*/
