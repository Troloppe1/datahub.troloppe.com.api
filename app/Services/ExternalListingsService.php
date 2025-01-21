<?php

namespace App\Services;

use \Illuminate\Database\Query\Builder;

class ExternalListingsService
{
    public function __construct(
        private readonly PostgresDbService $postgresDbService,
        private readonly FilterSortAndPaginateService $filterSortAndPaginateService,
    ) {}

    /**
     * Initializes a query builder for the "external_listings.listings" table
     * using the PostgreSQL connection.
     * 
     * @param string $table
     *
     * @return Builder Query builder instance.
     */
    private function getQueryBuilder(string $table = "external_listings.listings"): Builder
    {
        return $this->postgresDbService->createQueryBuilder($table);
    }

    /**
     * Retrieves all records from the external listings table.
     *
     * @return \Illuminate\Support\Collection Collection of all records.
     */
    public function getAll()
    {
        // Fetches all records without filtering or pagination.
        return $this->getQueryBuilder()->get();
    }

    /**
     * Retrieves paginated data from the external listings table, with optional filtering
     * based on the `updated_by_id` parameter.
     *
     * @param int $limit Number of records per page (default: 10).
     * @param int $page The current page number (default: 1).
     * @param int|null $updatedById Filter results by `updated_by_id` (optional).
     * @param string | null $stringifiedAgFilterModel JSON Stringified AG Grid Filter model from the client 
     *
     * @return array Paginated data including results and metadata.
     */
    public function getPaginatedData($limit = 10, $page = 1, $updatedById = null, $stringifiedAgFilterModel = null)
    {
        $updatedById = $updatedById ? intval($updatedById) : null;

        $queryBuilder = $this->getQueryBuilder()->when(
            $updatedById,
            fn($query) => $query->where('updated_by_id', '=', $updatedById)
        );

        if ($stringifiedAgFilterModel) {
            $agFilterModel = json_decode($stringifiedAgFilterModel, true);
            $this->filterSortAndPaginateService->filterUsingAgFilterModel($queryBuilder, $agFilterModel);
        }
        return $this->filterSortAndPaginateService->getPaginatedData($queryBuilder, $limit, $page);
    }
}
