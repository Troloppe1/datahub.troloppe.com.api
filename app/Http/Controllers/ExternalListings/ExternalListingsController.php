<?php

namespace App\Http\Controllers\ExternalListings;

use App\Http\Controllers\Controller;
use App\Services\ExternalListingsService;
use Illuminate\Http\Request;

class ExternalListingsController extends Controller
{
    public function __construct(private readonly ExternalListingsService $externalListingsService) {}

    public function paginatedListings(Request $request)
    {
        // Retrieves query parameters with defaults if not provided.
        $limit = $request->query("limit", 10);
        $page = $request->query("page", 1);
        $updatedById = $request->query("updated_by_id");

        return response()->json($this->externalListingsService->getPaginatedData($limit, $page, $updatedById));
    }
}
