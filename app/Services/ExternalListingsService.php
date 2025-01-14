<?php

namespace App\Services;

use \Illuminate\Database\Query\Builder; // Importing the Builder class for query building.
use Illuminate\Support\Facades\DB; // Importing Laravel's DB facade for database interactions.

class ExternalListingsService
{
    /**
     * Gets the query builder instance for the external listings view.
     *
     * @return Builder
     */
    private function getViewBuilder(): Builder
    {
        // Establishes a connection to the PostgreSQL database and prepares a query builder for the "external_listings.listings" table.
        $postgresDb = DB::connection("pgsql");
        return $postgresDb->table("external_listings.listings");
    }

    /**
     * Fetches all records from the external listings table.
     *
     * @return \Illuminate\Support\Collection
     */
    public function getAll()
    {
        // Uses the query builder to retrieve all records.
        return $this->getViewBuilder()->get();
    }

    /**
     * Fetches paginated data from the external listings table.
     *
     * @param int|null $updatedById Filter records by the 'updated_by_id' column (optional).
     * @param int $limit Number of records per page.
     * @param int $pageNumber Current page number.
     * @return array Returns the paginated data, including total pages, records, and metadata.
     */
    public function getPaginatedData(int $limit = 10, int $pageNumber = 1, int|null $updatedById = null)
    {
        // Applies a filter on the query if $updatedById is provided.
        $viewBuilder = $this->getViewBuilder()->when(
            $updatedById,
            function ($query, $updatedById) {
                $query->where('updated_by_id', '=', $updatedById);
            }
        );

        // Calculates the total number of records that match the query.
        $totalRecords = $viewBuilder->count();
        // Calculates the total number of pages based on the limit.
        $totalPages = ceil($totalRecords / $limit);
        // Calculates the offset for the current page.
        $offset = ($pageNumber - 1) * $limit;
        // Calculates next page.
        $nextPage = $pageNumber < $totalPages ? $pageNumber + 1 : null;
        // Calculates next page.
        $prevPage = $pageNumber > 1 ? $pageNumber - 1 : null;

        // Retrieves the records for the current page.
        $results =  $viewBuilder
            ->limit($limit)
            ->skip($offset)
            ->get();

        // Returns the paginated data with metadata.
        return [
            "results" => $results,
            "totalPages" => $totalPages,
            "recordsPerPage" => $limit,
            "totalRecords" =>  $totalRecords,
            "currentPage" => $pageNumber,
            "nextPage" => $nextPage,
            "prevPage" => $prevPage
        ];
    }
}
