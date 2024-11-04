<?php

return [
    'api' => [
        'DHL_ID'          => env('DHL_ID', ''),
        'DHL_KEY'         => env('DHL_KEY', ''),
        'DHL_USER'        => env('DHL_USER', ''),
        'DHL_PASSWORD'    => env('DHL_PASSWORD', ''),
        'DHL_ACCOUNT'     => env('DHL_ACCOUNT', ''),
        'DHL_COUNTRY'     => env('DHL_COUNTRY', 'ZA'),
        'DHL_CURRECY'     => env('DHL_CURRECY', 'USD'),
        'DHL_COUNTRYCODE' => env('DHL_COUNTRYCODE', ''),
        'DHL_POSTALCODE'  => env('DHL_POSTALCODE', ''),
        'DHL_CITY'        => env('DHL_CITY', ''),
    ],
    'shipper' => [
        'street'       => env('DHL_SHIPPER_STREET'),
        'city'         => env('DHL_SHIPPER_CITY'),
        'postal_code'  => env('DHL_SHIPPER_POSTAL_CODE'),
        'country_code' => env('DHL_SHIPPER_COUNTRY_CODE'),
        'person_name'  => env('DHL_SHIPPER_PERSON_NAME'),
        'company_name' => env('DHL_SHIPPER_COMPANY_NAME'),
        'phone'        => env('DHL_SHIPPER_PHONE'),
    ],
];
