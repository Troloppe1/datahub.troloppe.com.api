<?php

namespace App\Http\Controllers\PropertyData;

use App\Http\Controllers\Controller;
use App\Services\PropertyDataService;
use Illuminate\Http\Request;

class LcdasController extends Controller
{
    public function __construct(private readonly PropertyDataService $propertyDataService) {}

    public function getLcdas(Request $request)
    {
        $lgaId = $request->query('lga_id');
        return response()->json($this->propertyDataService->getLcdas($lgaId));
    }
}
