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

    public function getThumbnail($imageUrl, $size = 'album')
    {
        $imagePath = str_replace('https://mesa.iwa.web.tr/var/assets', '', $imageUrl);
        $image = Asset::getByPath($imagePath);
        if ($image instanceof Asset) {
            return $image->getThumbnail($size)->getPath();
        }
        return $imageUrl;
    }

    /**
     * @Route("/catalog/{query?all}/{category?all}/{page?0}", name="catalog")
     */
    public function catalogAction(Request $request): Response
    {
        $placeholderImage = Asset::getByPath('/iwapim.png');
        error_log(get_class($placeholderImage));
        if ($placeholderImage instanceof Asset) {
            $placeholder = $placeholderImage->getThumbnail('katalog')->getPath();
            error_log($placeholder);
        }
        $query = $request->get('query');
        $category = $request->get('category');
        $page = $request->get('page');

        $productTypes = $this->getProductTypeOptions();

        $catalogCount = $this->getProducts(query: $query, category: $category, countOnly: true);
        $catalog = $this->getProducts(query: $query, category: $category, page: $page, pageSize: 20);
        $products = [];
        foreach ($catalog as $product) {
            $imageUrl = $this->getThumbnail($product['imageUrl'] ?? '', 'katalog');
            $variationSizeList = $variationColorList = $iwaskuList = $listings = $album = [];
            $children = json_decode($product['children'], true);
            foreach ($children as $child) {
                $variationSizeList[] = $child['variationSize'];
                $variationColorList[] = $child['variationColor'];
                $tooltip = htmlspecialchars("Size: {$child['variationSize']} | Color: {$child['variationColor']}");
                $iwaskuList[] = "<span data-bs-toggle='tooltip' title='$tooltip'>{$child['iwasku']}</span>";
                if (strlen($imageUrl) == 0) {
                    $imageUrl = $this->getThumbnail($child['imageUrl'] ?? '', 'katalog');
                }
                foreach ($child['listings'] as $listing) {
                    if (strlen($imageUrl) == 0) {
                        $imageUrl = $this->getThumbnail($listing['imageUrl'] ?? '', 'katalog');
                    }
                    $url = unserialize($listing['urlLink'] ?? '');
                    if ($url instanceof Link && count($album)<24 && strlen($listing['imageUrl'])>0) {
                        $album[] = "<a href='{$url->getPath()}' target='_blank' data-bs-toggle='tooltip' title='{$listing['marketplaceType']} | {$tooltip}'><img src='".$this->getThumbnail($listing['imageUrl'] ?? '')."'></a>";
                    }
                }
            }
            $variationSizeList = array_unique($variationSizeList);
            $variationColorList = array_unique($variationColorList);
            asort($variationSizeList);
            asort($variationColorList);
            if (strlen($imageUrl) == 0) {
                $productObj = Product::getById($product['id']);
                if ($productObj instanceof Product) {
                    $image = $productObj->getImage();
                    if ($image instanceof Asset) {
                        $imageUrl = $image->getThumbnail('katalog')->getPath();
                    }
                }
            }
            if (strlen($imageUrl) == 0) {
                $imageUrl = $placeholder;
            }
            $products[] = [
                'id' => $product['id'],
                'productIdentifier' => $product['productIdentifier'] ?? '',
                'name' => $product['name'] ?? '',
                'variationSizeList' => implode(' | ', $variationSizeList),
                'variationColorList' => implode(' | ', $variationColorList),
                'iwaskuList' => implode(' | ', $iwaskuList),
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