<?php

namespace App\Services;

use App\Facades\ImageUploaderFacade;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
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
    public function compressImage(UploadedFile $uploadedFile, int $desiredWidth = 500): ImageInterface
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
    public function generateImageTmpPath(UploadedFile $uploadedFile): array
    {
        $imageClientName = $uploadedFile->getClientOriginalName();
        $imageNewName = time() . '-' . str($imageClientName)->replace(' ', '');
        $storagePath = storage_path("app/public/tmp/{$imageNewName}");
        $publicUrl = "storage/tmp/{$imageNewName}";
        return [$storagePath, $publicUrl];
    }

    /**
     * Delete Image. By default images are only deleted from tmp folder
     * unless disk name is specified 
     *
     * @param string $imageUrl
     * @param string $diskName
     * @return bool
     */
    public function deleteImage(?string $imageUrl, string $diskName = ImageUploaderFacade::TMP_DISK_NAME): bool
    {
        if (!$imageUrl) return false;

        $storedImageBasename = basename($imageUrl);
        if (Storage::disk($diskName)->exists($storedImageBasename)) {
            return Storage::disk($diskName)->delete($storedImageBasename);
        }

        return false;
    }

    /**
     * Moves image from temp store to destination directory and returns
     * public url of new location
     *
     * @param string $imageUrl
     * @param string $destinationDir
     * @return bool | string
     */
    public function moveImage(string $imageUrl, string $destinationDir, string $prefix = ''): bool|string
    {
        $tempStoragePath = $this->getStoragePath($imageUrl);
        if (Storage::exists($tempStoragePath)) {
            $basename = basename($tempStoragePath);
            $destStoragePath = "{$destinationDir}/{$prefix}-{$basename}";
            Storage::move($tempStoragePath, $destStoragePath);
            return preg_replace('/^\/public/', '/storage', $destStoragePath);
        }
        return false;
    }

    private function getStoragePath(string $imageUrl): string
    {
        $imagePath = parse_url($imageUrl, PHP_URL_PATH);
        return preg_replace('/^\/storage/', '/public', $imagePath);
    }
}
