<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CreateResourceRequest extends FormRequest
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
        $rules = ['name' => 'required|string'];

        $resourceDependencyMap = collect([
            'region' => ['state_id'],
            'location' => ['state_id', 'region_id'],
            'section' => ['state_id', 'region_id', 'location_id'],
            'lga' => ['state_id', 'region_id'],
            'lcda' => ['state_id', 'region_id', 'lga_id'],
            'subSector' => ['sector_id'],
        ]);

        $resourceName = $this->query('resource_name');

        if ($resourceDependencyMap->has($resourceName)) {
            $dependencies = $resourceDependencyMap->get($resourceName);
            foreach ($dependencies as $dependency) {
                $rules[$dependency] =  'required|numeric';
            }
        };
        return $rules;
    }
}
