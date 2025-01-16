<?php

namespace App\Http\Controllers\PropertyData;

use App\Http\Controllers\Controller;
use App\Services\PropertyDataService;
use Illuminate\Http\Request;

class LgasController extends Controller
{
    public function __construct(private readonly PropertyDataService $propertyDataService) {}

    public function getLgas(Request $request)
    {
        $regionId = $request->query('region_id');
        return response()->json($this->propertyDataService->getLgas($regionId));
    }
}
