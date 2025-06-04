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
     * @return Response
     */
    public function productDimensionsMainPage(): Response
    {
        return $this->render('productDimensions/productDimensions.html.twig');
    }

    /**
     * @Route("/productDimensions/filter", name="product_dimensions_filtered", methods={"GET"})
     */
    public function filterProductDimensions(Request $request): JsonResponse
    {
        $iwasku = $request->query->get('iwasku');
        $category = $request->query->get('category');
        $packageStatus = $request->query->get('packageStatus');
        $page = max(0, (int)$request->query->get('page', 1));
        $limit = max(1, (int)$request->query->get('limit', 50));
        $condition = "iwasku IS NOT NULL AND iwasku != ''";
        if ($iwasku) {
            $condition .= " AND iwasku LIKE :iwasku";
        }
        if ($category) {
            $condition .= " AND productCategory = :category";
        }
        if ($packageStatus) {
            if ($packageStatus === 'completed') {
                $condition .= " AND (packageWeight IS NOT NULL OR packageDimension1 IS NOT NULL)";
            } else {
                $condition .= " AND (packageWeight IS NULL OR packageDimension1 IS NULL)";
            }
        }
        $listingObject = new Product\Listing();
        $listingObject->setUnpublished(false);
        $listingObject->setCondition($condition);
        $listingObject->setLimit($limit);
        $listingObject->setOffset(($page - 1) * $limit);
        if ($iwasku) {
            $listingObject->setParameter('iwasku', '%'.$iwasku.'%');
        }
        if ($category) {
            $listingObject->setParameter('category', $category);
        }
        $products = $listingObject->load();
        $totalItems = $listingObject->count();
        $productData = array_map(function($product) {
            return [
                'id' => $product->getId(),
                'name' => $product->getInheritedField('name'),
                'variationSize' => $product->getVariationSize(),
                'category' => $product->getInheritedField('productCategory'),
                'packageWeight' => $product->getInheritedField('packageWeight'),
                'packageDimension1' => $product->getInheritedField('packageDimension1'),
                'packageDimension2' => $product->getInheritedField('packageDimension2'),
                'packageDimension3' => $product->getInheritedField('packageDimension3')
            ];
        }, $products);
        return new JsonResponse([
            'total' => $totalItems,
            'products' => $productData
        ]);
    }


}