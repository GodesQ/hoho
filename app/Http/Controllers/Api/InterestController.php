<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Models\Interest;

class InterestController extends Controller
{
    public function getInterests(Request $request) {
        $interests = Interest::get();

        return response([
            'status' => true,
            'interests' => $interests
        ]);
    }
}
