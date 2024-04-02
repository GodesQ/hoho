<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\TourTimeslot;
use Illuminate\Http\Request;

class TourTimeslotController extends Controller
{
    public function tourTimeslots(Request $request) {
        $timeslots = TourTimeslot::where("tour_id", $request->tour_id)->get();
        
    }
}
