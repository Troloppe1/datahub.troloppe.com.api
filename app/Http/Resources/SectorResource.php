<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class SectorResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $resource = [
            'id' => $this->id,
            'name' => ucwords($this->name),
        ];
        $isFormFieldRequest = request()->routeIs('street-data.form-field-data.index');

        if (!$isFormFieldRequest) {
            return $resource;
        }
        $resource['sub_sectors'] = SubSectorResource::collection($this->subSectors);
        return $resource;
    }
}
