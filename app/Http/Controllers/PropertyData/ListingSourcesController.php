<?php

namespace App\Http\Controllers\PropertyData;

use App\Http\Controllers\Controller;
use App\Services\PropertyDataService;
use Illuminate\Http\Request;

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
}
