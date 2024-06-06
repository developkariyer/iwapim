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
        $variation->setType(\Pimcore\Model\DataObject\AbstractObject::OBJECT_TYPE_VARIANT); 
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

    private function updateVariant(Product $variant, Product $parent, $key, $size, $color, $published): Product
    {
        $variant->setParent($parent);
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

        if ((empty($newSize) && empty($newColor)) || 
            (!empty($newSize) && !empty($newColor)) ||
            (empty($newSize) && in_array($newColor, $allVariants['colors'])) ||
            (empty($newColor) && in_array($newSize, $allVariants['sizes']))) {
            error_log('do nothing: C:'. $newColor .' S:'. $newSize);
            return $this->redirectToRoute('product_detail', ['id' => $id]);
        }

        // if a new color, first add its variation to parent
        if (!empty($newColor) && !empty($allVariants['colors'][0])) {
            error_log('1');
            $this->addVariant($product, $newColor, '', $newColor, false);
        }

        $colorVariants = $this->colorVariantObjects($product);

        if (!empty($newColor) && empty($allVariants['colors'][0])) {
            error_log('2');
            $colorVariants[$newColor] = $this->updateVariant($colorVariants[$allVariants['colors'][0]], $product, $newColor, '', $newColor, false);
        }

        if (!empty($newSize) && !empty($allVariants['sizes'][0])) {
            error_log('3');
            foreach ($colorVariants as $color => $variant) {
                error_log('3.1:'.$color);
                $this->addVariant($variant, $newSize.' '.$color, $newSize, '', true);
            }
        }

        if (!empty($newSize) && empty($allVariants['sizes'][0])) {
            error_log('4');
            foreach ($colorVariants as $color => $variant) {
                error_log('4.1:'.$color);
                $variants = $this->sizeVariantObjects($variant);
                $this->updateVariant($variants[$allVariants['sizes'][0]], $variant, $newSize.' '.$color, $newSize, '', true);
            }
        }

        return $this->redirectToRoute('product_detail', ['id' => $id]);
    }    

    private function generateUniqueCode()
    {
        while (true) {
            $candidateCode = $this->generateCustomString(6);
            if (!$this->isProductCodeExists($candidateCode)) {
                return $candidateCode;
            }
        }
    }

    private function isProductCodeExists($productCode)
    {
        $listing = new Listing();
        $listing->setCondition('productCode = ?', [$productCode]);
        return $listing->count() > 0;
    }

    private function generateCustomString($length = 6) {
        $characters = 'ABCDEFGHJKMNPQRSTVWXYZ123456789';
        $charactersLength = strlen($characters);
        $randomString = '';

        for ($i = 0; $i < $length; $i++) {
            $randomIndex = mt_rand(0, $charactersLength - 1);
            $randomString .= $characters[$randomIndex];
        }

        return $randomString;
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


