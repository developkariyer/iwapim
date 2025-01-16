<?php

namespace App\Controller;

use Doctrine\DBAL\Exception;
use Pimcore\Db;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

use Pimcore\Controller\FrontendController;

class WisersellController extends FrontendController
{

    /**
     * @Route("/wisersell/products", name="wisersell_products")
     * @throws Exception
     */
    public function productAction(): Response
    {
        $sql = "SELECT oqp.name, oqp.iwasku, oqp.packageWeight, oqp.productWeight, oqp.packageDimension1, oqp.packageDimension2, oqp.packageDimension3, oqp.productDimension1, 
            oqp.productDimension2, oqp.productDimension3, oqp.variationSize, oqp.variationColor, COALESCE(oqp.productCategory, parent_oqp.productCategory) AS productCategory
            FROM object_query_product oqp
            LEFT JOIN objects o ON oqp.oo_id = o.id
            LEFT JOIN object_query_product parent_oqp ON o.parentId = parent_oqp.oo_id
            WHERE oqp.iwasku IS NOT NULL AND oqp.iwasku NOT LIKE 'XX%'
            ORDER BY oqp.iwasku ASC;";
        $table = $this->fetchResultsAsArr($sql);

        return $this->render('202409/table.html.twig', [
            'title' => 'Wisersell Product List',
            'theads' => array_keys($table[0]),
            'table' => $table,
        ]);
    }

    /**
     * @Route("/wisersell/connections/{type}", name="wisersell_orders")
     * @throws Exception
     */
    public function connectionAction($type): Response
    {        
        $sql = "SELECT oqp.name, oqp.iwasku, oqp.packageWeight, oqp.productWeight, oqp.packageDimension1, oqp.packageDimension2, oqp.packageDimension3, oqp.productDimension1, 
            oqp.productDimension2, oqp.productDimension3, oqp.variationSize, oqp.variationColor, COALESCE(oqp.productCategory, parent_oqp.productCategory) AS productCategory
            FROM object_query_product oqp
            LEFT JOIN objects o ON oqp.oo_id = o.id
            LEFT JOIN object_query_product parent_oqp ON o.parentId = parent_oqp.oo_id
            WHERE oqp.iwasku IS NOT NULL AND oqp.iwasku NOT LIKE 'XX%'
            ORDER BY oqp.iwasku ASC LIMIT 100;";
        $table = $this->fetchResultsAsArr($sql);

        return $this->render('202409/table.html.twig', [
            'title' => "Wisersell Product List - $type",
            'theads' => array_keys($table[0]),
            'table' => $table,
        ]);
    }

    /**
     * @param string $sql
     * @return array
     * @throws Exception
     */
    protected function fetchResultsAsArr(string $sql): array
    {
        $db = Db::get();
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
                'size' => $row['variationSize'],
                'color' => $row['variationColor'],
                'category' => $row['productCategory'],
            ];
            $table[] = $trow;
        }
        return $table;
    }

}
