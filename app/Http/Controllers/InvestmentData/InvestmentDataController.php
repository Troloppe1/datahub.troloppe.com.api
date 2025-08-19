<?php

namespace App\Http\Controllers\InvestmentData;

use App\Http\Controllers\Controller;
use App\Services\InvestmentDataService;
use Illuminate\Http\Request;

class InvestmentDataController extends Controller
{
        public function __construct(private readonly InvestmentDataService $investmentDataService) {}

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
            ...$this->investmentDataService->getPaginatedData(
                $table,
                $limit,
                $page,
                $updatedById,
                $stringifiedAgFilterModel,
                $sort_by,
            )
        );
    }
}
