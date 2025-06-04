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
        return $this->render('productDimensions/productDimensions.html.twig');
    }

    /**
     * @Route("/product-dimensions-data", name="product_dimensions_data", methods={"GET"})
     */
    public function getProductDimensionsData(Request $request): JsonResponse
    {
        $iwasku = $request->query->get('iwasku');
        $category = $request->query->get('category');
        $packageStatus = $request->query->get('packageStatus');
        $page = max(1, (int) $request->query->get('page', 1));
        $offset = ($page - 1) * 50;

        $listingObject = new Product\Listing();
        $listingObject->setUnpublished(false);
        $conditions = "iwasku IS NOT NULL AND iwasku != '' AND (packageWeight IS NOT NULL OR packageDimension1 IS NOT NULL OR packageDimension2 IS NOT NULL OR packageDimension3 IS NOT NULL)";
        if ($iwasku) {
            $conditions .= " AND iwasku LIKE '%" . $iwasku . "%'";
        }
        if ($category) {
            $conditions .= " AND productCategory = '" . $category . "'";
        }
        if ($packageStatus === 'with-dimensions') {
            $conditions .= " AND (packageDimension1 IS NOT NULL OR packageDimension2 IS NOT NULL OR packageDimension3 IS NOT NULL)";
        } elseif ($packageStatus === 'without-dimensions') {
            $conditions .= " AND (packageDimension1 IS NULL AND packageDimension2 IS NULL AND packageDimension3 IS NULL)";
        }

        $listingObject->setCondition($conditions);
        $listingObject->setLimit(50);
        $listingObject->setOffset($offset);

        $products = $listingObject->load();
        $count = $listingObject->count();
        $productData = [];

        foreach ($products as $product) {
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

        return new JsonResponse([
            'products' => $productData,
            'total' => $count,
            'page' => $page,
            'pageSize' => 50
        ]);
    }





}