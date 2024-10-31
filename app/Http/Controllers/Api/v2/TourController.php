<?php

namespace App\Http\Controllers\Api\v2;

use App\Http\Controllers\Controller;
use App\Http\Resources\TourResource;
use App\Models\Tour;
use Carbon\Carbon;
use Illuminate\Http\Request;

class TourController extends Controller
{
    public function index(Request $request)
    {
        $tours = Tour::where('status', 1)
            ->when($request->query('type'), function ($query) use ($request) {
                $type = $request->query('type') == 'diy' ? 'DIY Tour' : 'Guided Tour';
                $query->where('type', $type);
            })
            ->whereIn('type', ['Guided Tour', 'DIY Tour', 'Seasonal Tour'])
            ->get();
        return TourResource::collection($tours);
    }

    public function show(Request $request, $tour_id)
    {
        return TourResource::make(Tour::findOrFail($tour_id));
    }

    public function getGuidedTours(Request $request)
    {
        $length = $request->query('length') ?? '';
        $tours = Tour::where('status', 1)
            ->where('type', 'Guided Tour')
            ->inRandomOrder()
            ->limit($length)
            ->get();

        return TourResource::collection($tours);
    }

    public function getDIYTours(Request $request)
    {
        $length = $request->query('length') ?? '';

        $tours = Tour::where('status', 1)
            ->where('type', 'DIY Tour')
            ->inRandomOrder()
            ->limit($length)
            ->get();

        return TourResource::collection($tours);
    }

    public function getTransitTours(Request $request)
    {

        $arrival_datetime = Carbon::parse($request->arrival_datetime);
        $departure_datetime = Carbon::parse($request->departure_datetime);

        // Calculate the difference in hours
        $total_hours = $arrival_datetime->diffInHours($departure_datetime);

        // Minimum of 8 hours
        if ($total_hours < 8) {
            return response([
                'status' => FALSE,
                'message' => 'Transit Tours are not available at your requested time.',
            ], 400);
        }

        $tours = Tour::where('type', 'Layover Tour')
            ->where('status', 1)
            ->get();

        foreach ($tours as $tour) {
            $tour->setAppends([]);
        }

        return response([
            'tours' => TourResource::collection($tours),
        ]);

    }


}
