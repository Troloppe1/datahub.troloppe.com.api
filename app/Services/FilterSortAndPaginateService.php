<?php

namespace App\Services;

use Illuminate\Database\Query\Builder;
use Illuminate\Support\Collection;

class FilterSortAndPaginateService
{
    /**
     *  * Applies filters from an AG-Grid filter model to a query builder instance.
     *
     * @param Builder $queryBuilder The query builder instance to apply filters to.
     * @param array $agFilterModel The filter model provided by AG-Grid
   
     * @return void
     */
    public function filterUsingAgFilterModel(Builder $queryBuilder, array $agFilterModel)
    {
        foreach ($agFilterModel as $key => $filterParams) {

            $operator = $filterParams['operator'] ?? null;
            $conditions = $filterParams['conditions'] ?? [];
            $filter = $filterParams['filter'] ?? null;

            if ($operator) {
                $filter1 = $conditions[0]['filter'];
                $filter2 = $conditions[1]['filter'];
                $queryBuilder->where(function ($query) use ($key, $filter1, $filter2, $operator) {
                    $query->where($key, 'ILIKE', $filter1);
                    if ($operator === 'OR') {
                        $query->orWhere(...[$key, 'ILIKE', "%$filter2%"]);
                    } else {
                        $query->where(...[$key, 'ILIKE', "%$filter2%"]);
                    }
                });
            } else {
                $queryBuilder->where(...[$key, 'ILIKE', "%$filter%"]);
            }
        }
    }

    /**
     *  * Applies sorting to a query builder instance.
     *
     * @param Builder $queryBuilder The query builder instance to apply filters to.
     * @param string $sortBy Column name to sort by. Can contain column followed by sort-order eg date:desc
   
     * @return void
     */
    public function sortOperation(Builder $queryBuilder, string $sortBy)
    {
        [$sortColumn, $sortOrder] = str_contains($sortBy, ':') ? explode(":", $sortBy) : [$sortBy, 'asc'];
        $queryBuilder->orderBy($sortColumn, $sortOrder);
    }

    public function filterByKeywordBuilder(Builder $queryBuilder, string $keyword, array $searchColumns = [])
    {
        if (count($searchColumns) > 0) {

            $queryBuilder->when($keyword, function ($queryBuilder, $keyword) use ($searchColumns) {
                foreach ($searchColumns as $column) {
                    $queryBuilder->orWhere($column, 'ILIKE', "%{$keyword}%");
                }
            });
        }
    }

    /**
     * Retrieves paginated data from the external listings table, with optional filtering
     * based on the `updated_by_id` parameter.
     *
     * @param Builder $queryBuilder Setup initial query builder
     * @param int $limit Number of records per page (default: 10).
     * @param int $page The current page number (default: 1).
     *
     * @return array Paginated data including results and metadata.
     */
    public function getPaginatedData(Builder $queryBuilder, $limit = 10, $page = 1, $resourceName = 'data')
    {
        // Sanitize input parameters
        $limit = max(1, intval($limit));
        $page = max(1, intval($page));

        // Get total records
        $totalRecords = $queryBuilder->count();

        // Return early if no records are found
        if ($totalRecords === 0) {
            return $this->formatPaginatedData(limit: $limit, page: $page, resourceName: $resourceName);
        }

        // Calculate pagination details
        $totalPages = ceil($totalRecords / $limit);
        $offset = ($page - 1) * $limit;
        $nextPage = $page < $totalPages ? $page + 1 : null;
        $prevPage = $page > 1 ? $page - 1 : null;


        // Retrieve paginated data
        $data = $queryBuilder
            ->limit($limit)
            ->offset($offset)
            ->get();


        // Return data with metadata
        return $this->formatPaginatedData(
            $data,
            $totalPages,
            $limit,
            $totalRecords,
            $page,
            $nextPage,
            $prevPage,
            resourceName: $resourceName
        );
    }
    /**
     * Format data with pagination metadata.
     *
     * This function formats the provided data into a standardized structure 
     * containing pagination metadata, including total pages, limit, total records, 
     * current page, and links to the next and previous pages.
     * 
     * @param Collection|array $data
     * @param int $totalPages
     * @param int $limit
     * @param int $totalRecords
     * @param int $page
     * @param int $nextPage
     * @param int $prevPage
     * @return array
     */
    private function formatPaginatedData(
        Collection | array $data = [],
        int $totalPages = 0,
        int $limit = 10,
        int $totalRecords = 0,
        int $page = 1,
        int $nextPage = null,
        int $prevPage = null,
        string $resourceName = 'data'
    ) {

        return [
            $resourceName => $data,
            "totalPages" => $totalPages,
            "limit" => $limit,
            "totalRecords" => $totalRecords,
            "currentPage" => $page,
            "nextPage" => $nextPage,
            "prevPage" => $prevPage
        ];
    }
}
