<?php

namespace App\Services;

use Illuminate\Http\UploadedFile;
use Intervention\Image\Drivers\Gd\Driver;
use Intervention\Image\ImageManager;
use Intervention\Image\Interfaces\ImageInterface;


class ImageUploaderService
{
    /**
     * Compress Image
     *
     * @param UploadedFile $uploadedFile
     * @param integer $desiredWidth
     * @return ImageInterface
     */
    public function compressImage(UploadedFile $uploadedFile, int $desiredWidth = 500)
    {
        $manager = new ImageManager(new Driver());
        $image = $manager->read($uploadedFile);
        $image->scaleDown(width: $desiredWidth);
        return $image;
    }

    /**
     * Generates both Storage Path and Public url
     *
     * @param UploadedFile $uploadedFile
     * @return array
     */
    public function generateImageTmpPath(UploadedFile $uploadedFile)
    {
        $imageClientName = $uploadedFile->getClientOriginalName();
        $imageNewName = time() . '-' . str($imageClientName)->replace(' ', '');
        $storagePath = storage_path('app/public/tmp/' . $imageNewName);
        $publicUrl = url('storage/tmp/' . $imageNewName);
        return [$storagePath, $publicUrl];
    }
}




