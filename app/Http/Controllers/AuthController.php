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

    public function __construct(private AuthService $authService){}
    
    /**
     * Verifies a user based on email and returns a JSON response
     * containing a boolean value
     *
     * @param AuthRequest $authRequest
     * @return JsonResponse
     */
    public function verifyUser(AuthRequest $authRequest): JsonResponse
    {
        $authRequest->validated();
        return response()->json([
            "isVerified" => $this->authService->verifyUserByEmail($authRequest->email)
        ]);
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
     * @param string $email
     * @return void
     */
    public function generateOTP(AuthRequest $authRequest){
        $authRequest->validated();

        try {   
            $this->authService->generateOTP($authRequest->email);
            return response()->json([
                'message' => 'OTP created successfully.'
            ], HttpResponse::HTTP_CREATED);
        } catch(Exception $e){
            return response()->json([
                'message' => $e->getMessage()
            ], $e->getCode());
        }        
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
