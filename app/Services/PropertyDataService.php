<?php

namespace App\Services;

use Exception;
use Illuminate\Database\Connection;
use \Illuminate\Database\Query\Builder;

class PropertyDataService
{
    private readonly Connection $dbConn;

    public function __construct(
        private readonly PostgresDbService $postgresDbService,
        private readonly FilterSortAndPaginateService $filterAndPaginateService,
    ) {
        $this->dbConn = $postgresDbService->dbConn;
    }

    private function getOrderedByName(Builder $builder)
    {
        return $builder->orderBy('name')->get();
    }



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
        return $this->postgresDbService->createQueryBuilder($table);
    }

    public function getInitialData()
    {
        $selectedColumns = fn(...$extraColumns) => array_merge(['id', 'name'], $extraColumns);

        $statesQueryBuilder = $this->getQueryBuilder('locations.states')->select($selectedColumns());
        $sectorsQueryBuilder = $this->getQueryBuilder('public.sectors')->select($selectedColumns());
        $offersQueryBuilder = $this->getQueryBuilder('public.offers')->select($selectedColumns());

        return [
            "states" => $this->getOrderedByName($statesQueryBuilder),
            "sectors" => $this->getOrderedByName($sectorsQueryBuilder),
            "offers" => $this->getOrderedByName($offersQueryBuilder)
        ];
    }

    public function getRegions(int $stateId = null)
    {
        $locationsQueryBuilder = $this->getQueryBuilder('locations.regions')->select(['id', 'name', 'state_id']);
        $locationsQueryBuilder->when($stateId, function (Builder $query, $stateId) {
            $query->where(['state_id' => $stateId]);
        });
        return ["regions" => $this->getOrderedByName($locationsQueryBuilder)];
    }

    public function getLocations(int $regionId = null)
    {
        $locationsQueryBuilder = $this->getQueryBuilder('locations.localities')->select(['id', 'name', 'region_id']);
        $locationsQueryBuilder->when($regionId, function ($query, $regionId) {
            $query->where(['region_id' => $regionId]);
        });
        return ["locations" => $this->getOrderedByName($locationsQueryBuilder)];
    }

    public function getSections(int $locationId = null)
    {
        $sectionsQueryBuilder = $this->getQueryBuilder('locations.sections')->select(['id', 'name', 'locality_id as location_id']);
        $sectionsQueryBuilder->when($locationId, function ($query, $locationId) {
            $query->where(['locality_id' => $locationId]);
        });
        return ["sections" => $this->getOrderedByName($sectionsQueryBuilder)];
    }

    public function getLgas(int $regionId = null)
    {
        $lgasQueryBuilder = $this->getQueryBuilder('locations.lgas')->select(['id', 'name', 'region_id']);
        $lgasQueryBuilder->when($regionId, function ($query, $regionId) {
            $query->where(['region_id' => $regionId]);
        });
        return ["lgas" => $this->getOrderedByName($lgasQueryBuilder)];
    }

    public function getLcdas(int $lgaId = null)
    {
        $lcdasQueryBuilder = $this->getQueryBuilder('locations.lcdas')->select(['id', 'name', 'lga_id']);
        $lcdasQueryBuilder->when($lgaId, function ($query, $lgaId) {
            $query->where(['lga_id' => $lgaId]);
        });
        return ["lcdas" => $this->getOrderedByName($lcdasQueryBuilder)];
    }
    public function getSubSectors(int $sectorId = null)
    {
        $subSectorsQueryBuilder = $this->getQueryBuilder('public.sub_sectors')->select(['id', 'name', 'sector_id']);
        $subSectorsQueryBuilder->when($sectorId, function ($query, $sectorId) {
            $query->where(['sector_id' => $sectorId]);
        });
        return ["sub_sectors" => $this->getOrderedByName($subSectorsQueryBuilder)];
    }

    public function getPaginatedDevelopersByKeyword($limit = 50, $page = 1, $keyword = '')
    {
        $queryBuilder = $this->getQueryBuilder('stakeholders.developers')
            ->select(['id', 'name', 'phone_number', 'email']);

        $this->getOrderedByName($queryBuilder);

        $this->filterAndPaginateService->filterByKeywordBuilder(
            $queryBuilder,
            $keyword,
            ["name", "phone_number", "email"]
        );

        return $this->filterAndPaginateService->getPaginatedData(
            $queryBuilder,
            $limit,
            $page,
            resourceName: 'developers'
        );
    }

    public function getPaginatedListingAgentsByKeyword($limit = 50, $page = 1, $keyword = '')
    {
        $queryBuilder = $this->getQueryBuilder('stakeholders.listing_agents')
            ->select(['id', 'name', 'phone_numbers', 'email']);

        $this->getOrderedByName($queryBuilder);

        $this->filterAndPaginateService->filterByKeywordBuilder(
            $queryBuilder,
            $keyword,
            ["name", "phone_numbers", "email"]
        );

        return $this->filterAndPaginateService->getPaginatedData(
            $queryBuilder,
            $limit,
            $page,
            resourceName: 'listingAgents'
        );
    }

    public function getPaginatedListingSourcesByKeyword($limit = 50, $page = 1, $keyword = '')
    {
        $queryBuilder = $this->getQueryBuilder('external_listings.listing_sources')
            ->select(['id', 'name']);

        $this->getOrderedByName($queryBuilder);

        $this->filterAndPaginateService->filterByKeywordBuilder(
            $queryBuilder,
            $keyword,
            ["name"]
        );
        return $this->filterAndPaginateService->getPaginatedData(
            $queryBuilder,
            $limit,
            $page,
            'listingSources'
        );
    }

    public function createNewState(string $stateName)
    {
        $stateName = ucwords($stateName);
        $builder = $this->getQueryBuilder("locations.states");

        try {
            $builder->insert(['name' => $stateName]);
            $newState = $builder->select(['id', 'name'])->where('name', '=', $stateName)->first();
            return ['success' => true, 'message' => "{$stateName}'s state created successfully", 'data' => ['state' => $newState]];
        } catch (Exception $e) {

            // Unique Constraint violation
            if ($e->getCode() == 23505) {
                return ['success' => false, 'message' => "{$stateName}'s state already exists"];
            }

            throw $e;
        }
    }
}
