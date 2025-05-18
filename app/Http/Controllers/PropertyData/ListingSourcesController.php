<?php

namespace App\Http\Controllers\PropertyData;

use App\Http\Controllers\Controller;
use App\Services\PropertyDataService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class ListingSourcesController extends Controller
{
    public function __construct(private readonly PropertyDataService $propertyDataService) {}

    public function getListingSources(Request $request)
    {
        // Retrieves query parameters with defaults if not provided.
        $limit = $request->query("limit", 50);
        $page = $request->query("page", 1);
        $searchQuery = $request->query("q", '');
        return $this->propertyDataService->getPaginatedListingSourcesByKeyword($limit, $page, $searchQuery);
    }

    public function getListingSourceById(string $id){
        $cacheKey = "listing_source_{$id}";
        $ttl = 60 * 10; // Cache for 5 minutes

        return Cache::remember($cacheKey, $ttl, fn () => 
            $this->propertyDataService->getListingSourceById(+$id)
        );
    }
}
