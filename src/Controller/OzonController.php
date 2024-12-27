<?php

namespace App\Controller;

use App\Model\DataObject\VariantProduct;
use App\Utils\Utility;
use Exception;
use Pimcore\Controller\FrontendController;
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
     */
    public function ozonAction(): Response
    {
        $mrkListing = new Marketplace\Listing();
        $mrkListing->setCondition("marketplaceType = ?", ['Ozon']);
        $marketplaces = $mrkListing->load();
        $taskListing = new ListingTemplate\Listing();
        $taskListing->setUnpublished(true);
        $tasksObjects = $taskListing->load();
        $tasks = [];
        foreach ($tasksObjects as $task) {
            if ($task->getMarketplace()->getMarketplaceType() !== 'Ozon') {
                continue;
            }
            $tasks[] = [
                'id' => $task->getId(),
                'title' => $task->getKey(),
            ];
        }
        return $this->render('ozon/ozon.html.twig', [
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
        // get key from request POST
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
            $selectedChildren[$child->getId()] = 0;
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
        $task = ListingTemplate::getById($request->get('taskId'));
        if (!$task) {
            return $this->redirectToRoute('ozon_menu');
        }
        $selectedChildren = $request->get('selectedChildren', []);
        $taskProducts = [];
        foreach ($selectedChildren as $productId => $listing) {
            if (!$listing) {
                continue;
            }
            $product = Product::getById($productId);
            if (!$product) {
                error_log('Product not found: ' . $productId);
                continue;
            }
            $listingItem = VariantProduct::getById($listing);
            if (!$listingItem) {
                error_log('Listing not found: ' . $listing);
                continue;
            }
            $objectMetadata = new ObjectMetadata('products', ['listing'], $product);
            $objectMetadata->setData(['listing'=>$listingItem->getId()]);
            $taskProducts[] = $objectMetadata;
        }
        $task->setProducts($taskProducts);
        $task->save();
        return $this->redirectToRoute('ozon_task', ['id' => $task->getId()]);
    }

}
