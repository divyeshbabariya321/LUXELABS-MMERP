<?php

namespace App\Helpers;

use GuzzleHttp\Client;
use GuzzleHttp\RequestOptions;
use Illuminate\Support\Facades\Http;
use Exception;

class GuzzleHelper
{
    public static function post(string $url, array $body, array $headers)
    {
        try {
            $response = Http::withHeaders($headers)->post($url, $body);
            return $response->json();
        } catch (Exception $e) {
            return $e->getMessage();
        }
    }

    public static function get(string $url, array $headers)
    {
        try {
            $response = Http::withHeaders($headers)->get($url);
            return $response->json();
        } catch (Exception $e) {
            return $e->getMessage();
        }
    }

    public static function patch(string $url, array $body, array $headers)
    {
        try {
            $response = Http::withHeaders($headers)->patch($url, $body);
            return $response->json();
        } catch (Exception $e) {
            return $e->getMessage();
        }
    }
}
