<?php

namespace App\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Messenger\MessageBusInterface;
use App\SlackAi\SlackMessage;

class SlackAi
{
    private MessageBusInterface $messageBus;

    public function __construct(MessageBusInterface $messageBus)
    {
        $this->messageBus = $messageBus;
    }

    /**
     * @Route("/iwai", name="iwai", methods={"POST"})
     */
    public function iwaiAction(Request $request): Response
    {
        $content = json_decode($request->getContent(), true);

        if (isset($content['type']) && $content['type'] === 'url_verification') {
            return new Response($content['challenge'], Response::HTTP_OK, ['Content-Type' => 'text/plain']);
        }

        if (!isset($content['text']) || !isset($content['response_url'])) {
            return new Response('Invalid Slack payload', Response::HTTP_BAD_REQUEST);
        }

        $incomingText = $content['text'];
        $responseUrl = $content['response_url'];
        $threadTs = $content['thread_ts'] ?? null;

        $this->messageBus->dispatch(new SlackMessage($incomingText, $responseUrl, $threadTs));

        return new Response('Message received and queued for processing', Response::HTTP_OK);
    }
}
