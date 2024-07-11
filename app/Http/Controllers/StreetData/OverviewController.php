<?php

namespace App\Http\Controllers\StreetData;

use App\Http\Controllers\Controller;
use App\Services\StreetDataOverviewService;
use Illuminate\Support\Facades\Gate;


class OverviewController extends Controller
{
    public function __construct(private readonly StreetDataOverviewService $streetDataOverviewService)
    {

    }
    public function widgetSet()
    {
        return response()->json($this->streetDataOverviewService->sumForWidgets());
    }

    public function visualSet()
    {
        return response()->json($this->streetDataOverviewService->forVisual());
    }
    public function userPerformance()
    {
        Gate::allowIf(auth()->user()?->isUpline());
        return response()->json($this->streetDataOverviewService->userPerformances());
    }
}