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
        $modelTwig = [];
        foreach ($pricingModels as $pricingModel) {
            $modelTwig[] = $pricingModel->getKey();
        }
        foreach ($products as $product) {
            if (!($imageUrl = $product->getInheritedField('imageUrl'))) {
                $imageUrl = ($image = $product->getInheritedField('image')) ? $image->getFullPath() : '';
            }
            $productModels = [];
            foreach ($pricingModels as $pricingModel) {
                $modelKey = $pricingModel->getKey();
                $productModels[$modelKey] = 123;
            }
            $productTwig[] = [
                'iwasku' => $product->getIwasku(),
                'productCategory' => $product->getInheritedField('productCategory'),
                'productIdentifier' => $product->getInheritedField('productIdentifier'),
                'name' => $product->getInheritedField('name'),
                'variationSize' => $product->getVariationSize(),
                'variationColor' => $product->getVariationColor(),
                'productDimension1' => $product->getInheritedField('productDimension1'),
                'productDimension2' => $product->getInheritedField('productDimension2'),
                'productDimension3' => $product->getInheritedField('productDimension3'),
                'packageWeight' => $product->getInheritedField('packageWeight'),
                'imageUrl' => $imageUrl,
                'productCost' => $product->getProductCost(),
                'models' => $productModels,
            ];
        }

        return $this->render(
            '202409/group.html.twig', 
            [
                'title' => $group->getKey(),
                'products' => $productTwig,
                'models' => $modelTwig,
            ]
        );
    }


}
