<?php

namespace App\Controller;

use App\Connector\Marketplace\Ozon\Utils;
use App\Form\OzonTaskFormType;
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
    orlt.dest_id AS id, 
    op.parentId,
    op.variationSize,
    op.variationColor,
    op.iwasku,
    op_parent.key AS parentKey,
    CONCAT_WS(' ', op_parent.key, op.variationSize, op.variationColor) AS productKey
FROM 
    object_relations_listingTemplate orlt
JOIN 
    object_product op ON orlt.dest_id = op.id
LEFT JOIN 
    object_product op_parent ON op_parent.id = op.parentId
WHERE 
    orlt.src_id = ? 
    AND orlt.fieldname = 'products'
    ORDER BY productKey";

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
            $this->addFlash('success', 'Yeni görev başarıyla oluşturuldu.');
            return $this->redirectToRoute('ozon_menu', ['taskId' => $task->getId()]);
        }
        $taskListing = new ListingTemplate\Listing();
        $taskListing->setUnpublished(true);
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
     * @Route("/ozontask/{id}", name="ozon_task")
     * @param Request $request
     * @return Response
     *
     * This controller method displays the detail page for an Ozon Listing task.
     * @throws \Doctrine\DBAL\Exception
     */
    public function getTaskProducts(Request $request): Response
    {
        $db = Db::get();
        $taskId = $request->get('id');
        $task = ListingTemplate::getById($taskId);
        if (!$task) {
            return $this->redirectToRoute('ozon_menu');
        }
        $parentProducts = [];
        $taskProductIds = $db->fetchAllAssociative($this->sqlTaskProducts, [$taskId]);
        foreach ($taskProductIds as $taskProductId) {
            $id = $taskProductId['parentId'];
            if (!isset($parentProducts[$id])) {
                $parentProducts[$id] = [
                    'parentProduct' => [
                        'id' => $taskProductId['parentId'],
                        'key' => $taskProductId['parentKey']
                    ],
                    'products' => [
                        [
                            'id' => $taskProductId['id'],
                            'iwasku' => $taskProductId['iwasku'],
                            'key' => $taskProductId['productKey'],
                        ]
                    ],
                ];
            } else {
                $parentProducts[$id]['products'][] = [
                    'id' => $taskProductId['id'],
                    'iwasku' => $taskProductId['iwasku'],
                    'key' => $taskProductId['productKey'],
                ];
            }
        }
        return $this->render('ozon/task.html.twig', [
            'taskId' => $taskId,
            'parentProducts' => $parentProducts,
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
        $taskProducts = $task->getProducts();
        foreach ($taskProducts as $taskProduct) {
            $product = $taskProduct->getObject();
            if (!$product instanceof Product) {
                continue;
            }
            $listingData = $taskProduct->getData()['listing'];
            if (!is_numeric($listingData)) {
                continue;
            }
            $selectedChildren[$product->getId()] = $listingData;
        }
        return $this->render('ozon/products.html.twig', [
            'task_id' => $task->getId(),
            'parent_product_id' => $parentProduct->getId(),
            'children' => $children,
            'selected_children' => $selectedChildren,
            'task_products' => $taskProducts,
            'preselected_product_type' => [],
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
            return $this->redirectToRoute('ozon_menu');
        }
        $task = ListingTemplate::getById($taskId);
        if (!$task) {
            return $this->redirectToRoute('ozon_menu');
        }
        $parentProduct = Product::getById($parentProductId);
        if (!$parentProduct) {
            return $this->redirectToRoute('ozon_task', ['taskId' => $task->getId()]);
        }
        $selectedChildren = $request->get('selectedChildren');
        $productType = $request->get('productType');
        $newTaskProducts = [];
        foreach ($selectedChildren as $childId => $listingId) {
            $child = Product::getById($childId);
            if (!$child) {
                continue;
            }
            $explodedProductType = explode('.', $productType) ?? [];
            $ozonGroupType = $explodedProductType[0] ?? 0;
            $ozonProductType = $explodedProductType[1] ?? 0;
            $objectMetadata = new ObjectMetadata('products', ['listing', 'grouptype', 'producttype'], $child);
            $objectMetadata->setData(['listing' => $listingId, 'grouptype' => $ozonGroupType, 'producttype' => $ozonProductType]);
            $objectMetadata->setObject($child);
            $newTaskProducts[] = $objectMetadata;
        }
        $taskProducts = $task->getProducts();
        foreach ($taskProducts as $taskProduct) {
            $product = $taskProduct->getObject();
            if ($product->getParent()->getId() != $parentProduct->getId()) {
                $newTaskProducts[] = $taskProduct;
            }
        }
        $task->setProducts($newTaskProducts);
        $task->save();
        return $this->redirectToRoute('ozon_menu', ['taskId' => $task->getId()]);
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
        $taskProducts = $task->getProducts();
        $dirty = false;
        foreach ($iwaskuList as $iwasku) {
            $iwasku = trim($iwasku);
            $product = Product::getByIwasku($iwasku, 1);
            if (!$product) {
                continue;
            }
            $parentProduct = $product->getParent();
            if (!$parentProduct instanceof Product) {
                continue;
            }
            $objectMetadata = new ObjectMetadata('products', ['listing'], $product);
            $objectMetadata->setData(['listing' => 0]);
            $taskProducts[] = $objectMetadata;
            $dirty = true;
        }
        if ($dirty) {
            $task->setProducts($taskProducts);
            $task->save();
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


}
