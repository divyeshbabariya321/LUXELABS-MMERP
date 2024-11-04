<?php

return [

    'connections' => [
        'sync' => [
            'driver' => env('QUEUE_CONNECTION_SYNC_DRIVER', 'sync'),
        ],

        'database' => [
            'driver' => env('QUEUE_CONNECTION_DB_DRIVER', 'database'),
            'table' => env('QUEUE_CONNECTION_DB_TABLE', 'jobs'),
            'queue' => env('QUEUE_CONNECTION_DB_QUEUE', 'default'),
            'retry_after' => env('QUEUE_CONNECTION_DB_RETRY_AFTER', 90),
            'after_commit' => env('QUEUE_CONNECTION_DB_AFTER_COMMIT', false),
        ],

        'beanstalkd' => [
            'driver' => env('BEANSTALKD_DRIVER', 'beanstalkd'),
            'host' => env('BEANSTALKD_HOST', 'localhost'),
            'queue' => env('BEANSTALKD_QUEUE', 'default'),
            'retry_after' => env('BEANSTALKD_RETRY_AFTER', 90),
            'block_for' => env('BEANSTALKD_BLOCK_FOR', 0),
            'after_commit' => env('BEANSTALKD_AFTER_COMMIT', false),
        ],

        'redis' => [
            'driver' => env('REDIS_DRIVER', 'redis'),
            'connection' => env('REDIS_CONNECTION', 'default'),
            'queue' => env('REDIS_QUEUE', 'default'),
            'retry_after'  => env('REDIS_RETRY_AFTER', 5000),
            'block_for' => env('REDIS_BLOCK_FOR', null),
            'after_commit' => env('REDIS_AFTER_COMMIT', false),
        ],
    ],

    'failed' => [
        'driver' => env('QUEUE_FAILED_DRIVER', 'database-uuids'),
        'database' => env('DB_CONNECTION', 'mysql'),
        'table' => env('QUEUE_FAILED_TABLE', 'failed_jobs'),
    ],

];
