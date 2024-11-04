<?php

use Illuminate\Support\Str;

return [

    'connections' => [
        'mysql' => [
            'read' => [
                'host' => [
                    env('DB_HOST_READ', '127.0.0.1'),
                ],
            ],

            'write' => [
                'host' => [
                    env('DB_HOST', '127.0.0.1'),
                ],
            ],

            'sticky'         => true,

            'driver' => 'mysql',
            'url' => env('DB_URL'),
            'host' => env('DB_HOST', '127.0.0.1'),
            'port' => env('DB_PORT', '3306'),
            'database' => env('DB_DATABASE', 'forge'),
            'username' => env('DB_USERNAME', 'forge'),
            'password' => env('DB_PASSWORD', ''),
            'unix_socket' => env('DB_SOCKET', ''),
            'charset' => 'utf8mb4',
            'collation' => 'utf8mb4_unicode_ci',
            'prefix' => '',
            'prefix_indexes' => true,
            'strict'         => false,
            'engine'         => 'InnoDB',
            'options' => extension_loaded('pdo_mysql') ? array_filter([
                PDO::MYSQL_ATTR_SSL_CA => env('MYSQL_ATTR_SSL_CA'),
            ]) : [],
        ],

        'mysql_read' => [
            'sticky'         => true,
            'driver'         => 'mysql',
            'url'            => env('DB_URL'),
            'host'           => env('DB_HOST_READ', '127.0.0.1'),
            'port'           => env('DB_PORT', '3306'),
            'database'       => env('DB_DATABASE', 'erp'),
            'username'       => env('DB_USERNAME', 'root'),
            'password'       => env('DB_PASSWORD', ''),
            'unix_socket'    => env('DB_SOCKET', ''),
            'charset'        => 'utf8mb4',
            'collation'      => 'utf8mb4_unicode_ci',
            'prefix'         => '',
            'prefix_indexes' => true,
            'strict'         => false,
            'engine'         => 'InnoDB',
        ],

        'brands-labels' => [
            'driver'   => 'mysql',
            'host'     => env('BRANDS_HOST', 'erp'),
            'database' => env('BRANDS_DB', 'erp'),
            'username' => env('MAGENTO_DB_USER', 'root'),
            'password' => env('MAGENTO_DB_PASSWORD', ''),
            'strict'   => false,
        ],

        'avoirchic' => [
            'driver'   => 'mysql',
            'host'     => env('AVOIRCHIC_HOST', 'erp'),
            'database' => env('AVOIRCHIC_DB', 'erp'),
            'username' => env('MAGENTO_DB_USER', 'root'),
            'password' => env('MAGENTO_DB_PASSWORD', ''),
            'strict'   => false,
        ],

        'olabels' => [
            'driver'   => 'mysql',
            'host'     => env('OLABELS_HOST', 'erp'),
            'database' => env('OLABELS_DB', 'erp'),
            'username' => env('MAGENTO_DB_USER', 'root'),
            'password' => env('MAGENTO_DB_PASSWORD', ''),
            'strict'   => false,
        ],

        'sololuxury' => [
            'driver'   => 'mysql',
            'host'     => env('SOLOLUXURY_HOST', 'erp'),
            'database' => env('SOLOLUXURY_DB', 'erp'),
            'username' => env('MAGENTO_DB_USER', 'root'),
            'password' => env('MAGENTO_DB_PASSWORD', ''),
            'strict'   => false,
        ],

        'suvandnet' => [
            'driver'   => 'mysql',
            'host'     => env('SUVANDNAT_HOST', 'erp'),
            'database' => env('SUVANDNAT_DB', 'erp'),
            'username' => env('MAGENTO_DB_USER', 'root'),
            'password' => env('MAGENTO_DB_PASSWORD', ''),
            'strict'   => false,
        ],

        'suvandnat' => [
            'driver'   => 'mysql',
            'host'     => env('SUVANDNAT_HOST', 'erp'),
            'database' => env('SUVANDNAT_DB', 'erp'),
            'username' => env('MAGENTO_DB_USER', 'root'),
            'password' => env('MAGENTO_DB_PASSWORD', ''),
            'strict'   => false,
        ],

        'thefitedit' => [
            'driver'   => 'mysql',
            'host'     => env('THEFITEDIT_HOST', 'erp'),
            'database' => env('THEFITEDIT_DB', 'erp'),
            'username' => env('MAGENTO_DB_USER', 'root'),
            'password' => env('MAGENTO_DB_PASSWORD', ''),
            'strict'   => false,
        ],

        'theshadesshop' => [
            'driver'   => 'mysql',
            'host'     => env('THESHADSSHOP_HOST', 'erp'),
            'database' => env('THESHADSSHOP_DB', 'erp'),
            'username' => env('MAGENTO_DB_USER', 'root'),
            'password' => env('MAGENTO_DB_PASSWORD', ''),
            'strict'   => false,
        ],

        'upeau' => [
            'driver'   => 'mysql',
            'host'     => env('UPEAU_HOST', 'erp'),
            'database' => env('UPEAU_DB', 'erp'),
            'username' => env('MAGENTO_DB_USER', 'root'),
            'password' => env('MAGENTO_DB_PASSWORD', ''),
            'strict'   => false,
        ],

        'veralusso' => [
            'driver'   => 'mysql',
            'host'     => env('VERALUSSO_HOST', 'erp'),
            'database' => env('VERALUSSO_DB', 'erp'),
            'username' => env('MAGENTO_DB_USER', 'root'),
            'password' => env('MAGENTO_DB_PASSWORD', ''),
            'strict'   => false,
        ],

        'tracker' => [
            'driver'    => 'mysql',
            'host'      => 'localhost',
            'database'  => env('DB_DATABASE', 'erp'),
            'username'  => env('DB_USERNAME', 'root'),
            'password'  => env('DB_PASSWORD', ''),
            'strict'    => false,    // to avoid problems on some MySQL installs
            'engine'    => 'MyISAM',
            'charset'   => 'utf8mb4',
            'collation' => 'utf8mb4_unicode_ci',
        ],
    ],

    'migrations' => [
        'table' => 'migrations',
        'update_date_on_publish' => false, // disable to preserve original behavior for existing applications
    ],
    
    /*
    |--------------------------------------------------------------------------
    | Redis Databases
    |--------------------------------------------------------------------------
    |
    | Redis is an open source, fast, and advanced key-value store that also
    | provides a richer body of commands than a typical key-value system
    | such as APC or Memcached. Laravel makes it easy to dig right in.
    |
    */

    'redis' => [

        'client' => env('REDIS_CLIENT', 'phpredis'),

        'options' => [
            'cluster' => env('REDIS_CLUSTER', 'redis'),
            'prefix' => env('REDIS_PREFIX', Str::slug(env('APP_NAME', 'laravel'), '_').'_database_'),
        ],

        'default' => [
            'url' => env('REDIS_URL'),
            'host' => env('REDIS_HOST', '127.0.0.1'),
            'username' => env('REDIS_USERNAME'),
            'password' => env('REDIS_PASSWORD'),
            'port' => env('REDIS_PORT', '6379'),
            'database' => env('REDIS_DB', '0'),
        ],

        'cache' => [
            'url' => env('REDIS_URL'),
            'host' => env('REDIS_HOST', '127.0.0.1'),
            'username' => env('REDIS_USERNAME'),
            'password' => env('REDIS_PASSWORD'),
            'port' => env('REDIS_PORT', '6379'),
            'database' => env('REDIS_CACHE_DB', '1'),
        ],

    ],
];
