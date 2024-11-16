<?php

namespace App\SlackAi;

use Symfony\Component\Messenger\Handler\MessageHandlerInterface;
use Symfony\Component\HttpClient\HttpClient;
use Psr\Log\LoggerInterface;

class SlackMessageHandler implements MessageHandlerInterface
{
    private LoggerInterface $logger;
    private string $botUserId;

    public function __construct(LoggerInterface $logger)
    {
        $this->logger = $logger;
        $this->botUserId = 'U080PEA1HAR'; 
    }

    public function __invoke(SlackMessage $message): void
    {
        try {
            // Process the message content
            $responseText = $this->processMessage(
                trim(
                    preg_replace(
                        '/<@' . preg_quote($this->botUserId, '/') . '>/', 
                        '', 
                        $message->getText()
                    )
                )
            );

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

            // Send the response back to Slack using the Webhook URL
            $this->sendResponseToSlack($payload);

            $this->logger->info('SlackMessage processed successfully', [
                'text' => $message->getText(),
                'thread_ts' => $message->getThreadTs(),
            ]);
        } catch (\Exception $e) {
            $this->logger->error('Failed to process SlackMessage', [
                'message' => $e->getMessage(),
                'text' => $message->getText(),
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

    private function sendResponseToSlack(array $payload): void
    {
        $httpClient = HttpClient::create();

        // Use predefined Webhook URL from environment variable
        $webhookUrl = $_ENV['SLACK_AI_WEBHOOK_URL'] ?? null;

        if (!$webhookUrl) {
            throw new \RuntimeException('SLACK_AI_WEBHOOK_URL is not defined in environment variables.');
        }

        // Send POST request to Slack Webhook URL
        $response = $httpClient->request('POST', $webhookUrl, [
            'json' => $payload,
        ]);

        // Log HTTP response for debugging
        if ($response->getStatusCode() !== 200) {
            throw new \RuntimeException('Failed to send response to Slack. Status Code: ' . $response->getStatusCode());
        }
    }
}
