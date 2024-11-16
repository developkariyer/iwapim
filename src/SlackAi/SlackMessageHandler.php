<?php

namespace App\SlackAi;

use Symfony\Component\Messenger\Handler\MessageHandlerInterface;
use Symfony\Component\HttpClient\HttpClient;
use Psr\Log\LoggerInterface;

class SlackMessageHandler implements MessageHandlerInterface
{
    private LoggerInterface $logger;

    public function __construct(LoggerInterface $logger)
    {
        $this->logger = $logger;
    }

    public function __invoke(SlackMessage $message): void
    {
        try {
            // Process the message content
            $responseText = $this->processMessage($message->getText());

            // Prepare payload for Slack response
            $payload = [
                'text' => $responseText,
                'response_type' => 'in_channel', // Makes the response visible to everyone
                'replace_original' => false,    // Avoids overwriting the original message
            ];

            // Add thread_ts if available
            if ($message->getThreadTs()) {
                $payload['thread_ts'] = $message->getThreadTs();
            }

            // Send the response back to Slack
            $this->sendResponseToSlack($message->getResponseUrl(), $payload);

            $this->logger->info('SlackMessage processed successfully', [
                'text' => $message->getText(),
                'response_url' => $message->getResponseUrl(),
                'thread_ts' => $message->getThreadTs(),
            ]);
        } catch (\Exception $e) {
            $this->logger->error('Failed to process SlackMessage', [
                'message' => $e->getMessage(),
                'text' => $message->getText(),
                'response_url' => $message->getResponseUrl(),
            ]);
        }
    }

    private function processMessage(string $text): string
    {
        // Simulate processing logic (e.g., OpenAI API call)
        // Replace this with actual business logic
        if (strtolower(trim($text)) === 'hello') {
            return 'Hello! How can I assist you today?';
        }

        return "I received your message: $text";
    }

    private function sendResponseToSlack(string $responseUrl, array $payload): void
    {
        $httpClient = HttpClient::create();

        // Send POST request to Slack's response_url
        $response = $httpClient->request('POST', $responseUrl, [
            'json' => $payload,
        ]);

        // Log HTTP response for debugging
        if ($response->getStatusCode() !== 200) {
            throw new \RuntimeException('Failed to send response to Slack. Status Code: ' . $response->getStatusCode());
        }
    }
}
