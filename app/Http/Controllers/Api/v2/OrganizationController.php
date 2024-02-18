<?php

namespace App\Http\Controllers\Api\v2;

use App\Http\Controllers\Controller;
use App\Http\Resources\OrganizationResource;
use App\Models\Organization;
use Illuminate\Http\Request;

class OrganizationController extends Controller
{
    public function index(Request $request) {
        $organizations = Organization::get();
        return OrganizationResource::collection($organizations);
    }

    public function show(Request $request, $organization_id) {
        return OrganizationResource::make(Organization::findOrFail($organization_id));
    }
}
