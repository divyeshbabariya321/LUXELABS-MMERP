<?php

namespace App\Helpers;
use App\Helpers;
use GuzzleHttp\Client;

class TranslationLambdaHelper
{

    private $baseUrl;
    private $client;

    public function __construct() {
        $this->baseUrl = config('settings.translation_lambda_api_url');
        $this->client = new Client([
            'base_uri' => $this->baseUrl,
        ]);
    }

    public function translate($text, $translationLangArr, $type = "translation")
    {
        try {
            $res = $this->client->post('translation_lambda', [
                'json' => [
                    'text' => $text,
                    'translationLanguage' => $translationLangArr,
                    'type' => $type,
                ],
            ]);
            $response = json_decode($res->getBody()->getContents(), true);
            
            if (!empty($response) && !empty($response['response'])) {
                return $response['response'];
            } else {
                return [];
            }
        } catch (\Throwable $th) {
            return [];
        }
        
    }

    public function translateWithScore($text, $translationLangArr, $type = "Both")
    {
        try {
            $res = $this->client->post('translation_lambda', [
                'json' => [
                    'text' => $text,
                    'translationLanguage' => $translationLangArr,
                    'type' => $type,
                ],
            ]);
            $response = json_decode($res->getBody()->getContents(), true);
    
            if (!empty($response) && !empty($response['response'])) {
                return $response['response'];
            } else {
                return [];
            }
        } catch (\Throwable $th) {
            return [];
        }
        
    }

    public function getTranslateScore($text, $translatedText, $type = "bleuScore")
    {
        try {
            $res = $this->client->post('translation_lambda', [
                'json' => [
                    'text' => $text,
                    'translatedText' => $translatedText,
                    'type' => $type,
                ],
            ]);
            $response = json_decode($res->getBody()->getContents(), true);
            
            if (!empty($response) && !empty($response['response']) && !empty($response['response']['bleuScore'])) {
                return number_format($response['response']['bleuScore'], 2);
            } else {
                return 0;
            }
        } catch (\Throwable $th) {
            return 0;
        }
        
    }
}
