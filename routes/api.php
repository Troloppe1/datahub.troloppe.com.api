<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\ImageUploader;
use App\Http\Controllers\LocationController;
use App\Http\Controllers\StreetData\FormDataController;
use App\Http\Controllers\StreetData\StreetDataController;
use App\Http\Resources\LocationResource;
use App\Models\Location;
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
    Route::post('/verify-user', "verifyUser")->name('verify-user');
    Route::post('/login', 'login')->name('login');

    Route::post('/forgot-password', 'forgotPassword')->name('forgot-password');
    Route::post('/reset-password', 'resetPassword')->name('reset-password');

    Route::middleware('auth:sanctum')->group(function () {
        Route::delete('/logout', 'logout')->name('logout');
        Route::post('/change-password', 'changePassword')->name('change-password');
    });
});

Route::controller(ImageUploader::class)->middleware('auth:sanctum')->name('temp-image-uploader.')->group(function () {
    Route::post('/store-temp-image', 'storeToTmp')->name('store-to-tmp');
    Route::delete('/delete-image', 'deleteImage')->name('delete-image');
});

Route::controller(FormDataController::class)->prefix('street-data')->name('street-data.')->group(function () {
    Route::get('form-data', 'formData')->name('form-data');
});

Route::apiResource('street-data', StreetDataController::class)->middleware('auth:sanctum');

Route::controller(LocationController::class)->name('locations.')->prefix('locations')->group(function () {
    Route::put('activate', 'activate')->name('activate')->middleware('auth:sanctum');
});









Route::get('/test', function () {
    return LocationResource::collection(Location::all());
});

// TESTING

Route::get('new-street-data-form', function (Request $request) {
    return getTestData();
});

Route::put('set-active-location', function (Request $request) {

    $data = $request->validate([
        "locationId" => 'numeric',
    ]);

    $locations = getTestData('locations');
    $idx = 0;
    while (true) {
        if ($locations[$idx]->isActive) {
            $locations[$idx]->isActive = false;
            break;
        }
        $idx++;
    };

    if ($data['locationId']) {
        $locationIdToActivate = $data['locationId'];
        $locations = array_map(function ($location) use ($locationIdToActivate) {
            if ($location->id === $locationIdToActivate) {
                $location->isActive = true;
            }
            return $location;
        }, $locations);
        setTestData('locations', $locations);
        $activeLocations = array_filter($locations, fn($location) => $location->id === $locationIdToActivate);
        return response()->json(['activeLocation' => $activeLocations[0]]);
    }

    return response()->json([], 204);
});