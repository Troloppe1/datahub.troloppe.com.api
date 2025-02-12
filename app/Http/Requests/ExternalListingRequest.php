<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ExternalListingRequest extends FormRequest
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
            "id" => "sometimes|integer",
            "state_id" => "required|numeric",
            "region_id" => "required|numeric",
            "locality_id" => "required|numeric",
            "section_id" => "required|numeric",
            "lga_id" => "required|numeric",
            "lcda_id" => "required|numeric",
            "street" => "required|string",
            "street_number" => "nullable|string",
            "development" => "nullable|string",
            "sector_id" => "required|numeric",
            "sub_sector_id" => "required|numeric",
            "subType" => "nullable|string",
            "offer_id" => "required|numeric",
            "no_of_beds" => "nullable|numeric",
            "size" => "nullable|numeric",
            "land_area" => "nullable|numeric",
            "sale_price" => "nullable|numeric",
            "lease_price" => "nullable|numeric",
            "price_per_sqm" => "nullable|numeric",
            "service_charge" => "nullable|numeric",
            "developer_id" => "nullable|numeric",
            "listing_agent_id" => "required|numeric",
            "listing_source_id" => "required|numeric",
            "comment" => "nullable|string",
            "updated_by_id" => "sometimes|numeric"
        ];
    }
}
