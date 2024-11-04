<?php

use App\Providers\AppServiceProvider;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Sentry\Laravel\Integration;

return Application::configure(basePath: dirname(__DIR__))
    ->withProviders([
        \Bugsnag\BugsnagLaravel\BugsnagServiceProvider::class,
        //\Collective\Html\HtmlServiceProvider::class,
        \Spatie\Permission\PermissionServiceProvider::class,
        \Barryvdh\LaravelIdeHelper\IdeHelperServiceProvider::class,
        \Plank\Mediable\MediableServiceProvider::class,
        \Nestable\NestableServiceProvider::class,
        \Intervention\Image\ImageServiceProvider::class,
        \Maatwebsite\Excel\ExcelServiceProvider::class,
        \Yajra\DataTables\DataTablesServiceProvider::class,
        \Intervention\Image\ImageServiceProvider::class,
        \Milon\Barcode\BarcodeServiceProvider::class,
        \Webklex\IMAP\Providers\LaravelServiceProvider::class,
        \Brotzka\DotenvEditor\DotenvEditorServiceProvider::class,
        \Madnest\Madzipper\MadzipperServiceProvider::class,
        \Modules\ChatBot\Providers\ChatBotServiceProvider::class,
        \Modules\LeadQueue\Providers\LeadQueueServiceProvider::class,
        \Modules\MessageQueue\Providers\MessageQueueServiceProvider::class,
        \Modules\StoreWebsite\Providers\StoreWebsiteServiceProvider::class,
        \Modules\UserManagement\Providers\UserManagementServiceProvider::class,
        \Modules\WebMessage\Providers\WebMessageServiceProvider::class,
    ])
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        channels: __DIR__.'/../routes/channels.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        // $middleware->redirectGuestsTo(fn () => route('login'));
        $middleware->redirectUsersTo(AppServiceProvider::HOME);

        $middleware->validateCsrfTokens(except: [
            '*',
            'zoom/webhook',
            'twilio/*',
            'run-webhook/*',
            'whatsapp/*',
            'livechat/*',
            'duty/v1/calculate',
            'hubstaff/linkuser',
            'time-doctor/link_time_doctor_user',
            'calendar',
            'calendar/*',
            'api/wetransfer-file-store',
            'cold-leads-broadcasts',
            'auto-build-process',
        ]);

        $middleware->append(\App\Http\Middleware\HttpClientTimeout::class);

        $middleware->web([
            \App\Http\Middleware\CheckDailyPlanner::class,
            \App\Http\Middleware\LogLastUserActivity::class,
        ]);

        $middleware->api(\App\Http\Middleware\LogAfterRequest::class);

        $middleware->alias([
            'affiliates' => \App\Http\Middleware\AffiliateMiddleware::class,
            'custom.api.auth' => \App\Http\Middleware\CustomApiAuthMiddleware::class,
            'optimizeImages' => \Spatie\LaravelImageOptimizer\Middlewares\OptimizeImages::class,
            'permission' => \Spatie\Permission\Middleware\PermissionMiddleware::class,
            'role' => \Spatie\Permission\Middleware\RoleMiddleware::class,
            'role_or_permission' => \Spatie\Permission\Middleware\RoleOrPermissionMiddleware::class,
            'twilio.voice.validate' => \App\Http\Middleware\VoiceValidateRequest::class,
        ]);

        $middleware->priority([
            \Illuminate\Session\Middleware\StartSession::class,
            \Illuminate\View\Middleware\ShareErrorsFromSession::class,
            // \App\Http\Middleware\Authenticate::class,
            Illuminate\Auth\Middleware\Authenticate::class,
            \Illuminate\Session\Middleware\AuthenticateSession::class,
            \Illuminate\Routing\Middleware\SubstituteBindings::class,
            \Illuminate\Auth\Middleware\Authorize::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions) {
        Integration::handles($exceptions);
    })->create();
