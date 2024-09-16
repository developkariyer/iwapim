<?php

namespace App\Utils;

class OpenAIChat {
    private $apiKey;
    private $apiUrl;

    public function __construct($apiKey) {
        $this->apiKey = $apiKey;
        $this->apiUrl = "https://api.openai.com/v1/chat/completions"; // Correct chat-based endpoint
    }

    public function translateProductName($productName) {
        // Prepare request data for GPT-4o mini API (chat model)
        $postData = [
            'model' => 'gpt-4o-mini', // Use GPT-4o mini model
            'messages' => [
                [
                    'role' => 'system', 
                    'content' => 'The following is a product name from our catalog. We sell wall art. Translate the given item name to English, but if the name contains Islamic terms (e.g., Ayetel Kürsi, Bismillah), transliterate them to the most common English form (e.g., Ayat al-Kursi, Bismillah). Translate the rest of the item name, such as the number of pieces, types, etc. Respond only with the translation, without any additional text or formatting.'
                ],
                [
                    'role' => 'user', 
                    'content' => $productName
                ],
            ],
            'max_tokens' => 150,
            'temperature' => 0.7
        ];
    
        // Send the API request
        $response = $this->sendRequest($postData);
    
        // Check if the response contains the expected text
        if (isset($response['choices'][0]['message']['content'])) {
            return trim($response['choices'][0]['message']['content']);
        }
    
        // Return an error message if the response is not as expected
        return "Error: Unable to process the product name.";
    }
    

    private function sendRequest($postData) {
        $ch = curl_init($this->apiUrl);

        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Content-Type: application/json',
            'Authorization: Bearer ' . $this->apiKey
        ]);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($postData));

        $response = curl_exec($ch);
        curl_close($ch);

        return json_decode($response, true);
    }
}
