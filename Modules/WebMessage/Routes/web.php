<?php

use Illuminate\Support\Facades\Route;
use Modules\WebMessage\Http\Controllers\WebMessageController;
use App\Http\Middleware\HttpClientTimeout;


/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::prefix('web-message')->middleware('auth', HttpClientTimeout::class)->group(function () {
    Route::get('/', [WebMessageController::class, 'index']);
    Route::post('/send', [WebMessageController::class, 'send']);
    Route::get('/message-list/{id}', [WebMessageController::class, 'messageList']);
    Route::get('/status', [WebMessageController::class, 'status']);
    Route::post('/action', [WebMessageController::class, 'action']);
    Route::post('/user-action', [WebMessageController::class, 'userAction']);
});
