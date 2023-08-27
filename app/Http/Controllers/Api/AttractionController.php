<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Models\Attraction;

class AttractionController extends Controller
{
    public function getAttraction(Request $request) {
        $attraction = Attraction::where('id', $request->id)->with('organization')->first();

        if(!$attraction) return response(['status' => FALSE, 'message' => 'Attraction not found.'], 404);

        return response([
            'status' => TRUE,
            'message' => 'Attraction Found',
            'attraction' => $attraction
        ]);

    }
}
