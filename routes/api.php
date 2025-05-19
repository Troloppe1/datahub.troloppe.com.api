<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\ExternalListings\ExternalListingsController;
use App\Http\Controllers\PropertyData\InitialController;
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
use App\Http\Controllers\PropertyData\ResourceCreationController as PropertyDataResourceCreationController;
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

// Auth
Route::controller(AuthController::class)->prefix('auth/')->group(function () {
    Route::post('/verify-user-by-email', "verifyUserByEmail");
    Route::post('/login', 'login');

    Route::post('/forgot-password', 'forgotPassword');
    Route::post('/reset-password', 'resetPassword');

    Route::middleware('auth:sanctum')->group(function () {
        Route::delete('/logout', 'logout');
        Route::post('/change-password', 'changePassword');
    });
});

// Temp Image Uploader
Route::controller(TmpImageUploadController::class)
    ->middleware('auth:sanctum')
    ->group(function () {
        Route::post('/store-temp-image', 'storeToTmp');
        Route::delete('/delete-tmp-image', 'deleteTmpImage');
    });

// Street Data Form Field 
Route::controller(StreetDataFormFieldDataController::class)
    ->prefix('street-data')
    ->middleware('auth:sanctum')
    ->group(function () {
        Route::get('form-field-data', 'index');
    });

// Street Data Search
Route::controller(StreetDataSearchController::class)
    ->prefix('street-data/search')
    ->middleware('auth:sanctum')
    ->group(function () {
        Route::get('/', 'index');
    });

// Street Data Overview
Route::controller(OverviewController::class)
    ->prefix('street-data/overview')
    ->middleware('auth:sanctum')
    ->group(function () {
        Route::get('widget-set', 'widgetSet');
        Route::get('visual-set', 'visualSet');
        Route::get('user-performance', 'userPerformance');
    });

// Street Data Resources
Route::get('/street-data/export', [StreetDataController::class, 'export'])->middleware(['auth:sanctum', 'user_is_upline']);
Route::apiResource('street-data', StreetDataController::class)->middleware('auth:sanctum');

// Location 
Route::controller(LocationController::class)
    ->prefix('locations')
    ->middleware('auth:sanctum')
    ->group(function () {
        Route::put('activate', 'activate');
        Route::get('get-active-location', 'getActiveLocation');
    });

// Notifications
Route::controller(NotificationsController::class)
    ->prefix('notifications')
    ->middleware('auth:sanctum')
    ->group(function () {
        Route::get('all', 'allNotifications');
        Route::put('mark-as-read', 'markAsRead');
        Route::delete('delete-all', 'deleteAll');
    });

// Sector Resources
Route::apiResource('sectors', SectorController::class)->middleware('auth:sanctum')->only('store');

// External Listings
Route::controller(ExternalListingsController::class)
    ->prefix('external-listings')
    ->middleware('auth:sanctum')
    ->group(function () {
        Route::get('export', 'export');
        Route::get('listings', 'paginatedListings');
        Route::get('listings/{id}', 'show');
        Route::post('listings', 'store');
        Route::put('listings/{id}', 'update');
        Route::delete('listings/{id}', 'destroy');
    });

Route::controller(\App\Http\Controllers\ExternalListings\OverviewController::class)
    ->prefix('external-listings/overview')
    ->middleware('auth:sanctum')
    ->group(function () {
        Route::get('widget-set', 'widgetSet');
        Route::get('visual-set', 'visualSet');
        Route::get('agent-performance', 'agentPerformance');
    });

Route::apiResource('external-listings/agents', \App\Http\Controllers\ExternalListings\AgentsController::class)
    ->middleware('auth:sanctum');

// Property Data Group
Route::group(
    [
        'name' => 'property-data.',
        'prefix' => 'property-data'
    ],
    function () {
        // Property Initial
        Route::get('/initial', [PropertyInitialController::class, 'getInitialData']);

        // Propert Data Index
        Route::controller(PropertyDataResourceCreationController::class)
            ->group(function () {
                Route::post('/create-resource', 'createResource');
            });

        // Property Regions
        Route::controller(PropertyRegionsController::class)
            ->prefix('regions')
            ->group(function () {
                Route::get('', 'getRegions');
            });

        // Property Locations
        Route::controller(PropertyLocationsController::class)
            ->prefix('locations')
            ->group(function () {
                Route::get('', 'getLocations');
            });

        // Property Sections
        Route::controller(PropertySectionsController::class)
            ->prefix('sections')
            ->group(function () {
                Route::get('', 'getSections');
            });

        // Property LGAs
        Route::controller(PropertyLgasController::class)
            ->prefix('lgas')
            ->group(function () {
                Route::get('', 'getLgas');
            });

        // Property LCDAs
        Route::controller(PropertyLcdasController::class)
            ->prefix('lcdas')
            ->group(function () {
                Route::get('', 'getLcdas');
            });

        // Property Sub-Sectors
        Route::controller(PropertySubSectorsController::class)
            ->prefix('sub-sectors')
            ->group(function () {
                Route::get('', 'getSubSectors');
            });

        // Property Developers
        Route::controller(PropertyDevelopersController::class)
            ->prefix('developers')
            ->group(function () {
                Route::get('', 'getDevelopers');
                Route::get('/{id}', 'getDeveloperById');
            });
        
        // Property Listing Agents
        Route::controller(PropertyListingAgentsController::class)
            ->prefix('listing-agents')
            ->group(function () {
                Route::get('', 'getPaginatedListingAgents');
                Route::get('/{id}', 'getListingAgentById');
            });

        // Property Listing Sources
        Route::controller(PropertyListingSourcesController::class)
            ->prefix('listing-sources')
            ->group(function () {
                Route::get('', 'getListingSources');
                Route::get('/{id}', 'getListingSourceById');
            });
    }
);
