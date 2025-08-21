<?php

namespace App\Http\Controllers\InvestmentData;

use App\Http\Controllers\Controller;
use App\Services\InvestmentData\ListingService as InvestmentDataListingService;
use Illuminate\Http\Request;

class InvestmentDataController extends Controller
{
    public function __construct(private readonly InvestmentDataListingService $investmentDataListingService) {}

    public function paginatedListings(Request $request)
    {
        // Retrieves query parameters with defaults if not provided.
        $table = $request->query("table", "residential");
        $limit = $request->query("limit", 10);
        $page = $request->query("page", 1);
        $updatedById = $request->query("updated_by_id");
        $sort_by = $request->query('sort_by');
        $stringifiedAgFilterModel = $request->query("ag_filter_model", null);

        return response()->json(
            ...$this->investmentDataListingService->getPaginatedData(
                $table,
                $limit,
                $page,
                $updatedById,
                $stringifiedAgFilterModel,
                $sort_by,
            )
        );
    }

    public function show(string $id)
    {
        $view = filter_var(request()->query('view'), FILTER_VALIDATE_BOOLEAN);
        $sector = request()->query('sector', 'residential');
        return response()->json(...$this
            ->investmentDataListingService
            ->getInvestmentDataListingById($id, $view, $sector));
    }
}
