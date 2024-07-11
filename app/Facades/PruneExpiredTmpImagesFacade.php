<?php

namespace App\Facades;

use App\Services\PruneExpiredTmpImageService;
use Illuminate\Support\Facades\Facade;

class PruneExpiredTmpImagesFacade extends Facade
{
    public static function getFacadeAccessor()
    {
        return PruneExpiredTmpImageService::class;
    }
}