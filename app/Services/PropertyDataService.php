<?php

namespace App\Services;

use App\QueryBuilders\PostgresDatahubDbBuilder;
use Exception;
use Illuminate\Database\Connection;
use \Illuminate\Database\Query\Builder;

class PropertyDataService
{

    public function __construct(
        private readonly PostgresDatahubDbBuilder $postgresDatahubDbBuilder,
        private readonly FilterSortAndPaginateService $filterAndPaginateService,
    ) {
       
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
        return $this->postgresDatahubDbBuilder->createQueryBuilder($table);
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

    public function getRegions(?int $stateId = null)
    {
        $locationsQueryBuilder = $this->getQueryBuilder('locations.regions')->select(['id', 'name', 'state_id']);
        $locationsQueryBuilder->when($stateId, function (Builder $query, $stateId) {
            $query->where(['state_id' => $stateId]);
        });
        return ["regions" => $this->getOrderedByName($locationsQueryBuilder)];
    }

    public function getLocations(?int $regionId = null)
    {
        $locationsQueryBuilder = $this->getQueryBuilder('locations.localities')->select(['id', 'name', 'region_id']);
        $locationsQueryBuilder->when($regionId, function ($query, $regionId) {
            $query->where(['region_id' => $regionId]);
        });
        return ["locations" => $this->getOrderedByName($locationsQueryBuilder)];
    }

    public function getSections(?int $locationId = null)
    {
        $sectionsQueryBuilder = $this->getQueryBuilder('locations.sections')->select(['id', 'name', 'locality_id as location_id']);
        $sectionsQueryBuilder->when($locationId, function ($query, $locationId) {
            $query->where(['locality_id' => $locationId]);
        });
        return ["sections" => $this->getOrderedByName($sectionsQueryBuilder)];
    }

    public function getLgas(?int $regionId = null)
    {
        $lgasQueryBuilder = $this->getQueryBuilder('locations.lgas')->select(['id', 'name', 'region_id']);
        $lgasQueryBuilder->when($regionId, function ($query, $regionId) {
            $query->where(['region_id' => $regionId]);
        });
        return ["lgas" => $this->getOrderedByName($lgasQueryBuilder)];
    }

    public function getLcdas(?int $lgaId = null)
    {
        $lcdasQueryBuilder = $this->getQueryBuilder('locations.lcdas')->select(['id', 'name', 'lga_id']);
        $lcdasQueryBuilder->when($lgaId, function ($query, $lgaId) {
            $query->where(['lga_id' => $lgaId]);
        });
        return ["lcdas" => $this->getOrderedByName($lcdasQueryBuilder)];
    }
    public function getSubSectors(?int $sectorId = null)
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

    public function getDeveloperById(int $id) {
        return $this->getQueryBuilder('stakeholders.developers')
        ->select(['id', 'name', 'phone_numbers', 'email'])->where('id', '=', $id)->first();
    }

    public function getListingAgentById(int $id) {
        return $this->getQueryBuilder('stakeholders.listing_agents')
        ->select(['id', 'name', 'phone_numbers', 'email'])->where('id', '=', $id)->first();
    }

    public function getListingSourceById(int $id) {
        return $this->getQueryBuilder('stakeholders.listing_sources')
        ->select(['id', 'name'])->where('id', '=', $id)->first();
    }

    public function createNewResource(string $resourceName, array $values)
    {
        $resourceTableMap = [
            'state' => 'locations.states',
            'region' => 'locations.regions',
            'location' => 'locations.localities',
            'section' => 'locations.sections',
            'lga' => 'locations.lgas',
            'lcda' => 'locations.lcdas',
            'subSector' => 'public.sub_sectors',
            'developer' => 'stakeholders.developers',
            'listingAgent' => 'stakeholders.listing_agents',
            'listingSource' => 'external_listings.listing_sources'
        ];

        if ($resourceName === 'section') {
            $values['locality_id'] = $values['location_id'];
            unset($values['location_id']);
        }

        if (!isset($resourceTableMap[$resourceName])) {
            return ['success' => false, 'message' => "Invalid resource: {$resourceName}"];
        }

        if (!isset($values['name'])) {
            return ['success' => false, 'message' => "Input must contain a name property with value"];
        }

        $resourceTableName = $resourceTableMap[$resourceName];
        $builder = $this->getQueryBuilder($resourceTableName);
        $statusMessages = $this->getResourceStatusMessages($resourceName, $values['name']);

        try {

            $builder->insert($values);
            $newResource = $builder->select(['id', 'name'])->where('name', '=', $values['name'])->first();
            return formatServiceResponse(true, $statusMessages['success'], [$resourceName => $newResource]);
        } catch (Exception $e) {
            // Unique Constraint violation for Postgres
            if ($e->getCode() == 23505) {
                return formatServiceResponse(false, $statusMessages['error']);
            }
            throw $e;
        }
    }

    private function getResourceStatusMessages(string $resourceName, string $resourceValueName)
    {
        $capitalizedResourceValueName = ucwords($resourceValueName);

        return [
            'success' => "{$capitalizedResourceValueName} {$resourceName} created successfully",
            'error' => "{$capitalizedResourceValueName} {$resourceName} already exists"
        ];
    }
}
