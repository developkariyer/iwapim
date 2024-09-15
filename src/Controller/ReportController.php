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
        $groupId = $request->get('group_id');
        $group = GroupProduct::getById($groupId);
        
        if (!$group) {
            return $this->render('202409/group.html.twig', ['title' => 'Group not found', 'products' => [], 'models' => []]);
        }

        $products = $group->getProducts();
        $pricingModels = $group->getPricingModels();
        
        $productTwig = $this->prepareProductsData($products, $pricingModels);
        $modelTwig = $this->prepareModelsData($pricingModels);

        return $this->render('202409/group.html.twig', [
            'title' => $group->getKey(),
            'products' => $productTwig,
            'models' => $modelTwig,
            'markets' => array_keys(Marketplace::getMarketplaceListAsArrayKeys()),
        ]);
    }

    private function prepareProductsData($products, $pricingModels)
    {
        $priceTemplate = Marketplace::getMarketplaceListAsArrayKeys();
        $productTwig = [];

        foreach ($products as $product) {
            if (!($imageUrl = $product->getInheritedField('imageUrl'))) {
                $imageUrl = ($image = $product->getInheritedField('image')) ? $image->getFullPath() : '';
            }

            $productModels = $this->getProductModels($pricingModels, $product);
            $prices = $this->getProductPrices($product, $priceTemplate);

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
        return $productTwig;
    }

    private function prepareModelsData($pricingModels)
    {
        $modelTwig = [];    
        foreach ($pricingModels as $pricingModel) {
            $modelTwig[] = $pricingModel->getKey();
        }    
        return $modelTwig;
    }

    private function getProductModels($pricingModels, $product)
    {
        $productModels = [];

        foreach ($pricingModels as $pricingModel) {
            $modelKey = $pricingModel->getKey();
            $productModels[$modelKey] = 123; // Modify as per actual logic
        }    
        return $productModels;
    }

    private function getProductPrices($product, $priceTemplate)
    {
        foreach ($product->getListingItems() as $listingItem) {
            if ($listingItem->getMarketplace()->getMarketplaceType() === 'Amazon') {
                $collection = $listingItem->getAmazonMarketplace();
                foreach ($collection as $amazonMarketplace) {
                    if ($amazonMarketplace->getStatus() === 'Active') {
                        $urlLink = $amazonMarketplace->getUrlLink();
                        $urlLink = $urlLink instanceof Link ? $urlLink->getHref() : '';
                        $priceTemplate["Amazon_{$amazonMarketplace->getMArketplaceId()}"] = [
                            'priceTL' => number_format(Currency::convertCurrency($amazonMarketplace->getSaleCurrency() ?? 'US DOLLAR', $amazonMarketplace->getPrice()), 2, '.', ','),
                            'priceUS' => number_format(Currency::convertCurrency($amazonMarketplace->getSaleCurrency() ?? 'US DOLLAR', $amazonMarketplace->getPrice(), 'US DOLLAR'), 2, '.', ','),
                            'urlLink' => $urlLink,
                        ];
                    }
                }
            } else {
                $urlLink = $listingItem->getUrlLink();
                $urlLink = $urlLink instanceof Link ? $urlLink->getHref() : '';
                $priceTemplate[$listingItem->getMarketplace()->getKey()] = [
                    'priceTL' => number_format(Currency::convertCurrency($listingItem->getSaleCurrency() ?? 'US DOLLAR', $listingItem->getSalePrice()), 2, '.', ','),
                    'priceUS' => number_format(Currency::convertCurrency($listingItem->getSaleCurrency() ?? 'US DOLLAR', $listingItem->getSalePrice(), 'US DOLLAR'), 2, '.', ','),
                    'urlLink' => $urlLink,
                ];
            }
        }
        return $priceTemplate;
    }

    private function prepareSingleProductData($product)
    {
        if (!($imageUrl = $product->getInheritedField('imageUrl'))) {
            $imageUrl = ($image = $product->getInheritedField('image')) ? $image->getFullPath() : '';
        }
        $prices = $this->getProductPrices($product, []);
        return [
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
    }
 
    
    /**
     * @Route("/report/connected", name="report_connected")
     */
    public function connectedAction(): Response
    {
        $db = \Pimcore\Db::get();
        $sql = "SELECT DISTINCT src_id FROM object_relations_product WHERE fieldname='listingItems' LIMIT 5";
        $products = array_map(function ($row) {
            return Product::getById($row['src_id']);
        }, $db->fetchAllAssociative($sql));

        $pricingModels = []; // Populate this as per your requirement
        $productTwig = $this->prepareProductsData($products, $pricingModels);
        $modelTwig = $this->prepareModelsData($pricingModels);

        return $this->render('202409/group.html.twig', [
            'title' => 'Bağlanmış Ürünler',
            'products' => $productTwig,
            'models' => $modelTwig,
            'markets' => array_keys(Marketplace::getMarketplaceListAsArrayKeys()),
        ]);
    }

    /**
     * @Route("/report/product/{product_id}", name="report_product")
     */
    public function productAction(Request $request): Response
    {
        $productId = $request->get('product_id');
        $product = Product::getById($productId);

        if (!$product) {
            return $this->render('202409/group.html.twig', ['title' => 'Product not found', 'products' => [], 'models' => []]);
        }

        $products = $product->getChildren();
        $pricingModels = []; // Populate this as per your requirement
        $productTwig = $this->prepareProductsData($products, $pricingModels);
        $modelTwig = $this->prepareModelsData($pricingModels);

        return $this->render('202409/group.html.twig', [
            'title' => $product->getKey(),
            'products' => $productTwig,
            'models' => $modelTwig,
            'markets' => array_keys(Marketplace::getMarketplaceListAsArrayKeys()),
        ]);
    }

    /**
     * @Route("/report/cost/{product_id}", name="report_cost")
     */
    public function costAction(Request $request): Response
    {
        $productId = $request->get('product_id');
        $product = Product::getById($productId);

        if (!$product) {
            return $this->render('202409/cost.html.twig', ['title' => 'Product not found']);
        }

        $productTwig = $this->prepareSingleProductData($product);

        return $this->render('202409/cost.html.twig', [
            'title' => $product->getKey(),
            'product' => $productTwig,
        ]);
    }

}
