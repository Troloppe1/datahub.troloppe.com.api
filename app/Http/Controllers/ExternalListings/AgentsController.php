<?php

namespace App\Http\Controllers\ExternalListings;

use App\Http\Controllers\Controller;
use App\Services\ExternalListingsService;
use Illuminate\Http\Request;

class AgentsController extends Controller
{
    public function __construct(private readonly ExternalListingsService $externalListingsService) {}

    public function index()
    {
        return apiResponse($this->externalListingsService->getAllListingAgents());
    }

    // Returns agent with listings
    public function show(string $id, Request $request)
    {
        $onlyListing = filter_var($request->query('only_listings'), FILTER_VALIDATE_BOOLEAN);
        return apiResponse($this->externalListingsService->getListingAgentById(+$id, $onlyListing));
    }

    public function update(string $id, Request $request)
    {
        $data = $request->validate([
            'id' => 'integer|required',
            'name' => 'required|string',
            'address' => 'nullable|string',
            'phone_numbers' => 'nullable|string',
            'email' => 'nullable|string',
            'website' => 'nullable|string',
            'note' => 'nullable|string',
        ]);
        return apiResponse($this->externalListingsService->updateListingAgent(+$id, $data));
    }
}
