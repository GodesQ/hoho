<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\TourFeedback\StoreRequest;
use App\Models\TourFeedBack;
use Illuminate\Http\Request;

class TourFeedBackController extends Controller
{   
    public function index(Request $request) {

    }

    public function store(StoreRequest $request) {
        $tour_feedback = TourFeedback::create($request->validated());
    }

    public function show(Request $request, $id) {

    }

    public function update(Request $request, $id) {
    
    }
}
