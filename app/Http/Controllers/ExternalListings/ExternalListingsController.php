<?php

namespace App\Http\Controllers\ExternalListings;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreExternalListing;
use App\Http\Requests\StoreExternalListingRequest;
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
        $sort_by = $request->query('sort_by');
        $stringifiedAgFilterModel = $request->query("ag_filter_model", null);

        return response()->json(
            $this->externalListingsService->getPaginatedData(
                $limit,
                $page,
                $updatedById,
                $stringifiedAgFilterModel,
                $sort_by
            )
        );
    }

    public function show(string $id)
    {
        return response()->json($this->externalListingsService->getExternalListingById($id));
    }

    public function store(StoreExternalListingRequest $request)
    {
        $data = $request->validated();
        return response()->json($this->externalListingsService->storeExternalListing($data));
    }

    public function delete(string $id)
    {
        return response()->json($this->externalListingsService->deleteExternalListing($id));
    }
}
