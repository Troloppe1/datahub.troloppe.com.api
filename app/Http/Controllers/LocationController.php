<?php

namespace App\Http\Controllers;

use App\Http\Resources\LocationResource;
use App\Services\ActivateLocationService;
use Illuminate\Http\Request;

class LocationController extends Controller
{
    public function __construct(
        private readonly ActivateLocationService $activateLocationService
    ) {
    }
    public function activate(Request $request)
    {
        $request->validate([
            "location_id" => 'numeric|nullable',
        ]);

        $locationIdToActivate = $request->input('location_id');
        $activeLocation = $this->activateLocationService->activate($locationIdToActivate);

        if ($activeLocation)
            return response()->json(['active_location' => new LocationResource($activeLocation)]);

        return response()->json([], status: 204);
    }
}
