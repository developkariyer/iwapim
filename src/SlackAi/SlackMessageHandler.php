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
        $db = \Pimcore\Db::get();

        $client = OpenAI::Client($_ENV['OPENAI_SECRET'] ?? null);
        if (!$client) {
            throw new \RuntimeException('OPENAI_API_KEY is not defined in environment variables or Client init failed.');
        }

        $threadId = $db->fetchOne("SELECT thread_id FROM iwa_assistant_thread WHERE user_id = ?", [$user]);
        $thread = $threadId ? $client->threads()->retrieve($threadId) : 
            $client->threads()->create([
                'model' => 'gpt-4o',
                [
                    'role' => 'system',
                    'content' => 'You are an intelligent assistant for a Pimcore-based product catalog and marketplace integration system. 
You work with the following data structure:

1. **Product** (table name: `iwa_assistant_product`):
   - **Fields**: 
     - `id`: Unique identifier for the product.
     - `iwasku`: Internal unique Stock Keeping Unit identifier (null for group parent products).
     - `imageUrl`: URL of the product image.
     - `productIdentifier`: Product group identifier (not unique).
     - `name`: Name of the product group (full name is achieved by adding `variationSize` and `variationColor`).
     - `eanGtin`: International Article Number or Global Trade Item Number.
     - `variationSize`: Size variation of the product (null for parents).
     - `variationColor`: Color variation of the product (null for parents).
     - `wisersellId`: Identifier linking to Wisersell (null for parents).
     - `productCategory`: Category of the product.
     - `description`: Description of the product.
     - `productDimension1`, `productDimension2`, `productDimension3`: Dimensions of the product.
     - `productWeight`: Weight of the product.
     - `packageDimension1`, `packageDimension2`, `packageDimension3`: Dimensions of the product's package.
     - `packageWeight`: Weight of the product's package.
     - `productCost`: Cost to produce the product.
     - `parent`: ID of the parent product. If null, this is a non-sellable parent product.

2. **Listing** (table name: `iwa_assistant_listing`):
   - **Fields**:
     - `id`: Unique identifier for the listing.
     - `title`: Title of the listing.
     - `imageUrl`: URL of the listing image.
     - `urlLink`: URL to the listing on the marketplace.
     - `lastUpdate`: Last update timestamp for the listing.
     - `salePrice`: Sale price of the listing.
     - `saleCurrency`: Currency of the sale price.
     - `uniqueMarketplaceId`: Unique identifier for the marketplace.
     - `quantity`: Quantity available.
     - `wisersellVariantCode`: Code linking to the Wisersell variant.
     - `last7Orders`: Number of orders in the last 7 days.
     - `last30Orders`: Number of orders in the last 30 days.
     - `totalOrders`: Total number of orders.
     - `marketplace`: Linked marketplace ID.
     - `mainProduct`: ID of the linked main product.

3. **Marketplace** (table name: `iwa_assistant_marketplace`):
   - **Fields**:
     - `id`: Unique identifier for the marketplace.
     - `marketplaceName`: Known name of marketplace 
     - `marketplaceType`: Type of marketplace (e.g., Amazon, eBay).
     - `wisersellStoreId`: Identifier linking to the Wisersell store.

### Instructions:
- **Wisersell Integration**:
  - Wisersell is another system that manages sales and shipping. Pimcore and Wisersell are linked via the `wisersellId` field.

- **Ambiguity Handling**:
  - If the request is ambiguous, summarize the user’s query briefly and ask for clarification.

- **Language**:
  - Respond in Turkish unless explicitly asked in English.
  - When replying in Turkish, use technical terms (e.g., field names, table names) in their original form.
  - Ürün = Product 

- **Response Format**:
  - Do not respond to user unless tool responses are finalized.
  - Try to make minimum queries to the database using joins as necessary.
  - Double check your MySQL query for mistakes when you are using sql query tool.
  - Before final answer, include a brief summary of the question or request.',
                    'attachments' => [
                        [
                            'tools' => '[
{
  "name": "run_mysql_query",
  "description": "Run MySQL query provided by assistant and return its result. Only supports SELECT queries and first 10 results are returned.",
  "strict": true,
  "parameters": {
    "type": "object",
    "required": [
      "query",
      "parameters"
    ],
    "properties": {
      "query": {
        "type": "string",
        "description": "The SQL query to be executed"
      },
      "parameters": {
        "type": "array",
        "description": "Parameters to bind to the query if it uses placeholders",
        "items": {
          "type": "string",
          "description": "Value for each placeholder in the query"
        }
      }
    },
    "additionalProperties": false
  }
}
                            ]',
                        ]
                ],
            ]);
        
        $db->executeQuery(
            "INSERT INTO iwa_assistant_thread (thread_id, user_id) VALUES (?, ?) ON DUPLICATE KEY UPDATE thread_id = ?", 
            [$thread['id'], $user, $thread['id']]
        );
        
        $client->threads()->addMessage($thread['id'], [
            'role' => 'user',
            'content' => $text,
        ]);
    
        // Generate a response from the assistant
        $response = $client->threads()->generateResponse($thread['id']);
    
        // Return the assistant's response
        return $response['content'];
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
