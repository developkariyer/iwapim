<?php

namespace App\Controller;

use App\Utils\Utility;
use Exception;
use Pimcore\Controller\FrontendController;
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
        $groupedProducts = [];
        $selectedListings = [];
        foreach ($taskProducts as $taskProduct) {
            $product = $taskProduct->getObject();
            $parentProduct = $product->getParent();
            if (!$parentProduct instanceof Product) {
                continue;
            }
            if (!isset($groupedProducts[$parentProduct->getId()])) {
                $groupedProducts[$parentProduct->getId()] = [
                    'product' => $parentProduct->getKey(),
                    'children' => [],
                ];
                foreach (explode("\n", $parentProduct->getVariationSizeList()) as $size) {
                    if (!empty($size)) {
                        $groupedProducts[$parentProduct->getId()]['children'][$size] = [];
                        foreach (explode("\n", $parentProduct->getVariationColorList()) as $color) {
                            if (!empty($color)) {
                                $groupedProducts[$parentProduct->getId()]['children'][$size][$color] = -1;
                            }
                        }
                    }
                }
                foreach ($parentProduct->getChildren() as $child) {
                    $groupedProducts[$parentProduct->getId()]['children'][$child->getVariationSize()][$child->getVariationColor()] = $child;
                }
            }
            $selectedListings[$product->getIwasku()] = $taskProduct->getData()['listing'];
        }

        return $this->render('ozon/task.html.twig', [
            'task' => $task,
            'products' => $groupedProducts,
            'selectedListings' => $selectedListings,
        ]);
    }

}
