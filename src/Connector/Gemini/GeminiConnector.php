<?php
namespace App\Connector\Gemini;
use Symfony\Component\HttpClient\HttpClient;


class GeminiConnector
{
    private $geminiApiKey;
    private $httpClient;

    //gemini-2.0-flash
    //gemini-2.5-flash-preview-04-17
    public static function chat($message, $model='gemini-2.5-flash-preview-04-17')
    {
        try {
            $geminiApiKey = $_ENV['GEMINI_API_KEY'];
            $httpClient = HttpClient::create();
            $url = "https://generativelanguage.googleapis.com/v1beta/models/" . $model . ":generateContent?key=" . $geminiApiKey;
            $response = $httpClient->request('POST', $url, [
                'headers' => [
                    'Content-Type' => 'application/json',
                ],
                'json' => [
                    'contents' => [
                        [
                            'parts' => [
                                ['text' => $message]
                            ]
                        ]
                    ]
                ],
            ]);

            if ($response->getStatusCode() === 200) {
                return $response->toArray();
            } else {
                throw new \Exception("API Error: " . $response->getStatusCode() . " " . $response->getContent(false));
            }
        } catch (\Exception $e) {
            echo "Hata: " . $e->getMessage();
            return null;
        }
    }
}