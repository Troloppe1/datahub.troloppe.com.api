<?php

namespace App\Http\Controllers\ExternalListings;

use App\Http\Controllers\Controller;
use App\Services\ExternalListingsService;
use Illuminate\Http\Request;

class OverviewController extends Controller
{
    public function __construct(private readonly ExternalListingsService $externalListingsService)
    {

    }
    public function widgetSet()
    {
        return response()->json($this->externalListingsService->sumForWidgets());
    }

    public function visualSet(Request $request){
        $type = $request->query('type', 'sectors');
        return response()->json($this->externalListingsService->visualSet($type));
    }
   
    public function agentPerformance(){
        return response()->json($this->externalListingsService->agentPerformance());
    }
}
