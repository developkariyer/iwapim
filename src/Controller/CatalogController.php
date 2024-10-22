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
use Pimcore\Model\Asset;

class CatalogController extends FrontendController
{
    
    protected function getImageAndAlbumAndListings($product)
    {
        $mainImage = null;
        $album = [];
        $listings = [];
        foreach ($product->getChildren() as $variant) {
            foreach ($variant->getListingItems() as $listing) {
                $listings[] = $listing->getUrlLink()->getPath();
                if (count($album) >= 30) {
                    continue;
                }
                foreach ($listing->getImageGallery() as $image) {
                    if (is_null($mainImage)) {
                        $mainImage = $image->getImage()->getThumbnail('katalog');
                    } else {
                        if (count($album) < 30) {
                            $album[] = $image->getImage()->getThumbnail('album');
                        }
                    }
                }
            }
        }
        error_log(print_r($listings, true));
        return [$mainImage, $album, $listings];
    }

    /**
     * @Route("/catalog", name="catalog")
     */
    public function catalogAction(): Response
    {
        $db = \Pimcore\Db::get();
        $sql = "SELECT DISTINCT SUBSTRING_INDEX(productIdentifier, '-', 1) AS productType FROM object_product ORDER BY productType";
        $result = $db->fetchAllAssociative($sql);
        $productTypes = array_filter(array_column($result, 'productType'));

        $listing = new Product\Listing();
        $listing->setCondition('iwasku IS NULL');
        $listing->setOrderKey('productIdentifier');
        $listing->setLimit(50);
        $result = $listing->load();

        $products = [];
        foreach ($result as $row) {
            if ($row->level() > 0) {
                continue;
            }
            [$image, $album, $listings] = $this->getImageAndAlbumAndListings($row);
            if (is_null($image)) {
                if ($row->getImage()) {
                    $image = $row->getImage()->getThumbnail('katalog');
                } else {
                    $image = Asset::getById(76678)->getThumbnail('katalog');
                }
            }
            $products[] = [
                'id' => $row->getId(),
                'productIdentifier' => $row->getProductIdentifier(),
                'name' => $row->getName(),
                'variationSizeList' => str_replace("\n", " | ", $row->getVariationSizeList()),
                'variationColorList' => str_replace("\n", " | ", $row->getVariationColorList()),
                'listings' => $listings,
                'image' => $image,
                'album' => $album,
            ];
        }

        return $this->render('catalog/catalog.html.twig', [
            'productTypes' => $productTypes,
            'products' => $products,
        ]);

    }

}