<?php

namespace App\Helpers;
use App\Social\SocialConfig;

use GuzzleHttp\Exception\RequestException;
use Illuminate\Support\Facades\Http;

class SocialHelper
{
    public static function httpGetRequest($url)
    {

        $response = Http::get($url);

        // Check if request was successful
        if ($response->successful()) {
            return $response->json();
        }

        return null; // Or throw an exception, log an error, etc.
    }

    public static function httpPostRequest($url, $post_params)
    {
        try {
            $response = Http::asForm()->post($url, $post_params);

            if ($response->successful()) {
                return $response->body();
            } else {
                return 'Error: '.$response->status();
            }
        } catch (RequestException $e) {
            return 'Request failed: '.$e->getMessage();
        }
    }

    public static function getSocialConfig($respToken)
    {
        return SocialConfig::where('id', $respToken)->first();
    }
}
