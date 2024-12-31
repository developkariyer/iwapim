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
        $groups = Utility::fetchFromSqlFile($this->sqlPath . 'select_all_groups.sql');

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
        $stickers = Utility::fetchFromSqlFile($this->sqlPath . 'select_stickers_by_group_id.sql', [
            'group_id' => $groupId
        ]);
        if (empty($stickers)) {
            return new JsonResponse(['success' => false, 'message' => 'No stickers found.']);
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
                    $productCode =  $product->getInheritedField('productCode');
                    $category = $product->getInheritedField('productCategory');
                    $productName  = $product->getInheritedField('Name');
                    $imageUrl = $product->getInheritedField('imageUrl');
                    $variationSize = $product->getVariationSize() ?? '';
                    $variationColor = $product->getVariationColor() ?? '';
                    $productDimension1 = $product->getInheritedField('productDimension1') ?? '';
                    $productDimension2 = $product->getInheritedField('productDimension2') ?? '';
                    $productDimension3 = $product->getInheritedField('productDimension3') ?? '';
                    $packageWeight = $product->getInheritedField('packageWeight') ?? '';
                    $attributes = $variationSize . ' ' . $variationColor . ' ' . $productDimension1 . ' ' . $productDimension2 . ' ' . $productDimension3 . ' ' . $packageWeight;
                    if ($product->getInheritedField('sticker4x6eu')) {
                        $sticker = $product->getInheritedField('sticker4x6eu');
                    }
                    else {
                        $stickerEu = $product->checkSticker4x6eu();
                        $stickerPath = $stickerEu->getFullPath();
                        $sticker = $stickerPath;
                    }

                    try {
                        Utility::executeSqlFile($this->sqlPath . 'insert_into_sticker.sql', [
                            'group_id' => $groupId,
                            'iwasku' => $iwasku,
                            'product_code' => $productCode,
                            'category' => $category,
                            'product_name' => $productName,
                            'attributes' => $attributes,
                            'image_link' => $imageUrl,
                            'sticker_link' => $sticker,
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

}
