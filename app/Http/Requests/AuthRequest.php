<?php

namespace App\Http\Requests;

use App\Rules\IsNumeric;
use Illuminate\Foundation\Http\FormRequest;

class AuthRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'email' => 'required|email',
            'password' => $this->routeIs('api-auth.login') ? 'required' : '',
            'otp' => $this->routeIs('api-auth.verify-otp') ? ['required', 'string', 'size:6', new IsNumeric] : '',
            'newPassword' => $this->routeIs('api-auth.change-password') ? 'required|string|min:8' : '',
            'oldPassword' => $this->routeIs('api-auth.change-password') ? 'required_without_all:resetPasswordToken' : '',
            'resetPasswordToken' => $this->routeIs('api-auth.change-password') ? 'required_without_all:oldPassword' : '',
        ];
    }
}
