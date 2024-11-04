<?php

/*
 * This file is part of Laravel Pusher.
 *
 * (c) Pusher, Ltd (https://pusher.com)
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

return [

    /*
    |--------------------------------------------------------------------------
    | Default Connection Name
    |--------------------------------------------------------------------------
    |
    | Here you may specify which of the connections below you wish to use as
    | your default connection for all work. Of course, you may use many
    | connections at once using the manager class.
    |
    */

    'default' => env('PUSHER_APP_default', 'main'),

    /*
    |--------------------------------------------------------------------------
    | Pusher Connections
    |--------------------------------------------------------------------------
    |
    | Here are each of the connections setup for your application. Example
    | configuration has been included, but you may add as many connections as
    | you would like.
    |
    */

    'connections' => [

        'main' => [
            'auth_key' => env('PUSHER_APP_KEY'),
            'secret'   => env('PUSHER_APP_SECRET'),
            'app_id'   => env('PUSHER_APP_ID'),
            'options'  => [
                'cluster' => env('PUSHER_APP_CLUSTER'),
                // 'encryption_master_key' => env('PUSHER_ENCRYPTION_MASTER_KEY'),
            ],
            'host'    => env('PUSHER_APP_HOST', null),
            'port'    => env('PUSHER_APP_PORT', null),
            'timeout' => env('PUSHER_APP_TIMEOUT', null),
        ],

        'alternative' => [
            'auth_key' => env('PUSHER_APP_ALT_KEY', 'your-auth-key'),
            'secret'   => env('PUSHER_APP_ALT_SECRET', 'your-secret'),
            'app_id'   => env('PUSHER_APP_ALT_ID', 'your-app-id'),
            'options'  => [],
            'host'     => env('PUSHER_APP_ALT_HOST', null),
            'port'     => env('PUSHER_APP_ALT_PORT', null),
            'timeout'  => env('PUSHER_APP_ALT_TIMEOUT', null),
        ],

    ],

];
