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
     */
    public function addStickerGroup(Request $request): Response
    {
        if ($request->isMethod('POST')) {
            $formData = $request->request->get('form_data');
            try {
                Utility::executeSqlFile($this->sqlPath . 'insert_into_group.sql', ['group_name' => $formData]);
                $this->addFlash('success', 'Grup Başarıyla Eklendi.');
                return $this->redirectToRoute('sticker_new_group');
            }  catch (\Doctrine\DBAL\Exception\UniqueConstraintViolationException $e) {
                $this->addFlash('error', 'Bu grup daha öncede eklenmiş.');
            } catch (\Exception $e) {
                $this->addFlash('error', 'Grup eklenirken bir hata oluştu.');
            }
        }
        return $this->render('sticker/add_sticker_group.html.twig');
    }

    /**
     * @Route("/sticker/get-stickers/{groupId}", name="get_stickers", methods={"GET"})
     */
    public function getStickers(int $groupId): JsonResponse
    {
        $db = Db::get();
        $stickers = [];
        $query = "
            SELECT 
                osp.iwasku,
                osp.name AS product_name,
                osp.productCode,
                osp.productCategory,
                osp.imageUrl,
                osp.variationSize,
                osp.variationColor,
                opr.dest_id AS sticker_id
            FROM object_relations_gproduct org
            JOIN object_product osp ON osp.oo_id = org.dest_id
            LEFT JOIN object_relations_product opr ON opr.src_id = osp.oo_id AND opr.type = 'asset' AND opr.fieldname = 'sticker4x6eu'
            WHERE org.src_id = ?";
        $products = $db->fetchAllAssociative($query, [$groupId]);
        foreach ($products as $product) {
            if ($product['sticker_id']) {
                $sticker = Asset::getById($product['sticker_id']);
                $stickerPath = $sticker ? $sticker->getFullPath() : '';
            } else {
                $productObject = Product::getById($product['dest_id']);
                if (!$productObject) {
                    continue;
                }
                $sticker = $productObject->checkSticker4x6eu();
                $stickerPath = $sticker ? $sticker->getFullPath() : '';
            }
            $stickers[] = [
                'iwasku' => $product['iwasku'],
                'product_name' => $product['product_name'],
                'sticker_link' => $stickerPath ?? '',
                'product_code' => $product['productCode'] ?? '',
                'category' => $product['productCategory'] ?? '',
                'image_link' => $product['imageUrl'] ?? '',
                'attributes' => $product['variationSize'] . ' ' . $product['variationColor']
            ];
        }





        /*$stickers = Utility::fetchFromSqlFile($this->sqlPath . 'select_stickers_by_group_id.sql', [
            'group_id' => $groupId
        ]);
        if (empty($stickers)) {
            return new JsonResponse(['success' => false, 'message' => 'No stickers found.']);
        }
        foreach ($stickers as $key => &$sticker) {
            $product = Product::findByField('iwasku', $sticker['iwasku']);
            if ($product instanceof Product) {
                $sticker['product_code'] = $product->getInheritedField('productCode') ?? '';
                $sticker['category'] = $product->getInheritedField('productCategory') ?? '';
                $sticker['product_name'] = $product->getInheritedField('Name') ?? '';
                $sticker['image_link'] = (string)$product->getInheritedField('imageUrl') ?? '';
                $sticker['variation_size'] = $product->getVariationSize() ?? '';
                $sticker['variation_color'] = $product->getVariationColor() ?? '';
                $sticker['attributes'] = $sticker['variation_size'] . ' ' . $sticker['variation_color'] ;
                $stickerEu = $product->getInheritedField('sticker4x6eu');
                $sticker['sticker_link'] = $stickerEu->getFullPath() ?? '';
            }
        }
        unset($sticker);*/
        return new JsonResponse(['success' => true, 'stickers' => $stickers]);
    }

    /**
     * @Route("/sticker/add-sticker", name="sticker_new", methods={"GET", "POST"})
     * @return Response
     */
    public function addSticker(Request $request): Response
    {
        if ($request->isMethod('POST')) {
            $asin = $request->request->get('form_data');
            $groupId = $request->request->get('group_id');
            $iwasku = Registry::getKey($asin,'asin-to-iwasku');
            if (isset($iwasku)) {
                $product = Product::findByField('iwasku',$iwasku);
                if ($product instanceof Product) {
                    if (!$product->getInheritedField('sticker4x6eu')) {
                        $product->checkSticker4x6eu();
                    }
                    try {
                        Utility::executeSqlFile($this->sqlPath . 'insert_into_sticker.sql', [
                            'group_id' => $groupId,
                            'iwasku' => $iwasku
                        ]);
                        $this->addFlash('success', 'Etiket Başarıyla Eklendi.');
                    } catch (\Doctrine\DBAL\Exception\UniqueConstraintViolationException $e) {
                        $this->addFlash('error', 'Bu etiket daha öncede eklenmiş.');
                    } catch (\Exception $e) {
                        $this->addFlash('error', 'Etiket eklenirken bir hata oluştu.');
                    }
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
        $groups = Utility::fetchFromSqlFile($this->sqlPath . 'select_all_groups.sql');
        return $this->render('sticker/add_sticker.html.twig', [
            'groups' => $groups
        ]);
    }

    /**
     * @Route("/sticker/test/", name="test")
     * @return Response
     */
    public function test(Request $request): Response
    {
        $gproduct = new GroupProduct\Listing();
        $result = $gproduct->load();
        $names = [];

        // all group products
        foreach ($result as $item) {
            $names[] = $item->getKey();
        }

        return $this->render('sticker/test.html.twig', [
            'result' => $names
        ]);
    }

}
