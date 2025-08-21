<?php

use App\Http\Controllers\InvestmentData\InvestmentDataController;
use Illuminate\Support\Facades\Route;

Route::controller(InvestmentDataController::class)
    ->group(function () {
        Route::get('listings', 'paginatedListings');
        Route::get('listings/{id}', 'show')
            ->where('id', '[0-9]+'); // Assuming ID is numeric
    });

