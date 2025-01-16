<?php

namespace App\Http\Controllers\PropertyData;

use App\Http\Controllers\Controller;
use App\Services\PropertyDataService;
use Illuminate\Http\Request;

class SectionsController extends Controller
{
    public function __construct(private readonly PropertyDataService $propertyDataService) {}

    public function getSections(Request $request)
    {
        $locationId = $request->query('location_id');
        return response()->json($this->propertyDataService->getSections($locationId));
    }
}
