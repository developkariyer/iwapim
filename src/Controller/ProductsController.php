<?php

namespace App\Controller;

use Pimcore\Controller\FrontendController;
use Pimcore\Model\DataObject\Product\Listing;
use Pimcore\Model\DataObject\Product;
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
        $products->setOrderKey('name'); // Example: Order by name
        $products->setOrder('asc'); // Example: Order ascending

        // Fetch the products
        $productList = $products->load();

        return $this->render('products/list.html.twig', [
            'products' => $productList,
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
