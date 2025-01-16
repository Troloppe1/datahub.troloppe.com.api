<?php

namespace App\Http\Controllers\PropertyData;

use App\Http\Controllers\Controller;
use App\Services\PropertyDataService;
use Illuminate\Http\Request;

class RegionsController extends Controller
{
    public function __construct(private readonly PropertyDataService $propertyDataService) {}

    public function getRegions(Request $request)
    {
        $stateId = $request->query('state_id');
        return response()->json($this->propertyDataService->getRegions($stateId));
    }
}
