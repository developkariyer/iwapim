<?php

namespace App\Utils;

class OpenAIChat {
    private $apiKey;
    private $apiUrl;
    private $previousTranslations = [
        "Strafor = Styrofoam",
        "Ayetel Kürsi = Ayat al-Kursi",
        "Damla Allah ve Muhammed (sav) Lafzı Set = Drop Allah and Muhammad (PBUH) Inscription Set",
        "Ayetel Kürsi Metal 3 Sıralı 4 Parça = Ayat al-Kursi Metal 3 Rows 4 Pieces",
        "Besmele Yatay Sülüs Klasik = Basmala Horizontal Thuluth Classic",
        "Kelime-i Tevhid Uzun Stil = Kelime-i Tevhid Long Style",
        "Maşallah Tebarakallah Yatay = Mashallah Tabarakallah Horizontal",
        "Hadha Min Fadli Yatay (Neml 40) = Hadha Min Fadli Horizontal (An-Naml 40)",
        "İnna Lillahi ve İnna İleyhi raciun = Inna Lillahi wa Inna Ilayhi Raji'un",
        "La Hawla ve La Kuvvete illa Billahil Aliyyul Azim = La Hawla wa La Quwwata illa Billahil 'Aliyyul Azim",
        "Er Rizku Al Allah = Er Rizqu Min Allah",
    ];

    public function __construct($apiKey) {
        $this->apiKey = $apiKey;
        $this->apiUrl = "https://api.openai.com/v1/chat/completions"; // Correct endpoint for chat-based models
    }

    // Function to translate product names and store translations incrementally in Turkish=English format
    public function translateProductName($productName) {
        // System prompt
        $systemPrompt = 'Translate the given Turkish product names into English. For Islamic terms, always transliterate them (e.g., Ayetel Kürsi = Ayat al-Kursi, Er Rizku Al Allah = Ar-Rizqu Min Allah). Do not translate these terms into English. Only return the transliteration of religious terms and translations for the rest of the item name. Respond only with the translation and do not include any additional text or symbols.';

        // Prepare the message array with the system prompt
        $messages = [
            [
                'role' => 'system',
                'content' => $systemPrompt
            ]
        ];

        // Add previous translations to the message array
        foreach ($this->previousTranslations as $previousTranslation) {
            $messages[] = ['role' => 'assistant', 'content' => $previousTranslation];
        }

        // Add the current product name in Turkish=English format
        $messages[] = ['role' => 'user', 'content' => "$productName = "];

        // Prepare request data for GPT-4o mini API
        $postData = [
            'model' => 'gpt-4o-mini', // Use GPT-4o mini model
            'messages' => $messages,
            'max_tokens' => 150,
            'temperature' => 0.7
        ];

        // Send the API request
        $response = $this->sendRequest($postData);

        // Debug the API response

        // Check if the response contains the expected translation
        if (isset($response['choices'][0]['message']['content'])) {
            $translation = trim($response['choices'][0]['message']['content']);

            // Store the new translation in Turkish=English format
            $this->previousTranslations[] = "$productName = $translation";
            $this->previousTranslations = array_unique($this->previousTranslations);

            usleep(500000);
            return $translation;
        }
        echo "<pre>";
        print_r($response); // Output the full response for debugging
        echo "</pre>";
        throw new \Exception("Error: Unable to process the product name.");
    }

    // Function to send the API request
    private function sendRequest($postData) {
        $ch = curl_init($this->apiUrl);

        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Content-Type: application/json',
            'Authorization: ' . 'Bearer ' . $this->apiKey
        ]);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($postData));

        $response = curl_exec($ch);
        curl_close($ch);

        return json_decode($response, true);
    }

    // Optional: Reset the stored translations (e.g., between sessions)
    public function resetTranslations() {
        $this->previousTranslations = [];
    }
}