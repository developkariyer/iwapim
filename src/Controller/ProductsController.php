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
        // Create a new Listing object
        $products = new Listing();
        $products->setOrderKey('productCode, key'); 
        $products->setOrder('asc'); 

        // Fetch the products
        $productList = $products->load();

        // Filter out child products
        $parentProducts = [];
        foreach ($productList as $product) {
            if ($product->getParent() instanceof Product) {
                continue; // Skip child products
            }
            $parentProducts[] = $product;
        }

        // Fetch product classes
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

        return $this->render('products/detail.html.twig', [
            'product' => $product,
        ]);
    }
}
