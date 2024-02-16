<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\TourFeedback\StoreRequest;
use App\Models\TourFeedback;
use Illuminate\Http\Request;

class TourFeedBackController extends Controller
{   
    public function index(Request $request) {

    }

    public function store(StoreRequest $request) {
        $data = $request->validated();
        $sum_of_categories = $request->category_one_rate + $request->category_two_rate + $request->category_three_rate + $request->category_four_rate + $request->category_five_rate + $request->category_six_rate;
        $total_rate = $this->calculateTotalRate($sum_of_categories);
        $data = array_merge($data, ['total_rate' => $total_rate]);
        $tour_feedback = TourFeedback::create($data);

        return response([
            'status' => TRUE,
            'message' => 'Feedback Created Successfully',
            'feedback' => $tour_feedback,
        ], 201);
    }

    public function show(Request $request, $id) {
        $tour_feedback = TourFeedback::where('id', $id)->with('tour')->first();

        $tour_feedback->tour->setAppends([]);
        
        return response([
            'status' => TRUE, 
            'message' => 'Feedback Found',
            'feedback' => $tour_feedback,
        ]);
    }

    public function update(StoreRequest $request, $id) {
        $tour_feedback = TourFeedback::where('id', $id)->first();

        $data = $request->validated();
        $sum_of_categories = $request->category_one_rate + $request->category_two_rate + $request->category_three_rate + $request->category_four_rate + $request->category_five_rate + $request->category_six_rate;
        $total_rate = $this->calculateTotalRate($sum_of_categories);
        
        $data = array_merge($data, ['total_rate' => $total_rate]);
        $tour_feedback->update($data);

        return response([
            'status' => TRUE, 
            'message' => 'Feedback Updated Successfully',
            'feedback' => $tour_feedback,
        ], 200);
    }

    public function getFeedBacksByTour(Request $request, $tour_id) {
        $tour_feedbacks = TourFeedback::where('tour_id', $tour_id)->orderBy('total_rate', 'desc')->get();
        
        return response([
            'status' => TRUE,
            'message' => 'Feedbacks Found.',
            'feedbacks' => $tour_feedbacks,
        ]);
    }

    private function calculateTotalRate($sum_of_categories) {
        $percentage =  ($sum_of_categories / 30) * 100; // 30 is the total for every maximum 5 rate of each category
        
        $total_range  = $percentage / 20;

        return round($total_range);
    }
}
