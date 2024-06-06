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
        while ($parent = $product->getParent()) {
            var_dump($parent->getPath());
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
        $allVariants = $this->traverseAllVariants($product);

        if (in_array($newSize, $allVariants['sizes']) || empty($newSize)) {
            return $this->redirectToRoute('product_detail', ['id' => $id]);
        }

        // if there are no children or no color variations
        if (!count($product->getChildren(includingUnpublished:TRUE)) || (count($allVariants['colors']) == 1 && empty($allVariants['colors'][0]))) {
            $newVariation = new Product();
            $newVariation->setProductCode($this->generateUniqueCode());
            $newVariation->setParent($product);
            $newVariation->setType(\Pimcore\Model\DataObject\AbstractObject::OBJECT_TYPE_VARIANT); 
            $newVariation->setKey($newSize);
            $newVariation->setVariationSize($newSize);
            $newVariation->setPublished(true);
            $newVariation->save();
            return $this->redirectToRoute('product_detail', ['id' => $id]);
        }

        // if there are no size variations
        if (count($allVariants['sizes']) == 1 && empty($allVariants['sizes'][0])) {
            foreach ($product->getChildren(includingUnpublished:TRUE) as $variant) {
                $variant->setVariationSize($newSize);
                $variant->setKey($newSize . ' ' . $variant->getVariationColor());
                $variant->save();
            }
            return $this->redirectToRoute('product_detail', ['id' => $id]);
        }

        // if there are both size and color variations
        foreach ($allVariants['colors'] as $color) {
            $newVariation = new Product();
            $newVariation->setParent($product);
            $newVariation->setProductCode($this->generateUniqueCode());
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
        $product = Product::getById($id);
        if (!$product) {
            throw $this->createNotFoundException('Product not found');
        }

        if ($product->getParent() instanceof Product) {
            throw $this->createNotFoundException('Variants cannot be re-varianted');
        }

        $newColor = $request->get('newColor');
        $allVariants = $this->traverseAllVariants($product);

        if (in_array($newColor, $allVariants['colors']) || empty($newColor)) {
            return $this->redirectToRoute('product_detail', ['id' => $id]);
        }

        // if there are no children or no size variations
        if (!count($product->getChildren(includingUnpublished:TRUE)) || (count($allVariants['sizes']) == 1 && empty($allVariants['sizes'][0]))) {
            $newVariation = new Product();
            $newVariation->setProductCode($this->generateUniqueCode());
            $newVariation->setParent($product);
            $newVariation->setType(\Pimcore\Model\DataObject\AbstractObject::OBJECT_TYPE_VARIANT); 
            $newVariation->setKey($newColor);
            $newVariation->setVariationColor($newColor);
            $newVariation->setPublished(true);
            $newVariation->save();
            return $this->redirectToRoute('product_detail', ['id' => $id]);
        }

        // if there are no size variations
        if (count($allVariants['colors']) == 1 && empty($allVariants['colors'][0])) {
            foreach ($product->getChildren(includingUnpublished:TRUE) as $variant) {
                $variant->setVariationColor($newColor);
                $variant->setKey($variant->getVariationSize(). ' ' . $newColor);
                $variant->save();
            }
            return $this->redirectToRoute('product_detail', ['id' => $id]);
        }

        // if there are both size and color variations
        foreach ($allVariants['sizes'] as $size) {
            $newVariation = new Product();
            $newVariation->setParent($product);
            $newVariation->setProductCode($this->generateUniqueCode());
            $newVariation->setType(\Pimcore\Model\DataObject\AbstractObject::OBJECT_TYPE_VARIANT); 
            $newVariation->setKey($size . ' ' . $newColor);
            $newVariation->setVariationSize($size);
            $newVariation->setVariationColor($newColor);
            $newVariation->setPublished(true);
            $newVariation->save();
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


