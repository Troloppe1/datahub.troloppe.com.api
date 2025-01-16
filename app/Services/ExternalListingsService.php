<?php

namespace App\Services;

use \Illuminate\Database\Query\Builder;
use Illuminate\Support\Facades\DB;

class ExternalListingsService
{
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
        // Connects to the PostgreSQL database and sets up the query builder for the specified table.
        $postgresDb = DB::connection("alt_pgsql");
        return $postgresDb->table($table);
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
     *
     * @return array Paginated data including results and metadata.
     */
    public function getPaginatedData($limit = 10, $page = 1, $updatedById = null)
    {
        // Sanitize input parameters
        $limit = max(1, intval($limit));
        $page = max(1, intval($page));
        $updatedById = $updatedById ? intval($updatedById) : null;

        // Build the query
        $query = $this->getQueryBuilder()->when(
            $updatedById,
            fn($query) => $query->where('updated_by_id', '=', $updatedById)
        );

        // Get total records
        $totalRecords = $query->count();

        // Return early if no records are found
        if ($totalRecords === 0) {
            return [
                "data" => [],
                "totalPages" => 0,
                "limit" => $limit,
                "totalRecords" => 0,
                "currentPage" => $page,
                "nextPage" => null,
                "prevPage" => null
            ];
        }

        // Calculate pagination details
        $totalPages = ceil($totalRecords / $limit);
        $offset = ($page - 1) * $limit;
        $nextPage = $page < $totalPages ? $page + 1 : null;
        $prevPage = $page > 1 ? $page - 1 : null;

        // Retrieve paginated data
        $data = $query
            ->limit($limit)
            ->offset($offset)
            ->orderBy('Date', 'desc')
            ->get();

        // Return data with metadata
        return [
            "data" => $data,
            "totalPages" => $totalPages,
            "limit" => $limit,
            "totalRecords" => $totalRecords,
            "currentPage" => $page,
            "nextPage" => $nextPage,
            "prevPage" => $prevPage
        ];
    }
}
