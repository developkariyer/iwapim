<?php
namespace App\Controller;

use App\Connector\Marketplace\CiceksepetiConnector;
use App\Model\DataObject\Marketplace;
use App\Model\DataObject\VariantProduct;
use App\Utils\Utility;
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
        return $this->render('ciceksepeti/ciceksepeti.html.twig', [
            'grouped' => $this->getCiceksepetiListings()
        ]);
    }

    public function getCiceksepetiListings(): array
    {
        $sql = "SELECT oo_id FROM `object_query_varyantproduct` WHERE marketplaceType = 'Ciceksepeti'";
        $ciceksepetiVariantIds = Utility::fetchFromSql($sql);
        $ciceksepetiVariant = [];
        $categoryIdList = [];
        foreach ($ciceksepetiVariantIds as $ciceksepetiVariantId) {
            $variantProduct = VariantProduct::getById($ciceksepetiVariantId['oo_id']);
            if (!$variantProduct instanceof VariantProduct) {
                continue;
            }
            $apiData = json_decode($variantProduct->jsonRead('apiResponseJson'), true);
            $categoryIdList[] = $apiData['categoryId'];
            $ciceksepetiVariant[] = [
                'link' => $apiData['link'],
                'images' => $apiData['images'],
                'barcode' => $apiData['barcode'],
                'variantIsActive' => $apiData['isActive'],
                'listPrice' => $apiData['listPrice'],
                'stockCode' => $apiData['stockCode'],
                'attributes' => $apiData['attributes'],
                'salesPrice' => $apiData['salesPrice'],
                'description' => $apiData['description'],
                'productCode' => $apiData['productCode'],
                'productName' => $apiData['productName'],
                'deliveryType' => $apiData['deliveryType'],
                'stockQuantity' => $apiData['stockQuantity'],
                'commissionRate' => $apiData['commissionRate'],
                'mainProductCode' => $apiData['mainProductCode'],
                'numberOfFavorites' => $apiData['numberOfFavorites'],
                'productIsActive' => $apiData['productStatusType'],
                'deliveryMessageType' => $apiData['deliveryMessageType'],
                'categoryId' => $apiData['categoryId']
            ];
        }
        $categoryIdList = array_unique($categoryIdList);
        $categoryIdString = implode(',', array_map('intval', $categoryIdList));

        $sqlCategory = "SELECT id, category_name FROM iwa_ciceksepeti_categories WHERE id IN ($categoryIdString)";
        $categories = Utility::fetchFromSql($sqlCategory);

        $categoryMap = [];
        foreach ($categories as $cat) {
            $categoryMap[$cat['id']] = $cat['category_name'];
        }

        $grouped = [];
        foreach ($ciceksepetiVariant as $listing) {
            $categoryId = $listing['categoryId'];
            $mainCode = $listing['mainProductCode'] ?? 'unknown';
            $categoryName = $categoryMap[$categoryId] ?? 'Bilinmeyen Kategori';

            $grouped[$categoryName][$mainCode][] = $listing;
        }
        return $grouped;
    }

    // Exist Categories
    public function getCiceksepetiListingCategories()
    {
        $sql = "SELECT oo_id FROM `object_query_varyantproduct` WHERE marketplaceType = 'Ciceksepeti'";
        $ciceksepetiVariantIds = Utility::fetchFromSql($sql);
        if (!is_array($ciceksepetiVariantIds) || empty($ciceksepetiVariantIds)) {
            return [];
        }
        $categoryIdList = [];
        foreach ($ciceksepetiVariantIds as $ciceksepetiVariantId) {
            $variantProduct = VariantProduct::getById($ciceksepetiVariantId['oo_id']);
            if (!$variantProduct instanceof VariantProduct) {
                continue;
            }
            $apiData = json_decode($variantProduct->jsonRead('apiResponseJson'), true);
            $categoryIdList[] = $apiData['categoryId'];
        }
        $categoryIdList = array_unique($categoryIdList);
        print_r($categoryIdList);
    }

    public function checkAndUpdateCategories()
    {
        $ciceksepetiConnector = new CiceksepetiConnector(Marketplace::getById(265384));
        $ciceksepetiConnector->downloadCategories();

    }

    public function getCiceksepetiListingByCategory($categoryId)
    {


    }

    public function getCiceksepetiAllCategories()
    {
        $sql = "SELECT id, category_name FROM iwa_ciceksepeti_categories";

    }

}
