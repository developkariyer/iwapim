<?php

namespace App\Controller;

use Pimcore\Controller\FrontendController;
use Pimcore\Model\DataObject;
use Pimcore\Model\DataObject\Product\Listing;
use Pimcore\Model\DataObject\Product;
use Pimcore\Model\DataObject\ProductClass\Listing as ProductClassListing;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Model\Traits\ProductTrait;

class ProductsController extends FrontendController
{
    use ProductTrait;

    /**
     * @Route("/products", name="products_list")
     */
    public function listAction(Request $request): Response
    {
        $products = new Listing();
        $products->setOrderKey('key');
        $products->setOrder('asc');
        $products->setObjectTypes([DataObject::OBJECT_TYPE_OBJECT]);
        $productList = $products->load();
        $topProducts = [];
        foreach ($productList as $product) {
            if ($product->getParent() instanceof Product) {
                continue;
            }
            $allVariants = $this->traverseAllVariants($product);
            $topProducts[] = [
                'product' => $product,
                'sizes' => $allVariants['sizes'],
                'colors' => $allVariants['colors'],
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
        $allVariants = $this->traverseAllVariants($product);
        $parentProduct = null;
        $parent = $product;
        while ($parent = $parent->getParent()) {
            if ($parent->getPath() === '/') {
                break;
            }
            if ($parent instanceof Product) {
                $parentProduct = $parent;
            }
        }
        return $this->render('products/detail.html.twig', [
            'product' => $product,
            'parentProduct' => $parentProduct,
            'sizes' => $allVariants['sizes'],
            'colors' => $allVariants['colors'],
            'variations' => $allVariants['variants'],
        ]);
    }

    /**
     * @Route("/product/{id}/add", name="add", methods={"POST"})
     */
    public function addAction(Request $request, $id): Response
    {
        $product = Product::getById($id);
        if (!$product) {
            throw $this->createNotFoundException('Product not found');
        }
        if ($product->getParent() instanceof Product) {
            throw $this->createNotFoundException('Variants cannot be re-varianted');
        }
        $newSize = $request->get('newSize', '');
        $newColor = $request->get('newColor', '');
        $allVariants = $this->traverseAllVariants($product);
        if ($this->shouldDoNothing($newSize, $newColor, $allVariants)) {
            return $this->redirectToRoute('product_detail', ['id' => $id]);
        }
        $colorVariants = $this->colorVariantObjects($product);
        if (!empty($newColor)) {
            $this->handleNewColor($newColor, $allVariants, $colorVariants, $product);
        }
        if (!empty($newSize)) {
            $this->handleNewSize($newSize, $allVariants, $colorVariants, $product);
        }
        return $this->redirectToRoute('product_detail', ['id' => $id]);
    }

    private function shouldDoNothing($newSize, $newColor, $allVariants)
    {
        return  (empty($newSize) && empty($newColor)) ||
                (!empty($newSize) && !empty($newColor)) ||
                (empty($newSize) && in_array($newColor, $allVariants['colors'])) ||
                (empty($newColor) && in_array($newSize, $allVariants['sizes']));
    }

    private function handleNewColor($newColor, $allVariants, $colorVariants, $product)
    {
        if (isset($allVariants['colors'][0]) && $allVariants['colors'][0] === 'Renk yok') {
            $oldColor = $allVariants['colors'][0];
            $this->updateVariant($colorVariants[$oldColor], null, $newColor, '', $newColor, false);
        } else {
            $colorVariants[$newColor] = $this->addVariant($product, $newColor, '', $newColor, false);
            if (empty($allVariants['sizes'])) {
                $allVariants['sizes'] = ['Ebat yok'];
            }
            foreach ($allVariants['sizes'] as $size) {
                $this->addVariant($colorVariants[$newColor], $size, $size, '', true);
            }
        }
    }

    private function handleNewSize($newSize, $allVariants, $colorVariants, $product)
    {
        if (isset($allVariants['sizes'][0]) && $allVariants['sizes'][0] === 'Ebat yok') {
            $oldSize = $allVariants['sizes'][0];
            foreach ($colorVariants as $variant) {
                $sizeVariants = $this->sizeVariantObjects($variant);
                $this->updateVariant($sizeVariants[$oldSize], $variant, $newSize, $newSize, '', true);
            }
        } else {
            if (empty($colorVariants)) {
                $colorVariants['Renk yok'] = $this->addVariant($product, 'Renk yok', '', 'Renk yok', false);
            }
            foreach ($colorVariants as $variant) {
                $this->addVariant($variant, $newSize, $newSize, '', true);
            }
        }
    }


    /* Private functions */

    
    private function colorVariantObjects(Product $product): array
    {
        $colorVariants = [];
        foreach ($product->getChildren(includingUnpublished:true) as $variant) {
            $color = $variant->getVariationColor() ?? '';
            $colorVariants[$color] = $variant;
        }
        return $colorVariants;
    }

    private function sizeVariantObjects(Product $product): array
    {
        $sizeVariants = [];
        foreach ($product->getChildren(includingUnpublished:true) as $variant) {
            $size = $variant->getVariationSize() ?? '';
            $sizeVariants[$size] = $variant;
        }
        return $sizeVariants;
    }

    private function addVariant(Product $parent, $key, $size, $color, $published): Product
    {
        $variation = new Product();
        $variation->setParent($parent);
        $variation->setProductCode($this->generateUniqueCode());
        //$variation->setType(\Pimcore\Model\DataObject\AbstractObject::OBJECT_TYPE_VARIANT); 
        $variation->setKey($key);
        if (!empty($size)) {
            $variation->setVariationSize($size);
        }
        if (!empty($color)) {
            $variation->setVariationColor($color);
        }
        $variation->setPublished($published);
        $variation->save();
        return $variation;
    }

    private function updateVariant(Product $variant, Product|null $parent, $key, $size, $color, $published): Product
    {
        if ($parent) {
            $variant->setParent($parent);
        }
        $variant->setKey($key);
        if (!empty($size)) {
            $variant->setVariationSize($size);
        }
        if (!empty($color)) {
            $variant->setVariationColor($color);
        }
        $variant->setPublished($published);
        $variant->save();
        return $variant;
    }

    /**
     * Traverses all variants of a given product and organizes them by size and color.
     *
     * @param Product $product
     * @param bool $includingUnpublished
     * @return array
     */
    private function traverseAllVariants(Product $product, bool $includingUnpublished = true): array
    {
        $variants = [];
        $sizes = [];
        $colors = [];

        // Helper function to traverse variants recursively
        $this->traverseVariantsRecursively($product, $variants, $sizes, $colors, $includingUnpublished);
        $this->initializeVariantsMatrix($variants, $sizes, $colors);

        return [
            'variants' => $variants,
            'sizes' => $sizes,
            'colors' => $colors
        ];
    }

    /**
     * Helper function to traverse variants recursively and populate arrays.
     *
     * @param \Pimcore\Model\DataObject\AbstractObject $product
     * @param array &$variants
     * @param array &$sizes
     * @param array &$colors
     * @param bool $includingUnpublished
     * @return void
     */
    private function traverseVariantsRecursively($product, array &$variants, array &$sizes, array &$colors, bool $includingUnpublished): void
    {
        foreach ($product->getChildren(includingUnpublished:$includingUnpublished) as $variant) {
            $size = $variant->getVariationSize() ?? '';
            $color = $variant->getVariationColor() ?? '';

            if ($variant->isPublished()) {
                if (!in_array($size, $sizes, true)) {
                    $sizes[] = $size;
                }

                if (!in_array($color, $colors, true)) {
                    $colors[] = $color;
                }

                if (!isset($variants[$size])) {
                    $variants[$size] = [];
                }
                $variants[$size][$color] = $variant;
            }

            $this->traverseVariantsRecursively($variant, $variants, $sizes, $colors, $includingUnpublished);
        }
    }

    /**
     * Ensures all combinations of sizes and colors are initialized in the variants matrix.
     *
     * @param array &$variants
     * @param array $sizes
     * @param array $colors
     * @return void
     */
    private function initializeVariantsMatrix(array &$variants, array $sizes, array $colors): void
    {
        foreach ($sizes as $size) {
            if (!isset($variants[$size])) {
                $variants[$size] = [];
            }
            foreach ($colors as $color) {
                if (!isset($variants[$size][$color])) {
                    $variants[$size][$color] = null;
                }
            }
        }
    }

}


