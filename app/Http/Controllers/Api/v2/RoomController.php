<?php

namespace App\Http\Controllers\Api\v2;

use App\Http\Controllers\Controller;
use App\Http\Resources\RoomResource;
use App\Models\Room;
use Illuminate\Http\Request;

class RoomController extends Controller
{
    public function index(Request $request) {

    }

    public function merchantRooms(Request $request, $merchant_id) {
        $rooms = Room::where("merchant_id", $merchant_id)->get();

        return RoomResource::collection($rooms);
    }

    public function show(Request $request, $room_id) {
        $room = Room::findOrFail($room_id);
        
        return RoomResource::make($room);
    }
}
