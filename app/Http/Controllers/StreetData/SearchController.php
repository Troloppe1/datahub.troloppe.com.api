<?php

namespace App\Http\Controllers\StreetData;

use App\Http\Controllers\Controller;
use App\Services\StreetDataService;
use Illuminate\Http\Request;

class SearchController extends Controller
{
    public function __construct(
        private readonly StreetDataService $streetDataService
    ) {
    }
    /**
     * Retrieve Street Data Options based on search query term
     * @param \Illuminate\Http\Request $request
     * @return array
     */
    public function index(Request $request)
    {
        return $this->streetDataService->getSearchedStreetDataOptions($request->query('q', ''));
    }
}
