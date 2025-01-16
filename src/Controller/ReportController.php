<?php

namespace App\Controller;

use Doctrine\DBAL\Exception;
use Pimcore\Db;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

use Pimcore\Controller\FrontendController;
use Pimcore\Model\DataObject\GroupProduct;
use Pimcore\Model\DataObject\Product;
use Pimcore\Model\DataObject\Currency;
use Pimcore\Model\DataObject\Marketplace;
use Pimcore\Model\DataObject\Data\Link;

class ReportController extends FrontendController
{
    private static function stickerPath($stickerType, $product)
    {
        if (!in_array($stickerType, ['iwasku', 'eu'])) {
            $stickerType = 'iwasku';
        }
        if (!$product instanceof Product) {
            return '';
        }
        $getStickerMethod = "getSticker4x6{$stickerType}";
        $checkStickerMethod = "checkSticker4x6{$stickerType}";
        $sticker = $product->$getStickerMethod();
        if (!$sticker) {
            $sticker = $product->$checkStickerMethod();
        }
        return $sticker->getFullPath();
    }

    private function prepareProductsData($products, $pricingModels, $showPrice = true, $stickerType = 'iwasku'): array
    {
        $priceTemplate = Marketplace::getMarketplaceListAsArrayKeys();
        $productTwig = [];

        foreach ($products as $product) {
            if (!($imageUrl = $product->getInheritedField('imageUrl'))) {
                $imageUrl = ($image = $product->getInheritedField('image')) ? $image->getFullPath() : '';
            }

            $productModels = $this->getProductModels($pricingModels);
            $prices = $this->getProductPrices($product, $priceTemplate);

            $sticker = self::stickerPath($stickerType, $product);

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
                'productCost' => $showPrice ? $product->getProductCost() : '',
                'models' => $productModels,
                'bundleItems' => [], //$product->getBundleItems(),
                'prices' => $prices,
                'sticker' => $sticker,
                'documents' => $product->getInheritedField('technicals'),
            ];
            unset($product);
            unset($productModels);
            unset($prices);
            gc_collect_cycles();
        }
        return $productTwig;
    }

    private function prepareModelsData($pricingModels): array
    {
        $modelTwig = [];    
        foreach ($pricingModels as $pricingModel) {
            $modelTwig[] = $pricingModel->getKey();
        }    
        return $modelTwig;
    }

    private function getProductModels($pricingModels): array
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
                        $fulfillment = $amazonMarketplace->getFulfillmentChannel() === 'DEFAULT' ? 'FBM' : 'FBA';
                        $fulfillment.= " ({$amazonMarketplace->getSku()})";
                        $priceTL = rtrim(rtrim(number_format(Currency::convertCurrency($amazonMarketplace->getSaleCurrency() ?? 'US DOLLAR', $amazonMarketplace->getSalePrice()), 4), '0'), '.');
                        $priceORIG = rtrim(rtrim(number_format($amazonMarketplace->getSalePrice(), 4), '0'), '.');
                        $currencyORIG = $amazonMarketplace->getSaleCurrency();
                        $price = "<a href='{$urlLink}' target='_blank' data-bs-toggle='tooltip' title='{$fulfillment}:{$priceORIG}{$currencyORIG}'>{$priceTL}</a>";
                        if (isset($priceTemplate["Amazon_{$amazonMarketplace->getMArketplaceId()}"])) {
                            $priceTemplate["Amazon_{$amazonMarketplace->getMArketplaceId()}"] .= "<br>{$price}";
                        } else {
                            $priceTemplate["Amazon_{$amazonMarketplace->getMArketplaceId()}"] = $price;
                        }
                    }
                }
            } else {
                $urlLink = $listingItem->getUrlLink();
                $urlLink = $urlLink instanceof Link ? $urlLink->getHref() : '';
                $priceTL = rtrim(rtrim(number_format(Currency::convertCurrency($listingItem->getSaleCurrency() ?? 'US DOLLAR', $listingItem->getSalePrice()), 4), '0'), '.');
                $priceORIG = rtrim(rtrim(number_format($listingItem->getSalePrice(), 4), '0'), '.');
                $currencyORIG = $listingItem->getSaleCurrency();
                $priceTemplate[$listingItem->getMarketplace()->getKey()] = "<a href='{$urlLink}' target='_blank' data-bs-toggle='tooltip' title='{$priceORIG}{$currencyORIG}'>{$priceTL}</a>";
            }
            unset($listingItem);
        }
        return $priceTemplate;
    }

    private function prepareSingleProductData($product): array
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
            'productCost' => true ? $product->getProductCost() : '',
            'bundleItems' => [], //$product->getBundleItems(),
            'prices' => $prices,
            'sticker' => '',
            'documents' => $product->getInheritedField('technicals'),
        ];
    }


    /**
     * @Route("/report/connected", name="report_connected")
     * @throws Exception
     */
    public function connectedAction(): Response
    {
        $db = Db::get();
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
     * @ Route("/report/cost/{product_id}", name="report_cost")
     * /
     * public function costAction(Request $request): Response
    * {
        * $productId = $request->get('product_id');
        * $product = Product::getById($productId);
 *
* if (!$product) {
            * return $this->render('202409/cost.html.twig', ['title' => 'Product not found']);
        * }
 *
* $productTwig = $this->prepareSingleProductData($product);
 *
* return $this->render('202409/cost.html.twig', [
            * 'title' => $product->getKey(),
            * 'product' => $productTwig,
     * ]);
     * }*/

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

    /**
     * @Route("/report/sticker/{group_id}/{type?iwasku}", name="report_sticker")
     */
    public function stickerAction(Request $request): Response
    {
        $groupId = $request->get('group_id');
        $type = $request->get('type', 'iwasku');
        $group = GroupProduct::getById($groupId);

        //if (!$group) {
        //    return $this->render('202409/sticker.html.twig', ['title' => 'Group not found']);
        //}

        $products = $group->getProducts();
        $pricingModels = [];
        $productTwig = $this->prepareProductsData($products, $pricingModels, false, $type);
        $modelTwig = [];

        unset($products);
        unset($pricingModels);
        gc_collect_cycles();

        return $this->render('202409/group.html.twig', [
            'title' => $group->getKey(),
            'products' => $productTwig,
            'models' => $modelTwig,
            'markets' => [],
        ]);
    }

}
