<?php

namespace App\Http\Controllers;

use App\Http\Requests\AuthRequest;
use App\Http\Requests\ChangePasswordRequest;
use App\Http\Requests\ResetPasswordRequest;
use App\Models\User;
use Illuminate\Validation\ValidationException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Password;
use Symfony\Component\HttpFoundation\Response as HttpResponse;

class AuthController extends Controller
{

    public function __construct()
    {
        $this->middleware('user_email_found')->only(['verifyUser']);
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
     * @return array
     */
    public function login(AuthRequest $authRequest)
    {
        $creds = $authRequest->validated();

        if (Auth::attempt($creds)) {
            return Auth::user()->getUserData();
        }

        throw ValidationException::withMessages([
            'email' => [
                __('auth.failed')
            ]
        ]);
    }

    /**
     * Logout authenticated user
     *
     * @param Request $request
     * @return void
     */
    public function logout()
    {
        Auth::logout();
        return response()->json(status: HttpResponse::HTTP_NO_CONTENT);
    }

    /**
     * Change user's password from Dashboard
     *
     * @param AuthRequest $authRequest
     * @return JsonResponse
     */
    public function changePassword(ChangePasswordRequest $changePasswordRequest): JsonResponse
    {
        $creds = $changePasswordRequest->validated();

        $user = Auth::user();
        $user->password = $creds['new_password'];
        $user->save();

        return response()->json([
            'message' => 'Password changed successfully'
        ]);
    }


    /**
     * Sends reset password token to mail
     *
     * @param AuthRequest $authRequest
     * @return JsonResponse
     */
    public function forgotPassword(AuthRequest $authRequest): JsonResponse
    {
        $authRequest->validated();

        $status = Password::sendResetLink($authRequest->only('email'));

        if ($status === Password::RESET_LINK_SENT) {
            return response()->json(['message' => __($status)]);
        }

        throw ValidationException::withMessages([
            'email' => [__($status)]
        ]);
    }

    /**
     * Sends reset password token to mail
     *
     * @param AuthRequest $authRequest
     * @return JsonResponse
     */
    public function resetPassword(ResetPasswordRequest $resetPasswordRequest): JsonResponse
    {
        $creds = $resetPasswordRequest->validated();

        $status = Password::reset(
            $creds,
            function (User $user, $new_password) {
                $user->create(['password' => $new_password]);
            }
        );

        if ($status === Password::PASSWORD_RESET) {
            return response()->json([
                'message' => __($status)
            ]);
        }

        throw ValidationException::withMessages([
            "email" => [__($status)]
        ]);
    }
}
