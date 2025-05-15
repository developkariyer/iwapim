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

    /**
     * @Route("/api/update-batch/{batchId}", name="api_update_batch", methods={"POST"})
     * @IsGranted("IS_AUTHENTICATED_ANONYMOUSLY")
     */
    public function updateBatch(Request $request, string $batchId): Response
    {
        try {
            $data = json_decode($request->getContent(), true);

            if (!$data) {
                return $this->json([
                    'success' => false,
                    'message' => 'Geçersiz veri'
                ], Response::HTTP_BAD_REQUEST);
            }

            $ciceksepetiConnector = new CiceksepetiConnector(265384);
            $response = $ciceksepetiConnector->getBatchRequestResult($batchId);
            $responseJson = json_decode($response, true);
            $queryOnly = isset($data['queryOnly']) && $data['queryOnly'];
            return $this->json([
                'success' => true,
                'message' => $queryOnly ? 'Batch bilgileri başarıyla alındı' : 'Batch başarıyla güncellendi',
                'batchId' => $batchId,
                'response' => $responseJson
            ]);
        } catch (\Exception $e) {
            return $this->json([
                'success' => false,
                'message' => 'İşlem sırasında hata oluştu: ' . $e->getMessage()
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * @Route("/batch-listings", name="batch_listings")
     */
    public function batchListingsPage(): Response
    {
        // Yeni bağımsız şablona doğrudan render yapıyoruz (extends kullanmıyoruz)
        $directory = PIMCORE_PROJECT_ROOT . "/tmp/marketplaces/Ciceksepeti";
        $batchData = [];
        $errorMessage = null;

        try {
            if (!is_dir($directory)) {
                $errorMessage = "Dizin bulunamadı: $directory";
            } else {
                $files = array_filter(scandir($directory), function ($file) use ($directory) {
                    return is_file($directory . DIRECTORY_SEPARATOR . $file) &&
                        str_starts_with($file, 'CREATE_LISTING_');
                });

                if (empty($files)) {
                    $errorMessage = "CREATE_LISTING_ ile başlayan dosya bulunamadı";
                } else {
                    foreach ($files as $fileName) {
                        $filePath = $directory . DIRECTORY_SEPARATOR . $fileName;
                        $content = file_get_contents($filePath);
                        $json = json_decode($content, true);

                        if (json_last_error() !== JSON_ERROR_NONE) {
                            continue;
                        }

                        $extractedData = $this->extractBatchIdData($json);
                        if ($extractedData) {
                            $batchData[] = $extractedData;
                        }
                    }
                }
            }
        } catch (\Exception $e) {
            $errorMessage = "Hata oluştu: " . $e->getMessage();
        }

        $response = new Response();
        return $this->render('ciceksepeti/batch_listings.html.twig', [
            'batchData' => $batchData,
            'errorMessage' => $errorMessage
        ], $response);
    }

    private function extractBatchIdData($json)
    {
        if (empty($json['response'])) {
            return;
        }
        $batchId = $json['response']['batchRequestResult']['batchId'];
        $items = $json['response']['batchRequestResult']['items'];
        $result = [];
        foreach ($items as $item) {
            $createdDate = $item['lastModificationDate'];
            $mainProduct = $item['data']['mainProductCode'] ?? null;
            $status = $item['status'] ?? null;
            $iwasku = $item['data']['stockCode'] ?? null;
            $failureReasons = [];
            if (!empty($item['failureReasons'])) {
                foreach ($item['failureReasons'] as $failureReason) {
                    $failureReasons[] = [
                        'code' => $failureReason['code'] ?? '',
                        'message' => $failureReason['message'] ?? ''
                    ];
                }
            }
            $result[] = [
                'batchId' => $batchId,
                'createdDate' => $createdDate,
                'mainProduct' => $mainProduct,
                'iwasku' => $iwasku,
                'status' => $status,
                'failureReasons' => $failureReasons
            ];
        }
        return $result;
    }


    /**
     * @Route("/create-ciceksepeti-listing", name="create_ciceksepeti_listing", methods={"POST"})
     */
    public function createListing(Request $request): Response
    {
        try {
            // JSON verisini al
            $content = $request->getContent();
            $data = json_decode($content, true);

            if (!$data) {
                return $this->json([
                    'success' => false,
                    'message' => 'Geçersiz JSON verisi'
                ], Response::HTTP_BAD_REQUEST);
            }

            // Gerekli alanların kontrolü
            if (empty($data['productId']) || empty($data['categoryId']) || empty($data['variants'])) {
                return $this->json([
                    'success' => false,
                    'message' => 'Eksik veri alanları: productId, categoryId ve en az bir varyant gerekli'
                ], Response::HTTP_BAD_REQUEST);
            }

            // Varyant verilerinin kontrolü
            foreach ($data['variants'] as $variant) {
                if (empty($variant['id']) || empty($variant['stock']) || empty($variant['price'])) {
                    return $this->json([
                        'success' => false,
                        'message' => 'Her varyant için id, stok ve fiyat bilgileri gereklidir'
                    ], Response::HTTP_BAD_REQUEST);
                }
            }

            $responseData = [
                'success' => true,
                'message' => 'Listing bilgileri başarıyla alındı',
                'timestamp' => new \DateTime(),
                'requestData' => $data,
                'summary' => [
                    'productName' => $data['productName'],
                    'productId' => $data['productId'],
                    'categoryId' => $data['categoryId'],
                    'variantCount' => count($data['variants']),
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
    public function getCiceksepetiListingCategoriesUpdate(MessageBusInterface $bus): Response
    {
        $marketplaceId = 265384;
        $message = new CiceksepetiCategoryUpdateMessage($marketplaceId);
        $bus->dispatch($message);
        $this->addFlash('success', 'ÇiçekSepeti kategorileri güncelleme işlemi kuyruğa alındı.');
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
