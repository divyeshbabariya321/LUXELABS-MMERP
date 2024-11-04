<?php

namespace App\Http\Controllers\ChatGPT;

use App\LogRequest;
use CURLFile;
use Exception;

class ChatGPTService
{
    private $api_key = '';

    private $base_api = 'https://api.openai.com/v1/';

    public function __construct()
    {
        $this->api_key = config('settings.chat_gpt_api_key');
    }

    /**
     * Creates a completion for the provided prompt and parameters.
     *
     * @param  mixed  $prompt
     * @param  mixed  $temperature
     * @param  mixed  $max_token
     * @param  mixed  $number
     * @param  mixed  $model
     */
    public function getCompletions($prompt, $temperature, $max_token, $number, $model = 'text-davinci-003'): array
    {
        $params = [
            'model' => $model,
            'prompt' => $prompt,
            'temperature' => $temperature, //0.7,
            'max_tokens' => (int) $max_token, //1024
            'n' => (int) $number,
        ];

        return $this->callApi('POST', 'completions', $params);
    }

    /**
     * Lists the currently available models
     *
     * @param  null  $modalId
     */
    public function getModels($modalId = null): array
    {
        return $this->callApi('GET', 'models'.($modalId ? "/$modalId" : ''));
    }

    /**
     * Creates a new edit for the provided input, instruction, and parameters.
     *
     * @param  mixed  $input
     * @param  mixed  $instruction
     * @param  mixed  $number
     * @param  mixed  $temperature
     * @param  mixed  $model
     */
    public function performEdit($input, $instruction, $number, $temperature = 0.7, $model = 'text-davinci-edit-001'): array
    {
        $params = [
            'model' => $model, // code-davinci-edit-001
            'input' => $input,
            'instruction' => $instruction,
            'n' => $number,
            'temperature' => $temperature,
        ];

        return $this->callApi('POST', 'edits', $params);
    }

    /**
     * Creates an image given a prompt.
     *
     * @param  mixed  $prompt
     * @param  mixed  $number_of_image
     * @param  mixed  $size
     */
    public function generateImage($prompt, $number_of_image, $size): array
    {
        $params = [
            'prompt' => $prompt,
            'n' => (int) $number_of_image, // MAX 10
            'size' => $size, //256x256, 512x512, or 1024x1024,
            'response_format' => 'url', // b64_json,
        ];

        return $this->callApi('POST', 'images/generations', $params);
    }

    /**
     * Creates an edited or extended image given an original image and a prompt.
     *
     * @param  mixed  $image
     * @param  mixed  $mask
     * @param  mixed  $prompt
     * @param  mixed  $number
     * @param  mixed  $size
     */
    public function editGeneratedImage($image, $mask, $prompt, $number, $size): array
    {
        $params = [
            'image' => new CURLFILE($image['tmp_name']),
            'mask' => new CURLFILE($mask['tmp_name']),
            'prompt' => $prompt,
            'n' => $number, // MAX 10
            'size' => $size, //256x256, 512x512, or 1024x1024,
            'response_format' => 'url', // b64_json,
        ];

        return $this->callApi('POST', 'images/edits', $params);
    }

    /**
     * Creates a variation of a given image.
     *
     * @param  mixed  $image
     * @param  mixed  $number
     * @param  mixed  $size
     */
    public function generateImageVariation($image, $number, $size = '1024x1024'): array
    {
        $params = [
            'image' => new CURLFILE($image['tmp_name']),
            'n' => $number, // MAX 10
            'size' => $size, //256x256, 512x512, or 1024x1024,
            'response_format' => 'url', // b64_json,
        ];

        return $this->callApi('POST', 'images/variations', $params);
    }

    /**
     * Classifies if text violates OpenAI's Content Policy
     *
     * @param  mixed  $input
     * @param  mixed  $modal
     */
    public function identifyModeration($input, $modal = 'text-moderation-stable'): array
    {
        $params = [
            'input' => $input,
            'model' => $modal, //"text-moderation-stable" // text-moderation-latest
        ];

        return $this->callApi('POST', 'moderations', $params);
    }

    /**
     * Common function to fetch data from API using CURL.
     *
     * @param  mixed  $method
     * @param  mixed  $url
     */
    public function callApi($method, $url, array $params = []): array
    {
        $header = ['Authorization: Bearer '.$this->api_key];
        if (isset($params['image'], $params)) {
            array_push($header, 'Content-Type: multipart/form-data');
        } else {
            array_push($header, 'Content-Type: application/json');
        }
        $startTime = date('Y-m-d H:i:s', LARAVEL_START);
        $curl = curl_init();
        curl_setopt_array($curl, [
            CURLOPT_URL => $this->base_api.$url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 30,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => $method,
            CURLOPT_HTTPHEADER => $header,
        ]);

        if (($method == 'POST' || $method == 'PATCH' || $method == 'PUT') && ! isset($params['image'], $params)) {
            curl_setopt($curl, CURLOPT_POSTFIELDS, json_encode($params));
        } else {
            curl_setopt($curl, CURLOPT_POSTFIELDS, $params);
        }
        $response = curl_exec($curl);
        $httpcode = curl_getinfo($curl, CURLINFO_HTTP_CODE);
        $parameters = [];
        LogRequest::log($startTime, $url, 'GET', json_encode($parameters), json_decode($response), $httpcode, ChatGPTService::class, 'callApi');
        curl_close($curl);
        $err = curl_error($curl);
        curl_close($curl);
        if ($err) {
            return ['status' => false, 'message' => 'cURL Error #:'.$err];
        } else {
            $response = json_decode($response, true);
            if (is_array($response)) {
                return ['status' => true, 'message' => 'Data found', 'data' => $response];
            } else {
                if ($method == 'DELETE') {
                    return ['status' => true, 'message' => 'Data found', 'data' => $response];
                } else {
                    return ['status' => false, 'message' => 'cURL Error #:'.$response];
                }
            }
        }
    }

    public function dataUnserialize($string)
    {
        try {
            $string = @unserialize($string);

            return $string;
        } catch (Exception $e) {
            return $string;
        }
    }
}
