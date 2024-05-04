<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use Symfony\Component\HttpFoundation\Response;

class ThrottleOTPRequest
{
    public function __construct(){}
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next, $maxAttempt = 1, $decayMinutes = 5): Response
    {
        $key = $request->ip();
        if(RateLimiter::tooManyAttempts($key, $maxAttempt)){
            $seconds = RateLimiter::availableIn($key);
            return response()->json([
                "message" => "OTP request for {$request->email} has been suspended",
                "reason" => "OTP generation is currently suspended for your account. You have already requested an OTP in the last 5 minutes. Please try again after {$seconds} seconds",
                "retryAfter" => $seconds
            ], Response::HTTP_TOO_MANY_REQUESTS);
        }

        RateLimiter::hit($key, $decayMinutes * 60);
        return $next($request);
    }
}
