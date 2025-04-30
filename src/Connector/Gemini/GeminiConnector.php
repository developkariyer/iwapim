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
            /*$response = $httpClient->request('POST', $url, [
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
            ]);*/

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
                    ],
                    'generationConfig' => [
                        'temperature'      => 0.0,
                        'topP'             => 1.0,
                        'candidateCount'   => 1,
                        'stopSequences'    => ["\n\n"],
                        'presencePenalty'  => 0.0,
                        'frequencyPenalty' => 0.0,
                        'responseSchema'   => json_encode([
                            'type' => 'object',
                            'patternProperties' => [
                                '^[A-Za-z0-9_-]+$' => [
                                    'type' => 'object',
                                    'properties' => [
                                        'productName'    => ['type'=>'string'],
                                        'mainProductCode'=> ['type'=>'string'],
                                        'stockCode'      => ['type'=>'string'],
                                        'description'    => ['type'=>'string'],
                                        'images'         => [
                                            'type'=>'array',
                                            'maxItems'=>5,
                                            'items'=>['type'=>'string','pattern'=>'^http?://']
                                        ],
                                        'salesPrice'     => ['type'=>'number'],
                                        'categoryId'     => ['type'=>'integer'],
                                        'renk'           => ['type'=>'string'],
                                        'ebat'           => ['type'=>'string']
                                    ],
                                    'required'=>['productName','mainProductCode','stockCode','description','images','salesPrice','categoryId','renk','ebat']
                                ]
                            ]
                        ]),
                        'nullOnViolation'  => true
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