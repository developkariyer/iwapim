<?php

namespace App\Controller;

use App\Form\OzonTaskFormType;
use App\Form\OzonTaskProductFormType;
use App\Model\DataObject\VariantProduct;
use App\Utils\Registry;
use App\Utils\Utility;
use Exception;
use Pimcore\Controller\FrontendController;
use Pimcore\Db;
use Pimcore\Model\Asset;
use Pimcore\Model\DataObject\Data\ObjectMetadata;
use Pimcore\Model\DataObject\GroupProduct;
use Pimcore\Model\DataObject\ListingTemplate;
use Pimcore\Model\DataObject\Product;
use Pimcore\Model\Element\DuplicateFullPathException;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Pimcore\Model\DataObject\Marketplace;


class StickerController extends FrontendController
{
    private string $sqlPath = PIMCORE_PROJECT_ROOT . '/src/SQL/Sticker/';

    /**
     * @Route("/sticker/", name="sticker_main_page")
     * @return Response
     */
    public function stickerMainPage(Request $request): Response
    {
        $gproduct = new GroupProduct\Listing();
        $result = $gproduct->load();
        $groups = [];
        foreach ($result as $item) {
            $groups[] = [
                'name' => $item->getKey(),
                'id' => $item->getId()
            ];
        }
        return $this->render('sticker/sticker.html.twig', [
            'groups' => $groups
        ]);
    }

    /**
     * @Route("/sticker/add-sticker-group", name="sticker_new_group", methods={"GET", "POST"})
     * @return Response
     * @throws DuplicateFullPathException
     */
    public function addStickerGroup(Request $request): Response
    {
        if ($request->isMethod('POST')) {
            $formData = $request->request->get('form_data');
            $newGroup = new GroupProduct();
            $operationFolder = Utility::checkSetPath('Operasyonlar');
            $newGroup->setParentId($operationFolder->getId());
            $newGroup->setKey($formData);
            $newGroup->setPublished(1);
            try {
                $newGroup->save();
            } catch (\Exception $e) {
                $this->addFlash('error', 'Grup eklenirken bir hata oluştu.');
                return $this->redirectToRoute('sticker_new_group');
            }
            $this->addFlash('success', 'Grup Başarıyla Eklendi.');
        }
        return $this->render('sticker/add_sticker_group.html.twig');
    }

    /**
     * @Route("/sticker/get-stickers/{groupId}/{page}/{limit}", name="get_stickers", methods={"GET"})
     * @throws \Doctrine\DBAL\Exception
     */
    public function getStickers(int $groupId, int $page = 1, int $limit = 5, ?string $searchTerm = null): JsonResponse
    {
        $groupedStickers = [];
        $page = isset($_GET['page']) ? (int) $_GET['page'] : 1;
        $limit = isset($_GET['limit']) ? (int) $_GET['limit'] : 5;
        $offset = ($page - 1) * $limit;
        $searchCondition = '';
        $searchTerm = $_GET['searchTerm'] ?? null;
        if ($searchTerm !== null) {
            $searchTerm = "%" . $searchTerm . "%";
            $searchCondition = "AND (name LIKE :searchTerm OR productCategory LIKE :searchTerm OR productIdentifier LIKE :searchTerm)";
            $offset = null;
        }
        $sql = "
            SELECT 
                osp.productIdentifier,
                MIN(osp.name) as name,
                MIN(osp.productCategory) as category,
                MIN(osp.imageUrl) as image
            FROM object_relations_gproduct org
            JOIN object_product osp ON osp.oo_id = org.dest_id
            WHERE org.src_id = :groupId
            " . $searchCondition . " 
            GROUP BY osp.productIdentifier
        ";
        if ($offset !== null) {
            $sql .= " LIMIT $limit OFFSET $offset";
        }
        else {
            $sql .= " LIMIT 10";
        }
        $parameters = ['groupId' => (int) $groupId];
        if ($searchTerm) {
            $parameters['searchTerm'] = $searchTerm;
        }
        $mainProducts = Db::get()->fetchAllAssociative($sql, $parameters);
        foreach ($mainProducts as $mainProduct) {
                $groupedStickers[$mainProduct['productIdentifier']][] = [
                    'product_name' => $mainProduct['name'] ?? '',
                    'category' => $mainProduct['category'] ?? '',
                    'image_link' => $mainProduct['image'] ?? '',
                    'product_identifier' => $mainProduct['productIdentifier'] ?? ''
                ];
        }
        $countSql = "
            SELECT 
                COUNT(DISTINCT osp.productIdentifier) AS totalCount
            FROM object_relations_gproduct org
            JOIN object_product osp ON osp.oo_id = org.dest_id
            LEFT JOIN object_relations_product opr 
                ON opr.src_id = osp.oo_id 
                AND opr.type = 'asset' 
                AND opr.fieldname = 'sticker4x6eu'
            WHERE org.src_id = :groupId
            " . $searchCondition;
        $countResult = Db::get()->fetchAssociative($countSql, $parameters);
        $totalProducts = $countResult['totalCount'] ?? 0;
        return new JsonResponse([
            'success' => true,
            'stickers' => $groupedStickers,
            'pagination' => [
                'current_page' => $page,
                'per_page' => $limit,
                'total_items' => $totalProducts,
                'total_pages' => ceil($totalProducts / $limit)
            ]
        ]);
    }

    /**
     * @Route("/sticker/get-product-details/{productIdentifier}/{groupId}", name="get_product_details", methods={"GET"})
     * @throws \Doctrine\DBAL\Exception
     */
    public function getProductDetails($productIdentifier, $groupId): JsonResponse
    {
        $sql = "
            SELECT 
                osp.iwasku,
                osp.name,
                osp.productCode,
                osp.productCategory,
                osp.imageUrl,
                osp.variationSize,
                osp.variationColor,
                osp.productIdentifier,
                opr.dest_id AS sticker_id
            FROM object_relations_gproduct org
            JOIN object_product osp ON osp.oo_id = org.dest_id
            LEFT JOIN object_relations_product opr
                ON opr.src_id = osp.oo_id
                AND opr.type = 'asset'
                AND opr.fieldname = 'sticker4x6eu'
            WHERE osp.productIdentifier = :productIdentifier AND org.src_id = :groupId;
        ";
        $products = Db::get()->fetchAllAssociative($sql, ['productIdentifier' => $productIdentifier, 'groupId' => $groupId]);
        foreach ($products as &$product) {
            if ($product['sticker_id']) {
                $sticker = Asset::getById($product['sticker_id']);
            } else {
                if (isset($product['dest_id'])) {
                    $productObject = Product::getById($product['dest_id']);
                    if (!$productObject) {
                        continue;
                    }
                    $sticker = $productObject->checkSticker4x6eu();
                }
                else {
                    $sticker = null;
                }
            }
            $stickerPath = $sticker ? $sticker->getFullPath() : '';
            $product['sticker_link'] = $stickerPath ;
        }
        unset($product);
        if ($products) {
            return new JsonResponse([
                'success' => true,
                'products' => $products
            ]);
        } else {
            return new JsonResponse([
                'success' => false,
                'message' => 'Product not found.'
            ]);
        }
    }

    /**
     * @Route("/sticker/add-sticker", name="sticker_new", methods={"GET", "POST"})
     * @return Response
     * @throws Exception
     */
    public function addSticker(Request $request): Response
    {
        if ($request->isMethod('POST')) {
            $asin = $request->request->get('form_data');
            $groupId = $request->request->get('group_id');
            $group = GroupProduct::getById($groupId);
            $iwasku = Registry::getKey($asin,'asin-to-iwasku');
            if (isset($iwasku)) {
                $product = Product::findByField('iwasku',$iwasku);
                if ($product instanceof Product) {
                    if (!$product->getInheritedField('sticker4x6eu')) {
                        $product->checkSticker4x6eu();
                    }
                    $group->setProducts(array_merge($group->getProducts(), [$product]));
                    try {
                        $group->save();
                    } catch (\Exception $e) {
                        $this->addFlash('error', 'Etiket eklenirken bir hata oluştu.');
                        return $this->redirectToRoute('sticker_new');
                    }
                    $this->addFlash('success', 'Etiket başarıyla eklendi.');
                } else {
                    $this->addFlash('error', 'Bu ASIN\'e ait ürün bulunamadı.');
                    return $this->redirectToRoute('sticker_new');
                }
            }
            else {
                $this->addFlash('error', 'Yanlış Ürün Sorumluya Ulaşın.');
                return $this->redirectToRoute('sticker_new');
            }
        }
        $gproduct = new GroupProduct\Listing();
        $result = $gproduct->load();
        $groups = [];
        foreach ($result as $item) {
            $groups[] = [
                'name' => $item->getKey(),
                'id' => $item->getId()
            ];
        }
        return $this->render('sticker/add_sticker.html.twig', [
            'groups' => $groups
        ]);
    }

}
