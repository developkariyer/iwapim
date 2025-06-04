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
     * @Route("/product-dimensions-data", name="product_dimensions_data", methods={"GET"})
     * @return Response
     */
    public function getProductDimensionsData(Request $request): Response
    {
        $page = max(1, (int)$request->query->get('page', 1));
        $pageSize = 50;
        $offset = ($page - 1) * $pageSize;
        $listing = new Product\Listing();
        $listing->setCondition("packageDimension1 IS NULL OR packageDimension2 IS NULL OR packageDimension3 IS NULL OR packageWeight IS NULL");
        $listing->setLimit($pageSize);
        $listing->setOffset($offset);
        $total = $listing->count();
        $products = $listing->load();
        $data = [
            'total' => $total,
            'products' => []
        ];
        foreach ($products as $product) {
            $data['products'][] = [
                'id' => $product->getId(),
                'name' => $product->getName() ?: '',
                'iwasku' => $product->getIwasku() ?: '',
                'category' => $product->getProductCategory() ? $product->getProductCategory()->getName() : '',
                'weight' => $product->getPackageWeight(),
                'width' => $product->getPackageDimension1(),
                'length' => $product->getPackageDimension2(),
                'height' => $product->getPackageDimension3(),
                'desi' => $this->calculateDesi($product)
            ];
        }
        return $this->json($data);
    }



}