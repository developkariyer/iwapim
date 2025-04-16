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


class CiceksepetiController extends FrontendController
{
    /**
     * @Route("/ciceksepeti", name="ciceksepeti_main_page")
     * @return Response
     */
    public function ciceksepetiMainPage(): Response
    {

        return $this->render('ciceksepeti/ciceksepeti.html.twig');
    }
}
