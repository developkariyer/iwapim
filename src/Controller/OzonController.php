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
use Pimcore\Model\Element\DuplicateFullPathException;
use Random\RandomException;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Pimcore\Model\DataObject\Marketplace;
use Pimcore\Model\DataObject\ListingTemplate;

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
    ob_parent.productCategory AS parentCategory,
    rel.listing_id AS listingId,
    rel.group_type AS groupType,
    rel.product_type AS productType
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
    iwa_ozon_product_relations (task_id, product_id, listing_id, group_type, product_type)
VALUES
    (?, ?, ?, ?, ?)
ON DUPLICATE KEY UPDATE
    listing_id = VALUES(listing_id),
    group_type = VALUES(group_type),
    product_type = VALUES(product_type);";

    private string $sqlDeleteProduct = "DELETE FROM
    iwa_ozon_product_relations
WHERE
    task_id = ?
    AND product_id = ?;";

    /**
     * @Route("/ozon/{taskId}/{parentProductId}", name="ozon_menu", defaults={"taskId"=null, "parentProductId"=null})
     * @return Response
     *
     * This controller method loads all marketplaces and tasks for Ozon and renders the page.
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
                }
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
            if (!$groupType && !empty($taskProduct['grouptype'])) {
                $groupType = $taskProduct['grouptype'];
            }
            if (!$productType && !empty($taskProduct['producttype'])) {
                $productType = $taskProduct['producttype'];
            }
            $selectedChildren[$taskProduct['id']] = $listingId;
        }
        $categoryFullName = Utils::isOzonProductType($groupType, $productType);
        if (!empty($categoryFullName)) {
            $preselectedProductType = ['id' => $groupType . '.' . $productType, 'text' => $categoryFullName];
        }
        return $this->render('ozon/products.html.twig', [
            'task_id' => $task->getId(),
            'parent_product_id' => $parentProduct->getId(),
            'children' => $children,
            'selected_children' => $selectedChildren,
            'preselected_product_type' => $preselectedProductType ?? null,
        ]);
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
                }
                continue;
            }
            $this->addTaskProductToDb($taskId, $childId, $listingId, $ozonGroupType, $ozonProductType);
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
        try {
            foreach ($iwaskuList as $iwasku) {
                $iwasku = trim($iwasku);
                $product = Product::getByIwasku($iwasku, 1);
                if (!$product) {
                    $iwaskuFromAsin = Registry::getKey($iwasku, 'asin-to-iwasku');
                    error_log("Fallback to asin-to-iwasku for $iwasku: found $iwaskuFromAsin");
                    if ($iwaskuFromAsin) {
                        $product = Product::getByIwasku($iwaskuFromAsin, 1);
                    }
                }
                if (!$product) {
                    error_log("Product not found for iwasku $iwasku");
                    continue;
                }
                if (isset($taskProducts[$product->getId()])) {
                    error_log("Product already added to task with iwasku $iwasku");
                    continue;
                }
                $dirty = true;
                $this->addTaskProductToDb($taskId, $product->getId(), 0, 0, 0);
                $taskProducts[$product->getId()] = 1;
                unset($product);
            }
            $db->commit();
            if ($dirty) {
                $this->addFlash('success', 'Yeni ürünler eklendi.');
            } else {
                $this->addFlash('warning', 'Hiçbir yeni ürün eklenemedi.');
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
    private function addTaskProductToDb(int $taskId, int $productId, int $listingId, int $groupType, int $productType): void
    {
        $db = Db::get();
        $db->executeStatement($this->sqlAddProduct, [$taskId, $productId, $listingId, $groupType, $productType]);
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
            $csv[] = [
                'CountryofOrigin' => 'Türkiye',
                'ProductName' => $taskProduct['parentName'],
                'MerchantSKU' => $taskProduct['iwasku'],
                'Type' => $taskProduct['parentCategory'],
                'Option1 Name' => 'Size',
                'Option1 Value' => $taskProduct['variationSize'],
                'Option2 Name' => 'Color',
                'Option2 Value' => $taskProduct['variationColor'],
                'ASIN' => '',
                'HSNCode' => '',
                'ProductImageURL1' => '',
                'ProductImageURL2' => '',
                'ProductImageURL3' => '',
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
