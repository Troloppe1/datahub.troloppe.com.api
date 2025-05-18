<?php

namespace App\Http\Controllers\PropertyData;

use App\Http\Controllers\Controller;
use App\Services\PropertyDataService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class ListingAgentsController extends Controller
{
    public function __construct(private readonly PropertyDataService $propertyDataService) {}

    public function getPaginatedListingAgents(Request $request)
    {
        // Retrieves query parameters with defaults if not provided.
        $limit = $request->query("limit", 50);
        $page = $request->query("page", 1);
        $searchQuery = $request->query("q", '');

        $cacheKey = "listing_agents_{$limit}_{$page}_" . md5($searchQuery);
        $ttl = 60 * 10; // Cache for 5 minutes

        return Cache::remember($cacheKey, $ttl, fn () => 
            $this->propertyDataService->getPaginatedListingAgentsByKeyword($limit, $page, $searchQuery)
        );
    }

    public function getListingAgentById(string $id){
        $cacheKey = "listing_agent_{$id}";
        $ttl = 60 * 10; // Cache for 5 minutes

        return Cache::remember($cacheKey, $ttl, fn () => 
            $this->propertyDataService->getListingAgentById(+$id)
        );
    }
}
