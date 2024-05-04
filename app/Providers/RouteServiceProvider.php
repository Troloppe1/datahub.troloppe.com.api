<?php

namespace App\Providers;

use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Foundation\Support\Providers\RouteServiceProvider as ServiceProvider;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\Route;
use Symfony\Component\HttpFoundation\Response;

class RouteServiceProvider extends ServiceProvider
{
    /**
     * The path to your application's "home" route.
     *
     * Typically, users are redirected here after authentication.
     *
     * @var string
     */
    public const HOME = '/home';

    /**
     * Define your route model bindings, pattern filters, and other route configuration.
     */
    public function boot(): void
    {
        RateLimiter::for('gen_otp', function(Request $request){  
            return Limit::perMinutes(5, 1)
                            ->by($request->email)
                            ->response(function(Request $request, array $header){
                                $seconds =  $header['Retry-After'] ?? 300;
                                return response()->json([
                                    "message" => "OTP request for {$request->email} has been suspended",
                                    "reason" => "OTP generation is currently suspended for your account. You have already requested an OTP in the last 5 minutes. Please try again after {$seconds} seconds",
                                    "retryAfter" => $seconds
                                ], Response::HTTP_TOO_MANY_REQUESTS);
                            });
        });

        RateLimiter::for('api', function (Request $request) {
            return Limit::perMinute(60)->by($request->user()?->id ?: $request->ip());
        });

        $this->routes(function () {
            Route::middleware('api')
                ->prefix('api')
                ->group(base_path('routes/api.php'));

            Route::middleware('web')
                ->group(base_path('routes/web.php'));
        });
    }
}
