<?php
/**
 * Created by PhpStorm.
 * User: Fabian
 * Date: 12.05.16
 * Time: 07:24
 */

return [
    /*
    |--------------------------------------------------------------------------
    | Path configuration
    |--------------------------------------------------------------------------
    |
    | Change the paths, so they fit your needs
    |
     */
    'pathToEnv'       => base_path('.env'),
    'backupPath'      => resource_path('backups/dotenv-editor/'),
    'filePermissions' => env('FILE_PERMISSIONS', 0755),

    /*
    |--------------------------------------------------------------------------
    | GUI-Settings
    |--------------------------------------------------------------------------
    |
    | Here you can set the different parameter for the view, where you can edit
    | .env via a graphical interface.
    |
    | Comma-separate your different middlewares.
    |
     */

    // Activate or deactivate the graphical interface
    'activated' => env('DOT_ENV_EDITOR_ACTIVETED', true),

    /* Default view */
    'template' => env('DOT_ENV_EDITOR_TEMPLATE', 'layouts.app'),
    'overview' => env('DOT_ENV_EDITOR_OVERVIEW', 'env_manager.overview'),

    /* This is my custom view, do not using */
    //'template'        => 'adminlte::page',
    //'overview'        => 'dotenv-editor::overview-adminlte',

    // Config route group
    'route' => [
        'namespace'  => 'Brotzka\DotenvEditor\Http\Controllers',
        'prefix'     => env('DOT_ENV_EDITOR_PREFIX', 'env-manager'),
        'as'         => env('DOT_ENV_EDITOR_AS', 'env-manager'),
        'middleware' => ['web', 'auth'],
    ],
    //  'route' => '/enveditor',
];
