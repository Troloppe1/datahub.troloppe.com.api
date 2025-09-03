<?php

namespace App\Services\InvestmentData;

use App\Exceptions\HttpException;
use App\Services\FilterSortAndPaginateService;
use Illuminate\Database\Query\Builder;
use App\QueryBuilders\PostgresDatahubDbBuilder;
use Illuminate\Support\Facades\Cache;

class ListingService
{

    public function __construct(
        private readonly PostgresDatahubDbBuilder $postgresDatahubDbBuilder,
        private readonly FilterSortAndPaginateService $filterSortAndPaginateService
    ) {}

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
        $table = "$schema.$table" . "_properties_without_amenities";
        return $this->postgresDatahubDbBuilder->createQueryBuilder($table);
    }

    private function getAmenitiesQueryBuilder(): Builder
    {
        return $this->postgresDatahubDbBuilder
            ->createQueryBuilder("investment_data.property_amenities_with_sub_amenities")
            ->join('amenities.amenities', 'property_amenities_with_sub_amenities.amenity_id', '=', 'amenities.id')
            ->join('amenities.sub_amenities', 'property_amenities_with_sub_amenities.sub_amenity_id', '=', 'sub_amenities.id', 'left')
            ->select('property_amenities_with_sub_amenities.*', 'amenities.name as amenity_name', 'sub_amenities.name as sub_amenity_name');
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
            "Investment data ($table) Retrieved Successfully (cached)",
            $data,
            rawResponse: true,
        );
    }


    public function getInvestmentDataListingById(int $id,  bool $view = true, string $sector = 'residential')
    {
        /**
         * @var array
         */
        $data =  $this->getQueryBuilder($sector)->where('property ID', '=', $id)->first();

        if (!$data) {
            throw new HttpException('Investment Data not found', 404);
        }

        // get previous property id
        $previousPropertyId = $this->getQueryBuilder($sector)
            ->where('property ID', '<', $id)
            ->orderByDesc('property ID')
            ->value('property ID');

        // get next property id
        $nextPropertyId = $this->getQueryBuilder($sector)
            ->where('property ID', '>', $id)
            ->orderBy('property ID')
            ->value('property ID');
        
        $dataWithMeta = [
            'property' => $data,
            "meta" => [
                'previous_property_id' => $previousPropertyId,
                'next_property_id' => $nextPropertyId,
            ],
        ];
    
        return formatServiceResponse("Investment Data Retrieved Successfully", $dataWithMeta);
    }

    public function getPropertyAmenitiesById(int $id)
    {
        $data = $this->getAmenitiesQueryBuilder()
            ->where('property_id', $id)
            ->get();

        $newData = $data
            ->groupBy('amenity_name')
            ->map(function ($items, $amenity) {
                return [
                    'amenity_name' => $amenity,
                    'sub_amenities' => $items
                        ->pluck('sub_amenity_name')
                        ->filter()
                        ->values()
                        ->join(', '),
                ];
            })
            ->values()
            ->all();

        logger()->info("Amenities for property ID $id: " . json_encode($data
            ->groupBy('amenity_name')));

        return formatServiceResponse("Amenities Retrieved Successfully", $newData);
    }
}
