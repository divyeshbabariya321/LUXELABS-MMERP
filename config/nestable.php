<?php

return [
    'parent'       => env('NESTABLE_PARENT', 'parent_id'),
    'primary_key'  => env('NESTABLE_PRIMARY_KEY', 'id'),
    'generate_url' => env('NESTABLE_GENERATE_URL', false),
    'childNode'    => env('NESTABLE_CHILD_NODE', 'child'),
    'body'         => [
        'id',
        'title',
        //        'slug',
    ],
    'html' => [
        'label' => 'title',
        'href'  => 'title',
    ],
    'dropdown' => [
        'prefix' => '',
        'label'  => 'title',
        'value'  => 'id',
    ],
];
