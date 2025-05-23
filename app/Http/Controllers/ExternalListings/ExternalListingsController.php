<?php

namespace App\Http\Controllers\ExternalListings;

use App\Exports\ExternalListingExport;
use App\Http\Controllers\Controller;
use App\Http\Requests\ExternalListing;
use App\Http\Requests\ExternalListingRequest;
use App\Services\ExternalListingsService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Maatwebsite\Excel\Facades\Excel;

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
                $sort_by,
                Auth::user()->isUpline() // Check if the user is an upline
            )
        );
    }

    public function show(string $id)
    {
        $view = filter_var(request()->query('view'), FILTER_VALIDATE_BOOLEAN);
        return response()->json($this->externalListingsService->getExternalListingById($id, $view));
    }

    public function store(ExternalListingRequest $request)
    {
        $data = $request->validated();
        return response()->json($this->externalListingsService->storeExternalListing($data));
    }
   
    public function update(ExternalListingRequest $request, string $id)
    {
        $data = $request->validated();
        return response()->json($this->externalListingsService->updateExternalListing($data, $id));
    }

    public function destroy(string $id)
    {
        return response()->json($this->externalListingsService->deleteExternalListing($id));
    }

    public function export(Request $request)
    {
        $request->validate([
            'format' => 'string',
            'startDate' => 'nullable|date',
            'endDate' => 'nullable|date',
        ]);
    
        $format = $request->format;
        $startDate = $request->startDate;
        $endDate = $request->endDate;
    
        return Excel::download(new ExternalListingExport($startDate, $endDate), 
            "external-listings." . ($this->exportFormatType[$format] ?? 'xlsx')
        );
    }
}
