<?php

namespace App\Providers;

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
