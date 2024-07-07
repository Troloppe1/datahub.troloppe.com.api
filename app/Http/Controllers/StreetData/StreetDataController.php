<?php

namespace App\Http\Controllers\StreetData;

use App\Http\Controllers\Controller;
use App\Http\Requests\StreetDataRequest;
use App\Http\Resources\StreetDataResource;
use App\Models\StreetData;
use App\Services\ImageUploaderService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;

class StreetDataController extends Controller
{
    public function __construct(private readonly ImageUploaderService $imageUploaderService)
    {
        //
    }
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        Gate::authorize('view-any', StreetData::class);
        \Log::debug(request()->route()->getName());
        $streetData = StreetData::with(['creator', 'section', 'location'])->latest()->get();
        return StreetDataResource::collection($streetData);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StreetDataRequest $request)
    {
        $streetData = new StreetData();
        $streetData->fill($request->input());
        $streetData->creator_id = Auth::user()->id;
        $streetData->location_id = $request->location;
        $streetData->section_id = $request->section;
        $image = $request->image;
        $streetData->image_path = $image;

        $image_path = $this->imageUploaderService->moveImage($image, '/public/images/street-data');

        if ($image_path) {
            $streetData->image_path = url($image_path);
        }
        $streetData->save();
        return response()->json($request->safe()->all(), 201);
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
        \Gate::authorize('update', $streetDatum);
        $data = $request->safe()->all();
        $streetDatum->update($data);
        return response()->json($data);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(StreetData $streetDatum)
    {
        \Gate::authorize('delete', $streetDatum);
        $streetDatum->delete();
        return response()->json([], 204);
    }
}
