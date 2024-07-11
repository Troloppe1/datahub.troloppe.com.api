<?php

namespace App\Http\Controllers;

use App\Services\ImageUploaderService;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class ImageUploader extends Controller
{
    public function __construct(private readonly ImageUploaderService $imageService)
    {
        //
    }

    public function storeToTmp(Request $request)
    {
        $request->validate([
            'image' => 'required|image|mimes:jpeg,png,jpg,webp'
        ]);

        $imageFile = $request->file('image');

        $image = $this->imageService->compressImage($imageFile);
        [$storagePath, $publicUrl] = $this->imageService->generateImageTmpPath($imageFile);
        $image->save($storagePath);
        return response()->json(['image_tmp_url' => url($publicUrl)]);
    }

    /**
     * Deletes Image
     *
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function deleteImage(Request $request)
    {
        $request->validate([
            'image_url' => 'required|string'
        ]);

        $deleted = $this->imageService->deleteImage($request->image_url);
        if ($deleted)
            return response(status: 204);

        throw ValidationException::withMessages([
            'image_url' => 'Image does not exist.'
        ]);
    }
}
