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
            if (!($imageUrl = $product->getInheritedField('imageUrl'))) {
                $imageUrl = ($image = $product->getInheritedField('image')) ? $image->getFullPath() : '';
            }
            $productTwig[] = [
                'iwasku' => $product->getIwasku(),
                'productCategory' => $product->getInheritedField('productCategory'),
                'productIdentifier' => $product->getInheritedField('productIdentifier'),
                'name' => $product->getInheritedField('name'),
                'variationSize' => $product->getVariationSize(),
                'variationColor' => $product->getVariationColor(),
                'packageDimension1' => $product->getInheritedField('packageDimension1'),
                'packageDimension2' => $product->getInheritedField('packageDimension2'),
                'packageDimension3' => $product->getInheritedField('packageDimension3'),
                'packageWeight' => $product->getInheritedField('packageWeight'),
                'imageUrl' => $imageUrl,
                'productCost' => $product->getProductCost(),
                'productDimension1' => $product->getInheritedField('productDimension1'),
                'productDimension2' => $product->getInheritedField('productDimension2'),
                'productDimension3' => $product->getInheritedField('productDimension3'),
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
