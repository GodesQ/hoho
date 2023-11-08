<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Models\ProductCategory;
use App\Models\Tour;

class TourController extends Controller
{
    public function getGuidedTours(Request $request) {
        $tours = Tour::where('type', 'Guided Tour')->where('status', 1)->inRandomOrder()->get();

        return response([
            'status' => TRUE,
            'message' => 'Tours Found',
            'tours' => $tours
        ]);
    }

    public function getDIYTours(Request $request) {
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
