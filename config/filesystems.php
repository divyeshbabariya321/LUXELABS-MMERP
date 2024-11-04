<?php

return [

    'disks' => [
        'analytics_files' => [
            'driver' => 'local',
            'root'   => base_path('resources/analytics_files'),
        ],

        'uploads' => [
            'driver'     => 'local',
            'root'       => public_path('uploads'),
            'url'        => env('APP_URL') . '/uploads',
            'visibility' => 'public',
        ],

        'logs' => [
            'driver' => 'local',
            'root'   => storage_path('logs'),
        ],

        'public_disk' => [
            'driver'     => 'local',
            'root'       => public_path(),
            'visibility' => 'public',
        ],

        'files' => [
            'driver'     => 'local',
            'root'       => storage_path('app/files'),
            'url'        => env('APP_URL') . '/storage/files',
            'visibility' => 'public',
        ],

        'adsapi' => [
            'driver'     => 'local',
            'root'       => storage_path('app/adsapi'),
            'url'        => env('APP_URL') . '/storage/adsapi',
            'visibility' => 'public',
        ],

        's3' => [
            'driver' => 's3',
            'key' => env('AWS_ACCESS_KEY_ID'),
            'secret' => env('AWS_SECRET_ACCESS_KEY'),
            'region' => env('AWS_DEFAULT_REGION'),
            'bucket' => env('AWS_BUCKET'),
            'url' => env('AWS_URL'),
            'endpoint' => env('AWS_ENDPOINT'),
            'use_path_style_endpoint' => env('AWS_USE_PATH_STYLE_ENDPOINT', false),
            'root'                    => 'public/uploads',
            'throw' => false,
        ],

        's3_social_media' => [
            'driver'                  => 's3',
            'key'                     => env('AWS_ACCESS_KEY_ID'),
            'secret'                  => env('AWS_SECRET_ACCESS_KEY'),
            'region'                  => env('AWS_DEFAULT_REGION'),
            'bucket'                  => env('AWS_SOCIAL_MEDIA_BUCKET'),
            'url'                     => env('AWS_URL'),
            'endpoint'                => env('AWS_ENDPOINT'),
            'use_path_style_endpoint' => env('AWS_USE_PATH_STYLE_ENDPOINT', false),
            'root'                    => 'public/uploads',
        ],

        'google_ads' => [
            'driver'     => 'local',
            'root'       => storage_path('app/google_ads'),
            'url'        => env('APP_URL') . '/storage/google_ads',
            'visibility' => 'public',
        ],

        'glacier' => [
            'driver' => 's3',
            'key'    => env('AWS_ACCESS_KEY_ID'),
            'secret' => env('AWS_SECRET_ACCESS_KEY'),
            'region' => env('AWS_DEFAULT_REGION'),
            'vault'  => env('AWS_GLACIER_VAULT'),
        ],
    ],

    'links' => [
        public_path('storage') => storage_path('app/public'),
        public_path('storage/product') => storage_path('app/product'),
        public_path('storage/google_ads') => storage_path('app/google_ads'),
    ],

];
