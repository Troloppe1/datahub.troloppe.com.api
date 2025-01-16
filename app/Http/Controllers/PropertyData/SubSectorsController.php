<?php

namespace App\Http\Controllers\PropertyData;

use App\Http\Controllers\Controller;
use App\Services\PropertyDataService;
use Illuminate\Http\Request;

class SubSectorsController extends Controller
{
    public function __construct(private readonly PropertyDataService $propertyDataService) {}

    public function getSubSectors(Request $request)
    {
        $sectorId = $request->query('sector_id');
        return response()->json($this->propertyDataService->getSubSectors($sectorId));
    }
}
