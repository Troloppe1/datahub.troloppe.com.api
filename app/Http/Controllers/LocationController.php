<?php

namespace App\Http\Controllers;

use App\Http\Resources\LocationResource;
use App\Models\Location;
use App\Models\User;
use App\Notifications\LocationActivated;
use App\Services\ActivateLocationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Notification;
use Symfony\Component\Translation\Exception\NotFoundResourceException;

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

        if ($activeLocation) {
            $researchStaff = User::role('Research Staff')->get();
            Notification::sendNow($researchStaff, new LocationActivated($activeLocation));
            return response()->json(['active_location' => new LocationResource($activeLocation)]);
        }


        return response(status: 204);
    }

    public function getActiveLocation(Request $request)
    {
        $activeLocation = Location::getActiveLocation();
        return new LocationResource($activeLocation);
    }
}
