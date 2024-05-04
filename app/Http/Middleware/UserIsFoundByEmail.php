<?php

namespace App\Http\Middleware;

use App\Models\User;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class UserIsFoundByEmail
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {

        $user = User::where(['email' => $request->email])->first();
        $request->user = $user;

        if ($user) {
            return $next($request);
        } else {
            return response()->json([
                'message' => 'This account cannot be found. Please use a different account.'
            ], Response::HTTP_NOT_FOUND);
        }
    }
}
