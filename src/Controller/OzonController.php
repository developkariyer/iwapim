<?php

namespace App\Controller;

use App\Form\OzonTaskFormType;
use App\Model\DataObject\VariantProduct;
use App\Utils\Utility;
use Exception;
use Pimcore\Controller\FrontendController;
use Pimcore\Db;
use Pimcore\Model\DataObject\Data\ObjectMetadata;
use Pimcore\Model\DataObject\Product;
use Pimcore\Model\Element\DuplicateFullPathException;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Pimcore\Model\DataObject\Marketplace;
use Pimcore\Model\DataObject\ListingTemplate;

class OzonController extends FrontendController
{

    /**
     * @Route("/ozon", name="ozon_menu")
     * @return Response
     *
     * This controller method loads all marketplaces and tasks for Ozon and renders the page.
     * Also displays the form to create a new Ozon Listing task.
     * @throws DuplicateFullPathException
     * @throws Exception
     */
    public function ozonAction(Request $request): Response
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
            return $this->redirectToRoute('ozon_menu');
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
        ]);
    }


    /**
     * @Route("/ozon/newtask", name="ozon_newtask_action")
     * @param Request $request
     * @return RedirectResponse|JsonResponse
     *
     * This controller method creates a new task for Ozon Listing and redirects to the task detail page.
     * @throws DuplicateFullPathException
     * @throws Exception
     */
    public function newTaskAction(Request $request): RedirectResponse|JsonResponse
    {
        $task = new ListingTemplate();
        $task->setKey($request->get('taskName', 'İsimsiz'));
        $task->setParent(Utility::checkSetPath('Listing'));
        $marketplaceId = $request->get('marketplace', 0);
        if (!$marketplaceId) {
            return new JsonResponse(['error' => 'Marketplace not found'], 400);
        }
        $task->setMarketplace(Marketplace::getById($marketplaceId) ?? null);
        $task->save();
        return $this->redirectToRoute('ozon_task', ['id' => $task->getId()]);
    }

    /**
     * @Route("/ozon/task/{id}", name="ozon_task")
     * @param Request $request
     * @return Response
     *
     * This controller method displays the detail page for an Ozon Listing task.
     */
    public function taskAction(Request $request): Response
    {
        $task = ListingTemplate::getById($request->get('id'));
        if (!$task) {
            return $this->redirectToRoute('ozon_menu');
        }
        $taskProducts = $task->getProducts();
        $parentProducts = [];
        foreach ($taskProducts as $taskProduct) {
            $product = $taskProduct->getObject();
            $parentProduct = $product->getParent();
            if (!$parentProduct instanceof Product) {
                continue;
            }
            $id = $parentProduct->getId();
            if (!isset($parentProducts[$id])) {
                $parentProducts[$id] = [
                    'parentProduct' => $parentProduct,
                    'products' => [$product],
                ];
            } else {
                $parentProducts[$id]['products'][] = $product;
            }
        }

        return $this->render('ozon/task.html.twig', [
            'task' => $task,
            'parentProducts' => $parentProducts,
        ]);
    }

    /**
     * @Route("/ozon/product/{taskId}/{productId}", name="ozon_task_product")
     * @param Request $request
     * @return RedirectResponse|Response
     *
     * This controller method is used to set variants for a product in an Ozon Listing task.
     */
    public function taskProductAction(Request $request): RedirectResponse|Response
    {
        $task = ListingTemplate::getById($request->get('taskId'));
        if (!$task) {
            return $this->redirectToRoute('ozon_menu');
        }
        $parentProduct = Product::getById($request->get('productId'));
        if (!$parentProduct) {
            return $this->redirectToRoute('ozon_task', ['id' => $task->getId()]);
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
            $selectedChildren[$product->getId()] = $taskProduct->getData()['listing'];
        }
        return $this->render('ozon/products.html.twig', [
            'task' => $task,
            'parentProduct' => $parentProduct,
            'children' => $children,
            'selectedChildren' => $selectedChildren,
        ]);
    }

    /**
     * @Route("/ozon/modify/{taskId}", name="ozon_modify_task")
     * @param Request $request
     * @return RedirectResponse
     *
     * This controller method is used to save the selected variants for a product in an Ozon Listing task.
     * @throws Exception
     */
    public function modifyTaskAction(Request $request): RedirectResponse
    {
        $parentProductId = $request->get('productId');
        $task = ListingTemplate::getById($request->get('taskId'));
        if (!$task) {
            return $this->redirectToRoute('ozon_menu');
        }
        $selectedChildren = $request->get('selectedChildren', []);
        $taskProducts = [];
        foreach ($task->getProducts() as $taskProduct) {
            $product = $taskProduct->getObject();
            if ($product->getParent()->getId() == $parentProductId) {
                continue;
            }
            $taskProducts[] = $taskProduct;
        }
        foreach ($selectedChildren as $productId => $listing) {
            if ($listing<0) {
                continue;
            }
            $product = Product::getById($productId);
            if (!$product) {
                error_log('Product not found: ' . $productId);
                continue;
            }
            $objectMetadata = new ObjectMetadata('products', ['listing'], $product);
            if ($listing) {
                $listingItem = VariantProduct::getById($listing);
                if (!$listingItem) {
                    error_log('Listing not found: ' . $listing);
                    continue;
                }
                $objectMetadata->setData(['listing'=>$listingItem->getId()]);
            } else {
                $objectMetadata->setData(['listing'=>0]);
            }
            $taskProducts[] = $objectMetadata;
        }
        $taskProducts = array_unique($taskProducts);
        $task->setProducts($taskProducts);
        $task->save();
        return $this->redirectToRoute('ozon_task', ['id' => $task->getId()]);
    }

    /**
     * @Route("/ozon/addproduct/{taskId}", name="ozon_add_product")
     * @param Request $request
     * @return RedirectResponse
     *
     * This controller method is used to add a product to an Ozon Listing task.
     */
    public function addProductAction(Request $request): RedirectResponse
    {
        $task = ListingTemplate::getById($request->get('taskId'));
        if (!$task) {
            $this->addFlash('danger', 'Task not found');
            return $this->redirectToRoute('ozon_menu');
        }
        $iwasku = $request->get('iwasku');
        $product = Product::getByIwasku($iwasku, 1);
        if (!$product) {
            return $this->redirectToRoute('ozon_task', ['id' => $task->getId()]);
        }
        $parentProduct = $product->getParent();
        if (!$parentProduct instanceof Product) {
            return $this->redirectToRoute('ozon_task', ['id' => $task->getId()]);
        }
        return $this->redirectToRoute('ozon_task_product', ['taskId' => $task->getId(), 'productId' => $parentProduct->getId()]);
    }

    /**
     * @Route("/ozon/tree", name="ozon_tree")
     * @return JsonResponse
     *
     * This controller iterates on database and outputs item list
     * @throws \Doctrine\DBAL\Exception
     */
    public function treeAction(): JsonResponse
    {
        $db = Db::get();
        $items = [];
        $results = $db->fetchAllAssociative('SELECT * FROM iwa_ozon_producttype');
        foreach ($results as $result) {
            $item = [
                'type_id' => $result['type_id'],
                //'type_name' => trim($result['type_name']),
                'description_category_id' => $result['description_category_id'],
                'category_name' => trim($result['type_name']),
            ];
            $parentId = $result['description_category_id'];
            while ($parentId) {
                $row = $db->fetchAssociative('SELECT * FROM iwa_ozon_category WHERE description_category_id = ?', [$parentId]);
                if (!$row) {
                    break;
                }
                $parentId = $row['parent_id'];
                $item['category_name'] = trim("{$row['category_name']} | {$item['category_name']}");
            }
            $items[] = $item;
            if (empty($result['category_full_name'])) {
                $db->executeQuery("UPDATE iwa_ozon_producttype SET category_full_name = ? WHERE type_id = ? AND description_category_id = ?", [$item['category_name'], $item['type_id'], $item['description_category_id']]);
            }
        }
        return new JsonResponse($items);
    }

}
