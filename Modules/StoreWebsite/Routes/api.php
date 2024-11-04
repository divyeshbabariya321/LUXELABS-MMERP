<?php

use Illuminate\Support\Facades\Route;
use Modules\StoreWebsite\Http\Controllers\ColorController;
use Modules\StoreWebsite\Http\Controllers\WebsiteController;
use App\Http\Middleware\HttpClientTimeout;


/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

//Giving All Brands with Reference
Route::middleware([HttpClientTimeout::class])->group(function () {
    Route::get('colors', [ColorController::class, 'colorReference']);
    Route::get('websitesStores', [WebsiteController::class, 'websitesStores']);
});
