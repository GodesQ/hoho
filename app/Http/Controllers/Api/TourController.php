<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Models\ProductCategory;
use App\Models\Tour;

class TourController extends Controller
{
    public function getGuidedTours(Request $request) {
        $tours = Tour::where('type', 'Guided Tour')->get();

        return response([
            'status' => TRUE,
            'message' => 'Tours Found',
            'tours' => $tours
        ]);
    }

    public function getDIYTours(Request $request) {
        $tours = Tour::where('type', 'DIY Tour')->get();
        $product_categories = ProductCategory::get();

        return response([
            'status' => TRUE,
            'message' => 'Tours Found',
            'product_categories' => $product_categories,
            'tours' => $tours
        ]);
    }
}
