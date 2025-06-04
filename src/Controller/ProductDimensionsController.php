<?php
namespace App\Controller;

use App\Connector\Marketplace\CiceksepetiConnector;
use App\Model\DataObject\Marketplace;
use App\Model\DataObject\VariantProduct;
use App\Utils\Utility;
use Doctrine\DBAL\Exception;
use Pimcore\Db;
use Random\RandomException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Message\CiceksepetiCategoryUpdateMessage;
use Symfony\Component\Messenger\MessageBusInterface;
use Pimcore\Controller\FrontendController;
use Pimcore\Model\DataObject\Product;
use Pimcore\Model\DataObject\Data\Link;
use Pimcore\Model\Asset;
use Symfony\Component\Security\Http\Attribute\IsGranted;


#[IsGranted('ROLE_PIMCORE_ADMIN')]
class ProductDimensionsController extends FrontendController
{
    /**
     * @Route("/productDimensions", name="product_dimensions_main_page")
     * @param Request $request
     * @return Response
     */
    public function productDimensionsMainPage(Request $request): Response
    {
        $pageSize = 50;
        $offset = 0;
        $iwasku = $request->query->get('iwasku');
        $category = $request->query->get('category');
        $page = max(1, (int) $request->query->get('page', 1));
        $offset = ($page - 1) * $pageSize;
        $listingObject = new Product\Listing();
        $listingObject->setUnpublished(false);
        $conditions = "iwasku IS NOT NULL AND iwasku != ''";
        if ($iwasku) {
            $conditions .= " AND iwasku LIKE '%" . $iwasku . "%'";
        }
        if ($category) {
            $conditions .= " AND productCategory = '" . $category . "'";
        }
        $packageStatus = $request->query->get('packageStatus');
        if ($packageStatus === 'with-dimensions') {
            $conditions .= " AND packageDimension1 IS NOT NULL AND packageDimension2 IS NOT NULL AND packageDimension3 IS NOT NULL";
        } elseif ($packageStatus === 'without-dimensions') {
            $conditions .= " AND (packageDimension1 IS NULL OR packageDimension2 IS NULL OR packageDimension3 IS NULL)";
        }
        $search = $request->query->get('search');
        if ($search) {
            $conditions .= " AND (name LIKE '%" . $search . "%' OR iwasku LIKE '%" . $search . "%' OR variationSize LIKE '%" . $search . "%' OR variationColor LIKE '%" . $search . "%')";
        }
        $listingObject->setCondition($conditions);
        $listingObject->setLimit($pageSize);
        $listingObject->setOffset($offset);
        $products = $listingObject->load();
        $count = $listingObject->count();
        $productData = [];
        foreach ($products as $product) {
            if ($product->level() != 1 || !$product instanceof Product) {
                continue;
            }
            $productData[] = [
                'id' => $product->getId(),
                'name' => $product->getInheritedField("name"),
                'iwasku' => $product->getInheritedField("iwasku"),
                'variationSize' => $product->getVariationSize(),
                'variationColor' => $product->getVariationColor(),
                'wsCategory' => $product->getInheritedField("productCategory"),
                'weight' => $product->getInheritedField("packageWeight"),
                'width' => $product->getInheritedField("packageDimension1"),
                'length' => $product->getInheritedField("packageDimension2"),
                'height' => $product->getInheritedField("packageDimension3"),
                'desi5000' => $product->getInheritedField("desi5000")
            ];
        }
        $categories = [];
        try {
            $sql = "SELECT DISTINCT(category) FROM object_query_category WHERE category IS NOT NULL AND category != '' ORDER BY category ASC";
            $result = Utility::fetchFromSql($sql);
            foreach ($result as $category) {
                $categories[] = $category['category'];
            }
        } catch (\Exception $e) {
            $categories = [];
        }
        return $this->render('productDimensions/productDimensions.html.twig', [
            'products' => $productData,
            'total' => $count,
            'page' => $page,
            'pageSize' => $pageSize,
            'categories' => $categories
        ]);
    }





}