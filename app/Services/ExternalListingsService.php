<?php

namespace App\Services;

use App\QueryBuilders\PostgresDatahubDbBuilder;
use Exception;
use \Illuminate\Database\Query\Builder;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;;

class ExternalListingsService
{
    public function __construct(
        private readonly PostgresDatahubDbBuilder $postgresDatahubDbBuilder,
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
        return $this->postgresDatahubDbBuilder->createQueryBuilder($table);
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
        $sortBy = null,
        $userIsUpline = false
    ) {

        // Generate a unique cache key based on query parameters
        $cacheKey = "external_listings:page_{$page}:limit_{$limit}:updatedBy_{$updatedById}:sort_{$sortBy}:filter_" . md5(json_encode($stringifiedAgFilterModel));

        // Check cache first
        return Cache::remember($cacheKey, now()->addMinutes(10), function () use ($limit, $page, $updatedById, $stringifiedAgFilterModel, $sortBy, $userIsUpline) {

            $updatedById = $updatedById ? intval($updatedById) : null;
            $queryBuilder = $this->getQueryBuilder();
            if (!$userIsUpline) {
                $queryBuilder->when(
                    $updatedById,
                    fn($query) => $query->where('updated_by_id', '=', $updatedById)
                );
            }
            
            if ($sortBy) {
                $this->filterSortAndPaginateService->sortOperation($queryBuilder, $sortBy);
            }

            if ($stringifiedAgFilterModel) {
                $agFilterModel = json_decode($stringifiedAgFilterModel, true);
                $this->filterSortAndPaginateService->filterUsingAgFilterModel($queryBuilder, $agFilterModel);
            }

            return $this->filterSortAndPaginateService->getPaginatedData($queryBuilder, $limit, $page);
        });
    }

    public function getExternalListingById(int $id, bool $view = true)
    {

        // Generate a unique cache key based on query parameters
        $cacheKey = "external_listings_by_id:id_{$id}:view_{$view}";

        $data = Cache::remember($cacheKey, now()->addMinutes(10), function () use ($id, $view) {
            $schema = "external_listings";
            $tableName = $view ? "listings" : "properties";
            /**
             * @var array
             */
            return $this->getQueryBuilder("$schema.$tableName")->where('id', '=', $id)->first();
        });

        if (!$data) {
            abort(404, 'External Listing not found');
        }

        return formatServiceResponse(true, "External Listing Retrieved Successfully", $data);
    }
    public function storeExternalListing(array $data)
    {
        try {
            $this->getQueryBuilder("external_listings.properties")->insert($data);
            $newExternalListing = $this->getQueryBuilder("external_listings.listings")->orderBy('id', 'desc')->first();
            Cache::flush();
            return formatServiceResponse(true, "External Listing Created Successfully", $newExternalListing);
        } catch (Exception $e) {
            return formatServiceResponse(false, $e->getMessage());
        }
    }

    public function updateExternalListing(array $data, int $id)
    {
        try {
            $this->getQueryBuilder("external_listings.properties")->where('id', '=', $id)->update($data);
            $updatedExternalListing = $this->getQueryBuilder("external_listings.listings")->where('id', '=', $id)->first();
            Cache::flush();
            return formatServiceResponse(true, "External Listing Updated Successfully", $updatedExternalListing);
        } catch (Exception $e) {
            return formatServiceResponse(false, $e->getMessage());
        }
    }

    public function deleteExternalListing(int $id)
    {
        $queryBuilder = $this->getQueryBuilder("external_listings.properties");
        $record =  $queryBuilder->where('id', '=', $id)->first();

        // Ensure User deleting is the creator of the resource
        if ($record->updated_by_id != Auth()->user()->id || Auth()->user()->id != 1) {
            abort(403, 'Forbidden access.');
        }
        $queryBuilder->delete($id);
        Cache::flush();
        return formatServiceResponse(true, "External Listing Deleted Successfully", $record->updated_by_id);
    }

    public function sumForWidgets()
    {
        // Generate a unique cache key 
        $cacheKey = "sum_for_widgets";

        return Cache::remember($cacheKey, now()->addMinutes(10), function () {
            $totalExternalListings = $this->getQueryBuilder()->count();
            $totalStatesCovered = $this->getQueryBuilder('locations.states')->count();
            $totalSectorsCovered = $this->getQueryBuilder('public.sectors')->count();
            $totalListingAgents = $this->getQueryBuilder('stakeholders.listing_agents')->count();

            return [
                "total_external_listings" => $totalExternalListings,
                "total_states_covered" => $totalStatesCovered,
                "total_sectors_covered" => $totalSectorsCovered,
                "total_listing_agents" => $totalListingAgents
            ];
        });
    }

    public function visualSet($type = 'sectors')
    {
        // generate unique cache key
        $cacheKey = "visual_set:type_{$type}";

        return Cache::remember($cacheKey, now()->addMinutes(10), function () use ($type) {
            if ($type === 'top-10-locations') {
                return $this->getQueryBuilder()
                    ->select("Location as name", DB::raw('count(*) as value'))
                    ->where('Location', '!=', null)
                    ->groupBy('Location')
                    ->orderBy('value', 'desc')
                    ->limit(10)
                    ->get();
            }

            return $this->getQueryBuilder()
                ->select("Sector as name", DB::raw('count(*) as value'))
                ->groupBy('Sector')
                ->get();
        });
    }

    public function agentPerformance()
    {
        $cacheKey = 'agent_performance';

        return Cache::remember($cacheKey, now()->addMinutes(10), function () {
            return $this->getQueryBuilder('external_listings.listing_agents_ranked')
                ->select(['id', 'name', 'total_listings as value'])
                ->limit(10)
                ->get();
        });
    }

    public function getAllListingAgents()
    {
        $cacheKey = 'all_listing_agents';

        $data = Cache::remember($cacheKey, now()->addMinutes(10), function () {
            $tableName = "external_listings.listing_agents_ranked";
            return $this->getQueryBuilder($tableName)->get();
        });

        return formatServiceResponse(true, "External Listing Agents Retrieved Successfully", $data);
    }

    public function getListingAgentById(int $id, bool $onlyListings = false)
    {
        $cacheKey = "listing_agents_by_id:id_{$id}:only_listings:{$onlyListings}";

        $data = Cache::remember($cacheKey, now()->addMinutes(10), function () use ($id) {

            $tableName = "external_listings.listing_agents_ranked";
            return  $this->getQueryBuilder($tableName)->where('id', '=', $id)->first();
        });

        if (!$data) {
            abort(404, 'External Listing Agent not found');
        }

        // Fetch agent's listings
        $listings =  $this->getQueryBuilder('external_listings.summary_listings')
            ->where('listing_agent_id', '=', $id)
            ->get();

        $onlyListings ? $data = $listings : $data->listings = $listings;

        return formatServiceResponse(true, "External Listing Agent Retrieved Successfully", $data);
    }

    public function updateListingAgent(int $id, array $data)
    {
        $tableName = "stakeholders.listing_agents";
        $queryBuilder = $this->getQueryBuilder($tableName);
        $agent = $queryBuilder->where('id', '=', $id)->first();

        if (!$agent) {
            abort(404, 'External Listing Agent not found');
        }

        $queryBuilder->update($data);

        $tableName = "external_listings.listing_agents_ranked";
        $data = $this->getQueryBuilder($tableName)->where('id', '=', $id)->first();
        Cache::flush();
        return formatServiceResponse(true, "External Listing Agent Updated Successfully", $data);
    }
}
