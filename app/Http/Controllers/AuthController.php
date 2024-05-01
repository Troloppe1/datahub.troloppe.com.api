<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Rules\IsNumeric;
use App\Services\AuthService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{

    public function __construct(private AuthService $authService){}
    /**
     * Log in a User
     */
    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required'
        ]);

        $token = $this->authService->login($request->only('email', 'password'));
          
        if ($token){          
            return response()->json(['token' => $token]);
        } 
        return response()->json(['message' => 'The provided credentials are incorrect.'], 401);
    }

    /**
     * Log out an authenticated user
     */
    public function logout(Request $request)
    {
        Auth::user()->tokens()->delete();
        return response()->json(['message' => 'Logged out successfully']);
    }

    public function generateOTP(Request $request){
        $request->validate([
            'email' => 'required|email'
        ]);

        try {   
            $this->authService->generateOTP(...$request->only('email'));
            return response()->json([
                'message' => 'OTP created successfully.'
            ], \Symfony\Component\HttpFoundation\Response::HTTP_CREATED);
        } catch(\Exception $e){
            return response()->json([
                'message' => $e->getMessage()
            ], $e->getCode());
        }        
    }

    public function verifyOTP(Request $request): JsonResponse 
    {
        $request->validate([
            'email' => 'required|email',
            'otp' => ['required', 'string', 'size:6', new IsNumeric]
        ]);

        try {
            $this->authService->verifyOTP(...$request->only(['email', 'otp']));
            return response()->json([
                'message' => 'OTP verification success.'
            ]); 
        } catch(\Exception $e){
            return response()->json([
                'message' => $e->getMessage()
            ], $e->getCode());
        }
    }
}
