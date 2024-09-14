<?php

namespace App\Controller;

use Pimcore\Controller\FrontendController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Pimcore\Model\DataObject\Product\Listing as ProductListing;
use Pimcore\Model\DataObject\ShopifyListing\Listing as ShopifyListingListing;
use Pimcore\Model\DataObject\ShopifyVariant\Listing as ShopifyVariantListing;
use Pimcore\Model\DataObject\AmazonVariant\Listing as AmazonVariantListing;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use GuzzleHttp\Client;


class ReportController extends FrontendController
{

    /**
     * @Route("/report/group/{group_id}", name="report_group")
     */
    public function groupAction(Request $request): Response
    {
        $groupId = $request->get('group_id');
        $group = \Pimcore\Model\DataObject\ProductGroup::getById($groupId);
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
