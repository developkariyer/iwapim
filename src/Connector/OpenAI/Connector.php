<?php

namespace App\Connector\OpenAI;

use Exception;
use OpenAI\Client;

class Connector
{
    private Client $client;

    /**
     * @throws Exception
     */
    public function __construct()
    {
        $this->client = new Client($_ENV['OPENAI_SECRET'] ?? null);
    }

    public function getClient(): Client
    {
        return $this->client;
    }

    public function chat(array $messages, $model = 'gpt-4o', $responseFormat = []): string
    {
        $parameters = [
            'model' => $model,
            'messages' => $messages,
        ];
        if (!empty($responseFormat)) {
            $parameters['responseFormat'] = [
                'type' => 'json_schema',
                'json_schema' => [
                    'name' => 'Json Schema',
                    'schema' => $responseFormat,
                ],
                'additional_properties' => false
            ];
        }
        $completion = $this->client->completions()->create($parameters);
        return $completion->choices[0]->text;
    }

}