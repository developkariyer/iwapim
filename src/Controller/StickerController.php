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
        $products = $db->fetchAllAssociative("SELECT dest_id FROM object_relations_gproduct WHERE src_id = ? AND fieldname = 'products'", [$groupId]);
        foreach ($products as $product) {
            $details = $db->fetchAssociative("SELECT * FROM object_store_product WHERE oo_id = ? LIMIT 1", [$product['dest_id']]);
            $stickerId = $db->fetchOne("SELECT dest_id FROM object_relations_product WHERE src_id = ? AND type='asset' AND fieldname='sticker4x6eu'", [$product['dest_id']]);
            if (!$stickerId) {
                $productObject = Product::getById($product['dest_id']);
                if (!$productObject) {
                    continue;
                }
                $sticker = $productObject->checkSticker4x6eu();
            } else {
                $sticker = Asset::getById($stickerId);
            }
            if ($sticker) {
                $stickerPath = $sticker->getFullPath();
            }
            $stickers[] = [
                'iwasku' => $details['iwasku'],
                'name' => $details['name'],
                'sticker' => $stickerPath ?? '',
                'product_code' => $details['product_code'] ?? '',
                'category' => $details['category'] ?? '',
                'image_link' => $details['imageUrl'] ?? '',
                'variation_size' => $details['variation_size'] ?? '',
                'variation_color' => $details['variation_color'] ?? '',
                'attributes' => $details['variation_size'] . ' ' . $details['variation_color']
            ];

        }
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
