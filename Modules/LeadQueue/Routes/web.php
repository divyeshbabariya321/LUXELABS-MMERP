<?php

use App\Http\Middleware\HttpClientTimeout;
use Illuminate\Support\Facades\Route;
use Modules\LeadQueue\Http\Controllers\LeadQueueController;

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

Route::middleware([HttpClientTimeout::class])->group(function () {
    Route::prefix('leadqueue')->middleware('auth')->group(function () {
        Route::get('/', [LeadQueueController::class, 'index']);
    });

    Route::prefix('lead-queue')->middleware('auth')->group(function () {
        Route::get('/', [LeadQueueController::class, 'index'])->name('lead-queue.index');
        Route::get('/approve', [LeadQueueController::class, 'approve'])->name('lead-queue.approve');
        Route::get('/approve/approved', [LeadQueueController::class, 'approved'])->name('lead-queue.approved');
        Route::get('/status', [LeadQueueController::class, 'status'])->name('lead-queue.status');
        Route::get('delete', [LeadQueueController::class, 'deleteRecord'])->name('lead-queue.delete.record');
        Route::prefix('records')->group(function () {
            Route::get('/', [LeadQueueController::class, 'records']);
            Route::post('/action-handler', [LeadQueueController::class, 'actionHandler']);
            // Route::prefix('{id}')->group(function() {
            // 	Route::get('delete', 'LeadQueueController@deleteRecord');
            // });
        });

        Route::prefix('report')->group(function () {
            Route::get('/', [LeadQueueController::class, 'report'])->name('lead-queue.report');
        });

        Route::prefix('setting')->group(function () {
            Route::post('update-limit', [LeadQueueController::class, 'updateLimit']);
            Route::get('recall', [LeadQueueController::class, 'recall']);
        });
    });
});
