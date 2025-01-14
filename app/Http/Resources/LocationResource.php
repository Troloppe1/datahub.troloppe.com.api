<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class LocationResource extends JsonResource
{

    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $resource = [
            "id" => $this->id,
            "name" => $this->name,
            "is_active" => $this->is_active
        ];
        $isFormFieldRequest = $request->routeIs('street-data.form-field-data.index');
        
        if (!$isFormFieldRequest) {
            return $resource;
        }
        $resource['sections'] = SectionResource::collection($this->sections);
        return $resource;
    }
}