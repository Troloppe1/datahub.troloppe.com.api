<?php

namespace App\Http\Controllers;

use App\Services\ImageUploaderService;
use Illuminate\Http\Request;

class ImageUploader extends Controller
{
    public function __construct(private readonly ImageUploaderService $imageService)
    {
        //
    }

    public function storeAsTmp(Request $request)
    {
        $request->validate([
            'image' => 'required|image|mimes:jpeg,png,jpg'
        ]);

        $imageFile = $request->file('image');

        $image = $this->imageService->compressImage($imageFile);
        [$storagePath, $publicUrl] = $this->imageService->generateImageTmpPath($imageFile);
        $image->save($storagePath);
        return response()->json(['image_tmp_url' => $publicUrl]);
    }
}
