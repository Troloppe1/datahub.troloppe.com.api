<?php

namespace App\Providers;

use App\Services\ImageUploaderService;
use App\Services\PruneExpiredTmpImageService;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->singleton(PruneExpiredTmpImageService::class, fn() => new PruneExpiredTmpImageService());
        $this->app->singleton(ImageUploaderService::class, fn() => new ImageUploaderService());  
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Model::preventLazyLoading(!app()->isProduction());
        JsonResource::withoutWrapping();
    }
}
