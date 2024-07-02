<?php

namespace App\Services;

use App\Models\Location;
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

    /**
     * Returns the unique street data codes for an active location
     *
     * @param Location $activeLocation
     * @return array
     */
    public function getUniqueStreetDataCodes(Location $activeLocation): array
    {
        $streetDataByActiveLocation = $activeLocation->streetData();
        $uniqueCodesByActiveLocation = $streetDataByActiveLocation
            ->select([\DB::raw('MAX(id) as id'), 'unique_code as value'])
            ->groupBy('value')
            ->get();
        return $uniqueCodesByActiveLocation->toArray();
    }
}