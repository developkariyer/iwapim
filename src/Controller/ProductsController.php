<?php

namespace App\Controller;

use Pimcore\Controller\FrontendController;
use Pimcore\Model\DataObject\Product\Listing;
use Pimcore\Model\DataObject\Product;
use Pimcore\Model\DataObject\Objectbrick;
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

        return $this->render('products/detail.html.twig', [
            'product' => $product,
            'sizes' => $sizes,
            'colors' => $colors,
            'variations' => $variations,
        ]);
    }

    /**
     * @Route("/product/{id}/add-size", name="add_size", methods={"POST"})
     */
    public function addSizeAction(Request $request, $id): Response
    {
        $product = Product::getById($id);
        if (!$product) {
            throw $this->createNotFoundException('Product not found');
        }

        if ($product->getParent() instanceof Product) {
            throw $this->createNotFoundException('Variants cannot be re-varianted');
        }

        $newSize = $request->get('newSize');
        $sizes = [];
        foreach ($product->getChildren() as $variant) {
            if ($variation = $variant->getVariation()) {
                foreach ($variation->getItems() as $item) {
                    $size = $item->getVariationSize();
                    if (!in_array($size, $sizes)) {
                        $sizes[] = $size;
                    }
                }
            }
        }

        if (in_array($newSize, $sizes) || empty($newSize)) {
            return $this->redirectToRoute('product_detail', ['id' => $id]);
        }

        // if there is no children
        if (count($product->getChildren()) == 0) {
            $newVariation = new Product();
            $newVariation->setParent($product);
            $newVariation->setKey($newSize);
            $variation = new Objectbrick\Data\Variation($newVariation);
            $variation->setVariationSize($newSize);
            $newVariation->save();            
            return $this->redirectToRoute('product_detail', ['id' => $id]);
        }

        if (count($sizes) == 1 && empty($sizes[0])) {
            $t = false;
            foreach ($product->getChildren() as $variant) {
                $variation = $variant->getVariation();
                $variation->setVariationSize($newSize);
                $variant->setKey($variation->getVariationSize() . ' ' . $variation->getVariationColor());
                $variant->save();
                $t = true;
            }
            if (!$t) {
                $product->addVariation($newSize, '');
            }
            return $this->redirectToRoute('product_detail', ['id' => $id]);
        }

        

        return $this->redirectToRoute('product_detail', ['id' => $id]);
    }    

    /**
     * @Route("/product/{id}/add-color", name="add_color", methods={"POST"})
     */
    public function addColorAction(Request $request, $id): Response
    {
        return $this->redirectToRoute('product_detail', ['id' => $id]);
    }
}


