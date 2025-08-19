<?php

namespace App\Services;

use Illuminate\Support\Facades\DB;
use Illuminate\Database\Query\Builder;
use App\QueryBuilders\PostgresDatahubDbBuilder;
use Illuminate\Support\Facades\Cache;

class InvestmentDataService
{

    public function __construct(
        private readonly PostgresDatahubDbBuilder $postgresDatahubDbBuilder,
        private readonly FilterSortAndPaginateService $filterSortAndPaginateService
    ) {
    
    }

    /**
     * Initializes a query builder for the "investment_data.investments" table
     * using the PostgreSQL connection.
     *
     * @param string $table
     *
     * @return Builder Query builder instance.
     */
    private function getQueryBuilder(string $table = "residential"): Builder
    {
        $schema = "investment_data";
        $table = "$schema.$table"."_properties";
        return $this->postgresDatahubDbBuilder->createQueryBuilder($table);
    }

    /**
     * Retrieves all investment records.
     *
     * @return \Illuminate\Support\Collection Collection of all investment records.
     */
    public function getAllInvestments()
    {
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
    $table = "residential",
    $limit = 10,
    $page = 1,
    $updatedById = null,
    $stringifiedAgFilterModel = null,
    $sortBy = null,
) {
    $updatedById = $updatedById ? intval($updatedById) : null;
    $queryBuilder = $this->getQueryBuilder($table);

    $queryBuilder->orderByDesc("property ID");

    if ($sortBy) {
        $this->filterSortAndPaginateService->sortOperation($queryBuilder, $sortBy);
    }

    if ($stringifiedAgFilterModel) {
        $agFilterModel = json_decode($stringifiedAgFilterModel, true);
        $this->filterSortAndPaginateService->filterUsingAgFilterModel($queryBuilder, $agFilterModel);
    }

    // 🔑 Create unique cache key based on parameters
    $cacheKey = "investment_data_{$table}_p{$page}_l{$limit}_u{$updatedById}_s" . md5(json_encode($sortBy)) . "_f" . md5($stringifiedAgFilterModel);

    // ⏱️ Cache for 5 minutes (adjust as needed)
    $data = Cache::remember($cacheKey, now()->addMinutes(5), function () use ($queryBuilder, $limit, $page) {
        return $this->filterSortAndPaginateService->getPaginatedData($queryBuilder, $limit, $page);
    });

    return formatServiceResponse(
        true,
        "Investment data ($table) Retrieved Successfully (cached)",
        $data,
        rawResponse: true,
    );
}

}
