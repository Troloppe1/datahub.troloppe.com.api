<?php

namespace App\Facades;

use App\Services\ImageUploaderService;
use Illuminate\Support\Facades\Facade;

class ImageUploaderFacade extends Facade
{
    const TMP_DISK_NAME = "tmp";
    const STREET_DATA_DISK_NAME = "street_data_images";

    protected static function getFacadeAccessor()
    {
        return ImageUploaderService::class;
    }
}
