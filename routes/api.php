<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\TmpImageUploadController;
use App\Http\Controllers\LocationController;
use App\Http\Controllers\NotificationsController;
use App\Http\Controllers\SectorController;
use App\Http\Controllers\StreetData\FormFieldDataController as StreetDataFormFieldDataController;
use App\Http\Controllers\StreetData\OverviewController;
use App\Http\Controllers\StreetData\SearchController as StreetDataSearchController;
use App\Http\Controllers\StreetData\StreetDataController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;


/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::middleware('auth:sanctum')->get('/auth/user', function (Request $request) {
    return $request->user()->getUserData();
});

Route::controller(AuthController::class)->prefix('auth/')->name('api-auth.')->group(function () {
    Route::post('/verify-user-by-email', "verifyUserByEmail")->name('verify-user-by-email');
    Route::post('/login', 'login')->name('login');

    Route::post('/forgot-password', 'forgotPassword')->name('forgot-password');
    Route::post('/reset-password', 'resetPassword')->name('reset-password');

    Route::middleware('auth:sanctum')->group(function () {
        Route::delete('/logout', 'logout')->name('logout');
        Route::post('/change-password', 'changePassword')->name('change-password');
    });
});

Route::controller(TmpImageUploadController::class)
    ->middleware('auth:sanctum')
    ->name('temp-image-uploader.')
    ->group(function () {
        Route::post('/store-temp-image', 'storeToTmp')->name('store-to-tmp');
        Route::delete('/delete-tmp-image', 'deleteTmpImage')->name('delete-tmp-image');
    });

Route::controller(StreetDataFormFieldDataController::class)
    ->prefix('street-data')
    ->name('street-data.')
    ->middleware('auth:sanctum')
    ->group(function () {
        Route::get('form-field-data', 'index')->name('form-field-data.index');
    });

Route::controller(StreetDataSearchController::class)
    ->name('street-data.search.')
    ->prefix('street-data/search')
    ->middleware('auth:sanctum')
    ->group(function () {
        Route::get('/', 'index')->name('index');
    });

Route::controller(OverviewController::class)
    ->name('street-data.overview.')
    ->prefix('street-data/overview')
    ->middleware('auth:sanctum')
    ->group(function () {
        Route::get('widget-set', 'widgetSet')->name('widget-set');
        Route::get('visual-set', 'visualSet')->name('visual-set');
        Route::get('user-performance', 'userPerformance')->name('user-performance');
    });

Route::apiResource('street-data', StreetDataController::class)->middleware('auth:sanctum');

Route::controller(LocationController::class)
    ->name('locations.')
    ->prefix('locations')
    ->middleware('auth:sanctum')
    ->group(function () {
        Route::put('activate', 'activate')->name('activate');
        Route::get('get-active-location', 'getActiveLocation')->name('get-active-location');
    });

Route::controller(NotificationsController::class)
    ->name('notifications.')
    ->prefix('notifications')
    ->middleware('auth:sanctum')
    ->group(function () {
        Route::get('all', 'allNotifications')->name('name');
        Route::put('mark-as-read', 'markAsRead')->name('mark-as-read');
        Route::delete('delete-all', 'deleteAll')->name('delete-all');
    });

Route::apiResource('sectors', SectorController::class)->middleware('auth:sanctum')->only('store');
