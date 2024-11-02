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
                $url = ($listing && $listing->getUrlLink()) ? $listing->getUrlLink()->getPath() : '';
                $listings[] = "<a href='{$url}' target='_blank' data-bs-toggle='tooltip' title='{$variant->getIwasku()} | {$variant->getVariationSize()} | {$variant->getVariationColor()}'>{$listing->getMarketplace()->getKey()}</a>";
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
        $sql = "SELECT DISTINCT segment FROM iwa_catalog WHERE segment IS NOT NULL ORDER BY segment";
        return $db->fetchFirstColumn($sql);
    }
    
    protected function getProducts($query, $category, $page = 0, $pageSize = 20, $countOnly = false)
    {
        $db = \Pimcore\Db::get();
        $params = [];
        $limit = (int) $pageSize;
        $offset = (int) $page * $pageSize;
        $sql = "SELECT `id`, `productIdentifier`, `name`, `category`, `segment`, `children` FROM iwa_catalog WHERE 1=1";
        if ($category !== 'all') {
            $sql .= " AND (`category` = :category OR `segment` = :category)";	
            $params['category'] = $category;
        }
        if ($query !== 'all') {
            $sql .= " AND `children` LIKE :query";
            $params['query'] = "%$query%";
        }
        if ($countOnly) {
            return $db->fetchOne("SELECT COUNT(*) AS total_count FROM ($sql) AS result", $params);
        } else {
            return $db->fetchAllAssociative("$sql ORDER BY `productIdentifier`, `name` LIMIT $limit OFFSET $offset", $params);
        }
    }

    /**
     * @Route("/catalog/{query?all}/{category?all}/{page?0}", name="catalog")
     */
    public function catalogAction(Request $request): Response
    {
        $query = $request->get('query');
        $category = $request->get('category');
        $page = $request->get('page');

        $productTypes = $this->getProductTypeOptions();

        $catalogCount = $this->getProducts(query: $query, category: $category, countOnly: true);
        $catalog = $this->getProducts(query: $query, category: $category, page: $page, pageSize: 20);
        $products = [];
        foreach ($catalog as $product) {
            $imageUrl = $product['imageUrl'] ?? '';
            $variationSizeList = $variationColorList = $iwaskuList = $listings = $album = [];
            $children = json_decode($product['children'], true);
            foreach ($children as $child) {
                $variationSizeList[] = $child['variationSize'];
                $variationColorList[] = $child['variationColor'];
                $iwaskuList[] = $child['iwasku'];
                $album[] = $child['imageUrl'] ?? '';
                foreach ($child['listings'] as $listing) {
                    $album[] = $listing['imageUrl'] ?? '';
                    $url = unserialize($listing['urlLink'] ?? '');
                    if ($url instanceof Link) {
                        $listings[$listing['marketplaceType'] ?? ''] = "<a href='{$url->getPath()}' target='_blank' data-bs-toggle='tooltip' title='{$child['iwasku']} | {$child['variationSize']} | {$child['variationColor']}'>{$listing['marketplace']}</a>";
                    }
                }
            }
            $variationSizeList = array_unique($variationSizeList);
            $variationColorList = array_unique($variationColorList);
            asort($variationSizeList);
            asort($variationColorList);
            if (strlen($imageUrl) == 0) {
                $imageUrl = $album[0] ?? '';
            }

            $products[] = [
                'id' => $product['id'],
                'productIdentifier' => $product['productIdentifier'] ?? '',
                'name' => $product['name'] ?? '',
                'variationSizeList' => $variationSizeList,
                'variationColorList' => $variationColorList,
                'iwaskus' => $iwaskuList,
                'listings' => array_unique($listings),
                'image' => $imageUrl,
                'album' => array_unique($album),
            ];
        }

        return $this->render('catalog/catalog.html.twig', [
            'pageCount' => ceil($catalogCount/20),
            'query' => $query,
            'category' => $category,
            'page' => $page,
            'productTypes' => $productTypes,
            'products' => $products,
        ]);
    }

}