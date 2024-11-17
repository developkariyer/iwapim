<?php

namespace App\SlackAi;

use Symfony\Component\Messenger\Handler\MessageHandlerInterface;
use Symfony\Component\HttpClient\HttpClient;
use Psr\Log\LoggerInterface;
use OpenAI;

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
                ),
                $message->getUser()
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

    private function processMessage(string $text, string $user): string
    {
        $client = OpenAI::Client($_ENV['OPENAI_SECRET'] ?? null);
        if (!$client) {
            throw new \RuntimeException('OPENAI_API_KEY is not defined in environment variables or Client init failed.');
        }
        error_log("OpenAI client initialized successfully.");
        $runResponse = $client->threads()->createAndRun([
            'assistant_id' => $_ENV['OPENAI_ASSISTANT_ID'] ?? null,
            'thread' => [
                'messages' => [
                    [
                        'role' => 'user',
                        'content' => $text,
                    ],
                ],
            ],
        ]);
        $threadId = $runResponse->threadId;
        error_log("Assistant run created successfully: {$runResponse->id}");

        $responseContent = "";
        do {
            $running = false;
            while (!in_array($runResponse->status, ['requires_action', 'completed', 'failed', 'cancelled', 'expired'])) {
                sleep(1);
                $runResponse = $client->threads()->runs()->retrieve($threadId, $runResponse->id);
            }
            $tool_outputs = [];
            $runStepList = $client->threads()->runs()->steps()->list($threadId, $runResponse->id);
            error_log("Assistant run steps fetched successfully.");
            $responseContent = null;
            foreach ($runStepList->data as $step) {
                if ($step->stepDetails->type === 'message_creation') {
                    error_log("Assistant response step found: {$step->stepDetails->messageCreation->messageId}");
                    $messageId = $step->stepDetails->messageCreation->messageId;
                    $assistantMessage = $client->threads()->messages()->retrieve($threadId, $messageId);
                    $responseContent .= "\n".$assistantMessage->content[0]->text->value;
                } elseif ($step->stepDetails->type === 'tool_calls') {
                    error_log("Function call detected.");
                    $functionCallDetails = $step->stepDetails->tool_calls->functionCall;
                    $callId = $functionCallDetails->id;
                    $functionName = $functionCallDetails->function->name;
                    $arguments = $functionCallDetails->function->arguments;
                    error_log("Function Name: {$functionName}");
                    error_log("Function Arguments: " . json_encode($arguments));
                    $functionResult = $this->executeFunction($functionName, $arguments);
                    $tool_outputs[] = [
                        'tool_call_id' => $callId,
                        'output' => $functionResult,
                    ];
                    $running = true;
                }
            }
            if ($running) {
                $client->threads()->runs()->submitToolOutputs($threadId, $runResponse->id, ['tool_outputs' => $tool_outputs]);
                error_log("Tool outputs submitted successfully.");
            }
        } while (!$running);
        $client->threads()->delete($threadId);
        return $responseContent ?? "Hüstın bir sorun var...";
    }

    private function executeFunction(string $functionName, array $arguments): string
    {
        return match($functionName) {
            'run_mysql_query' => $this->runMysqlQuery($arguments),
            default => "Function not found: {$functionName}",
        };
    }

    private function runMysqlQuery($arguments)
    {
        $db = \Pimcore\Db::get();
        $query = trim($arguments['query']);
        $params = $arguments['params'];
    
        // Ensure the query starts with SELECT
        if (!preg_match('/^SELECT\s/i', $query)) {
            throw new \InvalidArgumentException('Only SELECT queries are allowed.');
        }
    
        // Check if the query has a LIMIT clause
        if (!preg_match('/\bLIMIT\b/i', $query)) {
            // Append LIMIT 10 if not present
            $query = rtrim($query, ';') . ' LIMIT 10';
        }
    
        // Execute the query
        $result = $db->fetchAll($query, $params);
    
        return json_encode($result);
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
