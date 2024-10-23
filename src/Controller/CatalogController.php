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
                $listings[] = "<a href='{$listing->getUrlLink()->getPath()}' target='_blank' data-bs-toggle='tooltip' title='{$variant->getIwasku()} | {$variant->getVariationSize()} | {$variant->getVariationColor()}'>{$listing->getMarketplace()->getKey()}</a>";
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
        return [$mainImage, $album, $listings];
    }

    protected function getProductTypeOptions()
    {
        $db = \Pimcore\Db::get();
        $sql = "SELECT DISTINCT SUBSTRING_INDEX(productIdentifier, '-', 1) AS productType FROM object_product ORDER BY productType";
        $result = $db->fetchAllAssociative($sql);
        return array_filter(array_column($result, 'productType'));
    }

    protected function getProductQuery($query, $category, &$params = [])
    {
        $sql = "FROM object_store_product osp
                INNER JOIN objects o ON osp.oo_id = o.id
                WHERE o.published = 1
                AND o.className = 'Product'
                AND o.type = 'object'
                AND (SELECT parent.type FROM objects parent WHERE parent.id = o.parentId) = 'folder'";
        if ($category !== 'all') {
            $sql .= " AND osp.productIdentifier LIKE :category";
            $params['category'] = "$category-%";
        }
        if ($query !== 'all') {
            $sql .= " AND (osp.productIdentifier LIKE :query OR osp.name LIKE :name)";
            $params['query'] = "%$query%";
            $params['name'] = "%$query%";
        }
        $sql .= " ORDER BY osp.productIdentifier";
        return $sql;
    }
    
    protected function getProductCount($query, $category)
    {
        $db = \Pimcore\Db::get();
        $params = [];
        $sql = "SELECT COUNT(*) AS count " . $this->getProductQuery($query, $category, $params);
        return $db->fetchOne($sql, $params);
    }
    
    protected function getProducts($query, $category, $page, $pageSize = 100)
    {
        $db = \Pimcore\Db::get();
        $params = [];
        $sql = "SELECT osp.oo_id " . $this->getProductQuery($query, $category, $params) . " LIMIT :limit OFFSET :offset";
        $params['limit'] = (int) $pageSize;
        $params['offset'] = (int) $page * $pageSize;
        return $db->fetchFirstColumn($sql, $params);
    }

    /**
     * @Route("/catalog/{query?all}/{category?all}/{page?0}", name="catalog")
     */
    public function catalogAction(Request $request): Response
    {
        $query = $request->get('query');
        $page = $request->get('page');
        $category = $request->get('category');

        $productTypes = $this->getProductTypeOptions();

        $pimProductCount = $this->getProductCount($query, $category);
        $pimProductIds = $this->getProducts($query, $category, $page);
        $products = [];
        foreach ($pimProductIds as $pimProductId) {
            $pimProduct = Product::getById($pimProductId);
            if ($pimProduct->level() > 0) {
                continue;
            }
            [$image, $album, $listings] = $this->getImageAndAlbumAndListings($pimProduct);
            if (is_null($image)) {
                if ($pimProduct->getImage()) {
                    $image = $pimProduct->getImage()->getThumbnail('katalog');
                } else {
                    $image = Asset::getById(76678)->getThumbnail('katalog');
                }
            }
            $products[] = [
                'id' => $pimProduct->getId(),
                'productIdentifier' => $pimProduct->getProductIdentifier(),
                'name' => $pimProduct->getName(),
                'variationSizeList' => str_replace("\n", " | ", $pimProduct->getVariationSizeList()),
                'variationColorList' => str_replace("\n", " | ", $pimProduct->getVariationColorList()),
                'listings' => $listings,
                'image' => $image,
                'album' => $album,
            ];
        }

        return $this->render('catalog/catalog.html.twig', [
            'totalProductCount' => $pimProductCount,
            'currentPage' => $page,
            'productTypes' => $productTypes,
            'products' => $products,
        ]);

    }

}