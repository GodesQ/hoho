<?php

namespace App\Http\Controllers\Api\v2;

use App\Http\Controllers\Controller;
use App\Http\Resources\AttractionResource;
use App\Models\Attraction;
use Illuminate\Http\Request;

class AttractionController extends Controller
{
    public function index(Request $request) {
        return AttractionResource::collection(Attraction::get());
    }

    public function getByOrganization(Request $request, $organization_id) {
        return AttractionResource::collection(Attraction::where('organization_id', $organization_id)->get());
    }
}
