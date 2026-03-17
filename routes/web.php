<?php

use App\Http\Controllers\RouteController;
use App\Http\Controllers\StreetData\StreetDataController;
use Illuminate\Support\Facades\Route;
use Illuminate\Validation\UnauthorizedException;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::redirect('/', '/routes');
Route::get('/routes', [RouteController::class, 'index'])->name('api-routes');
Route::get('/unauthorized', function () {
    return response('Unauthorized...', 401);
})->name('login');