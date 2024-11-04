<?php

use Illuminate\Support\Facades\Route;
use App\Http\Middleware\SendgridEventMiddleware;
use App\Http\Controllers\WebhookController;

Route::middleware(SendgridEventMiddleware::class)->group(function () {
    Route::any(config('sendgridevents.webhook_url'), [WebhookController::class, 'post'])->name('sendgrid.webhook');

    // Route::post(
    //     config('sendgridevents.webhook_url'),
    //     [
    //         'as' => 'sendgrid.webhook',
    //         'uses' => 'WebhookController@post'
    //     ]
    // );
});
