<?php
namespace App\Controller;

use App\Connector\Marketplace\CiceksepetiConnector;
use App\Model\DataObject\Marketplace;
use App\Model\DataObject\VariantProduct;
use App\Utils\Utility;
use Doctrine\DBAL\Exception;
use Pimcore\Db;
use Random\RandomException;
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
     * @Route("/ciceksepeti/{category}", name="ciceksepeti_main_page", defaults={"category"=null})
     * @return Response
     */
    public function ciceksepetiMainPage(Request $request, $category = null): Response
    {
        /*return $this->render('ciceksepeti/ciceksepeti.html.twig', [
            'grouped' => $this->getCiceksepetiListings()
        ]);*/
        if ($category) {
            $grouped = $this->getCiceksepetiListingByCategory($category);
        }
        return $this->render('ciceksepeti/ciceksepeti.html.twig',[
            'categories' => $this->getCiceksepetiListingCategories(),
            'grouped' => $grouped
        ]);
    }

    /**
     * @Route("/variant/update", name="variant_update", methods={"POST"})
     */
    public function updateVariant(Request $request): Response
    {
        $data = $request->request->all();
        return new Response(
            json_encode($data),
            Response::HTTP_OK,
            ['Content-Type' => 'application/json']
        );

    }

    /*public function getCiceksepetiListings(): array
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
    }*/

    public function getCiceksepetiListingCategories()
    {
        $categoryIdList = $this->getCiceksepetiListingCategoriesIdList();
        $categoryIdString = implode(',', array_map('intval', $categoryIdList));
        $sqlCategory = "SELECT id, category_name FROM iwa_ciceksepeti_categories WHERE id IN ($categoryIdString)";
        $categories = Utility::fetchFromSql($sqlCategory);
        $result = [];
        foreach ($categories as $cat) {
            $result[] = [
                'id' => $cat['id'],
                'name' => $cat['category_name']
            ];
        }
        return $result;
    }

    /**
     * @Route("/ciceksepeti/category/update", name="update_category", methods={"POST"})
     * @return Response
     */
    public function getCiceksepetiListingCategoriesUpdate(): Response
    {
        // update category
        $ciceksepetiConnector = new CiceksepetiConnector(Marketplace::getById(265384));
        $ciceksepetiConnector->downloadCategories();

        $categoryIdList = $this->getCiceksepetiListingCategoriesIdList();
        // update category attributes
        foreach ($categoryIdList as $categoryId) {
            $ciceksepetiConnector->getCategoryAttributesAndSaveDatabase($categoryId);
        }
        $this->addFlash('success', 'ÇiçekSepeti kategorileri başarıyla güncellendi.');
        return $this->redirectToRoute('ciceksepeti_main_page');
    }

    public function getCiceksepetiListingCategoriesIdList(): array
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
        return array_unique($categoryIdList);
    }

    public function getCiceksepetiListingByCategory($categoryId)
    {
        $sql = "SELECT oo_id FROM `object_query_varyantproduct` WHERE marketplaceType = 'Ciceksepeti'";
        $ciceksepetiVariantIds = Utility::fetchFromSql($sql);
        $ciceksepetiVariant = [];
        foreach ($ciceksepetiVariantIds as $ciceksepetiVariantId) {
            $variantProduct = VariantProduct::getById($ciceksepetiVariantId['oo_id']);
            if (!$variantProduct instanceof VariantProduct) {
                continue;
            }
            $apiData = json_decode($variantProduct->jsonRead('apiResponseJson'), true);
            if ($apiData['categoryId'] != $categoryId) {
                continue;
            }
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
        $grouped = [];
        foreach ($ciceksepetiVariant as $listing) {
            $mainCode = $listing['mainProductCode'] ?? 'unknown';
            $grouped[$mainCode][] = $listing;
        }
        return $grouped;
    }

}
