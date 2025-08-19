<?php

use App\Http\Controllers\InvestmentData\InvestmentDataController;
use Illuminate\Support\Facades\Route;

Route::controller(InvestmentDataController::class)
    ->group(function () {
        Route::get('listings', 'paginatedListings');
    });

