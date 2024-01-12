<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Http\Requests\Room\StoreRequest;
use Illuminate\Http\Request;

use App\Models\Room;
use App\Models\Merchant;
use App\Models\ProductCategory;

class RoomController extends Controller
{
    public function index(Request $request) {
        return view('admin-page.rooms.list-room');
    }

    public function create(Request $request) {
        $merchants = Merchant::where('type', 'Hotel')->get();
        $product_categories = ProductCategory::get();
        return view('admin-page.rooms.create-room', compact('merchants', 'product_categories'));
    }

    public function store(StoreRequest $request) {
        $data = $request->validated();
        dd($data);
    }

    public function show(Request $request) {
    
    }

    public function edit(Request $request, Room $room) {
        
    }

    public function update(Request $request, Room $room) {
    
    }

    public function destroy(Request $request, Room $room) {
    
    }

}
