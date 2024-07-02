<?php

namespace App\Http\Controllers\StreetData;

use App\Http\Controllers\Controller;
use App\Http\Resources\LocationResource;
use App\Http\Resources\SectionResource;
use App\Models\Location;
use App\Models\Section;
use App\Services\ActivateLocationService;
use Illuminate\Http\Request;

class FormDataController extends Controller
{
    public function __construct(private readonly ActivateLocationService $activateLocationService)
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
        // Must be returned based on the Active Location
        // Get the latest street data with this code

        $activeLocation = $request->activeLocation;
        $unique_codes = $this->activateLocationService
            ->getUniqueStreetDataCodes($activeLocation);
        $unique_codes = [...[["id" => 0, "value" => "New Entry"]], ...$unique_codes];
        
        $formDataValues = [
            'unique_codes' => $unique_codes,
            'locations' => LocationResource::collection(Location::all()),
            'sections' => SectionResource::collection(Section::all()),
        ];
        return response()->json($formDataValues);
    }
}
