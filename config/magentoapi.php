<?php

return [
    //	'url' => 'https://www.sololuxury.co.in/api/v2_soap/?wsdl=1',
    'url' => env('MAGENTO_API_URL', 'https://devsite.sololuxury.com/api/v2_soap/?wsdl=1'),
    //	'url' => 'http://jephunertoqato.sololuxury.com/api/v2_soap/?wsdl=1',
    'user'     => env('MAGENTO_API_USER', 'apiUser'),
    'password' => env('MAGENTO_API_PASSWORD', 'Sakina88!'),
];
