<?php

namespace App\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Security;

use Pimcore\Controller\FrontendController;
use Pimcore\Model\DataObject\GroupProduct;
use Pimcore\Model\DataObject\Product;
use Pimcore\Model\DataObject\Currency;
use Pimcore\Model\DataObject\Marketplace;
use Pimcore\Model\DataObject\Data\Link;

class ReportController extends FrontendController
{
    private $security;

    public function __construct(Security $security)
    {
        $this->security = $security;
    }

    /**
     * @Route("/report/group/{group_id}", name="report_group")
     */
    public function groupAction(Request $request): Response
    {
        Product::setGetInheritedValues(true);
        $groupId = $request->get('group_id');
        $group = GroupProduct::getById($groupId);
        $priceTemplate = Marketplace::getMarketplaceListAsArrayKeys();
        if (!$group) {
            return $this->render('202409/group.html.twig', ['title' => 'Group not found','products' => [],'models' => [],]);
        }
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
            $prices = $priceTemplate;
            foreach ($product->getListingItems() as $listingItem) {
                if ($listingItem->getMarketplace()->getMarketplaceType() === 'Amazon') {
                    continue;
                } else {
                    $urlLink = $listingItem->getUrlLink();
                    $urlLink = $urlLink instanceof Link ? $urlLink->getHref() : '';
                    $prices[$listingItem->getMarketplace()->getKey()] = [
                        'priceTL' => number_format(Currency::convertCurrency($listingItem->getSaleCurrency() ?? 'US DOLLAR', $listingItem->getSalePrice()), 2, '.', ','),
                        'priceUS' => number_format(Currency::convertCurrency($listingItem->getSaleCurrency() ?? 'US DOLLAR', $listingItem->getSalePrice(), 'US DOLLAR'), 2, '.', ','),
                        'urlLink' => $urlLink,
                    ];
                }
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
                'bundleItems' => $product->getBundleItems(),
                'prices' => $prices,
            ];
        }
        return $this->render(
            '202409/group.html.twig', 
            [
                'title' => $group->getKey(),
                'products' => $productTwig,
                'models' => $modelTwig,
                'markets' => array_keys($priceTemplate),
            ]
        );
    }

    /**
     * @Route("/report/connected", name="report_connected")
     */
    public function connectedAction(Request $request): Response
    {
        Product::setGetInheritedValues(true);
        $products = [];
        $db = \Pimcore\Db::get();
        $sql = "SELECT DISTINCT src_id FROM object_relations_product WHERE fieldname='listingItems'";
        foreach ($db->fetchAllAssociative($sql) as $row) {
            $products[] = Product::getById($row['src_id']);
        }
        $priceTemplate = Marketplace::getMarketplaceListAsArrayKeys();
        $pricingModels = [];
        $productTwig = [];
        $modelTwig = [];
        foreach ($products as $product) {
            if (!($imageUrl = $product->getInheritedField('imageUrl'))) {
                $imageUrl = ($image = $product->getInheritedField('image')) ? $image->getFullPath() : '';
            }
            $productModels = [];
            foreach ($pricingModels as $pricingModel) {
                $modelKey = $pricingModel->getKey();
                $productModels[$modelKey] = 123;
            }
            $prices = $priceTemplate;
            foreach ($product->getListingItems() as $listingItem) {
                if ($listingItem->getMarketplace()->getMarketplaceType() === 'Amazon') {
                    continue;
                } else {
                    $urlLink = $listingItem->getUrlLink();
                    $urlLink = $urlLink instanceof Link ? $urlLink->getHref() : '';
                    $prices[$listingItem->getMarketplace()->getKey()] = [
                        'priceTL' => number_format(Currency::convertCurrency($listingItem->getSaleCurrency() ?? 'US DOLLAR', $listingItem->getSalePrice()), 2, '.', ','),
                        'priceUS' => number_format(Currency::convertCurrency($listingItem->getSaleCurrency() ?? 'US DOLLAR', $listingItem->getSalePrice(), 'US DOLLAR'), 2, '.', ','),
                        'urlLink' => $urlLink,
                    ];
                }
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
                'bundleItems' => $product->getBundleItems(),
                'prices' => $prices,
            ];
        }
        return $this->render(
            '202409/group.html.twig', 
            [
                'title' => 'Bağlanmış Ürünler',
                'products' => $productTwig,
                'models' => $modelTwig,
                'markets' => array_keys($priceTemplate),
            ]
        );
    }

    /**
     * @Route("/report/cost/{product_id}", name="report_cost")
     */
    public function costAction(Request $request): Response
    {
        Product::setGetInheritedValues(true);
        $productId = $request->get('product_id');
        $product = Product::getById($productId);
        if (!$product) {
            return $this->render('202409/cost.html.twig', ['title' => 'Product not found']);
        }
        if (!($imageUrl = $product->getInheritedField('imageUrl'))) {
            $imageUrl = ($image = $product->getInheritedField('image')) ? $image->getFullPath() : '';
        }
        $prices = [];
        foreach ($product->getListingItems() as $listingItem) {
            $urlLink = $listingItem->getUrlLink();
            $urlLink = $urlLink instanceof Link ? $urlLink->getHref() : '';
            $prices[] = [
                'marketplace' => $listingItem->getMarketplace()->getKey(),
                'price' => number_format(Currency::convertCurrency($listingItem->getSaleCurrency() ?? 'US DOLLAR', $listingItem->getSalePrice()), 2, '.', ',').
                    'TL ('.number_format(Currency::convertCurrency($listingItem->getSaleCurrency() ?? 'US DOLLAR', $listingItem->getSalePrice(), 'US DOLLAR'), 2, '.', ',').'$)',
                'urlLink' => $urlLink,
            ];
        }
        $productTwig = [
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
            'bundleItems' => $product->getBundleItems(),
            'prices' => $prices,
        ];
        

        return $this->render(
            '202409/cost.html.twig',
            [
                'title' => $product->getKey(),
            ]
        );
    }

}
