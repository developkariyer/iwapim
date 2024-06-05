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
            if ($product->getParent() instanceof Product) {
                continue;
            }
            $sizes = [];
            $colors = [];
            foreach ($product->getChildren() as $variant) {
                if ($variation = $variant->getVariation()) {
                    foreach ($variation->getItems() as $item) {
                        $size = $item->getVariationSize();
                        $color = $item->getVariationColor();
                        if ($size && !in_array($size, $sizes)) {
                            $sizes[] = $size;
                        }
                        if ($color && !in_array($color, $colors)) {
                            $colors[] = $color;
                        }                                
                    }
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

        $sizes = [];
        $colors = [];
        $variations = [];

        foreach ($product->getChildren() as $variant) {
            if ($variation = $variant->getVariation()) {
                foreach ($variation->getItems() as $item) {
                    $size = $item->getVariationSize() ?? 'Ebat Yok';
                    $color = $item->getVariationColor() ?? 'Renk Yok';
                    if (empty($variations[$size])) {
                        $variations[$size] = [];
                    }
                    $variations[$size][$color] = $variant;

                    if (!in_array($size, $sizes)) {
                        $sizes[] = $size;
                    }
                    if (!in_array($color, $colors)) {
                        $colors[] = $color;
                    }                                
                }
            }
        }

        var_dump($sizes, $colors, $variations); die();
        return $this->render('products/detail.html.twig', [
            'product' => $product,
            'sizes' => $sizes,
            'colors' => $colors,
            'variations' => $variations,
        ]);
    }
}


