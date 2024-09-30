<?php

namespace App\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

use Pimcore\Controller\FrontendController;
use Pimcore\Model\DataObject\Product;
use Pimcore\Model\DataObject\Marketplace;

class WisersellController extends FrontendController
{ 
    
    /**
     * @Route("/wisersell/products", name="wisersell_products")
     */
    public function productAction(Request $request): Response
    {
        $db = \Pimcore\Db::get();
        $sql = "SELECT * FROM object_query_product WHERE iwasku IS NOT NULL ORDER BY iwasku ASC";
        $rows = $db->fetchAllAssociative($sql);

        $table = [];
        foreach ($rows as $row) {
            $trow = [
                'name' => $row['name'],
                'code' => $row['iwasku'],
                'weight' => $row['packageWeight'] ?? $row['productWeight'],
                'width' => $row['packageDimension1'] ?? $row['productDimension1'],
                'height' => $row['packageDimension2'] ?? $row['productDimension2'],
                'length' => $row['packageDimension3'] ?? $row['productDimension3'],
                'category' => $row['category'],
            ];
            $table[] = $trow;
        }

        return $this->render('202409/table.html.twig', [
            'title' => 'Wisersell Product List',
            'theads' => array_keys($table[0]),
            'table' => $table,
        ]);
    }

}
