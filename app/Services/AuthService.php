<?php

namespace App\Services;

use App\Mail\PasswordResetOtpMail;
use Illuminate\Http\Request;
use App\Jobs\SendPasswordResetOtpMailJob;
use App\Models\User;
use Carbon\Carbon;
use Exception;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Mail;
use InvalidArgumentException;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Hash;

class AuthService
{

    /**
     * Log in operation
     *
     * @param array $creds
     * @return string|null
     */
    public function login(array $creds): ?string
    {
        if (Auth::attempt($creds)) {
            $user = Auth::user();
            $token = $user->createToken('auth-api-token')->plainTextToken;
            return $token;
        }
        return null;
    }

    /**
     * Log out operation
     *
     * @return void
     */
    public function logout()
    {
        Auth::user()->tokens()->delete();
    }

    /**
     * Generates OTP and sends same as mail
     *
     * @param Request $request
     * @return void
     */
    public function sendOTPMail(Request $request): void
    {
        $user = $request->user;
        $otp = $user->createOTP();

        // Send Login OTP Mail to user
        Mail::to($user->email)->queue(new PasswordResetOtpMail($user->name, $otp));
    }

    /**
     * Verifies OTP and deletes from databases
     *
     * @param string $email
     * @param string $otp
     * @return void
     */
    public function verifyOTP(string $email, string $otp)
    {
        $user = User::where(['email' => $email])->first();

        if (!$user) {
            throw new Exception('User does not exist.', Response::HTTP_NOT_FOUND);
        }

        try {
            $user->verifyOTP($otp);
            $user->deleteOTP();
        } catch (Exception $e) {
            $user->deleteOTP();
            throw new Exception($e->getMessage(), Response::HTTP_BAD_REQUEST);
        }
    }

    /**
     * Generates reset password yoken, stores token to cache with user's email 
     * as key. Returns created token 
     * 
     * @param string $email
     * @param integer $min
     * @return string
     */
    public function getResetPasswordToken(string $email, int $min = 5): string
    {
        $expires_at = Carbon::now()->addMinutes($min);
        $token = Str::random(60);
        Cache::put($email . '_reset_token', $token, $expires_at);
        return $token;
    }

    /**
     * Changes password either using the old password or reset password token
     *
     * @param string $email
     * @param string $newPassword
     * @param string $oldPassword
     * @param string $resetPasswordToken
     * @return void
     */
    public function changePassword(string $email, string $oldPassword, string $newPassword)
    {
        $user = User::where(['email' => $email])->first();

        if (!$user) {
            throw new ModelNotFoundException('User not found.', 404);
        }

        if ($oldPassword === $newPassword) {
            throw new InvalidArgumentException("Old and new Passwords can't be thesame", 400);
        }

        if (Hash::check($oldPassword, $user->password)) {
            $user->password = $newPassword;
            $user->save();
        } else {
            throw new InvalidArgumentException('Old password is incorrect.', 401);
        }
    }
}