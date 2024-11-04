<?php

use Illuminate\Support\Facades\Facade;
use Illuminate\Support\ServiceProvider;
use Modules\ChatBot\Providers\ChatBotServiceProvider;
use Modules\LeadQueue\Providers\LeadQueueServiceProvider;
use Modules\MessageQueue\Providers\MessageQueueServiceProvider;
use Modules\StoreWebsite\Providers\StoreWebsiteServiceProvider;
use Modules\UserManagement\Providers\UserManagementServiceProvider;
use Modules\WebMessage\Providers\WebMessageServiceProvider;

return [

    'rapid_api_key' => env('RAPID_API_KEY'),

    'providers' => ServiceProvider::defaultProviders()->merge([
        /*
         * Package Service Providers...
         */
        Bugsnag\BugsnagLaravel\BugsnagServiceProvider::class,

        /*
         * Application Service Providers...
         */
        App\Providers\AppServiceProvider::class,
        // App\Providers\AuthServiceProvider::class,
        // App\Providers\BroadcastServiceProvider::class,
        // App\Providers\EventServiceProvider::class,
        App\Providers\HorizonServiceProvider::class,
        App\Providers\RouteServiceProvider::class,
        Spatie\Permission\PermissionServiceProvider::class,
        Barryvdh\LaravelIdeHelper\IdeHelperServiceProvider::class,
        App\Providers\NotificationServiceProvider::class,
        Plank\Mediable\MediableServiceProvider::class,
        Nestable\NestableServiceProvider::class,
        Intervention\Image\ImageServiceProvider::class,
        Maatwebsite\Excel\ExcelServiceProvider::class,
        Illuminate\Pagination\PaginationServiceProvider::class,
        //        Thujohn\Twitter\TwitterServiceProvider::class,
        // BookStack replacement service providers (Extends Laravel)
        //        Modules\BookStack\Providers\PaginationServiceProvider::class,
        //        Modules\BookStack\Providers\TranslationServiceProvider::class,
        //
        //        // BookStack custom service providers
        //        Modules\BookStack\Providers\AuthServiceProvider::class,
        //        Modules\BookStack\Providers\AppServiceProvider::class,
        //        Modules\BookStack\Providers\BookStackServiceProvider::class,
        //        Modules\BookStack\Providers\CustomFacadeProvider::class,
        //        Modules\BookStack\Providers\BroadcastServiceProvider::class,
        //        Modules\BookStack\Providers\EventServiceProvider::class,
        //        Modules\BookStack\Providers\RouteServiceProvider::class,
        //        Modules\BookStack\Providers\CustomFacadeProvider::class,

        App\Providers\PermissionCheckServiceProvider::class,
        Yajra\DataTables\DataTablesServiceProvider::class,
        Intervention\Image\ImageServiceProvider::class,
        Milon\Barcode\BarcodeServiceProvider::class,
        Webklex\IMAP\Providers\LaravelServiceProvider::class,
        Brotzka\DotenvEditor\DotenvEditorServiceProvider::class,
        Madnest\Madzipper\MadzipperServiceProvider::class,
        // seo2websites\ErpCustomer\ErpCustomerServiceProvider::class,
        //LaravelFCM\FCMServiceProvider::class,

        App\Providers\EmailServiceProvider::class,
        App\Providers\ConfigServiceProvider::class,
        App\Providers\ViewServiceProvider::class,
        Barryvdh\Debugbar\ServiceProvider::class,

        //// module provider
        ChatBotServiceProvider::class,
        LeadQueueServiceProvider::class,
        MessageQueueServiceProvider::class,
        StoreWebsiteServiceProvider::class,
        UserManagementServiceProvider::class,
        WebMessageServiceProvider::class,
    ])->toArray(),

    'aliases' => Facade::defaultAliases()->merge([
        'DNS1D' => Milon\Barcode\Facades\DNS1DFacade::class,
        'DNS2D' => Milon\Barcode\Facades\DNS2DFacade::class,
        'DataTables' => Yajra\DataTables\Facades\DataTables::class,
        'DotenvEditor' => Brotzka\DotenvEditor\DotenvEditorFacade::class,
        'Excel' => Maatwebsite\Excel\Facades\Excel::class,
        'IImage' => Intervention\Image\Facades\Image::class,
        'Image' => Intervention\Image\Facades\Image::class,
        'Input' => Illuminate\Support\Facades\Request::class,
        'MediaUploader' => Plank\Mediable\MediaUploaderFacade::class,
        'Nestable' => Nestable\Facades\NestableService::class,
        'PDF' => Barryvdh\Snappy\Facades\SnappyPdf::class,
        'PermissionCheck' => App\Facades\PermissionCheckClass::class,
        'Pusher' => Pusher::class,
        'Redis' => Illuminate\Support\Facades\Redis::class,
        'SnappyImage' => Barryvdh\Snappy\Facades\SnappyImage::class,
        'UnsplashCollections' => shweshi\LaravelUnsplashWrapper\Facades\UnsplashCollectionsFacade::class,
        'UnsplashPhotos' => shweshi\LaravelUnsplashWrapper\Facades\UnsplashPhotosFacade::class,
        'UnsplashSearch' => shweshi\LaravelUnsplashWrapper\Facades\UnsplashSearchFacade::class,
        'UnsplashUsers' => shweshi\LaravelUnsplashWrapper\Facades\UnsplashUsersFacade::class,
        'Zipper' => Madnest\Madzipper\Madzipper::class,
    ])->toArray(),

    'failed_email_addresses' => [
        'Mailer-Daemon@se1.mailspamprotection.com',
        'Mailer-Daemon@se2.mailspamprotection.com',
        'Mailer-Daemon@se3.mailspamprotection.com',
        'Mailer-Daemon@se4.mailspamprotection.com',
        'Mailer-Daemon@se5.mailspamprotection.com',
        'Mailer-Daemon@se6.mailspamprotection.com',
        'Mailer-Daemon@se7.mailspamprotection.com',
        'Mailer-Daemon@se8.mailspamprotection.com',
        'Mailer-Daemon@se9.mailspamprotection.com',
        'Mailer-Daemon@se10.mailspamprotection.com',
        'Mailer-Daemon@se11.mailspamprotection.com',
        'Mailer-Daemon@se12.mailspamprotection.com',
        'Mailer-Daemon@se13.mailspamprotection.com',
        'Mailer-Daemon@se14.mailspamprotection.com',
        'Mailer-Daemon@se15.mailspamprotection.com',
        'Mailer-Daemon@se16.mailspamprotection.com',
        'Mailer-Daemon@se17.mailspamprotection.com',
        'Mailer-Daemon@se18.mailspamprotection.com',
        'Mailer-Daemon@se19.mailspamprotection.com',
        'Mailer-Daemon@se20.mailspamprotection.com',
    ],

];
