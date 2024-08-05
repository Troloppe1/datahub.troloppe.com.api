<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StreetDataRequest extends FormRequest
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
            'is_verified' => request()->routeIs('street-data.update') ? 'required|boolean' : '',
            'unique_code' => 'string|nullable',
            'street_address' => 'required|string',
            'development_name' => 'string|nullable',
            'description' => 'required|string',
            'sector_id' => 'required|numeric',
            'sub_sector_id' => 'required|numeric',
            'location_id' => 'required|numeric',
            'section_id' => 'required|numeric',
            'number_of_units' => 'required|numeric',
            'contact_name' => 'nullable|string|max:250',
            'contact_numbers' => 'nullable|string|max:250',
            'contact_email' => 'nullable|string|max:250',
            'construction_status' => 'required|string',
            'image_path' => 'required|string',
            'geolocation' => 'nullable|string|max:250',
        ];
    }
}
