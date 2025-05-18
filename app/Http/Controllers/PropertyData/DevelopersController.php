<?php

namespace App\Http\Controllers\PropertyData;

use App\Http\Controllers\Controller;
use App\Services\PropertyDataService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class DevelopersController extends Controller
{
    public function __construct(private readonly PropertyDataService $propertyDataService) {}

    public function getDevelopers(Request $request)
    {
        // Retrieves query parameters with defaults if not provided.
        $limit = $request->query("limit", 50);
        $page = $request->query("page", 1);
        $searchQuery = $request->query("q", '');
        return $this->propertyDataService->getPaginatedDevelopersByKeyword($limit, $page, $searchQuery);
    }

    public function getDeveloperById(string $id){
        $cacheKey = "developer_{$id}";
        $ttl = 60 * 10; // Cache for 5 minutes

        return Cache::remember($cacheKey, $ttl, fn () => 
            $this->propertyDataService->getDeveloperById(+$id)
        );
    }
}
