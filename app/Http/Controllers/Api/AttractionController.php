<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Models\Attraction;

class AttractionController extends Controller
{   
    public function getAllAttractions(Request $request) {
        
    }

    public function getAttraction(Request $request) {
        $attraction = Attraction::where('id', $request->id)->with('organization')->firstOrFail();
        
        return response()->json($attraction);

        // return response([
        //     'status' => TRUE,
        //     'message' => 'Attraction Found',
        //     'attraction' => $attraction
        // ]);

    }
}
