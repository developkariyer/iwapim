<?php

namespace App\Controller;

use App\Utils\Registry;
use App\Utils\Utility;
use Exception;
use Pimcore\Controller\FrontendController;
use Pimcore\Db;
use Pimcore\Model\Asset;
use Pimcore\Model\DataObject\GroupProduct;
use Pimcore\Model\DataObject\Product;
use Pimcore\Model\Element\DuplicateFullPathException;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[IsGranted('ROLE_STICKERMANAGER'), IsGranted('ROLE_PIMCORE_ADMIN')]
class StickerController extends FrontendController
{
    protected function getGroupList(): array
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
        return $groups;
    }

    /**
     * @Route("/sticker", name="sticker_main_page")
     * @return Response
     */
    public function stickerMainPage(): Response
    {
        return $this->render('sticker/sticker.html.twig', [
            'groups' => $this->getGroupList()
        ]);
    }

    /**
     * @Route("/sticker/add-sticker-group", name="sticker_new_group", methods={"POST"})
     * @param Request $request
     * @return Response
     * @throws DuplicateFullPathException
     */
    public function addStickerGroup(Request $request): Response
    {
        $formData = $request->request->get('form_data');
        if (!preg_match('/^[a-zA-Z0-9_ ]+$/', $formData)) {
            $this->addFlash('error', 'Grup adı sadece harf, rakam, boşluk ve alt çizgi içerebilir.');
            return $this->redirectToRoute('sticker_main_page');
        }
        if (mb_strlen($formData) > 190) {
            $this->addFlash('error', 'Grup adı 190 karakterden uzun olamaz.');
            return $this->redirectToRoute('sticker_main_page');
        }
        $operationFolder = Utility::checkSetPath('Operasyonlar');
        if (!$operationFolder) {
            $this->addFlash('error', 'Operasyonlar klasörü bulunamadı.');
            return $this->redirectToRoute('sticker_main_page');
        }
        $existingGroup = GroupProduct::getByPath($operationFolder->getFullPath() . '/' . $formData);
        if ($existingGroup) {
            $this->addFlash('error', 'Bu grup zaten mevcut.');
            return $this->redirectToRoute('sticker_main_page');
        }
        $newGroup = new GroupProduct();
        $newGroup->setParent($operationFolder);
        $newGroup->setKey($formData);
        $newGroup->setPublished(true);
        try {
            $newGroup->save();
        } catch (Exception $e) {
            $this->addFlash('error', 'Grup eklenirken bir hata oluştu:'.' '.$e);
            return $this->redirectToRoute('sticker_main_page');
        }
        $this->addFlash('success', 'Grup Başarıyla Eklendi.');
        return $this->redirectToRoute('sticker_main_page');
    }

    /**
     * @Route("/sticker/get-stickers/{groupId}", name="get_stickers", methods={"GET"})
     * @throws \Doctrine\DBAL\Exception
     */
    public function getStickers(int $groupId): JsonResponse
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
        $sql = "SELECT 
                osp.productIdentifier,
                MIN(osp.name) as name,
                MIN(osp.productCategory) as category,
                MIN(osp.imageUrl) as image
            FROM object_relations_gproduct org
            JOIN object_product osp ON osp.oo_id = org.dest_id
            WHERE org.src_id = :groupId
            " . $searchCondition . " 
            GROUP BY osp.productIdentifier
            ORDER BY osp.productIdentifier
        ";
        if ($offset !== null) {
            $sql .= " LIMIT $limit OFFSET $offset";
        }
        else {
            $sql .= " LIMIT 10";
        }
        $parameters = ['groupId' => $groupId];
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
        $countSql = "SELECT 
                COUNT(DISTINCT osp.productIdentifier) AS totalCount
            FROM object_relations_gproduct org
            JOIN object_product osp ON osp.oo_id = org.dest_id
            LEFT JOIN object_relations_product opr 
                ON opr.src_id = osp.oo_id 
                AND opr.type = 'asset' 
                AND opr.fieldname = 'sticker4x6eu'
            WHERE org.src_id = :groupId
            " . $searchCondition . "ORDER BY osp.productIdentifier";
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
                org.dest_id,
                osp.name,
                osp.productCode,
                osp.productCategory,
                osp.imageUrl,
                osp.variationSize,
                osp.variationColor,
                osp.productIdentifier,
                sticker_eu.dest_id AS sticker_id_eu,
                sticker_normal.dest_id AS sticker_id
            FROM object_relations_gproduct org
            JOIN object_product osp ON osp.oo_id = org.dest_id
            LEFT JOIN object_relations_product sticker_eu
                ON sticker_eu.src_id = osp.oo_id
                AND sticker_eu.type = 'asset'
                AND sticker_eu.fieldname = 'sticker4x6eu'
            LEFT JOIN object_relations_product sticker_normal
                ON sticker_normal.src_id = osp.oo_id
                AND sticker_normal.type = 'asset'
                AND sticker_normal.fieldname = 'sticker4x6iwasku'
            WHERE osp.productIdentifier = :productIdentifier AND org.src_id = :groupId;
        ";

        $products = Db::get()->fetchAllAssociative($sql, ['productIdentifier' => $productIdentifier, 'groupId' => $groupId]);
        foreach ($products as &$product) {
            if (isset($product['sticker_id_eu'])) {
                $stickerEu = Asset::getById($product['sticker_id_eu']);
            } else {
                if (isset($product['dest_id'])) {
                    $productObject = Product::getById($product['dest_id']);
                    if ($productObject) {
                        $stickerEu = $productObject->checkSticker4x6eu();
                    } else {
                        $stickerEu = null;
                    }
                } else {
                    $stickerEu = null;
                }
            }
            if (isset($product['sticker_id'])) {
                $sticker = Asset::getById($product['sticker_id']);
            } else {
                if (isset($product['dest_id'])) {
                    $productObject = Product::getById($product['dest_id']);
                    if ($productObject) {
                        $sticker = $productObject->checkSticker4x6iwasku();
                    } else {
                        $sticker = null;
                    }
                } else {
                    $sticker = null;
                }
            }
            $product['sticker_link_eu'] = $stickerEu ? $stickerEu->getFullPath() : '';
            $product['sticker_link'] = $sticker ? $sticker->getFullPath() : '';
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
     * @Route("/sticker/add-sticker", name="sticker_new", methods={"POST"})
     * @param Request $request
     * @return Response
     * @throws \Doctrine\DBAL\Exception
     */
    public function addSticker(Request $request): Response
    {
        $productId = $request->request->get('form_data');
        $groupId = $request->request->get('group_id');
        $group = GroupProduct::getById($groupId);
        $iwasku = Registry::getKey($productId,'asin-to-iwasku');
        if (empty($iwasku)) {
            $iwasku = $productId;
        }
        if (empty($iwasku)) {
            $this->addFlash('error', 'HATALI VEYA BOŞ ÜRÜN KODU.');
            return $this->redirectToRoute('sticker_main_page');
        }
        $product = Product::findByField('iwasku',$iwasku);
        if ($product instanceof Product) {
            if (!$product->getInheritedField('sticker4x6eu')) {
                $product->checkSticker4x6eu();
            }
            if (!$product->getInheritedField('sticker4x6iwasku')) {
                $product->checkSticker4x6iwasku();
            }
            $existingProducts = $group->getProducts();
            if (!in_array($product, $existingProducts, true)) {
                $group->setProducts(array_merge($existingProducts, [$product]));
            }
            else {
                $this->addFlash('error', 'Bu ürün zaten bu grupta bulunmaktadır.');
                return $this->redirectToRoute('sticker_main_page');
            }
            try {
                $group->save();
            } catch (Exception $e) {
                $this->addFlash('error: ', $e . ' Etiket eklenirken bir hata oluştu.');
                return $this->redirectToRoute('sticker_main_page');
            }
            $this->addFlash('success', 'Etiket başarıyla eklendi.');
        } else {
            $this->addFlash('error', 'Bu Ürün Pimcore\'da Bulunamadı.');
        }
        return $this->redirectToRoute('sticker_main_page');
    }

}