<?php

namespace App\Controller;

use App\Form\OzonTaskFormType;
use App\Form\OzonTaskProductFormType;
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
     */
    public function getTaskProducts(Request $request): Response
    {
        $taskId = $request->get('id');
        $task = ListingTemplate::getById($taskId);
        if (!$task) {
            return $this->redirectToRoute('ozon_menu');
        }
        $taskProducts = $task->getProducts();
        $parentProducts = [];
        foreach ($taskProducts as $taskProduct) {
            $product = $taskProduct->getObject();
            $parentProduct = $product->getParent();
            if (!$parentProduct instanceof Product) {
                $parentProduct = $product;
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
        uasort($parentProducts, function ($a, $b) {
            return strcmp($a['parentProduct']->getKey(), $b['parentProduct']->getKey());
        });
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
    public function getProductDetails(Request $request): RedirectResponse|Response
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
        $form = $this->createForm(OzonTaskProductFormType::class, null, [
            'task_id' => $task->getId(),
            'parent_product_id' => $parentProduct->getId(),
            'children' => $children,
            'selected_children' => $selectedChildren,
        ]);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $data = $form->getData();
            //var_dump($data); exit;
            $taskProducts = $task->getProducts();
            $newTaskProducts = [];
            foreach ($taskProducts as $taskProduct) {
                $product = $taskProduct->getObject();
                if (!$product) {
                    continue;
                }
                if ($product->getParent() instanceof Product) {
                    continue;
                }
                if ($product->getParent()->getId() != $parentProduct->getId()) {
                    $newTaskProducts[] = $taskProduct;
                }
            }
            foreach ($data['selectedChildren'] as $productId => $listing) {
                $product = Product::getById($productId);
                if (!$product) {
                    continue;
                }
                $objectMetadata = new ObjectMetadata('products', ['listing'], $product);
                if ($listing<0) {
                    continue;
                }
                if ($listing) {
                    $listingItem = VariantProduct::getById($listing);
                    if ($listingItem) {
                        $objectMetadata->setData(['listing'=>$listingItem->getId()]);
                    }
                } else {
                    $objectMetadata->setData(['listing'=>0]);
                }
                $newTaskProducts[] = $objectMetadata;
            }
            $newTaskProducts = array_unique($newTaskProducts);
            $task->setProducts($newTaskProducts);
            $task->save();
            return $this->redirectToRoute('ozon_menu', ['id' => $task->getId()]);
        }
        return $this->render('ozon/products.html.twig', [
            'form' => $form->createView(),
            'task_id' => $task->getId(),
            'parent_product_id' => $parentProduct->getId(),
            'task_products' => $taskProducts,
        ]);
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
        return $this->redirectToRoute('ozon_menu', ['taskId' => $task->getId(), 'productId' => $parentProduct->getId()]);
    }

    /**
     * @Route("/ozontree", name="ozon_tree")
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
