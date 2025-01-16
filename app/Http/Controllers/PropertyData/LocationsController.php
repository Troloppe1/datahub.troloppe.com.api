<?php

namespace App\Http\Controllers\PropertyData;

use App\Http\Controllers\Controller;
use App\Services\PropertyDataService;
use Illuminate\Http\Request;

class LocationsController extends Controller
{
    public function __construct(private readonly PropertyDataService $propertyDataService) {}

    public function getLocations(Request $request)
    {
        $regionId = $request->query('region_id');
        return response()->json($this->propertyDataService->getLocations($regionId));
    }
}
