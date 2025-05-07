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
use App\Message\CiceksepetiCategoryUpdateMessage;
use Symfony\Component\Messenger\MessageBusInterface;
use Pimcore\Controller\FrontendController;
use Pimcore\Model\DataObject\Product;
use Pimcore\Model\DataObject\Data\Link;
use Pimcore\Model\Asset;
use Symfony\Component\Security\Http\Attribute\IsGranted;


#[IsGranted('ROLE_PIMCORE_ADMIN')]
class AutoListingController extends FrontendController
{
    /**
     * @Route("/autolisting", name="autolisting_main_page")
     * @return Response
     */
    public function autolistingMainPage(): Response
    {
        return $this->render('autolisting/autolisting.html.twig');
    }

    /**
     * @Route("/api/products/search/{identifier}", name="api_product_search", methods={"GET"})
     */
    public function searchProduct(string $identifier): Response
    {
        if (empty($identifier)) {
            return $this->json(['success' => false, 'message' => 'Ürün kodu belirtilmedi'], 400);
        }

        $productSql = '
        SELECT oo_id, name, productCategory from object_query_product
        WHERE productIdentifier = :productIdentifier AND productLevel = 0
        LIMIT 1';
        $variantSql = '
        SELECT oo_id, iwasku, variationSize, variationColor FROM object_query_product
        WHERE productIdentifier = :productIdentifier AND productLevel = 1 AND listingItems IS NOT NULL';

        $product = Utility::fetchFromSql($productSql, ['productIdentifier' => $identifier]);
        if (!is_array($product) || empty($product)) {
            return $this->json(['success' => false, 'message' => 'Ürün bulunamadı']);
        }

        $variants = Utility::fetchFromSql($variantSql, ['productIdentifier' => $identifier]);
        if (!is_array($variants) || empty($variants)) {
            return $this->json(['success' => false, 'message' => 'Variant bulunamadı']);
        }

        $productData = [
            'id' => $product[0]['oo_id'],
            'name' => $product[0]['name'],
            'productCategory' => $product[0]['productCategory']
        ];
        $variantData = [];
        foreach ($variants as $variant) {
            $variantData[] = [
                'id' => $variant['oo_id'],
                'iwasku' => $variant['iwasku'],
                'variationSize' => $variant['variationSize'],
                'variationColor' => $variant['variationColor']
            ];
        }
        $productData['variants'] = $variantData;

        return $this->json([
            'success' => true,
            'product' => $productData
        ]);
    }

    /**
     * @Route("/create-marketplace-listing", name="create_marketplace_listing", methods={"POST"})
     */
    public function createMarketplaceListing(Request $request): Response
    {
        try {
            $content = $request->getContent();
            $data = json_decode($content, true);

            if (!$data) {
                return $this->json([
                    'success' => false,
                    'message' => 'Geçersiz JSON verisi'
                ], Response::HTTP_BAD_REQUEST);
            }

            if (empty($data['productId']) || empty($data['selectedMarketplaces']) || empty($data['variants'])) {
                return $this->json([
                    'success' => false,
                    'message' => 'Eksik veri alanları: productId, selectedMarketplaces ve en az bir varyant gerekli'
                ], Response::HTTP_BAD_REQUEST);
            }

            foreach ($data['variants'] as $variant) {
                if (empty($variant['id']) || empty($variant['stock']) || empty($variant['price'])) {
                    return $this->json([
                        'success' => false,
                        'message' => 'Her varyant için id, stok ve fiyat bilgileri gereklidir'
                    ], Response::HTTP_BAD_REQUEST);
                }
            }

            foreach ($data['selectedMarketplaces'] as $marketplace) {
                if (empty($data['marketplaceFields'][$marketplace])) {
                    return $this->json([
                        'success' => false,
                        'message' => $marketplace . ' için gerekli alanlar eksik'
                    ], Response::HTTP_BAD_REQUEST);
                }

                if (empty($data['marketplaceFields'][$marketplace]['category'])) {
                    return $this->json([
                        'success' => false,
                        'message' => $marketplace . ' için kategori seçilmedi'
                    ], Response::HTTP_BAD_REQUEST);
                }
            }

            $responseData = [
                'success' => true,
                'message' => 'Listing işlemi başarıyla başlatıldı',
                'timestamp' => new \DateTime(),
                'requestData' => $data,
                'summary' => [
                    'productId' => $data['productId'],
                    'variantCount' => count($data['variants']),
                    'marketplaceCount' => count($data['selectedMarketplaces']),
                    'totalStock' => array_sum(array_column($data['variants'], 'stock')),
                    'priceRange' => [
                        'min' => min(array_column($data['variants'], 'price')),
                        'max' => max(array_column($data['variants'], 'price'))
                    ]
                ]
            ];

            return $this->json($responseData);

        } catch (\Exception $e) {
            return $this->json([
                'success' => false,
                'message' => 'İşlem sırasında bir hata oluştu: ' . $e->getMessage()
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }



}