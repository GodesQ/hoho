<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

use App\Models\ProductCategory;
use App\Models\Tour;

class TourController extends Controller
{
    public function getGuidedTours(Request $request)
    {
        $userInterests = Auth::user()->interest_ids ? json_decode(Auth::user()->interest_ids) : [];
        // return $userInterests;
        $tours = Tour::where('type', 'Guided Tour')
            ->where('status', 1)
            ->inRandomOrder()
            ->get();

        $filtered_tours = [];

        if(count($userInterests) > 0) {
            $filtered_tours_interests = $tours->filter(function ($tour) use ($userInterests) {
                $tour_interests = $tour->interests ? json_decode($tour->interests) : [];
                return $tour_interests && array_intersect($userInterests, $tour_interests);
            });
            $filtered_tours = array_merge($filtered_tours, $filtered_tours_interests->all());
        }

        if(count($filtered_tours) > 0) {
            $tours = $filtered_tours;
        }

        return response([
            'status' => TRUE,
            'message' => 'Tours Found',
            'tours' => $tours
        ]);
    }

    public function getDIYTours(Request $request)
    {
        $tours = Tour::where('type', 'DIY Tour')->get();

        foreach ($tours as $tour) {
            $tour->setAppends([]); // Exclude the "attractions" attribute for this instance
        }

        $product_categories = ProductCategory::select('id', 'name', 'description', 'featured_image')->get();

        foreach ($product_categories as $category) {
            $category->setAppends([]); // Exclude the "organizations" attribute for this instance
        }

        return response([
            'status' => TRUE,
            'message' => 'Tours Found',
            'product_categories' => $product_categories,
            'tours' => $tours
        ]);
    }
}
