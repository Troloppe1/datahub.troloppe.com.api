<?php

use App\Http\Controllers\RouteController;
use App\Http\Controllers\StreetData\StreetDataController;
use Illuminate\Support\Facades\Route;

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

Route::get('/', [RouteController::class, 'index']);

Route::get('/street-data/export', [StreetDataController::class, 'export'])->name('street-data.export')->middleware('auth:web');