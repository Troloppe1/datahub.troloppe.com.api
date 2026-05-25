<?php

namespace App\Services\InvestmentData;

use App\Exceptions\HttpException;
use App\Services\FilterSortAndPaginateService;
use Illuminate\Database\Query\Builder;
use App\QueryBuilders\PostgresDatahubDbBuilder;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

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
     * @param int $sectorId default 1 -> residential
     *
     * @return Builder Query builder instance.
     */
    private function getQueryBuilder(int $sectorId = 1, bool $view = true): Builder
    {
        // $schema = "investment_data";
        // $table = $view ?  "$schema.$table" . "_properties_without_amenities" : "$schema." . "properties";
        // return $this->postgresDatahubDbBuilder->createQueryBuilder($table);

        $sql = $this->propertiesWithoutAmenitiesQuery($sectorId);
        $tableOrView = $view ? DB::raw("({$sql}) as report") : "investment_data.properties";
        return $this->postgresDatahubDbBuilder->createQueryBuilder($tableOrView);
    }

    private function propertiesWithoutAmenitiesQuery(int $sectorId = 1)
    {
        return  <<<SQL
            WITH property_facility_managers AS (
                SELECT
                    pfm.property_id,
                    STRING_AGG(fm.name, ' & ') AS facility_manager
                FROM investment_data.property_facility_managers pfm
                LEFT JOIN stakeholders.facility_managers fm
                    ON fm.id = pfm.facility_manager_id
                GROUP BY pfm.property_id
            )

            SELECT
                p.id AS property_id,
                p.period AS period,

                CASE
                    WHEN p.data_rating > 0
                    THEN CONCAT(ROUND(p.data_rating * 100), '%')
                END AS data_rating,

                p.unique_code AS property_code,

                r.name AS region,
                loc.name AS locality,
                sec.name AS section,
                lga.name AS lga,
                lcda.name AS lcda,

                p.street_name AS street,
                p.street_number AS street_number,
                p.development AS development,

                s.name AS sector,
                ss.name AS building_type,

                p.sub_type AS sub_type,
                p.classification AS classification,
                p.no_of_beds AS unit_type,
                p.no_of_units AS size,

                cs.name AS construction_status,
                p.completion_year AS year_of_completion,

                pfm.facility_manager AS facility_manager,

                cc.name AS construction_company,
                d.name AS developer,

                p.available_units AS available_unit,

                CASE
                    WHEN p.sales_price IS NOT NULL THEN
                        CONCAT(
                            CASE
                                WHEN p.sales_price_currency_id = 2 THEN '$'
                                ELSE '₦'
                            END,
                            TO_CHAR(p.sales_price, 'FM999,999,999,999.00')
                        )
                END AS sales_price,

                CASE
                    WHEN p.rental_price IS NOT NULL THEN
                        CONCAT(
                            CASE
                                WHEN p.rental_price_currency_id = 2 THEN '$'
                                ELSE '₦'
                            END,
                            TO_CHAR(p.rental_price, 'FM999,999,999.00')
                        )
                END AS lease_price,

                CASE
                    WHEN p.annual_service_charge IS NOT NULL THEN
                        CONCAT(
                            CASE
                                WHEN p.annual_service_charge_currency_id = 2 THEN '$'
                                ELSE '₦'
                            END,
                            TO_CHAR(p.annual_service_charge, 'FM999,999,999.00')
                        )
                END AS service_charge

            FROM investment_data.properties p

            LEFT JOIN locations.regions r
                ON r.id = p.region_id

            LEFT JOIN locations.localities loc
                ON loc.id = p.locality_id

            LEFT JOIN locations.sections sec
                ON sec.id = p.section_id

            LEFT JOIN locations.lgas lga
                ON lga.id = p.lga_id

            LEFT JOIN locations.lcdas lcda
                ON lcda.id = p.lcda_id

            LEFT JOIN sectors s
                ON s.id = p.sector_id

            LEFT JOIN sub_sectors ss
                ON ss.id = p.sub_sector_id

            LEFT JOIN construction_status cs
                ON cs.id = p.status_id

            LEFT JOIN property_facility_managers pfm
                ON pfm.property_id = p.id

            LEFT JOIN stakeholders.construction_companies cc
                ON cc.id = p.construction_company_id

            LEFT JOIN stakeholders.developers d
                ON d.id = p.developer_id

            WHERE p.sector_id = {$sectorId}

            ORDER BY p.id
            SQL;
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
        $sectorId = 1,
        $limit = 10,
        $page = 1,
        $updatedById = null,
        $stringifiedAgFilterModel = null,
        $sortBy = null,
    ) {
        $updatedById = $updatedById ? intval($updatedById) : null;
        $queryBuilder = $this->getQueryBuilder($sectorId);

        $queryBuilder->orderByDesc("property_id");

        if ($sortBy) {
            $this->filterSortAndPaginateService->sortOperation($queryBuilder, $sortBy);
        }

        if ($stringifiedAgFilterModel) {
            $agFilterModel = json_decode($stringifiedAgFilterModel, true);
            $this->filterSortAndPaginateService->filterUsingAgFilterModel($queryBuilder, $agFilterModel);
        }

        // 🔑 Create unique cache key based on parameters
        $cacheKey = "investment_data_sector_{$sectorId}_p{$page}_l{$limit}_u{$updatedById}_s" . md5(json_encode($sortBy)) . "_f" . md5($stringifiedAgFilterModel);

        // ⏱️ Cache for 5 minutes (adjust as needed)
        $data = Cache::remember($cacheKey, now()->addMinutes(5), function () use ($queryBuilder, $limit, $page) {
            return $this->filterSortAndPaginateService->getPaginatedData($queryBuilder, $limit, $page);
        });

        return formatServiceResponse(
            "Investment data with sector ID: ($sectorId) Retrieved Successfully (cached)",
            $data,
            rawResponse: true,
        );
    }


    public function getInvestmentDataListingById(int $id,  bool $view = true, int $sectorId = 1)
    {
        /**
         * @var array
         */
        $data =  $this->getQueryBuilder($sectorId)->where('property_id', '=', $id)->first();
        $secondayData = $this->getQueryBuilder($sectorId, false)
            ->where('id', '=', $id)
            ->first();

        $data = array_merge((array)$data, ["source_data" => (array)$secondayData]);

        if (!$data) {
            throw new HttpException('Investment Data not found', 404);
        }

        //get previous property id
        $previousPropertyId = $this->getQueryBuilder($sectorId)
            ->where('property_id', '<', $id)
            ->orderByDesc('property_id')
            ->value('property_id');

        // get next property_id
        $nextPropertyId = $this->getQueryBuilder($sectorId)
            ->where('property_id', '>', $id)
            ->orderBy('property_id')
            ->value('property_id');

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

    public function getInvestmentDataSectors()
    {
        $data = $this->postgresDatahubDbBuilder
            ->createQueryBuilder("sectors")
            ->get();

        $newData = $data->map(function ($value) {
            return [
                'key' => $value->id,
                'label' => $value->name,
                'route' => sprintf('investment-data/%s', strtolower($value->name))
            ];
        });

        return formatServiceResponse("Investment Data Sectors Retrieved Successfully", $newData);
    }
}
