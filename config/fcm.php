<?php

return [
    'driver'      => env('FCM_PROTOCOL', 'http'),
    'log_enabled' => env('FCM_LOG_ENABLED', false),

    'http' => [
        'server_key'       => env('FCM_SERVER_KEY', 'Your FCM server key'),
        'sender_id'        => env('FCM_SENDER_ID', 'Your sender id'),
        'server_send_url'  => env('FCM_SERVER_SEND_URL', 'https://fcm.googleapis.com/fcm/send'),
        'server_group_url' => env('FCM_SERVER_GROUP_URL', 'https://android.googleapis.com/gcm/notification'),
        'timeout'          => env('FCM_TIMEOUT', 30.0), // in second
    ],
];
