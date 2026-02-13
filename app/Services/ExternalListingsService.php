<?php

namespace App\Services;

use App\Exceptions\HttpException;
use App\QueryBuilders\PostgresDatahubDbBuilder;
use Exception;
use \Illuminate\Database\Query\Builder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;;

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
    ) {


        $updatedById = $updatedById ? intval($updatedById) : null;
        $queryBuilder = $this->getQueryBuilder();

        if ($sortBy) {
            $this->filterSortAndPaginateService->sortOperation($queryBuilder, $sortBy);
        }

        if ($stringifiedAgFilterModel) {
            $agFilterModel = json_decode($stringifiedAgFilterModel, true);
            $this->filterSortAndPaginateService->filterUsingAgFilterModel($queryBuilder, $agFilterModel);
        }

        return formatServiceResponse(
            "External Listings Retrieved Successfully",
            $this->filterSortAndPaginateService->getPaginatedData($queryBuilder, $limit, $page),
            rawResponse: true,
        );
    }

    public function getExternalListingById(int $id, bool $view = true)
    {

        // Generate a unique cache key based on query parameters
        $cacheKey = "external_listings_by_id:id_{$id}:view_{$view}";

        $schema = "external_listings";
        $tableName = $view ? "listings" : "properties";
        /**
         * @var array
         */
        $data =  $this->getQueryBuilder("$schema.$tableName")->where('id', '=', $id)->first();


        if (!$data) {
            throw new HttpException('External Listing not found', 404);
        }

        return formatServiceResponse("External Listing Retrieved Successfully", $data);
    }
    public function storeExternalListing(array $data)
    {
        try {
            $this->getQueryBuilder("external_listings.properties")->insert($data);
            $newExternalListing = $this->getQueryBuilder("external_listings.listings")->orderBy('id', 'desc')->first();
            return formatServiceResponse("External Listing Created Successfully", $newExternalListing);
        } catch (Exception $e) {
            throw new HttpException($e->getMessage());
        }
    }

    public function updateExternalListing(array $data, int $id)
    {
        try {
            $externalListingQuery = $this->getQueryBuilder("external_listings.properties")->where('id', '=', $id);

            if ($externalListingQuery->where('updated_by_id', '!=', Auth()->user()->id)->exists() && !Auth()->user()->isUpline()) {
                throw new HttpException("User does not have permission to update this listing.", 403);
            }

            $this->getQueryBuilder("external_listings.properties")->where('id', '=', $id)->update($data);
            $updatedExternalListing = $this->getQueryBuilder("external_listings.listings")->where('id', '=', $id)->first();
            return formatServiceResponse("External Listing Updated Successfully", $updatedExternalListing);
        } catch (Exception $e) {
            throw new HttpException($e->getMessage());
        }
    }

    public function deleteExternalListing(int $id, bool $userIsUpline = false)
    {
        $externalListingQuery = $this->getQueryBuilder("external_listings.properties");
        $recordQuery =  $externalListingQuery->where('id', '=', $id);

        // Ensure User deleting is the creator of the resource
        if ($recordQuery->where('updated_by_id', '!=', Auth()->user()->id)->exists() && !Auth()->user()->isUpline()) {
            throw new HttpException('User does not have permission to delete this listing.', 403);
        }
        $updatedById = $recordQuery->first()->updated_by_id;
        $externalListingQuery->delete($id);
        return formatServiceResponse("External Listing Deleted Successfully", $updatedById);
    }

    public function sumForWidgets()
    {

        $totalExternalListings = $this->getQueryBuilder()->count();
        $totalStatesCovered = $this->getQueryBuilder('locations.states')->count();
        $totalSectorsCovered = $this->getQueryBuilder('public.sectors')->count();
        $totalListingAgents = $this->getQueryBuilder('stakeholders.listing_agents')->count();


        return formatServiceResponse("Sum for Widgets Retrieved Successfully", [
            "total_external_listings" => $totalExternalListings,
            "total_states_covered" => $totalStatesCovered,
            "total_sectors_covered" => $totalSectorsCovered,
            "total_listing_agents" => $totalListingAgents
        ], rawResponse: true);
    }

    public function visualSet($type = 'sectors')
    {

        if ($type === 'top-10-locations') {
            return $this->getQueryBuilder()
                ->select("Location as name", DB::raw('count(*) as value'))
                ->where('Location', '!=', null)
                ->groupBy('Location')
                ->orderBy('value', 'desc')
                ->limit(10)
                ->get();
        }

        return
            $this->getQueryBuilder()
            ->select("Sector as name", DB::raw('count(*) as value'))
            ->groupBy('Sector')
            ->get();
    }

    public function agentPerformance()
    {
        return $this->getQueryBuilder('external_listings.listing_agents_ranked')
            ->select(['id', 'name', 'total_listings as value'])
            ->limit(10)
            ->get();
    }

    public function getAllListingAgents()
    {
        $tableName = "external_listings.listing_agents_ranked";;
        return formatServiceResponse("External Listing Agents Retrieved Successfully",  $this->getQueryBuilder($tableName)->get());
    }

    public function getListingAgentById(int $id, bool $onlyListings = false)
    {
        $tableName = "external_listings.listing_agents_ranked";
        $data =  $this->getQueryBuilder($tableName)->where('id', '=', $id)->first();

        if (!$data) {
            throw new HttpException('External Listing Agent not found', 404);
        }


        // Fetch agent's listings
        $listings =  $this->getQueryBuilder('external_listings.summary_listings')
            ->where('listing_agent_id', '=', $id)
            ->get();

        $onlyListings ? $data = $listings : $data->listings = $listings;

        return formatServiceResponse("External Listing Agent Retrieved Successfully", $data);
    }

    public function updateListingAgent(int $id, array $data)
    {
        $tableName = "stakeholders.listing_agents";
        $queryBuilder = $this->getQueryBuilder($tableName);
        $agent = $queryBuilder->where('id', '=', $id)->first();

        if (!$agent) {
            throw new HttpException('External Listing Agent not found', 404);
        }

        $queryBuilder->update($data);

        $tableName = "external_listings.listing_agents_ranked";
        $data = $this->getQueryBuilder($tableName)->where('id', '=', $id)->first();
        return formatServiceResponse("External Listing Agent Updated Successfully", $data);
    }
}
