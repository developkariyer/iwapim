<?php

namespace App\Controller;

use App\Utils\Utility;
use Exception;
use Pimcore\Controller\FrontendController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Pimcore\Model\DataObject\Marketplace;
use Pimcore\Model\DataObject\ListingTemplate;

class OzonController extends FrontendController
{

    /**
     * @Route("/ozon", name="ozon_menu")
     * @param Request $request
     * @return Response
     */
    public function ozonAction(Request $request): Response
    {
        /*
        // ozon marketplace id is 268776
        $ozonMarketplace = Marketplace::getByMarketplaceType('Ozon', ['limit' => 1]);
        if (!$ozonMarketplace) {
            return new Response('Ozon marketplace not found');
        }

        $ozonConnector = new OzonConnector($ozonMarketplace);
        $categories = $ozonConnector->getCategories();
        */
        $mrkListing = new Marketplace\Listing();
        $mrkListing->setCondition("marketplaceType = ?", ['Ozon']);
        $marketplaces = $mrkListing->load();

        $taskListing = new ListingTemplate\Listing();
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
     * @Route("/ozon/task/{id}", name="ozon_task")
     * @param Request $request
     * @param int $id
     * @return Response
     */
    public function taskAction(Request $request, int $id): Response
    {
        $task = ListingTemplate::getById($id);
        if (!$task) {
            return new Response('Task not found');
        }
        return $this->render('ozon/task.html.twig', [
            'task' => $task,
        ]);
    }

    /**
     * @Route("/ozon/newtask", name="ozon_newtask_action")
     * @param Request $request
     *
     * This controller method creates a new ListingTemplate and redirects page to /ozon/task/{id} page
     * @throws Exception
     */
    public function newTaskAction(Request $request)
    {
        $task = new ListingTemplate();
        // get key from request POST
        $task->setKey($request->get('taskName', 'İsimsiz'));
        $marketplaceId = $request->get('marketplace', 0);
        if (!$marketplaceId) {
            return new JsonResponse(['error' => 'Marketplace not found'], 400);
        }
        $task->setMarketplace(Marketplace::getById($marketplaceId) ?? null);
        $task->save();
        return $this->redirectToRoute('ozon_task', ['id' => $task->getId()]);
    }

}
