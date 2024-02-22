<?php

namespace App\Http\Controllers\Api\v2;

use App\Http\Controllers\Controller;
use App\Http\Resources\AttractionResource;
use App\Models\Attraction;
use Exception;
use Illuminate\Http\Exceptions\ThrottleRequestsException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class AttractionController extends Controller
{
    public function index(Request $request) {
        $attractions = Cache::remember('attractions', 60, function() use ($request) {
            $length = $request->query('length') ?? '';
            return Attraction::where('status', 1)->inRandomOrder()->limit($length)->get();
        });

        return AttractionResource::collection($attractions);
    }

    public function show(Request $request, $attraction_id) {
        $attraction = Attraction::findOrFail($attraction_id);
        return AttractionResource::make($attraction);
    }

    public function attractionsByOrganization(Request $request, $organization_id) {
        return AttractionResource::collection(Attraction::where('organization_id', $organization_id)->get());
    }
}
