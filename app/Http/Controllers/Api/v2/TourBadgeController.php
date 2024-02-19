<?php

namespace App\Http\Controllers\Api\v2;

use App\Http\Controllers\Controller;
use App\Http\Resources\TourBadgeResource;
use App\Models\TourBadge;
use Illuminate\Http\Request;

class TourBadgeController extends Controller
{
    public function index(Request $request) {
        return TourBadgeResource::collection(TourBadge::get());
    }

    public function show(Request $request, $tour_badge_id) {
        return TourBadgeResource::make(TourBadge::findOrFail($tour_badge_id)); 
    }
}
