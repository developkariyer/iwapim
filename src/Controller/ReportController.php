<?php

namespace App\Controller;

use Pimcore\Controller\FrontendController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Pimcore\Model\DataObject\GroupProduct;
use Pimcore\Model\DataObject\Product;


class ReportController extends FrontendController
{

    /**
     * @Route("/report/group/{group_id}", name="report_group")
     */
    public function groupAction(Request $request): Response
    {
        Product::setGetInheritedValues(true);
        $groupId = $request->get('group_id');
        $group = GroupProduct::getById($groupId);
        $products = $group->getProducts();
        $pricingModels = $group->getPricingModels();
        $productTwig = [];
        /*
                        <td>{{ product.iwasku }}</td>
                <td>{{ product.productCategory }}</td>
                <td>{{ product.productIdentifier }}</td>
                <td>{{ product.name }}</td>
                <td>{{ product.variationSize }}</td>
                <td>{{ product.variationColor }}</td>
                <td>{{ product.packageDimension1 }}</td>
                <td>{{ product.packageDimension2 }}</td>
                <td>{{ product.packageDimension3 }}</td>
                <td>{{ product.packageWeight }}</td>
                <td><img src="{{ product.imageUrl }}" alt="Product Image" style="max-width: 100px; max-height: 100px;"></td>
                <td>{{ product.productCost }}</td>
                <td>{{ product.productDimension1 }}</td>
                <td>{{ product.productDimension2 }}</td>
                <td>{{ product.productDimension3 }}</td>

        */
        foreach ($products as $product) {
            $productTwig[] = [
                'iwasku' => $product->getIwasku(),
                'productCategory' => $product->getProductCategory(),
                'productIdentifier' => $product->getProductIdentifier(),
                'name' => $product->getName(),
                'variationSize' => $product->getVariationSize(),
                'variationColor' => $product->getVariationColor(),
                'packageDimension1' => $product->getPackageDimension1(),
                'packageDimension2' => $product->getPackageDimension2(),
                'packageDimension3' => $product->getPackageDimension3(),
                'packageWeight' => $product->getPackageWeight(),
                'imageUrl' => $product->getImageUrl()->getFullPath(),
                'productCost' => $product->getProductCost(),
                'productDimension1' => $product->getProductDimension1(),
                'productDimension2' => $product->getProductDimension2(),
                'productDimension3' => $product->getProductDimension3(),
            ];
        }

        return $this->render(
            '202409/group.html.twig', 
            [
                'products' => $productTwig,
            ]
        );
    }


}
