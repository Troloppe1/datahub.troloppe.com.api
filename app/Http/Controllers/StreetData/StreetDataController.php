<?php

namespace App\Http\Controllers\StreetData;

use App\Http\Controllers\Controller;
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
        $streetData =  StreetData::with(['creator','section', 'location'])->get();
        return StreetDataResource::collection($streetData);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $image = $request->image;

        $image_url = $this->imageUploaderService->moveImage($image, '/public/images/street-data');
        return response()->json(['creator' => Auth::user()->name, 'image_url' => url($image_url), 'geolocation' => $request->geolocation]);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
