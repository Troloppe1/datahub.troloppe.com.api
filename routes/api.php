<?php

use App\Http\Controllers\AuthController;
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

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});


Route::controller(AuthController::class)->prefix('auth/')->name('api-auth.')->group(function(){
    Route::post('/verify-user', "verifyUser")->name('verify-user');
    Route::post('/login', 'login')->name('login');
    Route::delete('/logout', 'logout')->middleware('auth:sanctum')->name('logout');

    Route::post('/send-otp', 'sendOTPMail')->name('generate-otp');
    Route::post('/verify-otp', 'verifyOTP')->name('verify-otp');
    Route::post('/change-password', 'changePassword')->name('change-password');
});

