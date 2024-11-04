<?php

namespace App\Helpers;
use App\MessagingGroupCustomer;
use App\Helpers;

use Illuminate\Support\Facades\Http;

class TwilioHelper
{
    public static function httpGetRequest($url, $sid, $token)
    {
        $response = Http::withBasicAuth($sid, $token)->get($url);
        if ($response->successful()) {
            return $response->body();
        }

        return 'Error: '.$response->status();
    }

    public static function httpPostRequest($url, $post_params, $sid, $token)
    {
        $response = Http::withBasicAuth($sid, $token)
            ->post($url, $post_params);
        // Check if request was successful
        if ($response->successful()) {
            return $response->body();
        }

        return 'Error: '.$response->status();
    }

    public static function getMessageGroupCountByMessageGroupId($messageGroupId)
    {
        return MessagingGroupCustomer::where('message_group_id', $messageGroupId)->count();
    }
}
