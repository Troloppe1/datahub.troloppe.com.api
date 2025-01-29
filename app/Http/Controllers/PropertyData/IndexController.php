<?php

namespace App\Http\Controllers\PropertyData;

use App\Http\Controllers\Controller;
use App\Services\PropertyDataService;
use Illuminate\Http\Request;

class IndexController extends Controller
{
    public function __construct(private readonly PropertyDataService $propertyDataService) {}

    public function createState(Request $request)
    {
        $request->validate(['state' => 'required|string']);

        $result = $this->propertyDataService->createNewState($request->state);

        return response()->json($result, $result['success'] ? 201 : 400);
    }
}
