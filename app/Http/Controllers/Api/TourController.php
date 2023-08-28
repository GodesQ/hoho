<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

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
}
