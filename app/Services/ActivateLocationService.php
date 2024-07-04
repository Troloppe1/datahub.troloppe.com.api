<?php

namespace App\Services;

use App\Models\Location;
use App\Models\StreetData;
use Filament\Notifications\Collection;

class ActivateLocationService
{
    /**
     * Activates a location
     *
     * @param integer|null|null $locationIdToActivate
     * @return Location | null
     */
    public function activate(int|null $locationIdToActivate = null)
    {
        Location::where(['is_active' => true])->update(['is_active' => false]);
        if ($locationIdToActivate) {
            $location = Location::find($locationIdToActivate);
            $location->is_active = true;
            $location->save();
            return $location;
        }

        return null;
    }

    /**
     * Get Active Location
     *
     * @return Location|null
     */
    public function getActiveLocation(): Location|null
    {
        return Location::where(['is_active' => true])->first();
    }
}