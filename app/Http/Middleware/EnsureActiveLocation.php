<?php

namespace App\Http\Middleware;

use App\Services\ActivateLocationService;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureActiveLocation
{

    public function __construct(private ActivateLocationService $activateLocationService)
    {

    }
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $activeLocation = $this->activateLocationService->getActiveLocation();

        if ($activeLocation){
           $request->merge(['activeLocation' => $activeLocation]);
            return $next($request);
        }
         abort(404);
    }
}
