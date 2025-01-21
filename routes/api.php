<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\ExternalListings\ExternalListingsController;
use App\Http\Controllers\TmpImageUploadController;
use App\Http\Controllers\LocationController;
use App\Http\Controllers\NotificationsController;
use App\Http\Controllers\PropertyData\InitialController as PropertyInitialController;
use App\Http\Controllers\PropertyData\RegionsController as PropertyRegionsController;
use App\Http\Controllers\PropertyData\LocationsController as PropertyLocationsController;
use App\Http\Controllers\PropertyData\SectionsController as PropertySectionsController;
use App\Http\Controllers\PropertyData\LgasController as PropertyLgasController;
use App\Http\Controllers\PropertyData\LcdasController as PropertyLcdasController;
use App\Http\Controllers\PropertyData\SubSectorsController as PropertySubSectorsController;
use App\Http\Controllers\PropertyData\DevelopersController as PropertyDevelopersController;
use App\Http\Controllers\PropertyData\ListingAgentsController as PropertyListingAgentsController;
use App\Http\Controllers\PropertyData\ListingSourcesController as PropertyListingSourcesController;
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
        Route::get('all', 'allNotifications')->name('all-notifications');
        Route::put('mark-as-read', 'markAsRead')->name('mark-as-read');
        Route::delete('delete-all', 'deleteAll')->name('delete-all');
    });

Route::apiResource('sectors', SectorController::class)->middleware('auth:sanctum')->only('store');

Route::controller(ExternalListingsController::class)
    ->name('external-listings.')
    ->prefix('external-listings')
    ->middleware('auth:sanctum')
    ->group(function () {
        Route::get('listings', 'paginatedListings')->name('paginated-listings');
    });

Route::group(
    [
        'name' => 'property-data.',
        'prefix' => 'property-data'
    ],
    function () {
        Route::get('initial', [PropertyInitialController::class, 'getInitialData'])->name('get-initial-data');

        Route::controller(PropertyRegionsController::class)
            ->name('regions.')
            ->prefix('regions')
            ->group(function () {
                Route::get('', 'getRegions')->name('get-regions');
            });

        Route::controller(PropertyLocationsController::class)
            ->name('locations.')
            ->prefix('locations')
            ->group(function () {
                Route::get('', 'getLocations')->name('get-locations');
            });

        Route::controller(PropertySectionsController::class)
            ->name('sections.')
            ->prefix('sections')
            ->group(function () {
                Route::get('', 'getSections')->name('get-sections');
            });

        Route::controller(PropertyLgasController::class)
            ->name('lgas.')
            ->prefix('lgas')
            ->group(function () {
                Route::get('', 'getLgas')->name('get-lgas');
            });

        Route::controller(PropertyLcdasController::class)
            ->name('lcdas.')
            ->prefix('lcdas')
            ->group(function () {
                Route::get('', 'getLcdas')->name('get-lcdas');
            });

        Route::controller(PropertySubSectorsController::class)
            ->name('sub-sectors.')
            ->prefix('sub-sectors')
            ->group(function () {
                Route::get('', 'getSubSectors')->name('get-sub-sectors');
            });

        Route::controller(PropertyDevelopersController::class)
            ->name('developers.')
            ->prefix('developers')
            ->group(function () {
                Route::get('', 'getDevelopers')->name('get-developers');
            });
        Route::controller(PropertyListingAgentsController::class)
            ->name('listing-agents.')
            ->prefix('listing-agents')
            ->group(function () {
                Route::get('', 'getListingAgents')->name('get-listing-agents');
            });

        Route::controller(PropertyListingSourcesController::class)
            ->name('listing-sources.')
            ->prefix('listing-sources')
            ->group(function () {
                Route::get('', 'getListingSources')->name('get-listing-sources');
            });
    }
);
