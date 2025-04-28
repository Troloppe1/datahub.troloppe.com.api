<?php

namespace App\Http\Controllers\StreetData;

use App\Exports\StreetDataExport;
use App\Http\Controllers\Controller;
use App\Http\Requests\StreetDataRequest;
use App\Http\Resources\StreetDataResource;
use App\Models\StreetData;
use App\Services\ImageUploaderService;
use App\Services\StreetDataService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use Maatwebsite\Excel\Excel;


class StreetDataController extends Controller
{
    public function __construct(
        private readonly StreetDataService $streetDataService,
        private readonly ImageUploaderService $imageUploaderService,
        private readonly Excel $excel,
    ) {
        //
    }
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        Gate::authorize('view-any', StreetData::class);
        $streetData = StreetData::with(['creator', 'section', 'location', 'sector', 'subSector'])->latest()->get();
        return StreetDataResource::collection($streetData);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StreetDataRequest $request)
    {
        $request->validated();
        $streetData = new StreetData();
        $streetData->fill($request->safe()->all());
        $streetData->creator_id = Auth::user()->id;
        $prefix = $this->streetDataService->imagePrefixGenerator($streetData->section_id, $streetData->sector_id);
        $image_perm_path = $this->imageUploaderService->moveImage($streetData->image_path, "/public/images/street-data/", $prefix);

        if ($image_perm_path) {
            $streetData->image_path = url($image_perm_path);
        }
        $streetData->save();
        return response()->json($streetData, 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $streetDatum = StreetData::find($id);
        return new StreetDataResource($streetDatum);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(StreetDataRequest $request, StreetData $streetDatum)
    {
        Gate::authorize('update', $streetDatum);
        $data = $request->safe()->all();
        $streetDatum->update($data);
        return response()->json($data);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(StreetData $streetDatum)
    {
        Gate::authorize('delete', $streetDatum);
        $streetDatum->delete();
        return response()->json([], 204);
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
    
        return $this->excel->download(
            new StreetDataExport($startDate, $endDate), 
            "street-data." . ($this->exportFormatType[$format] ?? 'xlsx')
        );
    }
    

    private $exportFormatType = [
        'excel' => 'xlsx',
        'csv' => 'csv',
    ];
}
