<?php

namespace App\Http\Controllers\PropertyData;

use App\Http\Controllers\Controller;
use App\Http\Requests\CreateResourceRequest;
use App\Services\PropertyDataService;
use Illuminate\Http\Request;

class ResourceCreationController extends Controller
{
    public function __construct(private readonly PropertyDataService $propertyDataService) {}

    public function createResource(CreateResourceRequest $request)
    {
        $data = $request->validated();
        $resourceName = $request->query('resource_name');
        $result = $this->propertyDataService->createNewResource($resourceName, $data);
        return apiResponse($result);
    }
}
