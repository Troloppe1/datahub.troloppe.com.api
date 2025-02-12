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
        return response()->json($this->externalListingsService->getAllListingAgents());
    }

    public function show(string $id)
    {
        return response()->json($this->externalListingsService->getListingAgentById(+$id));
    }
}
