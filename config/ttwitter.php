<?php

// You can find the keys here : https://apps.twitter.com/

return [
    'debug' => function_exists('env') ? env('APP_DEBUG', false) : false,

    'API_URL'           => env('TWITTER_API_URL', 'api.twitter.com'),
    'UPLOAD_URL'        => env('TWITTER_UPLOAD_URL', 'upload.twitter.com'),
    'API_VERSION'       => env('TWITTER_API_VERSION', '1.1'),
    'AUTHENTICATE_URL'  => env('TWITTER_AUTHENTICATE_URL', 'https://api.twitter.com/oauth/authenticate'),
    'AUTHORIZE_URL'     => env('TWITTER_AUTHORIZE_URL', 'https://api.twitter.com/oauth/authorize'),
    'ACCESS_TOKEN_URL'  => env('TWITTER_ACCESS_TOKEN_URL', 'https://api.twitter.com/oauth/access_token'),
    'REQUEST_TOKEN_URL' => env('TWITTER_REQUEST_TOKEN_URL', 'https://api.twitter.com/oauth/request_token'),
    'USE_SSL'           => env('TWITTER_USE_SSL', true),

    'CONSUMER_KEY'        => function_exists('env') ? env('TWITTER_CONSUMER_KEY', '') : '',
    'CONSUMER_SECRET'     => function_exists('env') ? env('TWITTER_CONSUMER_SECRET', '') : '',
    'ACCESS_TOKEN'        => function_exists('env') ? env('TWITTER_ACCESS_TOKEN', '') : '',
    'ACCESS_TOKEN_SECRET' => function_exists('env') ? env('TWITTER_ACCESS_TOKEN_SECRET', '') : '',
];
