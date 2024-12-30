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

        return $this->render('sticker/base.html.twig');
    }
}
