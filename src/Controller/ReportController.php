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

        return $this->render(
            '202409/group.html.twig', 
            [
                'products' => $products,
            ]
        );
    }


}
