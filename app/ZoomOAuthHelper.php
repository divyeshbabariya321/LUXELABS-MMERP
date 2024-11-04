<?php

namespace App;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class ZoomOAuthHelper
{
    public static function getAccessToken()
    {
        $clientId = config('services.zoom.client_id');
        $clientSecret = config('services.zoom.client_secret');
        $accountId = config('services.zoom.account_id');

        Log::info('ZOOM client_id -->'.$clientId);
        Log::info('ZOOM clientSecret -->'.$clientSecret);
        Log::info('ZOOM accountId -->'.$accountId);

        $response = Http::withBasicAuth($clientId, $clientSecret)
            ->asForm()
            ->post('https://zoom.us/oauth/token', [
                'grant_type' => 'account_credentials',
                'account_id' => $accountId,
            ]);

        if ($response->successful()) {
            $tokenResponse = $response->json();

            // Handle the token response as needed
            return $tokenResponse;
        } else {
            // Log and handle errors
            $errorResponse = $response->json();
            // Log the error message and code
            Log::error('Zoom OAuth Error: '.$errorResponse['error']);
            Log::error('Zoom OAuth Error Description: '.$errorResponse['error_description']);

            // Handle the error in an appropriate way (e.g., return an error response).
            return $errorResponse;
        }
    }
}
