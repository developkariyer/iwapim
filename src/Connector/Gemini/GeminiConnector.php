<?php
namespace App\Connector\Gemini;
use Symfony\Component\HttpClient\HttpClient;

/*
 * MODELS
 * gemini-2.0-flash (LAST REQUEST 404 ERROR)
 * gemini-2.5-flash-preview-04-17
 * */
class GeminiConnector
{
//    private static array $ciceksepetiGenerationConfig  = [
//        'responseMimeType' => 'application/json',
//        'temperature'      => 0.0,
//        'topP'             => 1.0,
//        'candidateCount'   => 1,
//        'stopSequences'    => ["\n\n"],
//        'presencePenalty'  => 0.0,
//        'frequencyPenalty' => 0.0,
//        'responseSchema'   => [
//            'type'  => 'ARRAY',
//            'items' => [
//                'type'       => 'OBJECT',
//                'properties' => [
//                    'productName'     => ['type'=>'STRING'],
//                    'mainProductCode' => ['type'=>'STRING'],
//                    'stockCode'       => ['type'=>'STRING'],
//                    'description'     => ['type'=>'STRING'],
//                    'images'          => [
//                        'type'     => 'ARRAY',
//                        'minItems' => 0,
//                        'maxItems' => 5,
//                        'items'    => ['type'=>'STRING']
//                    ],
//                    'salesPrice'      => ['type'=>'NUMBER'],
//                    'categoryId'      => ['type'=>'INTEGER'],
//                    'deliveryMessageType' => ['type'=>'INTEGER'],
//                    'deliveryType' => ['type'=>'INTEGER'],
//                    'stockQuantity' => ['type'=>'INTEGER'],
//                    'renk'            => ['type'=>'STRING'],
//                    'ebat'            => ['type'=>'STRING'],
//                ],
//                'required'=>[
//                    'productName','mainProductCode','stockCode',
//                    'description','images','salesPrice','categoryId','renk','ebat'
//                ]
//            ]
//        ]
//    ];

    private static array $ciceksepetiGenerationConfig  = [
        'responseMimeType' => 'application/json',
        'temperature'      => 0.0,
        'topP'             => 1.0,
        'candidateCount'   => 1,
        'stopSequences'    => ["\n\n"],
        'presencePenalty'  => 0.0,
        'frequencyPenalty' => 0.0,
        'responseSchema'   => [
            'type'  => 'ARRAY',
            'items' => [
                'type'       => 'OBJECT',
                'properties' => [
                    'stockCode'       => ['type'=>'STRING'],
                    'categoryId'      => ['type'=>'INTEGER'],
                    'color'            => ['type'=>'STRING']
                ],
                'required'=>[
                    'stockCode', 'categoryId','color','size'
                ]
            ]
        ]
    ];

    public static function chat($message, string $platform = 'default', $model='gemini-2.5-flash-preview-04-17', $maxRetries = 3, $initialDelay  = 30)
    {
        $geminiApiKey = $_ENV['GEMINI_API_KEY'];
        $httpClient = HttpClient::create();
        $url = "https://generativelanguage.googleapis.com/v1beta/models/" . $model . ":generateContent?key=" . $geminiApiKey;
        $generationConfig = match ($platform) {
            'ciceksepeti' => self::$ciceksepetiGenerationConfig,
            default       => null,
        };
        $payload = [
            'headers' => ['Content-Type' => 'application/json'],
            'json' => [
                'contents' => [[ 'parts' => [['text' => $message]]]],
                'generationConfig' => $generationConfig,
            ]
        ];
       $delay = $initialDelay;
        for ($attempt = 1; $attempt <= $maxRetries; $attempt++) {
            try {
                $response = $httpClient->request('POST', $url, $payload);
                if ($response->getStatusCode() === 200) {
                    return $response->toArray();
                } else {
                    throw new \Exception("API Error: " . $response->getStatusCode() . " " . $response->getContent(false));
                }
            } catch (\Exception $e) {
                if ($attempt < $maxRetries) {
                    sleep($delay);
                    $delay *= 2;
                } else {
                    echo "Error after {$maxRetries} attempts: " . $e->getMessage();
                    return null;
                }
            }
        }
    }

}