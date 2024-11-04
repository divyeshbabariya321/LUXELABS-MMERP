<?php

use App\Http\Middleware\HttpClientTimeout;
use Illuminate\Support\Facades\Route;
use Modules\MessageQueue\Http\Controllers\MessageQueueController;

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

Route::prefix('message-queue')->middleware('auth',HttpClientTimeout::class)->group(function () {
        Route::get('/', [MessageQueueController::class, 'index'])->name('message-queue.index');
        Route::get('/approve', [MessageQueueController::class, 'approve'])->name('message-queue.approve');
        Route::get('/status', [MessageQueueController::class, 'status'])->name('message-queue.status');
        Route::get('/update-do-not-disturb', [MessageQueueController::class, 'updateDoNotDisturb'])->name('message-queue.updateDoNotDisturb');

        Route::prefix('records')->group(function () {
            Route::get('/', [MessageQueueController::class, 'records']);
            Route::post('action-handler', [MessageQueueController::class, 'actionHandler']);
            Route::prefix('{id}')->group(function () {
                Route::get('delete', [MessageQueueController::class, 'deleteRecord']);
            });
        });

        Route::prefix('report')->group(function () {
            Route::get('/', [MessageQueueController::class, 'report'])->name('message-queue.report');
        });

        Route::prefix('setting')->group(function () {
            Route::post('update-limit', [MessageQueueController::class, 'updateLimit']);
            Route::post('update-time', [MessageQueueController::class, 'updateTime']);
            Route::get('recall', [MessageQueueController::class, 'recall']);
        });
    });
