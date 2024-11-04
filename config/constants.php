<?php

$minutes = range(0, 59);
$hrs = range(1, 24);
$days = range(1, 31);
$months = range(1, 12);
$weeks = range(0, 6);

return [
    'uploads_dir' => '/uploads/',
    'archive__dir' => '/uploads/archives/',

    /* Added because using Storage facade and assuming root path set for driver */
    'default_uploads_dir' => '/',
    'default_archive__dir' => '/archives/',
    'default_trash__dir' => '/trash/',

    'media_tags' => ['untagged'],
    'attach_image_tag' => ['original', 'gallery', 'untagged'],
    'media_original_tag' => ['original'],
    'media_gallery_tag' => 'gallery',
    'media_barcode_tag' => ['barcode'],
    'media_screenshot_tag' => ['screenshot'],
    'media_barcode_path' => '/uploads/product-barcode/',
    'paginate' => '10',
    'image_per_folder' => '10000',
    'excelimporter' => 'excelimporter',
    'gd_supported_files' => ['jpg', 'jpeg', 'png', 'webp', 'gif'],
    'no_of_product' => '100',
    'mode' => 'product-push',
    'cron_minutes' => [
        '*' => 'Every Minutes',
        '5' => 'Every Five Minutes',
        '10' => 'Every Ten Minutes',
        '15' => 'Every Fifteen Minutes',
    ] + $minutes,
    'cron_hours' => [
        '*' => 'Every Hours',
        '4' => 'Every Four Hours',
        '6' => 'Every Six Hours',
    ] + $hrs,
    'cron_days' => [
        '*' => 'Every Day',
    ] + $days,
    'cron_months' => [
        '*' => 'Every Months',
    ] + $months,
    'cron_weekdays' => [
        '*' => 'Every WeekDay',
    ] + $weeks,
    'google_text_search' => 'googletextsearch',

    'size_chart_media_tag' => 'size_chart',

    /* Google Webmaster Credentials */
    'GOOGLE_CLIENT_APPLICATION_NAME' => env('GOOGLE_CLIENT_APPLICATION_NAME'),
    'GOOGLE_CLIENT_ID' => env('GOOGLE_CLIENT_ID'),
    'GOOGLE_CLIENT_SECRET' => env('GOOGLE_CLIENT_SECRET'),
    'GOOGLE_CLIENT_KEY' => env('GOOGLE_CLIENT_KEY'),
    'py_facebook_script' => env('PY_FB_SCRIPT_URL'),
    'py_crop_script' => env('PY_CROP_INSTANCE'),
    'product_check_py' => env('PRODUCT_CHECK_PY'),

    /** website root access */
    'WEBSITES_LOGS_FOLDER' => 'storage/websites',
    'PRINT_LATER_AUTO_DELETE_DAYS' => 15,
    'AVAILABLE_TIMEZONES' => [
        'Asia/Dubai' => 'Asia/Dubai',
        'Asia/Kolkata' => 'Asia/Kolkata',
    ],
    'google_indexing_state_enum' => [
        'INDEXING_STATE_UNSPECIFIED' => 'Unknown indexing status.',
        'INDEXING_ALLOWED' => 'Indexing allowed.',
        'BLOCKED_BY_META_TAG' => "Indexing not allowed, 'noindex' detected in 'robots' meta tag.",
        'BLOCKED_BY_HTTP_HEADER' => "Indexing not allowed, 'noindex' detected in 'X-Robots-Tag' http header.",
        'BLOCKED_BY_ROBOTS_TXT' => 'Reserved, no longer in use.',
    ],
    'google_verdict_enum' => [
        'VERDICT_UNSPECIFIED' => 'Unknown verdict.',
        'PASS' => 'Equivalent to "Valid" for the page or item in Search Console.',
        'PARTIAL' => 'Reserved, no longer in use.',
        'FAIL' => 'Equivalent to "Error" or "Invalid" for the page or item in Search Console.',
        'NEUTRAL' => 'Equivalent to "Excluded" for the page or item in Search Console.',
    ],
    'bing_site_role_enum' => [
        0 => 'Administrator',
        1 => 'ReadOnly',
        2 => 'ReadWrite',
    ],
    'TIME_DOCTOR_API_RESPONSE_MESSAGE' => [
        '401' => 'Time Doctor Account user\'s Token ID is invalid or access is denied.',
        '403' => 'Time Doctor Account user don\'t have permission to perform this action',
        '409' => 'The same resource of this type has already been registered.',
        '422' => 'Missing value in at least one of required parameters.',
        '404' => 'Something went wrong',
        '500' => 'Something went wrong',
    ],
    'temporary_url_expiry_time' => 2,
    'auth_user_timeout' => env('AUTH_USER_TIMEOUT', 30000),
    'HUBSTAFF_ACCOUNT_URL' => 'https://account.hubstaff.com/access_tokens',
    'HUBSTAFF_API_URL' => 'https://api.hubstaff.com/v2/organizations/',
    'GOOGLE_SERVICE_ACCOUNTS' => [
        'GOOGLE_WEB_MASTER' => 'Web Master',
        'GOOGLE_SERVER' => 'Google Server',
        'GOOGLE_AD_WORDS' => 'Google AdWords',
        'GOOGLE_DIALOG_FLOW' => 'Google Dialog Flow',
        'GOOGLE_TRANSLATION' => 'Google Translation',
    ],

    /* You need to change the google translate type Free or paid as per your requirement.Cureently, we have used free package of google translate
     * 'GOOGLE_TRANSLATE_TYPE'=> FREE/PAID
    */
    'GOOGLE_TRANSLATE_TYPE' => 'FREE',
    'ADMIN_ROLE' => 'Admin',
    'LEAD_TESTER_ROLE' => 'Lead Tester',
    'HOD_OF_CRM_ROLE' => 'HOD of CRM',

    'BROWSERS' => ['chrome' => 'Chrome', 'firefox' => 'Firefox', 'edge' => 'Edge'],

    'TEST_LIST' => ['TestHomePage' => 'TestHomePage'],
    'TEST_FLOW_LIST' => ['account' => 'Account', 'cart' => 'Cart', 'checkout' => 'Checkout', 'contact_us' => 'Contact Us', 'home' => 'Home',
        'login' => 'Login', 'notify_when_product_out_of_stock' => 'Notify When Product Out Of Stock', 'order' => 'Order', 'product' => 'Product',
        'return_cancel' => 'Return Cancel', 'search' => 'Search', 'signup' => 'Signup', 'ticket' => 'Ticket',
    ],
];
