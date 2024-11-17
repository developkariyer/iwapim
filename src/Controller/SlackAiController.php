<?php

namespace App\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Messenger\MessageBusInterface;
use App\SlackAi\SlackMessage;

class SlackAiController
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

        // Log the payload for debugging
        error_log('Payload received: ' . print_r($content, true));

        // Handle Slack URL Verification Challenge
        if (isset($content['type']) && $content['type'] === 'url_verification') {
            return new Response($content['challenge'], Response::HTTP_OK, ['Content-Type' => 'text/plain']);
        }

        // Validate payload structure
        if (!isset($content['event']['text']) || !isset($content['event']['channel'])) {
            error_log('Invalid payload structure: ' . json_encode($content));
            return new Response('Invalid Slack payload', Response::HTTP_BAD_REQUEST);
        }

        // Extract necessary data
        $incomingText = $content['event']['text'];
        $channel = $content['event']['channel'];
        $user = $content['event']['user'];
        //$responseUrl = $content['response_url'] ?? null; // Note: response_url might not always be present in this payload
        $threadTs = $content['event']['thread_ts'] ?? $content['event']['ts'] ?? null;

        // Dispatch the message to the queue
        $this->messageBus->dispatch(new SlackMessage($incomingText, $channel, $user, $threadTs));
        error_log('Message dispatched to queue: ' . json_encode([
            'text' => $incomingText,
            'channel' => $channel,
            'user' => $user,
            'thread_ts' => $threadTs,
        ]));

        // Return an immediate response
        return new Response('Message received and queued for processing', Response::HTTP_OK);
    }
}
