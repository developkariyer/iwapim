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
                'headers' => ['Content-Type' => 'application/json'],
                'json' => [
                    'contents' => [[
                        'parts' => [['text' => $message]]
                    ]],
                    'generationConfig' => [
                        'responseMimeType' => 'application/json',
                        'temperature'      => 0.0,

                        'responseSchema'   => [
                            'type'            => 'object',
                            'properties'      => [
                            ],
                            'patternProperties'=> [
                                '^[A-Za-z0-9_-]+$' => [
                                    'type'       => 'object',
                                    'properties' => [
                                        'productName'     => ['type'=>'string','nullable'=>false],
                                        'mainProductCode' => ['type'=>'string','nullable'=>false],
                                        'stockCode'       => ['type'=>'string','nullable'=>false],
                                        'description'     => ['type'=>'string','nullable'=>true],
                                        'images'          => [
                                            'type'=>'array',
                                            'minItems'=>0,
                                            'maxItems'=>5,
                                            'items'=>['type'=>'string','pattern'=>'^https?://'],
                                            'nullable'=>false
                                        ],
                                        'salesPrice'      => ['type'=>'number','nullable'=>false],
                                        'categoryId'      => ['type'=>'integer','nullable'=>false],
                                        'renk'            => ['type'=>'string','nullable'=>true],
                                        'ebat'            => ['type'=>'string','nullable'=>true],
                                    ],
                                    'required'=>[
                                        'productName','mainProductCode','stockCode',
                                        'description','images','salesPrice','categoryId','renk','ebat'
                                    ]
                                ]
                            ]
                        ],
                    ],
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