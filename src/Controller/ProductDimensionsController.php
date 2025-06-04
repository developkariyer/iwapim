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
        $page = max(0, (int)$request->query->get('page', 0));
        $limit = max(1, (int)$request->query->get('limit', 50));
        $offset = $page * $limit;
        $conditions = ["iwasku IS NOT NULL AND iwasku != ''"];
        if ($iwasku) {
            $conditions[] = "iwasku LIKE '%" . addslashes($iwasku) . "%'";
        }
        if ($category) {
            $conditions[] = "productCategory = '" . addslashes($category) . "'";
        }
        if ($packageStatus === 'all_empty') {
            $conditions[] = "packageWeight IS NULL AND packageDimension1 IS NULL AND packageDimension2 IS NULL AND packageDimension3 IS NULL";
        } elseif ($packageStatus === 'any_empty') {
            $conditions[] = "packageWeight IS NULL OR packageDimension1 IS NULL OR packageDimension2 IS NULL OR packageDimension3 IS NULL";
        } elseif ($packageStatus === 'all_filled') {
            $conditions[] = "packageWeight IS NOT NULL AND packageDimension1 IS NOT NULL AND packageDimension2 IS NOT NULL AND packageDimension3 IS NOT NULL";
        }
        $listing = new Product\Listing();
        $listing->setUnpublished(false);
        $listing->setCondition(implode(' AND ', $conditions));
        $listing->setLimit($limit);
        $listing->setOffset($offset);
        $totalCount = $listing->count();
        $products = $listing->load();
        $result = [];
        foreach ($products as $product) {
            if (!$product instanceof Product || $product->level() !== 1) {
                continue;
            }
            $result[] = [
                'name' => $product->getInheritedField("name"),
                'iwasku' => $product->getInheritedField("iwasku"),
                'variationSize' => $product->getVariationSize(),
                'variationColor' => $product->getVariationColor(),
                'category' => $product->getInheritedField("productCategory"),
                'weight' => $product->getInheritedField("packageWeight"),
                'width' => $product->getInheritedField("packageDimension1"),
                'length' => $product->getInheritedField("packageDimension2"),
                'height' => $product->getInheritedField("packageDimension3"),
                'desi5000' => $product->getInheritedField("desi5000"),
            ];
        }
        return new JsonResponse([
            'total' => $totalCount,
            'page' => $page,
            'limit' => $limit,
            'products' => $result,
        ]);
    }


}