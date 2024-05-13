<?php

namespace App\Http\Controllers;

use App\Http\Requests\AuthRequest;
use App\Services\AuthService;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response as HttpResponse;
class AuthController extends Controller
{

    public function __construct(private AuthService $authService){
        $this->middleware('user_email_found')->only(['sendPasswordResetOTPMail', 'verifyUser']);
        $this->middleware('throttle_OTP')->only(['sendPasswordResetOTPMail']);
    }

    /**
     * Verifies a user based on email and returns HTTP status 200 if found
     * and 404 if not from the middleware
     *
     * @return JsonResponse
     */
    public function verifyUser(): JsonResponse
    {
        return response()->json(['message' => 'User exists'], HttpResponse::HTTP_OK);
    }
    /**
     * Login a user
     *
     * @param Request $request
     * @return void
     */
    public function login(AuthRequest $authRequest)
    {
        $authRequest->validated();

        $token = $this->authService->login($authRequest->only('email', 'password'));
          
        if ($token){          
            return response()->json(['token' => $token]);
        } 
        return response()->json(['message' => 'The provided credentials are incorrect.'], 401);
    }

    /**
     * Logout authenticated user
     *
     * @param Request $request
     * @return void
     */
    public function logout()
    {
        Auth::user()->tokens()->delete();
        return response()->json(['message' => 'Logged out successfully']);
    }

    /**
     * Generates OTP and sends same to provided email
     *
     * @param AuthRequest $authRequest
     * @return void
     */
    public function sendPasswordResetOTPMail(Request $request): JsonResponse
    {
        $this->authService->sendOTPMail($request);
        return response()->json(['message' => 'OTP created successfully.'],  HttpResponse::HTTP_CREATED);
    } 

    /**
     * Verify OTP and returns response containing unique token and expiration time in seconds
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function verifyOTP(AuthRequest $authRequest): JsonResponse 
    {
        $authRequest->validated();
        try {
            $this->authService->verifyOTP(...$authRequest->all());
            $token = $this->authService->getResetPasswordToken($authRequest->email);
    
            return response()->json([
                'message' => 'OTP verification success.',
                'resetPasswordToken' => $token,
            ]); 
        } catch(Exception $e){
            return response()->json([
                'message' => $e->getMessage()
            ], $e->getCode());
        }
    }

    /**
     * Change user's password
     *
     * @param AuthRequest $authRequest
     * @return JsonResponse
     */
    public function changePassword(AuthRequest $authRequest): JsonResponse
    {
        $authRequest->validated();

        try {
            $this->authService->changePassword(...$authRequest->all());
            return response()->json([
                'message' => 'Password changed successfully'
            ]);
        } catch(Exception $e) {
            return response()->json([
                'message' => $e->getMessage()
            ], $e->getCode());
        }
    }
}
