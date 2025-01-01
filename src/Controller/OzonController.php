<?php

namespace App\Controller;

use App\Connector\Marketplace\Ozon\Utils;
use App\Form\OzonTaskFormType;
use App\Form\OzonTaskProductFormType;
use App\Utils\Utility;
use Exception;
use Pimcore\Controller\FrontendController;
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
                continue;
            }
            $id = $parentProduct->getId();
            if (!isset($parentProducts[$id])) {
                $parentProducts[$id] = [
                    'parentProduct' => $parentProduct,
                    'products' => [$product],
                    'productType' => Utils::isOzonProductType($taskProduct->getData()['grouptype'] ?? 0, $taskProduct->getData()['producttype'] ?? 0),
                ];
            } else {
                $parentProducts[$id]['products'][] = $product;
                if (empty($parentProducts[$id]['productType']) && !empty($taskProduct->getData()['grouptype']) && !empty($taskProduct->getData()['producttype'])) {
                    $parentProducts[$id]['productType'] = Utils::isOzonProductType($taskProduct->getData()['grouptype'], $taskProduct->getData()['producttype']);
                }
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
        $form = $this->createForm(OzonTaskProductFormType::class, null, [
            'task_id' => $task->getId(),
            'parent_product_id' => $parentProduct->getId(),
            'children' => $children,
            'selected_children' => $selectedChildren,
        ]);
        $form->handleRequest($request);
        if ($form->isSubmitted()) {
            $data = $form->getData();
            error_log(json_encode($data));
            $taskProducts = $task->getProducts();
            $newTaskProducts = [];
            foreach ($taskProducts as $taskProduct) {
                $product = $taskProduct->getObject();
                $listingData = $taskProduct->getData()['listing'];
                if (!$product || !is_numeric($listingData)) {
                    continue;
                }
                if ($product->getParent()->getId() != $parentProduct->getId()) {
                    $newTaskProducts[] = $taskProduct;
                }
            }
            foreach ($data['selectedChildren'] as $productId => $listing) {
                $product = Product::getById($productId);
                $groupType = $data['productType']['descriptionCategoryId'] ?? 0;
                $productType = $data['productType']['typeId'] ?? 0;
                if ($listing<0 || !$product) {
                    continue;
                }
                $objectMetadata = new ObjectMetadata('products', ['listing', 'grouptype', 'producttype'], $product);
                $objectMetadata->setData(['listing'=>$listing, 'grouptype'=>$groupType, 'producttype'=>$productType]);
                error_log("{$product->getIwasku()} {$product->getKey()} l:{$listing} g:{$groupType} t:{$productType}");
                $newTaskProducts[] = $objectMetadata;
            }
            $newTaskProducts = array_unique($newTaskProducts);
            $task->setProducts($newTaskProducts);
            $task->save();
            return $this->redirectToRoute('ozon_menu', ['taskId' => $task->getId()]);
        }
        return $this->render('ozon/products.html.twig', [
            'form' => $form->createView(),
            'task_id' => $task->getId(),
            'parent_product_id' => $parentProduct->getId(),
            'task_products' => $taskProducts,
        ]);
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
