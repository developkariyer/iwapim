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
use Pimcore\Asset;

class CatalogController extends FrontendController
{
    
    /**
     * @Route("/catalog", name="catalog")
     */
    public function catalogAction(): Response
    {
        $db = \Pimcore\Db::get();
        $sql = "SELECT DISTINCT SUBSTRING_INDEX(productIdentifier, '-', 1) AS productType FROM object_product ORDER BY productType";
        $result = $db->fetchAllAssociative($sql);
        $productTypes = array_column($result, 'productType');

        $listing = new Product\Listing();
        $listing->setCondition('iwasku IS NULL');
        $listing->setOrderKey('productIdentifier');
        $listing->setLimit(50);
        $result = $listing->load();

        $products = [];
        foreach ($result as $row) {
            $album = [];
            foreach ($row->getListingItems() as $listing) {
                foreach ($listing->getImageGallery() as $image) {
                    $album[] = $image->getImage()->getThumbnail('album');
                    if (count($album) >= 30) {
                        break;
                    }
                }
            }
            $image = $row->getImage() ? $row->getImage()->getThumbnail('katalog') : null;
            $products[] = [
                'id' => $row->getId(),
                'productIdentifier' => $row->getProductIdentifier(),
                'name' => $row->getName(),
                'variationSizeList' => $row->getVariationSizeList(),
                'variationColorList' => $row->getVariationColorList(),
                'image' => $image,
                'album' => $album
            ];
        }

        return $this->render('catalog/catalog.html.twig', [
            'productTypes' => $productTypes,
            'products' => $products
        ]);

    }

}