<?php

namespace App\Http\Controllers\PropertyData;

use App\Http\Controllers\Controller;
use App\Services\PropertyDataService;

class InitialController extends Controller
{
    public function __construct(private readonly PropertyDataService $propertyDataService) {}

    public function getInitialData()
    {
        return response()->json($this->propertyDataService->getInitialData());
    }
}
