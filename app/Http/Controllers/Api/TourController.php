<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\TourResource;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

use App\Models\ProductCategory;
use App\Models\Tour;

class TourController extends Controller
{
    public function getGuidedTours(Request $request)
    {
        $userInterests = Auth::user()->interest_ids && Auth::user()->interest_ids != 'null' ? json_decode(Auth::user()->interest_ids) : [];

        // return $userInterests;
        $tours = Tour::where('type', 'Guided Tour')
            ->where('status', 1)
            ->inRandomOrder()
            ->with('timeslots')
            ->get();

        $tours->each(function ($tour) {
            $tour->setAppends([]);
        });

        $filtered_tours = [];

        if (count($userInterests) > 0) {
            $filtered_tours_interests = $tours->filter(function ($tour) use ($userInterests) {
                $tour_interests = $tour->interests ? json_decode($tour->interests) : [];
                return $tour_interests && array_intersect($userInterests, $tour_interests);
            });
            $filtered_tours = array_merge($filtered_tours, $filtered_tours_interests->all());
        }

        if (count($filtered_tours) > 0) {
            $tours = $filtered_tours;
        }

        return response([
            'status' => TRUE,
            'tours' => $tours
        ]);
    }

    public function getDIYTours(Request $request)
    {
        $tours = Tour::where('type', 'DIY Tour')->where('status', 1)->get();

        foreach ($tours as $tour) {
            $tour->setAppends([]);
        }

        $product_categories = ProductCategory::select('id', 'name', 'description', 'featured_image')->get();

        foreach ($product_categories as $category) {
            $category->setAppends([]);
        }

        return response([
            'status' => TRUE,
            'message' => 'Tours Found',
            'product_categories' => $product_categories,
            'tours' => $tours
        ]);
    }

    public function getTransitTours(Request $request)
    {

        $arrival_datetime = Carbon::parse($request->arrival_datetime);
        $departure_datetime = Carbon::parse($request->departure_datetime);

        // Calculate the difference in hours
        $total_hours = $arrival_datetime->diffInHours($departure_datetime);

        // Minimum of 5 hours
        if($total_hours <= 5) {
            return response([
                'status' => FALSE,
                'message' => 'Layover Tours is not available for your specified time',
            ], 400);
        } 

        $tours = Tour::where('type', 'Layover Tour')->where('status', 1)->get();
        foreach ($tours as $tour) {
            $tour->setAppends([]);
        }

        return response([
            'status' => TRUE,
            'tours' => $tours
        ]);

    }

    public function getSeasonalTours(Request $request) {
        $tours = Tour::where('type', 'Seasonal Tour')->get();

        foreach ($tours as $tour) {
            $tour->setAppends([]);
        }

        return response([
            'status'=> TRUE,
            'tours' => $tours,
        ]);
    }
}
