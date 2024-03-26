<?php

namespace App\Http\Controllers\Api\v2;

use App\Http\Controllers\Controller;
use App\Http\Resources\InterestResource;
use App\Models\Interest;
use App\Models\User;
use Illuminate\Http\Request;

class InterestController extends Controller
{
    public function index(Request $request) {
        return InterestResource::collection(Interest::get());
    }

    public function show(Request $request, $interest_id) {
        return InterestResource::make(Interest::findOrFail($interest_id));
    }

    public function userInterests(Request $request, $user_id) {
        $user = User::where("id", $user_id)->first();

        $user_interest_ids = json_decode($user->interest_ids);
        $user_interests = Interest::whereIn("id", $user_interest_ids)->get();

        return response([
            'interests' => $user_interests,
            'user' => $user->setAppends([]),
        ]);
    }
}
