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
        $products = new Listing();
        $products->setOrderKey('name'); 
        $products->setOrder('asc'); 

        $productList = $products->load();

        $productClasses = [];
        foreach ($productList as $product) {
            if (!in_array($product->getProductClass(), $productClasses)) {
                $productClasses[] = $product->getProductClass();
            }
        }

        return $this->render('products/list.html.twig', [
            'products' => $productList,
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
