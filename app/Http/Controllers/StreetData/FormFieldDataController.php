<?php

namespace App\Http\Controllers\StreetData;

use App\Http\Controllers\Controller;
use App\Http\Resources\LocationResource;
use App\Http\Resources\SectionResource;
use App\Http\Resources\SectorResource;
use App\Http\Resources\SubSectorResource;
use App\Models\Location;
use App\Models\Section;
use App\Models\Sector;
use App\Models\SubSector;
use App\Services\StreetDataService;
use Illuminate\Http\Request;

class FormFieldDataController extends Controller
{
    public function __construct(private readonly StreetDataService $streetDataService)
    {
    }

    /**
     * Returns Form Field Data Values for Street Data Creation
     * 
     *
     * @return \Response
     */
    public function index(Request $request)
    {
        // Unique Code Dependent On the Street Data created and vetted by the Research Manager

        $unique_codes = $this->streetDataService
            ->getAllUniqueStreetDataCodes();
        $unique_codes = [...[["id" => 0, "value" => "New Entry"]], ...$unique_codes];

        // Eagerly load relationships
        $locations = Location::with('sections');
        $sectors = Sector::with('subSectors');

        $formDataValues = [
            'unique_codes' => $unique_codes,
            'locations' => LocationResource::collection($locations->get()),
            'sectors' => SectorResource::collection($sectors->get()),
        ];
        return response()->json($formDataValues);
    }
}
