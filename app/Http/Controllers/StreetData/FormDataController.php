<?php

namespace App\Http\Controllers\StreetData;

use App\Http\Controllers\Controller;
use App\Http\Resources\LocationResource;
use App\Http\Resources\SectionResource;
use App\Models\Location;
use App\Models\Section;
use App\Services\ActivateLocationService;
use App\Services\StreetDataService;
use Illuminate\Http\Request;

class FormDataController extends Controller
{
    public function __construct(private readonly StreetDataService $streetDataService)
    {
    }

    /**
     * Returns FormData Values for Street Data Creation
     * 
     *
     * @return \Response
     */
    public function formData(Request $request)
    {
        // Unique Code Dependent On the Street Data created and vetted by the Research Manager

        $unique_codes = $this->streetDataService
            ->getAllUniqueStreetDataCodes();
        $unique_codes = [...[["id" => 0, "value" => "New Entry"]], ...$unique_codes];

        $formDataValues = [
            'unique_codes' => $unique_codes,
            'locations' => LocationResource::collection(Location::all()),
            'sections' => SectionResource::collection(Section::all()),
        ];
        return response()->json($formDataValues);
    }
}
