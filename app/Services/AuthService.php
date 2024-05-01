<?php

namespace App\Services;
use App\Jobs\SendLoginOtpMailJob;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class AuthService{
    
    public function login(array $creds): ?string
    {
        if (Auth::attempt($creds)){
            $user = Auth::user();
            $token = $user->createToken('auth-api-token')->plainTextToken;
            return $token;
        }
        return null;
    }

    public function logout()
    {
        Auth::user()->tokens()->delete();
    }

    public function generateOTP(string $email): void
    {
        $user = User::where(['email' => $email])->first();

        if (!$user) {
            throw new \Exception('User does not exist.', Response::HTTP_NOT_FOUND);
        }
        
        $user->createOTP();

        // Send Login OTP Mail to user
        dispatch(new SendLoginOtpMailJob($user));
    }

    public function verifyOTP(string $email, string $otp) 
    {
        $user = User::where(['email' => $email])->first();

        // Throw Exception for user not found
        if (!$user) {
            throw new \Exception('User does not exist.', Response::HTTP_NOT_FOUND);
        }

        try {
            $user->verifyOTP($otp);
            $user->deleteOTP();
        } catch(\Exception $e) {
            $user->deleteOTP();
            throw new \Exception($e->getMessage(), Response::HTTP_BAD_REQUEST);
        }
    }
}