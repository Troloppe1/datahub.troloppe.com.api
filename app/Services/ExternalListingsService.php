<?php

namespace App\Services;

use Exception;
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
     * @param string | null $sortBy Column name to sort by. Can contain column followed by sort-order eg date:desc
     *
     * @return array Paginated data including results and metadata.
     */
    public function getPaginatedData(
        $limit = 10,
        $page = 1,
        $updatedById = null,
        $stringifiedAgFilterModel = null,
        $sortBy = null
    ) {
        $updatedById = $updatedById ? intval($updatedById) : null;

        $queryBuilder = $this->getQueryBuilder()->when(
            $updatedById,
            fn($query) => $query->where('updated_by_id', '=', $updatedById)
        );

        if ($sortBy) {
            $this->filterSortAndPaginateService->sortOperation($queryBuilder, $sortBy);
        }

        if ($stringifiedAgFilterModel) {
            $agFilterModel = json_decode($stringifiedAgFilterModel, true);
            $this->filterSortAndPaginateService->filterUsingAgFilterModel($queryBuilder, $agFilterModel);
        }

        return $this->filterSortAndPaginateService->getPaginatedData($queryBuilder, $limit, $page);
    }

    public function getExternalListingById(int $id)
    {
        try {
            /**
             * @var array
             */
            $data = $this->getQueryBuilder()->where('id', '=', $id)->first();

            if (!$data) {
                abort(404, 'External Listing not found');
            }
            
            return formatServiceResponse(true, "External Listing Retrieved Successfully", $data);
        } catch (Exception $e) {
            return formatServiceResponse(false, $e->getMessage());
        }
    }
    public function storeExternalListing(array $data)
    {
        try {
            $this->getQueryBuilder("external_listings.properties")->insert($data);
            return formatServiceResponse(true, "External Listing Created Successfully");
        } catch (Exception $e) {
            return formatServiceResponse(false, $e->getMessage());
        }
    }

    public function deleteExternalListing(int $id)
    {
        // Ensure User deleting is the creator of the resource
        try {
            $this->getQueryBuilder("external_listings.properties")->delete($id);
            return formatServiceResponse(true, "External Listing Deleted Successfully");
        } catch (Exception $e) {
            return formatServiceResponse(false, $e->getMessage());
        }
    }
}
