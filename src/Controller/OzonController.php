<?php

namespace App\Controller;

use App\Connector\Marketplace\Ozon\Utils;
use App\Form\OzonTaskFormType;
use App\Utils\Registry;
use App\Utils\Utility;
use Exception;
use Pimcore\Controller\FrontendController;
use Pimcore\Db;
use Pimcore\Model\DataObject\Product;
use Pimcore\Model\DataObject\VariantProduct;
use Pimcore\Model\Element\DuplicateFullPathException;
use Random\RandomException;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Pimcore\Model\DataObject\Marketplace;
use Pimcore\Model\DataObject\ListingTemplate;
use Symfony\Component\Security\Http\Attribute\IsGranted;

/**
 * Class OzonController
 * This controller class is used to manage Ozon Listing tasks. It displays
 * @package App\Controller
 */
#[IsGranted('ROLE_PIMCORE_ADMIN')]
class OzonController extends FrontendController
{
    private string $sqlTaskProducts = "SELECT
    ob.id,
    ob.variationSize,
    ob.variationColor,
    ob.iwasku,
    ob.parentId,
    ob.`key` AS productKey,
    ob_parent.id AS parentId,
    ob_parent.`key` AS parentKey,
    ob_parent.`name` AS parentName,
    ob_parent.nameEnglish AS parentNameEnglish,
    ob_parent.productCategory AS parentCategory,
    rel.listing_id AS listingId,
    rel.group_type AS groupType,
    rel.product_type AS productType,
    rel.asin AS asin
FROM
    iwa_ozon_product_relations AS rel
JOIN
    object_product AS ob ON rel.product_id = ob.id
LEFT JOIN
    object_product AS ob_parent ON ob.parentId = ob_parent.id
WHERE
    rel.task_id = ?
ORDER BY
    productKey;";

    private string $sqlAddProduct = "INSERT INTO
    iwa_ozon_product_relations (task_id, product_id, listing_id, group_type, product_type, asin)
VALUES
    (?, ?, ?, ?, ?, ?);";

    private string $sqlModifyProduct = "UPDATE
    iwa_ozon_product_relations
SET
    listing_id = ?,
    group_type = ?,
    product_type = ?
WHERE
    task_id = ?
    AND product_id = ?;";

    private string $sqlDeleteProduct = "DELETE FROM
    iwa_ozon_product_relations
WHERE
    task_id = ?
    AND product_id = ?;";

    /**
     * @Route("/ozon/{taskId}/{parentProductId}", name="ozon_menu", defaults={"taskId"=null, "parentProductId"=null})
     * @return Response
     *
     * Loads all marketplaces and tasks for Ozon and renders the page.
     * Also displays the form to create a new Ozon Listing task.
     * @throws DuplicateFullPathException
     * @throws Exception
     */
    public function ozonMainPage(Request $request): Response
    {
        $mrkListing = new Marketplace\Listing();
        $mrkListing->setCondition("marketplaceType = ?", ['Ozon']);
        $marketplaces = $mrkListing->load();
        $newTaskForm = $this->createForm(OzonTaskFormType::class, null, ['marketplaces' => $marketplaces]);
        $newTaskForm->handleRequest($request);
        if ($newTaskForm->isSubmitted() && $newTaskForm->isValid()) {
            $data = $newTaskForm->getData();
            $task = new ListingTemplate();
            $task->setKey($data['taskName']);
            $task->setParent(Utility::checkSetPath('Listing'));
            $task->setMarketplace($data['marketplace']);
            $task->save();
            $this->addFlash('success', 'Yeni görev oluşturuldu.');
            return $this->redirectToRoute('ozon_menu', ['taskId' => $task->getId()]);
        }
        $taskListing = new ListingTemplate\Listing();
        $taskListing->setUnpublished(true);
        $taskListing->setOrderKey('key');
        $tasksObjects = $taskListing->load();
        $tasks = [];
        foreach ($tasksObjects as $task) {
            if ($task->getMarketplace()->getMarketplaceType() !== 'Ozon') {
                continue;
            }
            $tasks[] = $task;
        }
        return $this->render('ozon/ozon.html.twig', [
            'newTaskForm' => $newTaskForm->createView(),
            'tasks' => $tasks,
            'marketplaces' => $marketplaces,
            'taskId' => $request->get('taskId'),
            'parentProductId' => $request->get('parentProductId'),
        ]);
    }

    /**
     * @Route("/ozontask/{taskId}/{parentProductId}", name="ozon_task", defaults={"parentProductId"=0})
     * @param Request $request
     * @return Response
     *
     * This controller method displays the detail page for an Ozon Listing task.
     * @throws \Doctrine\DBAL\Exception
     */
    public function getTaskProducts(Request $request): Response
    {
        $db = Db::get();
        $taskId = $request->get('taskId');
        $task = ListingTemplate::getById($taskId);
        if (!$task) {
            return $this->redirectToRoute('ozon_menu');
        }
        $parentProducts = [];
        $taskProducts = $db->fetchAllAssociative($this->sqlTaskProducts, [$taskId]);
        foreach ($taskProducts as $taskProduct) {
            $id = $taskProduct['parentId'];
            $groupType = $taskProduct['groupType'] ?? 0;
            $productType = $taskProduct['productType'] ?? 0;
            $categoryFullName = Utils::isOzonProductType($groupType, $productType) ?? '';
            if (!isset($parentProducts[$id])) {
                $parentProducts[$id] = [
                    'parentProduct' => [
                        'id' => $taskProduct['parentId'],
                        'key' => $taskProduct['parentKey'],
                        'categoryFullName' => $categoryFullName,
                        'groupType' => $groupType,
                        'productType' => $productType,
                    ],
                    'products' => [
                        [
                            'id' => $taskProduct['id'],
                            'iwasku' => $taskProduct['iwasku'],
                            'key' => $taskProduct['productKey'],
                        ]
                    ],
                ];
            } else {
                $parentProducts[$id]['products'][] = [
                    'id' => $taskProduct['id'],
                    'iwasku' => $taskProduct['iwasku'],
                    'key' => $taskProduct['productKey'],
                ];
                if (empty($parentProducts[$id]['parentProduct']['categoryFullName'])) {
                    $parentProducts[$id]['parentProduct']['categoryFullName'] = $categoryFullName;
                    $parentProducts[$id]['parentProduct']['groupType'] = $groupType;
                    $parentProducts[$id]['parentProduct']['productType'] = $productType;
                }
            }
        }
        foreach ($parentProducts as $id => $parentProduct) {
            if (empty($parentProduct['parentProduct']['categoryFullName'])) {
                $parentProducts[$id]['parentProduct']['key'] .= ' ⚠️';
            }
        }
        return $this->render('ozon/task.html.twig', [
            'taskId' => $taskId,
            'parentProducts' => $parentProducts,
            'showParentProductId' => $request->get('parentProductId'),
        ]);
    }

    /**
     * @Route("/ozonproduct/{taskId}/{productId}", name="ozon_task_product")
     * @param Request $request
     * @return RedirectResponse|Response
     *
     * This controller method is used to set variants for a product in an Ozon Listing task.
     * @throws Exception
     */
    public function getProductDetailsForm(Request $request): RedirectResponse|Response
    {
        $db = Db::get();
        $task = ListingTemplate::getById($request->get('taskId'));
        if (!$task) {
            return $this->redirectToRoute('ozon_menu');
        }
        $parentProduct = Product::getById($request->get('productId'));
        if (!$parentProduct) {
            return $this->redirectToRoute('ozon_task', ['taskId' => $task->getId()]);
        }
        $children = [];
        $selectedChildren = [];
        foreach (explode("\n", $parentProduct->getVariationSizeList()) as $size) {
            if (!empty($size)) {
                $children[$size] = [];
                foreach (explode("\n", $parentProduct->getVariationColorList()) as $color) {
                    if (!empty($color)) {
                        $children[$size][$color] = null;
                    }
                }
            }
        }
        foreach ($parentProduct->getChildren() as $child) {
            $children[$child->getVariationSize()][$child->getVariationColor()] = $child;
            $selectedChildren[$child->getId()] = -1;
        }
        $taskProducts = $db->fetchAllAssociative($this->sqlTaskProducts, [$task->getId()]);
        $groupType = $productType = 0;
        foreach ($taskProducts as $taskProduct) {
            if ($taskProduct['parentId'] != $parentProduct->getId()) {
                continue;
            }
            $listingId = $taskProduct['listingId'] ?? 0;
            if (!$groupType && !empty($taskProduct['groupType'])) {
                $groupType = $taskProduct['groupType'];
            }
            if (!$productType && !empty($taskProduct['productType'])) {
                $productType = $taskProduct['productType'];
            }
            $selectedChildren[$taskProduct['id']] = $listingId;
        }
        error_log("Group type: $groupType, Product type: $productType");
        $categoryFullName = Utils::isOzonProductType($groupType, $productType);
        error_log("Category full name: $categoryFullName");
        if (!empty($categoryFullName)) {
            $preselectedProductType = ['id' => $groupType . '.' . $productType, 'text' => $categoryFullName];
        }
        error_log("Preselected product type: ".json_encode($preselectedProductType ?? null));
        return $this->render('ozon/products.html.twig', [
            'task_id' => $task->getId(),
            'parent_product_id' => $parentProduct->getId(),
            'children' => $children,
            'selected_children' => $selectedChildren,
            'preselected_product_type' => $preselectedProductType ?? null,
        ]);
    }

    /**
     * @Route("/ozoncharacteristics/{groupType}/{productType}", name="ozon_characteristics")
     * @param Request $request
     * @return Response
     * @throws \Doctrine\DBAL\Exception
     */
    public function getCharacteristics(Request $request): Response
    {
        $db = Db::get();
        $groupType = $request->get('groupType');
        $productType = $request->get('productType');
        $categoryFullName = Utils::isOzonProductType($groupType, $productType);
        if (empty($categoryFullName)) {
            return new Response();
        }
        $characteristics = $db->fetchAllAssociative('SELECT attribute_id, dictionary_id, attribute_json FROM iwa_ozon_category_attribute WHERE description_category_id = ? AND type_id = ?', [$groupType, $productType]);
        $response = "";
        foreach ($characteristics as $characteristic) {
            $chars = json_decode($characteristic['attribute_json'], true);
            $response .= json_encode($chars, JSON_PRETTY_PRINT)."\n";
            if ($characteristic['dictionary_id'] == 0) {
                continue;
            }
            $dictValues = $db->fetchFirstColumn('SELECT JSON_EXTRACT(value_json, "$.value") AS value, value_id FROM iwa_ozon_attribute_value WHERE attribute_id = ? AND dictionary_id = ? ORDER BY value_id', [$characteristic['attribute_id'], $characteristic['dictionary_id']]);
            $response .= json_encode($dictValues, JSON_PRETTY_PRINT)."\n";
        }

        return new Response("<pre>".$response."</pre>");
    }

    /**
     * @Route("/ozonmodifyproduct/{taskId}/{productId}", name="ozon_modify_product")
     * @param Request $request
     * @return RedirectResponse
     * @throws Exception
     */
    public function modifyProductAction(Request $request): RedirectResponse
    {
        $formTaskId = $request->get('task_id');
        $formParentProductId = $request->get('parent_product_id');
        $taskId = $request->get('taskId');
        $parentProductId = $request->get('productId');
        if (!$formTaskId || !$formParentProductId || $formTaskId != $taskId || $formParentProductId != $parentProductId) {
            error_log("Invalid form data: $formTaskId, $formParentProductId, $taskId, $parentProductId");
            return $this->redirectToRoute('ozon_menu', ['taskId' => $taskId, 'parentProductId' => $parentProductId]);
        }
        $task = ListingTemplate::getById($taskId);
        if (!$task) {
            error_log("Invalid task with id $taskId");
            return $this->redirectToRoute('ozon_menu');
        }
        $parentProduct = Product::getById($parentProductId);
        if (!$parentProduct) {
            error_log("Invalid parent product with id $parentProductId");
            return $this->redirectToRoute('ozon_task', ['taskId' => $task->getId()]);
        }
        $selectedChildren = $request->get('selectedChildren');
        $productType = $request->get('productType');
        $explodedProductType = explode('.', $productType) ?? [];
        $ozonGroupType = $explodedProductType[0] ?? 0;
        $ozonProductType = $explodedProductType[1] ?? 0;
        $taskProducts = $this->getTaskProductsFromDb($taskId);
        foreach ($selectedChildren as $childId => $listingId) {
            if ($listingId == -1) {
                if (isset($taskProducts[$childId])) {
                    $this->deleteTaskProductFromDb($taskId, $childId);
                    unset($taskProducts[$childId]);
                }
                continue;
            }
            if (isset($taskProducts[$childId])) {
                $this->modifyTaskProductInDb($taskId, $childId, $listingId, $ozonGroupType, $ozonProductType);
            } else {
                $this->addTaskProductToDb($taskId, $childId, $listingId, $ozonGroupType, $ozonProductType, '');
                $taskProducts[$childId] = 1;
            }
        }
        $this->addFlash('success', 'Ürünler güncellendi.');
        return $this->redirectToRoute('ozon_menu', ['taskId' => $task->getId(), 'parentProductId' => $parentProduct->getId()]);
    }

    /**
     * @Route("/ozonaddproduct/{taskId}", name="ozon_add_product")
     * @param Request $request
     * @return RedirectResponse
     *
     * This controller method is used to add a product to an Ozon Listing task.
     * @throws Exception
     */
    public function addProductAction(Request $request): RedirectResponse
    {
        $db = Db::get();
        $taskId = $request->get('taskId');
        $task = ListingTemplate::getById($taskId);
        if (!$task) {
            return $this->redirectToRoute('ozon_menu');
        }
        $iwasku = $request->get('iwasku');
        $iwaskuList = preg_split('/[\s,;|]+/', $iwasku);
        $iwaskuList = array_filter($iwaskuList);
        if (empty($iwaskuList)) {
            return $this->redirectToRoute('ozon_menu', ['taskId' => $taskId]);
        }
        $taskProducts = $this->getTaskProductsFromDb($taskId);
        $db->beginTransaction();
        $dirty = false;
        $message = '<pre>';
        try {
            foreach ($iwaskuList as $iwasku) {
                $asin = '';
                $iwasku = trim($iwasku);
                $message .= "Entered value: $iwasku\n";
                $product = Product::getByIwasku($iwasku, 1);
                if (!$product) {
                    $iwaskuFromAsin = Registry::getKey($iwasku, 'asin-to-iwasku');
                    $message .= "  Product not found. Maybe asin entered. Trying to find iwasku from asin: $iwaskuFromAsin\n";
                    if ($iwaskuFromAsin) {
                        $product = Product::getByIwasku($iwaskuFromAsin, 1);
                        $asin = $iwasku;
                    }
                }
                if (!$product) {
                    $message .= "  Product not found for iwasku $iwasku\n";
                    continue;
                }
                $message .= "  Product found with iwasku $iwasku\n";
                if (isset($taskProducts[$product->getId()])) {
                    $message .= "  Product already added to task with iwasku $iwasku: ".json_encode($taskProducts[$product->getId()])."\n";
                    continue;
                }
                $dirty = true;
                $this->addTaskProductToDb($taskId, $product->getId(), 0, 0, 0, $asin);
                $message .= "  Product {$product->getId()} added to task $taskId with iwasku $iwasku\n";
                $taskProducts[$product->getId()] = ['iwasku' => $iwasku];
                unset($product);
            }
            $db->commit();
            $message .= '</pre>';
            if ($dirty) {
                $this->addFlash('success', $message);
            } else {
                $this->addFlash('warning', $message);
            }
        } catch (Exception $e) {
            $db->rollBack();
            throw $e;
        }
        return $this->redirectToRoute('ozon_menu', ['taskId' => $task->getId()]);
    }

    /**
     * @Route("/ozontree", name="ozon_tree")
     * @return JsonResponse
     *
     * This controller iterates on database and outputs item list
     * @throws \Doctrine\DBAL\Exception
     * @throws RandomException
     */
    public function treeAction(Request $request): JsonResponse
    {
        $q = $request->get('q');
        return new JsonResponse(Utils::getOzonProductTypes($q));
    }

    /**
     * @throws \Doctrine\DBAL\Exception
     */
    private function getTaskProductsFromDb(int $taskId): array
    {
        $db = Db::get();
        $taskProductsDb = $db->fetchAllAssociative($this->sqlTaskProducts, [$taskId]);
        $taskProducts = [];
        foreach ($taskProductsDb as $taskProduct) {
            $taskProducts[$taskProduct['id']] = $taskProduct;
        }
        return $taskProducts;
    }

    /**
     * @throws \Doctrine\DBAL\Exception
     */
    private function deleteTaskProductFromDb(int $taskId, int $productId): void
    {
        $db = Db::get();
        $db->executeStatement($this->sqlDeleteProduct, [$taskId, $productId]);
    }

    /**
     * @throws \Doctrine\DBAL\Exception
     */
    private function addTaskProductToDb(int $taskId, int $productId, int $listingId, int $groupType, int $productType, string $asin): void
    {
        $db = Db::get();
        $db->executeStatement($this->sqlAddProduct, [$taskId, $productId, $listingId, $groupType, $productType, $asin]);
    }

    /**
     * @throws \Doctrine\DBAL\Exception
     */
    private function modifyTaskProductInDb(int $taskId, int $productId, int $listingId, int $groupType, int $productType): void
    {
        $db = Db::get();
        $db->executeStatement($this->sqlModifyProduct, [$listingId, $groupType, $productType, $taskId, $productId]);
    }
    /**
     * @Route("/ozoncsv/{taskId}", name="ozon_csv_output")
     * @throws \Doctrine\DBAL\Exception
     */
    public function csvOutput(Request $request): Response
    {
        $taskId = $request->get('taskId');
        $task = ListingTemplate::getById($taskId);
        if (!$task) {
            return $this->redirectToRoute('ozon_menu');
        }
        $taskProducts = $this->getTaskProductsFromDb($taskId);
        $csv = [];
        foreach ($taskProducts as $taskProduct) {
            $asin = empty($taskProduct['asin']) ? Registry::searchKeys($taskProduct['iwasku'], 'asin-to-iwasku', 1) : $taskProduct['asin'];
            $listing = VariantProduct::getByUniqueMarketplaceId($asin, 1);
            if (!$listing) {
                error_log("Listing not found for asin $asin");
                continue;
            }
            $listingImages = $listing->getImageGallery();
            $images = [];
            foreach ($listingImages as $image) {
                $images[] = $image->getImage()->getFullPath();
                if (count($images) >= 3) {
                    break;
                }
            }
            $csv[] = [
                'CountryofOrigin' => 'Türkiye',
                'ProductName' => empty($taskProduct['parentNameEnglish']) ? $taskProduct['parentName'] : $taskProduct['parentNameEnglish'],
                'MerchantSKU' => $taskProduct['iwasku'],
                'Type' => $taskProduct['parentCategory'],
                'Option1 Name' => 'Size',
                'Option1 Value' => $taskProduct['variationSize'],
                'Option2 Name' => 'Color',
                'Option2 Value' => $taskProduct['variationColor'],
                'ASIN' => $asin,
                'HSNCode' => '',
                'ProductImageURL1' => 'https://iwa.web.tr'. ($images[0] ?? ''),
                'ProductImageURL2' => 'https://iwa.web.tr'. ($images[1] ?? ''),
                'ProductImageURL3' => 'https://iwa.web.tr'. ($images[2] ?? ''),
            ];
        }
        $response = new Response();
        $response->headers->set('Content-Type', 'text/csv');
        $response->headers->set('Content-Disposition', 'attachment; filename="product_list.csv"');
        $output = fopen('php://memory', 'w');
        fputcsv($output, array_keys($csv[0]));
        foreach ($csv as $row) {
            fputcsv($output, $row);
        }
        fseek($output, 0);
        $response->setContent(stream_get_contents($output));
        fclose($output);
        return $response;
    }
}
