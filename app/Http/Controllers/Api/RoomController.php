<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Room;
use Illuminate\Http\Request;

class RoomController extends Controller
{
    public function getRooms(Request $request) {
        $merchant_id = $request->merchant_id;
        $rooms = Room::where("merchant_id", $merchant_id)->get();

        return response([
            "status" => TRUE,
            "rooms" => $rooms
        ]);
    }

    public function getRoom(Request $request) {
        $room_id = $request->room_id;
        $room = Room::where("id", $room_id)->first();
    }
}
