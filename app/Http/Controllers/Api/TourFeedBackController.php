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
        $data = $request->validated();
        $total_rate = $this->calculateTotalRate($request->category_one_rate, $request->category_two_rate, $request->category_three_rate);
        
        $data = array_merge($data, ['total_rate' => $total_rate]);
        $tour_feedback = TourFeedback::create($data);

        return response([
            'status' => TRUE,
            'message' => 'Feedback Created Successfully',
            'feedback' => $tour_feedback,
        ], 201);
    }

    public function show(Request $request, $id) {
        $tour_feedback = TourFeedBack::where('id', $id)->with('tour')->first();

        $tour_feedback->tour->setAppends([]);
        
        return response([
            'status' => TRUE, 
            'message' => 'Feedback Found',
            'feedback' => $tour_feedback,
        ]);
    }

    public function update(StoreRequest $request, $id) {
        $tour_feedback = TourFeedBack::where('id', $id)->first();

        $data = $request->validated();
        $total_rate = $this->calculateTotalRate($request->category_one_rate, $request->category_two_rate, $request->category_three_rate);
        
        $data = array_merge($data, ['total_rate' => $total_rate]);
        $tour_feedback->update($data);

        return response([
            'status' => TRUE, 
            'message' => 'Feedback Updated Successfully',
            'feedback' => $tour_feedback,
        ], 200);
    }

    private function calculateTotalRate($category_one, $category_two, $category_three) {
        $sum_of_categories = $category_one + $category_two + $category_three;
        $percentage =  ($sum_of_categories / 15) * 100; // 15 is the total for every maximum 5 rate of each category
        
        $total_range  = $percentage / 20;

        return round($total_range);
    }
}
