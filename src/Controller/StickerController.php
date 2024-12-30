<?php

namespace App\Controller;

use App\Form\OzonTaskFormType;
use App\Form\OzonTaskProductFormType;
use App\Model\DataObject\VariantProduct;
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
    /**
     * @Route("/sticker/", name="sticker_main_page")
     * @return Response
     */
    public function stickerMainPage(Request $request): Response
    {

        return $this->render('sticker/sticker.html.twig');
    }

    /**
     * @Route("/sticker/add-sticker-group", name="sticker_new_group", methods={"GET", "POST"})
     * @return Response
     */
    public function addStickerGroup(Request $request): Response
    {
       /* if ($request->isMethod('POST')) {
            // Formdan gelen veriyi işleme, yeni grup ekleme işlemleri yapılabilir
            $formData = $request->request->get('form_data'); // Form verisini alabilirsiniz

            // Veritabanına kaydetme işlemleri veya başka işlemler yapabilirsiniz

            // Sonrasında kullanıcıyı bir başarı sayfasına yönlendirebilirsiniz
            return $this->redirectToRoute('sticker_main_page');
        }*/
        return $this->render('sticker/add_sticker_group.html.twig');

    }

}
