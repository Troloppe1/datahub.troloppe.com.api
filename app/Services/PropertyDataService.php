<?php

namespace App\Services;

use \Illuminate\Database\Query\Builder;
use Illuminate\Support\Facades\DB;

class PropertyDataService
{
    /**
     * Initializes a query builder for the "external_listings.listings" table
     * using the PostgreSQL connection.
     * 
     * @param string $table
     *
     * @return Builder Query builder instance.
     */
    private function getQueryBuilder(string $table): Builder
    {
        // Connects to the PostgreSQL database and sets up the query builder for the specified table.
        $postgresDb = DB::connection("alt_pgsql");
        return $postgresDb->table($table);
    }

    public function getInitialData()
    {
        $selectedColumns = ['id', 'name'];

        $statesQueryBuilder = $this->getQueryBuilder('locations.states')->select($selectedColumns);
        $sectorsQueryBuilder = $this->getQueryBuilder('public.sectors')->select($selectedColumns);
        $offersQueryBuilder = $this->getQueryBuilder('public.offers')->select($selectedColumns);

        return [
            "states" => $statesQueryBuilder->orderBy('name')->get(),
            "sectors" => $sectorsQueryBuilder->orderBy('name')->get(),
            "offers" => $offersQueryBuilder->orderBy('name')->get()
        ];
    }

    public function getRegions(int $stateId = null)
    {
        $locationsQueryBuilder = $this->getQueryBuilder('locations.regions')->select(['id', 'name', 'state_id']);
        $locationsQueryBuilder->when($stateId, function (Builder $query, $stateId) {
            $query->where(['state_id' => $stateId]);
        });
        return ["regions" => $locationsQueryBuilder->orderBy('name')->get()];
    }

    public function getLocations(int $regionId = null)
    {
        $locationsQueryBuilder = $this->getQueryBuilder('locations.localities')->select(['id', 'name', 'region_id']);
        $locationsQueryBuilder->when($regionId, function ($query, $regionId) {
            $query->where(['region_id' => $regionId]);
        });
        return ["locations" => $locationsQueryBuilder->orderBy('name')->get()];
    }

    public function getSections(int $locationId = null)
    {
        $sectionsQueryBuilder = $this->getQueryBuilder('locations.sections')->select(['id', 'name', 'locality_id as location_id']);
        $sectionsQueryBuilder->when($locationId, function ($query, $locationId) {
            $query->where(['locality_id' => $locationId]);
        });
        return ["sections" => $sectionsQueryBuilder->orderBy('name')->get()];
    }
    public function getLgas(int $regionId = null)
    {
        $lgasQueryBuilder = $this->getQueryBuilder('locations.lgas')->select(['id', 'name', 'region_id']);
        $lgasQueryBuilder->when($regionId, function ($query, $regionId) {
            $query->where(['region_id' => $regionId]);
        });
        return ["lgas" => $lgasQueryBuilder->orderBy('name')->get()];
    }

    public function getLcdas(int $lgaId = null)
    {
        $lcdasQueryBuilder = $this->getQueryBuilder('locations.lcdas')->select(['id', 'name', 'lga_id']);
        $lcdasQueryBuilder->when($lgaId, function ($query, $lgaId) {
            $query->where(['lga_id' => $lgaId]);
        });
        return ["lcdas" => $lcdasQueryBuilder->orderBy('name')->get()];
    }
    public function getSubSectors(int $sectorId = null)
    {
        $subSectorsQueryBuilder = $this->getQueryBuilder('public.sub_sectors')->select(['id', 'name', 'sector_id']);
        $subSectorsQueryBuilder->when($sectorId, function ($query, $sectorId) {
            $query->where(['sector_id' => $sectorId]);
        });
        return ["sub_sectors" => $subSectorsQueryBuilder->orderBy('name')->get()];
    }
}
