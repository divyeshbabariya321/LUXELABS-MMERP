<?php

use Monolog\Handler\NullHandler;
use Monolog\Handler\StreamHandler;
use Monolog\Handler\SyslogUdpHandler;
use Monolog\Processor\PsrLogMessageProcessor;

return [

    'deprecations' => env('LOG_DEPRECATIONS_CHANNEL', 'null'),

    'channels' => [
        'listMagento' => [
            'driver' => 'daily',
            'path'   => storage_path('logs/list-magento.log'),
            'days'   => 7,
        ],

        'productUpdates' => [
            'driver' => 'daily',
            'path'   => storage_path('logs/product-updates.log'),
            'days'   => 7,
        ],

        'chatapi' => [
            'driver' => 'daily',
            'path'   => storage_path('logs/chatapi/chatapi.log'),
            'level'  => 'debug',
            'days'   => 7,
        ],

        'customerDnd' => [
            'driver' => 'daily',
            'path'   => storage_path('logs/customers/dnd.log'),
            'level'  => 'debug',
        ],

        'customer' => [
            'driver' => 'daily',
            'path'   => storage_path('logs/general/general.log'),
            'level'  => 'debug',
            'days'   => 7,
        ],

        'whatsapp' => [
            'driver' => 'daily',
            'path'   => storage_path('logs/whatsapp/whatsapp.log'),
            'days'   => 7,
        ],

        'scraper' => [
            'driver' => 'daily',
            'path'   => storage_path('logs/scraper/scraper.log'),
            'days'   => 7,
        ],

        'update_category_job' => [
            'driver' => 'daily',
            'path'   => storage_path('logs/category_job/category_job.log'),
            'days'   => 7,
        ],

        'update_color_job' => [
            'driver' => 'daily',
            'path'   => storage_path('logs/color_job/color_job.log'),
            'days'   => 7,
        ],

        'broadcast_log' => [
            'driver' => 'daily',
            'path'   => storage_path('logs/general/broadcast.log'),
            'days'   => 1,
        ],

        'hubstaff_activity_command' => [
            'driver' => 'daily',
            'path'   => storage_path('logs/hubstaff-activity-command/hubstaff-activity-command.log'),
            'days'   => 7,
        ],

        'insta_message_queue_by_rate_limit' => [
            'driver' => 'daily',
            'path'   => storage_path('logs/insta-message-queue-by-rate-limit/insta-message-queue-by-rate-limit.log'),
            'days'   => 7,
        ],

        'product_push_information_csv' => [
            'driver' => 'daily',
            'path'   => storage_path('logs/product-push-information-csv/product-push-information-csv.log'),
            'days'   => 7,
        ],

        'product-thumbnail' => [
            'driver' => 'daily',
            'path'   => storage_path('logs/product-thumbnail/product-thumbnail-command.log'),
            'days'   => 7,
        ],

        'scrapper_images' => [
            'driver' => 'daily',
            'path'   => storage_path('logs/scrapper_images/scrapper_images.log'),
            'days'   => 7,
        ],

        'social_webhook' => [
            'driver' => 'daily',
            'path'   => storage_path('logs/social_webhook/social_webhook.log'),
            'days'   => 7,
        ],

        'time_doctor_activity_command' => [
            'driver' => 'daily',
            'path'   => storage_path('logs/time-doctor-activity-command/time-doctor-activity-command.log'),
            'days'   => 7,
        ],

        'bugsnag' => [
            'driver' => 'bugsnag',
        ],

        'github_error' => [
            'driver' => 'daily',
            'path'   => storage_path('logs/github_error.log'),
            'level'  => 'error',
            'days'   => 7,
        ],

        'magento_problem_error' => [
            'driver' => 'daily',
            'path'   => storage_path('logs/magento_problem_error.log'),
            'level'  => 'error',
            'days'   => 7,
        ],

        'general' => [
            'driver' => 'daily',
            'path'   => storage_path('logs/general/general.log'),
            'level'  => 'debug',
            'days'   => 7,
        ],
        
        'jobPushWebsiteToMagento' => [
            'driver' => 'daily',
            'path'   => storage_path('logs/job-PushWebsiteToMagento.log'),
            'days'   => 7,
        ],
        
    ],

];
