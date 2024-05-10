<?php

namespace App\Http\Controllers\Api\v2;

use App\Http\Controllers\Controller;
use App\Http\Resources\TourResource;
use App\Models\Tour;
use Illuminate\Http\Request;

class TourController extends Controller
{
    public function index(Request $request) {
        $tours = Tour::where('status', 1)
                ->when($request->query('type'), function ($query) use ($request) {
                    $type = $request->query('type') == 'diy' ? 'DIY Tour' : 'Guided Tour';
                    $query->where('type', $type);
                })
                ->whereIn('type', ['Guided Tour', 'DIY Tour'])
                ->get();
        return TourResource::collection($tours);
    }

    public function show(Request $request, $tour_id) {
        return TourResource::make(Tour::findOrFail($tour_id));
    }

    public function getGuidedTours(Request $request) {
        $length = $request->query('length') ?? '';
        $tours = Tour::where('status', 1)
                ->where('type', 'Guided Tour')
                ->inRandomOrder()
                ->limit($length)
                ->get();

        return TourResource::collection($tours);
    }

    public function getDIYTours(Request $request) {
        $length = $request->query('length') ?? '';

        $tours = Tour::where('status', 1)
                ->where('type', 'DIY Tour')
                ->inRandomOrder()
                ->limit($length)
                ->get();

        return TourResource::collection($tours);
    }

    
}
