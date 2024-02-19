<?php

namespace App\Http\Controllers\Api\v2;

use App\Http\Controllers\Controller;
use App\Http\Resources\InterestResource;
use App\Models\Interest;
use Illuminate\Http\Request;

class InterestController extends Controller
{
    public function index(Request $request) {
        return InterestResource::collection(Interest::get());
    }

    public function show(Request $request, $interest_id) {
        return InterestResource::make(Interest::findOrFail($interest_id));
    }

    // public function userInterest(Request $request, $user_id) {
    //     $interests = Interest
    // }
}
