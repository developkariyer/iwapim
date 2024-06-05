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
        $topProducts = [];
        foreach ($productList as $product) {
            if ($product->getParent() instanceof Product) {
                continue;
            }
            $sizes = [];
            $colors = [];
            foreach ($product->getChildren(includingUnpublished:TRUE) as $variant) {
                $size = $variant->getVariationSize();
                $color = $variant->getVariationColor();
                if ($size && !in_array($size, $sizes)) {
                    $sizes[] = $size;
                }
                if ($color && !in_array($color, $colors)) {
                    $colors[] = $color;
                }                                
            }
            $topProducts[] = [
                'product' => $product,
                'sizes' => $sizes,
                'colors' => $colors,
            ];
        }
        $productClassListing = new ProductClassListing();
        $productClasses = $productClassListing->load();
        return $this->render('products/list.html.twig', [
            'products' => $topProducts,
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

        foreach ($product->getChildren(includingUnpublished:TRUE) as $variant) {
            $size = $variant->getVariationSize() ?? 'Ebat Yok';
            $color = $variant->getVariationColor() ?? 'Renk Yok';
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
        $colors = [];
        foreach ($product->getChildren(includingUnpublished:TRUE) as $variant) {
            $size = $variant->getVariationSize();
            $color = $variant->getVariationColor();
            if (!in_array($size, $sizes)) {
                $sizes[] = $size;
            }
            if (!in_array($color, $colors)) {
                $colors[] = $color;
            }
        }

        if (in_array($newSize, $sizes) || empty($newSize)) {
            return $this->redirectToRoute('product_detail', ['id' => $id]);
        }

        // if there are no children or no color variations
        if (!count($product->getChildren(includingUnpublished:TRUE)) || (count($colors) == 1 && empty($colors[0]))) {
            $newVariation = new Product();
            $newVariation->setParent($product);
            $newVariation->setType(\Pimcore\Model\DataObject\AbstractObject::OBJECT_TYPE_VARIANT); 
            $newVariation->setKey($newSize);
            $newVariation->setVariationSize($newSize);
            $newVariation->setPublished(true);
            $newVariation->save();
            return $this->redirectToRoute('product_detail', ['id' => $id]);
        }

        // if there are no size variations
        if (count($sizes) == 1 && empty($sizes[0])) {
            foreach ($product->getChildren(includingUnpublished:TRUE) as $variant) {
                $variant->setVariationSize($newSize);
                $variant->setKey($newSize . ' ' . $variant->getVariationColor());
                $variant->save();
            }
            return $this->redirectToRoute('product_detail', ['id' => $id]);
        }

        // if there are both size and color variations
        foreach ($colors as $color) {
            $newVariation = new Product();
            $newVariation->setParent($product);
            $newVariation->setType(\Pimcore\Model\DataObject\AbstractObject::OBJECT_TYPE_VARIANT); 
            $newVariation->setKey($newSize . ' ' . $color);
            $newVariation->setVariationSize($newSize);
            $newVariation->setVariationColor($color);
            $newVariation->setPublished(true);
            $newVariation->save();
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


