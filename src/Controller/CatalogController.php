<?php

namespace App\Controller;

use Doctrine\DBAL\Exception;
use Pimcore\Db;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

use Pimcore\Controller\FrontendController;
use Pimcore\Model\DataObject\Product;
use Pimcore\Model\DataObject\Data\Link;
use Pimcore\Model\Asset;

class CatalogController extends FrontendController
{

    /**
     * @throws Exception
     */
    public static function getProductTypeOptions(): array
    {
        $db = Db::get();
        $sql = "SELECT DISTINCT segment FROM iwa_catalog WHERE segment IS NOT NULL ORDER BY segment";
        return $db->fetchFirstColumn($sql);
    }

    /**
     * @throws Exception
     */
    public static function getProducts($query, $category, $page = 0, $pageSize = 20, $countOnly = false)
    {
        $db = Db::get();
        $params = [];
        $limit = (int) $pageSize;
        $offset = (int) $page * $pageSize;
        $sql = "SELECT `id`, `productIdentifier`, `name`, `category`, `segment`, `children` FROM iwa_catalog";
        $sqlWhere = "";
        if ($category !== 'all') {
            $sqlWhere .= " WHERE (";
            $sqlWhere .= "LOWER(`category`) = LOWER(:category COLLATE utf8mb4_unicode_ci)";
            $sqlWhere .= " OR LOWER(`segment`) = LOWER(:category COLLATE utf8mb4_unicode_ci)";
            $sqlWhere .= ")";
            $params['category'] = $category;
        }
        if ($query !== 'all') {
            if (empty($sqlWhere)) {
                $sqlWhere = " WHERE";
            } else {
                $sqlWhere .= " AND";
            }
            $sqlWhere .= " LOWER(`children`) LIKE LOWER(:query COLLATE utf8mb4_unicode_ci)";
            $params['query'] = "%$query%";
        }
        $sql .= $sqlWhere;
        if ($countOnly) {
            return $db->fetchOne("SELECT COUNT(*) AS total_count FROM ($sql) AS result", $params);
        } else {
            return $db->fetchAllAssociative("$sql ORDER BY `productIdentifier`, `name` LIMIT $limit OFFSET $offset", $params);
        }
    }
        
    public static function getThumbnail($imageUrl, $size = 'album')
    {
        $imagePath = str_replace('https://mesa.iwa.web.tr/var/assets', '', $imageUrl);
        $image = Asset::getByPath($imagePath);
        if ($image instanceof Asset) {
            return $image->getThumbnail($size)->getPath();
        }
        return $imageUrl;
    }

    /**
     * @Route("/catalog/{query?all}/{category?all}/{page?0}/{pagesize?20}", name="catalog")
     * @throws Exception
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
        $pageSize = $request->get('pagesize');

        $productTypes = $this->getProductTypeOptions();

        $catalogCount = $this->getProducts(query: $query, category: $category, countOnly: true);
        $catalog = $this->getProducts(query: $query, category: $category, page: $page, pageSize: $pageSize);
        $products = [];
        foreach ($catalog as $product) {
            $imageUrl = $this->getThumbnail($product['imageUrl'] ?? '', 'katalog');
            $variationSizeList = $variationColorList = $iwaskuList = $album = [];
            $children = json_decode($product['children'], true);
            foreach ($children as $child) {
                $variationSizeList[] = $child['variationSize'];
                $variationColorList[] = $child['variationColor'];
                $tooltip = htmlspecialchars("{$child['variationSize']}-{$child['variationColor']}");
                $iwaskuList[] = "<span data-bs-toggle='tooltip' title='$tooltip'>{$child['iwasku']}</span>";
                if (strlen($imageUrl) == 0) {
                    $imageUrl = $this->getThumbnail($child['imageUrl'] ?? '', 'katalog');
                }
                foreach (($child['listings'] ?? []) as $listing) {
                    if (strlen($imageUrl) == 0) {
                        $imageUrl = $this->getThumbnail($listing['imageUrl'] ?? '', 'katalog');
                    }
                    $url = unserialize($listing['urlLink'] ?? '');
                    if ($url instanceof Link && strlen($listing['imageUrl'])>0) {
                        $album[] = "<a href='{$url->getPath()}' target='_blank' data-bs-toggle='tooltip' title='{$listing['marketplaceType']} | {$tooltip}'><img src='".$this->getThumbnail($listing['imageUrl'] ?? '')."' alt=''></a>";
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
                $imageUrl = $placeholder ?? '';
            }
            $products[] = [
                'id' => $product['id'],
                'productIdentifier' => $product['productIdentifier'] ?? '',
                'name' => $product['name'] ?? '',
                'variationSizeList' => implode(' | ', $variationSizeList),
                'variationColorList' => implode(' | ', $variationColorList),
                'iwaskuList' => $iwaskuList, //implode(' | ', $iwaskuList),
                'image' => $imageUrl,
                'album' => array_unique($album),
            ];
        }

        return $this->render('catalog/catalog.html.twig', [
            'pageCount' => ceil($catalogCount/$pageSize),
            'query' => $query,
            'category' => $category,
            'page' => $page,
            'pageSize' => $pageSize,
            'productTypes' => $productTypes,
            'products' => $products,
        ]);
    }

}