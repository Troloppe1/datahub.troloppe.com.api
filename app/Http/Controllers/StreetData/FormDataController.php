<?php

namespace App\Http\Controllers\StreetData;

use App\Http\Controllers\Controller;
use App\Http\Resources\LocationResource;
use App\Http\Resources\SectionResource;
use App\Models\Location;
use App\Models\Section;
use Illuminate\Http\Request;

class FormDataController extends Controller
{
    public function formData()
    {
        $unique_codes = [["id" => 0, "value" => "Not Available"], ["id" => 1, "value" => "ST001"], ["id" => 2, "value" => "ST002"], ["id" => 3, "value" => "ST003"], ["id" => 4, "value" => "ST004"], ["id" => 5, "value" => "ST005"]];

        $mergedData = [
            'unique_codes' => $unique_codes,
            'locations' => LocationResource::collection(Location::all()),
            'sections' => SectionResource::collection(Section::all()),
        ];

        return response()->json($mergedData);
    }
}
