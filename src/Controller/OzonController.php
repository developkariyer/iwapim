<?php

namespace App\Controller;

use App\Connector\Marketplace\Ozon\Utils;
use App\Form\OzonTaskFormType;
use App\Utils\Registry;
use App\Utils\Utility;
use Exception;
use Pimcore\Controller\FrontendController;
use Pimcore\Db;
use Pimcore\Model\DataObject\Data\ObjectMetadata;
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
    ob_parent.`key` AS parentKey,
    MAX(CASE WHEN omlt.`column` = 'listing' THEN omlt.data END) AS listingId,
    MAX(CASE WHEN omlt.`column` = 'grouptype' THEN omlt.data END) AS groupType,
    MAX(CASE WHEN omlt.`column` = 'producttype' THEN omlt.data END) AS productType
FROM
    object_relations_listingTemplate AS orlt
JOIN
    object_product AS ob ON orlt.dest_id = ob.id
LEFT JOIN
    object_product AS ob_parent ON ob.parentId = ob_parent.id
JOIN
    object_metadata_listingTemplate AS omlt ON orlt.dest_id = omlt.dest_id
    AND orlt.src_id = omlt.id
WHERE
    orlt.fieldname = 'products'
    AND omlt.fieldname = 'products'
    AND orlt.src_id = ?
GROUP BY
    ob.id, ob.variationSize, ob.variationColor, ob.iwasku, ob.`key`, ob_parent.`key`
ORDER BY
    productKey;";


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
        [$newTaskProducts,] = $this->getTaskProductsAsMetadata($task->getId(), $parentProduct->getId());
        foreach ($selectedChildren as $childId => $listingId) {
            if ($listingId == -1) {
                continue;
            }
            $child = Product::getById($childId);
            if (!$child) {
                error_log("Invalid child product with id $childId");
                continue;
            }
            $objectMetadata = new ObjectMetadata('products', ['listing', 'grouptype', 'producttype'], $child);
            $objectMetadata->setData(['listing' => $listingId, 'grouptype' => $ozonGroupType, 'producttype' => $ozonProductType]);
            $objectMetadata->setObject($child);
            $newTaskProducts[] = $objectMetadata;
        }
        $task->setProducts($newTaskProducts);
        $task->save();
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
        $task = ListingTemplate::getById($request->get('taskId'));
        if (!$task) {
            return $this->redirectToRoute('ozon_menu');
        }
        $iwasku = $request->get('iwasku');
        $iwaskuList = preg_split('/[\s,;|]+/', $iwasku);
        $iwaskuList = array_filter($iwaskuList);
        if (empty($iwaskuList)) {
            return $this->redirectToRoute('ozon_menu', ['taskId' => $task->getId()]);
        }
        [$newTaskProducts, $objectIdList] = $this->getTaskProductsAsMetadata($task->getId());
        $dirty = false;
        $parentProduct = null;
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
            if (in_array($product->getId(), $objectIdList)) {
                error_log("Product already added to task with iwasku $iwasku");
                continue;
            }
            $parentProduct = $product->getParent();
            if (!$parentProduct instanceof Product) {
                error_log("Parent product not found for product with iwasku $iwasku");
                continue;
            }
            $objectMetadata = new ObjectMetadata('products', ['listing'], $product);
            $objectMetadata->setData(['listing' => 0]);
            $newTaskProducts[] = $objectMetadata;
            $objectIdList[] = $product->getId();
            $dirty = true;
        }
        if ($dirty) {
            $task->setProducts($newTaskProducts);
            $this->addFlash('success', 'Yeni ürün eklendi.');
            $task->save();
        }
        return $this->redirectToRoute('ozon_menu', ['taskId' => $task->getId(), 'parentProductId' => is_object($parentProduct) ? $parentProduct->getId() : 0]);
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
    private function getTaskProductsAsMetadata(int $taskId, int $parentProductIdToIgnore = 0): array
    {
        $db = Db::get();
        $taskProducts = $db->fetchAllAssociative($this->sqlTaskProducts, [$taskId]);
        $taskProductsMetadata = [];
        $objectIdList = [];
        foreach ($taskProducts as $taskProduct) {
            if ($taskProduct['parentId'] == $parentProductIdToIgnore) {
                continue;
            }
            $object = Product::getById($taskProduct['id']);
            if (!$object) {
                error_log("Invalid product with id {$taskProduct['id']}");
                continue;
            }
            $objectMetadata = new ObjectMetadata('products', ['listing', 'grouptype', 'producttype'], $object);
            $objectMetadata->setData(['listing' => $taskProduct['listingId'] ?? 0, 'grouptype' => $taskProduct['groupType'] ?? 0, 'producttype' => $taskProduct['productType'] ?? 0]);
            $taskProductsMetadata[] = $objectMetadata;
            $objectIdList[] = $taskProduct['id'];
        }
        return [$taskProductsMetadata, $objectIdList];
    }

}
