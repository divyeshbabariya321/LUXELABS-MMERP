<?php

return [
    'GOOGLE_CLIENT_ACCESS_TOKEN'                            => env('GOOGLE_CLIENT_ACCESS_TOKEN', ''),
    'GOOGLE_CLIENT_MULTGOOGLE_CLIENT_ACCESS_TOKENIPLE_KEYS' => env('GOOGLE_CLIENT_MULTIPLE_KEYS', ''),
    'GOOGLE_ADS_WORDS_API_SCOPE'                            => env('GOOGLE_ADS_WORDS_API_SCOPE', 'https://www.googleapis.com/auth/adwords'),
    'GOOGLE_ADS_MANAGER_API_SCOPE'                          => env('GOOGLE_ADS_MANAGER_API_SCOPE', 'https://www.googleapis.com/auth/dfp'),
    'GOOGLE_ADS_AUTHORIZATION_URI'                          => env('GOOGLE_ADS_AUTHORIZATION_URI', 'https://accounts.google.com/o/oauth2/v2/auth'),
];
